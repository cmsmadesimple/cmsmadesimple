<?php
#-------------------------------------------------------------------------
# Module: FilePicker - A CMSMS addon module to provide file picking capabilities.
# (c) 2016 by Fernando Morgado <jomorg@cmsmadesimple.org>
# (c) 2016 by Robert Campbell <calguy1000@cmsmadesimple.org>
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
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
final class FilePicker extends \CMSModule
{
/*
  reference for type:
  
  - \CMSMSFilePicker\ProfileParameter::TYPE_TEXTINPUT    = 0;
  - \CMSMSFilePicker\ProfileParameter::TYPE_TEXTAREA     = 1;
  - \CMSMSFilePicker\ProfileParameter::TYPE_DROPDOWN     = 2;
  - \CMSMSFilePicker\ProfileParameter::TYPE_MULTISELECT  = 3;
  - \CMSMSFilePicker\ProfileParameter::TYPE_CHECKBOX     = 4;
*/
 
  var $_params_list = array(
                              'profile'         => array(
                                                          'type' => 0, 
                                                          'value' => '',
                                                          'options' => array()
                                                        ),
                              'mode'            => array(
                                                          'type' => 2, 
                                                          'value' => 'dropdown',
                                                          'options' => array()
                                                        ),
                              'dir'             => array(
                                                          'type' => 0, 
                                                          'value' => 'uploads',
                                                          'options' => array()
                                                        ),
                              'upload'          => array(
                                                          'type' => 4,
                                                          'value' => FALSE,
                                                          'options' => array()
                                                        ),
                              'exclude_prefix'  => array(
                                                          'type' => 0, 
                                                          'value' => '',
                                                          'options' => array()
                                                        ),
                              'include_prefix'  => array(
                                                          'type' => 0, 
                                                          'value' => '',
                                                          'options' => array()
                                                        ),
                              'exclude_suffix'   => array(
                                                          'type' => 0, 
                                                          'value' => '',
                                                          'options' => array()
                                                        ),
                              'include_suffix'   => array(
                                                          'type' => 0, 
                                                          'value' => '',
                                                          'options' => array()
                                                        ),
                              'file_extensions' => array(
                                                          'type' => 0, 
                                                          'value' => '',
                                                          'options' => array()
                                                        ),
                              'show_thumbs'     => array(
                                                          'type' => 4, 
                                                          'value' => FALSE,
                                                          'options' => array()
                                                        ),
                              'can_delete'      => array(
                                                          'type' => 4, 
                                                          'value' => FALSE,
                                                          'options' => array()
                                                        ),
                              'groups'          => array(
                                                          'type' => 3, 
                                                          'value' => 1,
                                                          'options' => array()
                                                        )
                            );
                                
  private $_defaults = array();
  
  function __construct()
  {
    $this->_params_list['mode']['options'] = array(
                                                    'dropdown' => $this->Lang('ModeOptions_Dropdown'),
                                                    'browser' => $this->Lang('ModeOptions_Browser')
                                                  );
    
    $this->_params_list['groups']['options'] = $this->_get_groups_list();                                           
    $this->_load_default_preferences();
    parent::__construct();
  }

  function _output_header_javascript()
  {
    $out = '';
    $urlpath = $this->GetModuleURLPath() . '/js/ext';
    $jsfiles = array('jquery.iframe-transport.js');
    $jsfiles[] = 'jquery.fileupload.js';

    $fmt = '<script type="text/javascript" src="%s/%s"></script>';
    foreach( $jsfiles as $one ) 
    {
      $out .= sprintf($fmt, $urlpath, $one) . "\n";
    }

//    $fmt = '<link rel="stylesheet" type="text/css" href="%s/%s"/>';
//    $cssfiles = array('jrac/style.jrac.css');
//    foreach($cssfiles as $one) 
//    {
//      $out .= sprintf($fmt, $urlpath, $one);
//    }

    return $out;
  }
  
  private function _load_default_preferences()
  {
    foreach($this->_params_list as $k => $v)
    {
      $this->_defaults[$k] = $this->GetPreference($k, $v['value']);
    }
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
    return( 
            $this->CheckPermission('Modify Any Page') || 
            $this->CheckPermission('Manage All Content') ||
            $this->CheckPermission('Modify Templates')
          );
  }
    
