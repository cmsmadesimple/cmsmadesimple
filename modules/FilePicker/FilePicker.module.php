<?php
#-------------------------------------------------------------------------
# Module: FilePicker - A CMSMS addon module to provide file picking capabilities.
# (c) 2016 by Fernando Morgado <jomorg@cmsmadesimple.org>
# (c) 2016 by Robert Campbell <calguy1000@cmsmadesimple.org>
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This projects homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
#-------------------------------------------------------------------------
# BEGIN_LICENSE
#-------------------------------------------------------------------------
# This file is part of FilePicker
# FilePicker is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# FilePicker is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#-------------------------------------------------------------------------
# END_LICENSE
#-------------------------------------------------------------------------
use \FilePicker\TemporaryProfileStorage;

require_once(__DIR__.'/lib/class.ProfileDAO.php');

final class FilePicker extends \CMSModule implements \CMSMS\FilePickerInterface
{
    protected $_dao;

    public function __construct()
    {
        parent::__construct();
        $this->_dao = new \FilePicker\ProfileDAO( \CmsApp::get_instance()->GetDb() );
    }

    private function _encodefilename($filename)
    {
        return str_replace('==', '', base64_encode($filename));
    }

    private function _decodefilename($encodedfilename)
    {
        return base64_decode($encodedfilename . '==');
    }

    function VisibleToAdminUser()
    {
        return $this->CheckPermission('Modify Site Preferences');
    }

    private function _to_url($dir = '', $relative = TRUE)
    {
        $config = \cms_config::get_instance();

        if($dir == '' || $dir == '.') return $config['root_url'];

        $ret = str_replace($config['root_url'], '', $dir);
        $ret = str_replace(DIRECTORY_SEPARATOR, '/', $ret);
        $ret = trim($ret, '/');

        return $ret;
    }

    private function _GetTemplateObject()
    {
        $ret = $this->GetActionTemplateObject();
        if( is_object($ret) ) return $ret;
        return CmsApp::get_instance()->GetSmarty();
    }

    /**
     * end of private methods
     */

    function GetFriendlyName() { return $this->Lang('friendlyname');  }
    function GetVersion() { return '1.0.alpha'; }
    function GetHelp() { return $this->Lang('help'); }
    function IsPluginModule() { return FALSE; }
    function HasAdmin() { return TRUE; }
    function GetAdminSection() { return 'extensions'; }

    function HasCapability( $capability, $params = array() )
    {
        switch( $capability ) {
        case 'contentblocks':
        case 'filepicker':
        case 'upload':
            return TRUE;
        default:
            return FALSE;
        }
    }

    function GetHeaderHTML()
    {
        return $this->_output_header_javascript();
    }

    /**
     * A function to try to extract a valid full path to a directory.
     * It accepts full paths (to validate them)
     * or relative paths.
     * It also accepts full or relative URLs
     * and tries to extract a valid full path from them.
     * The only condition it that they must be children
     * of the root dir where CMSMS is installed
     * otherwise it returns an empty string
     *
     * @todo we can expand it to accept directories outside CMSMS root
     *
     * @param mixed $dir
     * @param mixed $full indicates if we want a full or relative path
     * @returns either a full valid or a verified relative path to a directory,
     * or an empty string in case of failure
     */
    public function getValidDir($dir, $full = FALSE)
    {
        $config = \cms_config::get_instance();

        if($dir == '.') $dir = $config['root_path'];

        # we have a valid dir...
        if( startswith( $dir, $config['root_path']) && is_dir($dir) ) {
            if($full) return $dir;
            return  str_replace($config['root_path'], '', $dir);
        }

        # else we try to solve $dir into a valid full path or return an empty string
        $ret = '';

        # if it is a valid relative dir we are done
        $tmp = $config['root_path'] . DIRECTORY_SEPARATOR . $dir;

        if( is_dir($tmp) ) {
            if($full) return $tmp;
            return $dir;
        }

        # remove protocols if they exist in cases that dir is a full URL
        $tmp = explode(':', $dir);

        if($tmp[0] == 'http' ||  $tmp[0] == 'https' ) $dir = $tmp[1];

        # and try to extract a valid path from it
        if( startswith( $dir, $config['root_url']) ) {
            $dir = str_replace($config['root_url'], '', $dir);

            if( empty($dir) ) {
                // it's CMSMS root
                $ret = $config['root_path'];
            }
            else {
                $dir = implode( DIRECTORY_SEPARATOR, explode('/', trim($dir, '/') ) );
                $ret = cms_join_path($config['root_path'] , $dir);
            }
        }

        $ret = is_dir($ret) ? $ret : '';

        if($full) return $ret;
        return  str_replace($config['root_path'], '', $ret);
    }

