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
if (isset($params["cancel"])) $this->Redirect($id,"defaultadmin",$returnid,$params);

$selall = $params['selall'];
if( !is_array($selall) ) $selall = unserialize($selall);
if( !is_array($selall) ) $selall = unserialize($selall);

if (count($selall)==0) {
  $params["fmerror"]="nofilesselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

// decode the sellallstuff.
foreach( $selall as &$one ) {
  $one = $this->decodefilename($one);
}

// process form
$errors = array();
if( isset($params['submit']) ) {
  $advancedmode = filemanager_utils::check_advanced_mode();
  $basedir = $config['root_path'];
  $config = cmsms()->GetConfig();

  foreach( $selall as $file ) {
    // build complete path
    $fn = filemanager_utils::join_path($basedir,filemanager_utils::get_cwd(),$file);
    if( !file_exists($fn) ) continue; // no error here.

    if( !is_writable($fn) ) {
      $errors[] = $this->Lang('error_notwritable',$file);
      continue;
    }

    if( is_dir($fn) ) {
      // check to make sure it's empty
      $tmp = scandir($fn);
      if( count($tmp) > 2 ) { // account for . and ..
	$errors[] = $this->Lang('error_dirnotempty',$file);
	continue;
      }
    }

    $thumb = '';
    if( filemanager_utils::is_image_file($file) ) {
      // check for thumb, make sure it's writable.
      $thumb = filemanager_utils::join_path($basedir,filemanager_utils::get_cwd(),'thumb_'.basename($file));
      if( file_exists($fn) && !is_writable($fn) ) $errors[] = $this->Lang('error_thumbnotwritable',$file);
    }

    // at this point, we should be good to delete.
    if( is_dir($fn) ) {
      @rmdir($fn);
    } else {
      @unlink($fn);
    }
    if( $thumb != '' ) @unlink($thumb);

    $parms = array('file'=>$fn);
    if( $thumb ) $parms['thumb'] = $thumb;
    audit('',"File Manager", "Removed file: ".$fn);
    \CMSMS\HookManager::do_hook('FileManager::OnFileDeleted', $parms );
  } // foreach

  if( count($errors) == 0 ) {
    $paramsnofiles["fmmessage"]="deletesuccess"; //strips the file data
    $this->Redirect($id,"defaultadmin",$returnid,$paramsnofiles);
  }
} // if submit

// give everything to smarty.
if( count($errors) ) {
  echo $this->ShowErrors($errors);
  $smarty->assign('errors',$errors);
}
if( is_array($params['selall']) ) $params['selall'] = serialize($params['selall']);
$smarty->assign('selall',$selall);
$smarty->assign('mod',$this);
$smarty->assign('startform', $this->CreateFormStart($id, 'fileaction', $returnid,"post","",false,"",$params));
$smarty->assign('endform', $this->CreateFormEnd());

echo $this->ProcessTemplate('delete.tpl');

#
# EOF
#