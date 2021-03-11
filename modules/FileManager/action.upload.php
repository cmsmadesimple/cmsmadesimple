<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;
require_once( __DIR__.'/lib/class.jquery_upload_handler.php' );

class FileManagerUploadHandler extends jquery_upload_handler
{
  function __construct($options=null)
  {
    if( !is_array($options) ) $options = array();

    // remove image handling, we're gonna handle this another way
    $options['orient_image'] = false;  // turn off auto image rotation
    $options['image_versions'] = array();

    $options['upload_dir'] = filemanager_utils::get_full_cwd().'/';
    $options['upload_url'] = filemanager_utils::get_cwd_url().'/';

    // set everything up.
    parent::__construct($options);
  }

  protected function is_file_acceptable( $file )
  {
      $config = \cms_config::get_instance();
      if( !$config['developer_mode'] ) {
          $ext = strtolower(substr(strrchr($file, '.'), 1));
          if( startswith($ext,'php') || endswith($ext,'php') ) return FALSE;
      }
      return TRUE;
  }

  protected function after_uploaded_file($fileobject)
  {
      // here we may do image handling, and other cruft.
      if( is_object($fileobject) && $fileobject->name != '' ) {

          $mod = cms_utils::get_module('FileManager');
          $file = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),$fileobject->name);
          $parms['file'] = $file;
          $parms = \CMSMS\HookManager::do_hook( 'FileManager::OnFileUploaded', $parms );
          if( is_array($parms) && isset($parms['file']) ) $file = $parms['file']; // file name could have changed.

          $thumb = null;
          if( $mod->GetPreference('create_thumbnails') ) {
              $thumb = filemanager_utils::create_thumbnail($file);
          }

          $str = basename($file).' uploaded to '.filemanager_utils::get_full_cwd();
          if( $thumb ) $str .= ' and a thumbnail was generated';
          audit('',$mod->GetName(),$str);
      }
  }
}

$options = array('param_name'=>$id.'files');
$upload_handler = new FileManagerUploadHandler($options);

header('Pragma: no-cache');
header('Cache-Control: private, no-cache');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'OPTIONS':
        break;
    case 'HEAD':
    case 'GET':
        $upload_handler->get();
        break;
    case 'POST':
        $upload_handler->post();
        break;
    case 'DELETE':
        $upload_handler->delete();
        break;
    default:
        header('HTTP/1.1 405 Method Not Allowed');
}

exit;

#
# EOF
#