    /**
     * @internal
     *
     * note: probably should be private
     */
    function _is_user_from_groups( $uid = -1, $groups = array() )
    {
        $ret = FALSE; // user is not a member of any of the specified groups as a default

        foreach($groups as $gid) {
            $users = cmsms()->GetUserOperations()->LoadUsersInGroup($gid);

            if( !is_array($users) || !count($users) ) continue;

            foreach( $users as $user ) {
                if($user->id == $uid) {
                    $ret = TRUE;
                    break;
                }
            }

            if($ret) break;
        }

        return $ret;
    }

    function GetContentBlockFieldInput($blockName, $value, $params, $adding, ContentBase $content_obj)
    {
        if( empty($blockName) ) return FALSE;
        $uid = get_userid(FALSE);
        //$adding = (bool)( $adding || ($content_obj->Id() < 1) ); // hack for the core. Have to ask why though (JM)

        $profile = $this->get_default_profile();
        $profile_name = get_parameter_value($params,'profile');
        if( $profile_name ) {
            $tmp = $this->get_profile($profile);
            if( $tmp ) $profile = $tmp;
        }
        // todo: optionally allow further overriding the profile
        $out = $this->get_html($blockName, $value, $profile);
        return $out;
    }

//  function ValidateContentBlockFieldValue($blockName,$value,$blockparams,ContentBase $content_obj)
//  {
//    echo('<br/>:::::::::::::::::::::<br/>');
//    debug_display($blockName, '$blockName');
//    debug_display($value, '$value');
//    debug_display($blockparams, '$blockparams');
//    //debug_display($adding, '$adding');
//    echo('<br/>' . __FILE__ . ' : (' . __CLASS__ . ' :: ' . __FUNCTION__ . ') : ' . __LINE__ . '<br/>');
//    //die('<br/>RIP!<br/>');
//  }

    public function GetFileListDropdown($path = '')
    {
        $ret = array( -1 => lang('none') );
        $config = \cms_config::get_instance();

        $url = $this->_to_url($path);

        if($path == '' || $path == '.') {
            $fullpath = $config['root_path'];
        }
        else {
            $fullpath = $this->getValidDir($path, TRUE);
        }

        $files = $this->GetFileList($path);

        if( is_array($files) ) {
            foreach($files as $file) {
                if(!$file['dir']) {
                    $val = $url . '/' . $file['name'];
                    $ret[$val] = $file['name'];
                }
            }
        }

        return $ret;
    }

    public function GetFileList($path = '')
    {
        return filemanager_utils::get_file_list($path);
    }

    public function get_default_profile()
    {
        // todo:  allow some defaults here, or load something from a rpeference
        $profile = new \CMSMS\FilePickerProfile;
        return $profile;
    }

    public function get_browser_url()
    {
        return $this->create_url('m1_','filepicker');
    }

    public function get_html( $name, $value, \CMSMS\FilePickerProfile $profile )
    {
        $_instance = 'i'.uniqid();
        if( $value === '-1' ) $value = null;

        // store the profile as a 'useonce' and add it's signature to the params on the url
        $sig = TemporaryProfileStorage::set( $profile );
        $smarty = \cms_utils::get_smarty(); // $this->_GetTemplateObject();
        $tpl_ob = $smarty->CreateTemplate($this->GetTemplateResource('contentblock.tpl'),null,null,$smarty);
        $tpl_ob->assign('sig',$sig);
        $tpl_ob->assign('blockName',$name);;
        $tpl_ob->assign('value',$value);
        $tpl_ob->assign('instance',$_instance);
        $tpl_ob->assign('profile',$profile);
        $out = $tpl_ob->fetch();
        return $out;
    }
} // end of class