  private function _to_url($dir = '', $relative = TRUE)
  {
    $config = cmsms()->GetConfig();
    
    if($dir == '' || $dir == '.')
    {
      return $config['root_url'];
    }
    
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
  
  private function _validate_profile_name($string = 'profile')
  {
    $ret = $string;
    $cnt = 0;
    
    while($this->profile_name_exists($ret))
    {
      $ret = $string . '-' . $cnt++;
    }
    
    return $ret;
  }
  
      private function _create_profile($name, $data = array() )
  {
    if( empty($name) ) return FALSE;
    
    $data = serialize($data);
    $db = cmsms()->GetDb();
    $now = $db->DbTimeStamp( time() );     
    $table = cms_db_prefix() . 'module_filepicker_profiles';
    $q = 'INSERT INTO ' . $table . " (name,data,create_date,modified_date) VALUES (?,?,$now,$now)";
    $r = $db->Execute($q, array($name, $data) );
    return TRUE;
  }
    
  private function _update_profile($id, $name, $data = array() )
  {
    if( !$this->profile_id_exists($id) ) return FALSE;
    if( empty($name) ) return FALSE;
        
    $data = serialize($data);
    $db = cmsms()->GetDb();
    $now = $db->DbTimeStamp( time() );  
    $table = cms_db_prefix() . 'module_filepicker_profiles';
    $q = $q = 'UPDATE ' . $table . " SET name=?,data=?,modified_date=$now  WHERE id = ?";
    $r = $db->Execute($q, array($name, $data, $id) );    
    return TRUE;
  }
  
  private function &_get_all_profiles()
  {
    $db = cmsms()->GetDb();
    $table = cms_db_prefix() . 'module_filepicker_profiles';
    $q = 'SELECT * FROM ' . $table;
    $row = $db->GetAll($q);
    foreach($row as &$one) $one['data'] = unserialize($one['data']);
    return $row;
  }
  
  /**
  * end of private methods  
  */
  
  /**
  * uses the given $params array
  * otherwise gets hardcoded defaults
  * 
  * @param mixed $params
  */
  public function _set_default_preferences( $params = array() )
  {
    foreach($this->_params_list as $k => $v)
    {
      $value = isset($params[$k]) ? $params[$k] : $v['value'];
      if($v['type'] == ProfileParameter::TYPE_MULTISELECT)
        $value = implode(',', $value);  
      if($v['type'] == ProfileParameter::TYPE_CHECKBOX)
        $value = (bool)$value;
      $this->SetPreference($k, $value);
    }
  }
  
  function GetFriendlyName() 
  { 
    return $this->Lang('friendlyname');  
  }
  
  function GetVersion() 
  { 
    return '1.0.alpha'; 
  }
  
  function GetHelp() {
	return $this->Lang('help');
  }
		
  function IsPluginModule() 
  { 
    return true; 
  }
  
  function HasAdmin()
  {
    return true;
    //return $this->CheckPermission('Manage FilePicker');
  }
  
  function GetAdminSection() 
  { 
    return 'extensions'; 
  }
  
  function HasCapability( $capability, $params = array() )
  {
    switch( $capability )
    {
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
  * returns if a given parameter is recognized by the module or not
  * 
  * @param mixed $param
  * @returns bool
  */
  public function IsValidParam($param)
  {
    return in_array($param, $this->_params_list);
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
    $config = cmsms()->GetConfig();
    
    if($dir == '.') $dir = $config['root_path']; 
    
    # we have a valid dir... 
    if( startswith( $dir, $config['root_path']) && is_dir($dir) )
    {
      if($full) return $dir;
      return  str_replace($config['root_path'], '', $dir); 
    }
    
    # else we try to solve $dir into a valid full path or return an empty string
    $ret = ''; 
    
    # if it is a valid relative dir we are done
    $tmp = $config['root_path'] . DIRECTORY_SEPARATOR . $dir;
    
    if( is_dir($tmp) )
    {
      if($full) return $tmp;
      return $dir;
    }
    
    # remove protocols if they exist in cases that dir is a full URL
    $tmp = explode(':', $dir);
        
    if($tmp[0] == 'http' ||  $tmp[0] == 'https' )
    {
      $dir = $tmp[1];
    }
    
    # and try to extract a valid path from it   
    if( startswith( $dir, $config['root_url']) )
    {
      $dir = str_replace($config['root_url'], '', $dir);
      
      if( empty($dir) )
      {
        # it's CMSMS root
        $ret = $config['root_path'];
      }
      else
      {
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
  function &_get_groups_list()
  {
    $ret = array();

    // get a hash of all of the groups and ids.
    $tmp = cmsms()->GetGroupOperations()->LoadGroups();
        
    if( !is_array($tmp) || count($tmp) == 0 ) return FALSE; // no groups?
    
    foreach( $tmp as $one ) 
    {
      if( !$one->active ) continue;
      $ret[$one->id] = $one->name;
    }
    
    return $ret;
  }
  
  /**
  * @internal
  * 
  * note: probably should be private
  */
  function _is_user_from_groups( $uid = -1, $groups = array() )
  {
    $ret = FALSE; // user is not a member of any of the specified groups as a default
    
    foreach($groups as $gid) 
    {
      $users = cmsms()->GetUserOperations()->LoadUsersInGroup($gid);
      
      if( !is_array($users) || !count($users) ) continue;
      
      foreach( $users as $user ) 
      {
        if($user->id == $uid) 
        {
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
    if( $uid <= 0 ) return FALSE; // not loggedin?

    $adding = (bool)( $adding || ($content_obj->Id() < 1) ); // hack for the core. Have to ask why though (JM)

    # handle profile if there is one
    $profile_data = array();
    
    if( isset($params['profile']) )
    {
      $profile = $this->_get_profile_by_name( trim($params['profile']) );
 
      if( !empty($profile) )
        $profile_data = &$profile['data'];
    }
        
    $prms = array();
    
    # handle overrides
    # a parameter set explicitly on the tag overrides the profile
    # if all else fails use defaults preferences
    foreach($this->_params_list as $k => $v)
    { 
      if('profile' == $k) continue; # profile parameter won't be needed here
      $prms[$k] = isset($profile_data[$k]) ? $profile_data[$k] : $this->_defaults[$k];
      $prms[$k] = isset($params[$k]) ? $params[$k] : $prms[$k];
    }
    
    # now $prms array should hold a complete set of valid parameters 
        
    # handle users permission
    #make sure the user has permissions
    $groups = $this->_get_groups_list();
    
    $tmp = explode(',', $prms['groups']);
    
    $valid_groups = array();
    foreach( $tmp as $one ) 
    {
      $one = trim($one);
      
      if($one) 
      { 
        if( in_array($one, $groups) )
        {
          $flp = array_flip($groups);
          $valid_groups[] = $flp[$one];
        }
        elseif( in_array($one, array_keys($groups) ) )
        {
          $valid_groups[] = $one;
        }
      }
    }
    
    if( count($valid_groups) == 0 ) 
    {
      // no valid groups specified... user has to be an administrator
      $valid_groups[] = 1;
    }
    
    $valid_groups = array_unique($valid_groups);
    if( !$this->_is_user_from_groups($uid, $valid_groups) ) return FALSE;
    
    #user is valid, handle the rest    
    
    // for adding situations, if we do not have a value, but have one in the field definition... use it.
    if( $adding && !$value ) $value = '-1';
    
    
    $dir = !empty($prms['dir']) ? trim($prms['dir']) : '';
    $dir = $this->getValidDir($dir);
    $smarty = cmsms()->GetSmarty();
    $smarty->assign('fpmod', $this); # can't be "mod" as it conflicts with CoMa
    $smarty->assign('upload_link', $this->create_url('', 'upload','', array('test' => 'test') )); 
    
    $smarty->assign('name', $blockName);
    $smarty->assign('options', $filelist_dropdown = $this->GetFileListDropdown($dir) );
    $smarty->assign('value', $value);
    $smarty->assign('actionurl', str_replace('&amp;', '&', $this->create_url('m1_', 'upload') ) );
    return $this->ProcessTemplate('filepicker.tpl');
    /*
    switch ($prms['mode']) 
    {
       case 'dropdown': #dropdown
              $smarty->assign('name', $blockName);
              $smarty->assign('options', $filelist_dropdown = $this->GetFileListDropdown($dir) );
              $smarty->assign('value', $value);
              return $this->ProcessTemplate('fpdropdown.tpl');
    
         break;
       case 'browser': #browser
              $smarty->assign('name', $blockName);
              $smarty->assign('options', $filelist_dropdown = $this->GetFileListDropdown($dir) );
              $smarty->assign('value', $value);
              return $this->ProcessTemplate('fpdropdown.tpl');
         break;
       
       default:
                return FALSE;
    }
    */
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
    $config = cmsms()->GetConfig();
    
    $url = $this->_to_url($path);
    
    if($path == '' || $path == '.')
    {
      $fullpath = $config['root_path'];  
    } 
    else
    {
      $fullpath = $this->getValidDir($path, TRUE); 
    }
    
    $files = $this->GetFileList($path);
        
    if( is_array($files) )
    {
      foreach($files as $file)
      {
        if(!$file['dir']) 
        {
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
  
  /**
  * @param string $path
  * @return stdClass[]
  */
  public function GetBrowsableFileList($path = '')
  {
    $filemanager = cms_utils::get_module('FileManager');
    $sortby = self::$_filemanager->GetPreference('sortby', 'nameasc');
    $filelist = filemanager_utils::get_file_list($path);
    
    $countdirs = 0;
    $countfiles = 0;
    $countfilesize = 0;
    $files = array();

    for ($i = 0; $i < count($filelist); $i++)
    {
      $onerow = new stdClass();
      
      if( isset($filelist[$i]['url']) ) 
      {
        $onerow->url = $filelist[$i]['url'];
      }
      
      $onerow->name = $filelist[$i]['name'];
      $onerow->urlname = $this->_encodefilename($filelist[$i]['name']);
      $onerow->type = array('file');
      $onerow->mime = $filelist[$i]['mime'];
      
      if( isset($params[$onerow->urlname]) ) 
      {
        $onerow->checked = true;
      }

      if( strpos($onerow->mime,'text') !== FALSE ) 
      {
        $onerow->type[] = 'text';
      }

      $onerow->thumbnail = '';
      $onerow->editor = '';
      
      if ($filelist[$i]['image']) 
      {
        $onerow->type[] = 'image';
        $params['imagesrc'] = $path . '/' . $filelist[$i]['name'];
        
        if($filemanager->GetPreference('showthumbnails', 0) == 1) 
        {
          $onerow->thumbnail = $filemanager->GetThumbnailLink($filelist[$i], $path);
        }
      }

      if ($filelist[$i]['dir']) 
      {
        $onerow->iconlink = $filemanager->CreateLink(
                                                      $id, 
                                                      'changedir',  
                                                      '', 
                                                      $filemanager->GetFileIcon($filelist[$i]['ext'], 
                                                      $filelist[$i]['dir']),
                                                      array(
                                                              'newdir' => $filelist[$i]['name'], 
                                                              'path' => $path, 
                                                              'sortby' => $sortby
                                                            )
                                                    );
      } 
      else 
      {
        $onerow->iconlink = '<a href="' . $filelist[$i]['url'] . '" target="_blank">' . $filemanager->GetFileIcon($filelist[$i]['ext']) . '</a>';
      }

      $link = $filelist[$i]['name'];
      
      if ($filelist[$i]['dir']) 
      {
          if( $filelist[$i]['name'] != '..' ) 
          {
            $countdirs++;
            $onerow->type = array('dir');
          }
          else 
          {
            $onerow->noCheckbox = 1;
          }
          
          $url = $filemanager->create_url($id, 'changedir', '', array('newdir' => $filelist[$i]['name'], 'path' => $path, 'sortby' => $sortby) );
          $onerow->txtlink = '<a href="' . $url . '" title="' . $filemanager->Lang('title_changedir') . '">' . $link . '</a>';
      } 
      else 
      {
        $countfiles++;
        $countfilesize+=$filelist[$i]["size"];
        $url = $filemanager->create_url($id,'view','',array('file' => $this->_encodefilename($filelist[$i]['name'])));
        $onerow->txtlink = '<a href="' . $url . '" target="_blank" title="' . $filemanager->Lang('title_view_newwindow') . '">' . $link . '</a>';
      }
      
      if( $filelist[$i]['archive']  ) $onerow->type[] = 'archive';

      $onerow->fileinfo = trim($filelist[$i]['fileinfo']);
      
      if ($filelist[$i]['name'] == '..') 
      {
        $onerow->fileaction = '&nbsp;';
        $onerow->filepermissions = '&nbsp;';
      } 
      else 
      {
        $onerow->fileowner = $filelist[$i]['fileowner'];
        $onerow->filepermissions = $filelist[$i]['permissions'];
      }
      
      if ($filelist[$i]['dir']) 
      {
        $onerow->filesize = '&nbsp;';
      }
      else 
      {
        $filesize = filemanager_utils::format_filesize($filelist[$i]['size']);
        $onerow->filesize = $filesize['size'];
        $onerow->filesizeunit = $filesize['unit'];
      }

      if (!$filelist[$i]['dir']) 
      {
        $onerow->filedate = $filelist[$i]['date'];
      } 
      else 
      {
        $onerow->filedate = '';
      }

      $files[] = $onerow;
    }
    
    return $files;
  }
   
  /**
  * @internal
  * 
  * note: should be private
  */  
  public function &_get_profile_data($data = array() )
  {
    $ret_data = array(); 
         
    foreach($this->_params_list as $k => $v)
    {
      $pre_params = array(); 
      if('profile' == $k) continue; # profile parameter won't be needed here

      $pre_params['name'] = $k;
      $pre_params['options'] = $v['options'];
            
      if( isset($data[$k]) )
      {
        $pre_params['type'] = $v['type'];
        $pre_params['value'] = $data[$k];
      }
      else
      {
        $pre_params['type'] = $v['type'];
        $pre_params['value'] = $this->_defaults[$k]; 
      }
      
      $ret_data [$k] = new ProfileParameter($pre_params);
    }
  
    return $ret_data;
  }

  public function _conform_profile_name($string = 'profile')
  {
    return preg_replace('/[^a-z0-9_-]+/i', '-', $string);
  }
  
  public function _save_profile($profile)
  { 
        
    if( !is_object($profile) || empty($profile->name ) ) return FALSE;
    
    $profile->name = $this->_validate_profile_name($profile->name);
        
    $data = array();
    
    foreach(array_keys($this->_params_list) as $one) 
    {
      if($one == 'profile') continue;
      
      if( isset($profile->params[$one]) )
      {
        if($profile->params[$one]['type'] == ProfileParameter::TYPE_CHECKBOX)
        {
          $data[$one] = (bool)$profile->params[$one]['value'];
          continue;
        }}
      
      $data[$one] = $profile->params[$one]['value'];
    }
    
    if($profile->id > -1)
    {
      return $this->_update_profile($profile->id, $profile->name, $data);
    }

    return $this->_create_profile($profile->name, $data);
  }
  
  # db stuff
  # profiles
  
  public function profile_id_exists($id)
  {
    return (bool)$this->_get_profile_by_id($id);
  } 
   
  public function profile_name_exists($name)
  {
    return (bool)$this->_get_profile_by_name($name);
  }
  
  public function &GetProfiles()
  {
    $tmp = $this->_get_all_profiles();
    
    $profiles = array();
    foreach($tmp as $one)
    {
      $profile = new stdClass();
      $profile->id = $one['id'];
      $profile->name = $one['name'];
      $profile->create_date = $one['create_date'];
      $profile->modified_date = $one['modified_date'];
      $profile->params = $this->_get_profile_data($one['data']);
      $profiles[] = $profile;
    }
    
    return $profiles;
  }
  
  /**
  * @internal
  * 
  * note: probably should be private
  *       or be in a lib class
  */
  public function &_get_profile_by_id($id)
  {
    $db = cmsms()->GetDb();
    $table = cms_db_prefix() . 'module_filepicker_profiles';
    $q = 'SELECT * FROM ' . $table . ' WHERE id = ?';
    $row = $db->GetRow( $q, array($id) );
    
    if(!$row)
    {
      $ret = FALSE;
      return $ret;
    }
    
    $row['data'] = unserialize($row['data']);
    return $row;
  }  
  
  /**
  * @internal
  * 
  * note: probably should be private
  *       or be in a lib class  * 
  */
  public function &_get_profile_by_name($name)
  {
    $db = cmsms()->GetDb();
    $table = cms_db_prefix() . 'module_filepicker_profiles';
    $q = 'SELECT * FROM ' . $table . ' WHERE name = ?';
    $row = $db->GetRow( $q, array($name) );
    
    if(!$row)
    {
      $ret = FALSE;
      return $ret;
    }
    
    $row['data'] = unserialize($row['data']);
    return $row;
  }
  
  
  /**
  * @internal
  * 
  * note: probably should be private
  *       or be in a lib class  * 
  */    
  public function _delete_profile($id)
  {
    if( !$this->profile_id_exists($id) ) return FALSE;
        
    $data = serialize($data);
    $db = cmsms()->GetDb();
    $now = $db->DbTimeStamp( time() );  
    $table = cms_db_prefix() . 'module_filepicker_profiles';
    $q = $q = 'DELETE FROM ' . $table . " WHERE id = ?"; 
    $r = $db->Execute($q, array($id) );  
    return TRUE;
  }
  
}
?>