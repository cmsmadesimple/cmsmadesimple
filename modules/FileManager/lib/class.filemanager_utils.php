<?php
# FileManager. A plugin for CMS - CMS Made Simple
# Copyright (c) 2006-08 by Morten Poulsen <morten@poulsen.org>
#
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#

final class filemanager_utils
{
  static private $_can_do_advanced = -1;

  protected function __construct() {} 

  public static function is_valid_filename($name)
  {
    if( $name == '' ) return FALSE;
    if( strpos($name,'/') !== false ) return FALSE;
    if( strpos($name,'\\') !== false ) return FALSE;
    if( strpos($name,'..') !== false ) return FALSE;
    if( $name[0] == '.' || $name[0] == ' ' ) return FALSE;
    if( preg_match('/[\n\r\t\[\]\&\?\<\>\!\@\#\$\%\*\(\)\{\}\|\"\'\:\;\+]/',$name) )
      {
	return FALSE;
      }
    return TRUE;
  }

  public static function can_do_advanced()
  {
    if( self::$_can_do_advanced < 0 )
      {
	$filemod = cms_utils::get_module('FileManager');
	$config = cmsms()->GetConfig();
	if( startswith($config['uploads_path'],$config['root_path']) && $filemod->AdvancedAccessAllowed() )
	  {
	    self::$_can_do_advanced = 1;
	  }
	else
	  {
	    self::$_can_do_advanced = 0;
	  }
      }
    return self::$_can_do_advanced;
  }

  public static function check_advanced_mode()
  {
    $filemod = cms_utils::get_module('FileManager');
    $a = self::can_do_advanced();
    $b = $filemod->GetPreference('advancedmode',0);
    if( $a && $b ) return TRUE;
    return FALSE;
  }

  public static function get_default_cwd()
  {
    $config = cmsms()->GetConfig();
    $advancedmode=filemanager_utils::check_advanced_mode();
    $dir = $config['uploads_path'];
    if( !startswith($dir,$config['root_path']) ) {
      $dir = $config['root_path'].'/uploads';
    }
    if( $advancedmode ) {
      $dir = $config['root_path'];
    }

    $dir = substr($dir,strlen($config['root_path'])+1);
    if( $dir == '' ) $dir = '/';
    return $dir;
  }

  public static function test_invalid_path($path)
  {
    $gCms = cmsms();
    $config = $gCms->GetConfig();
    $advancedmode=filemanager_utils::check_advanced_mode();
    
    $prefix = $config['root_path'];
    $path = self::join_path($prefix,$path);
    $path=realpath($path);
    if( $path === FALSE ) {
      return true;
    }

    if (!$advancedmode) {
      // uploading in 'non advanced mode', path has to start with the upload dir.
      if (!startswith($path,$config["uploads_path"])) return true;
    }
    else {
      // advanced mode, path has to start with the root path.
      if (!startswith($path,$config["root_path"])) return true;
    }
    return false;
  }


  public static function get_cwd()
  {
    // check the path
    $path = cms_userprefs::get('filemanager_cwd',self::get_default_cwd());
    if( self::test_invalid_path($path) ) {
      $path = self::get_default_cwd();
    }
    if( $path == '' ) $path = '/';
    return $path;
  }

  public static function set_cwd($path)
  {
    $config = cmsms()->GetConfig();
    if( startswith($path,$config['root_path']) ) {
      $path = substr($path,strlen($config['root_path'])+1);
    }
    $advancedmode = self::check_advanced_mode();      

    // validate the path.
    $prefix = $config['root_path'];
    $tmp = self::join_path($prefix,$path);
    $tmp = realpath($tmp);
    if( !is_dir($tmp) ) {
      throw new Exception('Cannot set current working directory to an invalid path');
    }
    if( !self::test_invalid_path($tmp) ) {
      throw new Exception('Cannot set current working directory to an invalid path');
    }

    $path = str_replace('\\','/',substr($tmp,strlen($prefix)+1));
    cms_userprefs::set('filemanager_cwd',$path);
  }


  public static function join_path()
  {
    $args = func_get_args();
    if( count($args) < 1 ) return;
    if( count($args) < 2 ) return $args[0];

    $args2 = array();
    for( $i = 0; $i < count($args); $i++ ) {
      if( $args[$i] == '' ) continue;
      if( $i != 0 && (startswith($args[$i],'/') || startswith($args[$i],'\\')) ) $args[$i] = substr($args[$i],1);
      if( endswith($args[$i],'/') || endswith($args[$i],'\\') ) $args[$i] = substr($args[$i],0,-1);
      $args2[] = $args[$i];
    }

    return implode(DIRECTORY_SEPARATOR,$args2);
  }

  public static function get_full_cwd()
  {
    $path = self::get_cwd();
    $config = cmsms()->GetConfig();
    if( self::test_invalid_path($path) ) {
      $path = self::get_default_cwd();
    }
    $base = $config['root_path'];
    $realpath = self::join_path($base,$path);
    return $realpath;
  }

  public static function get_cwd_url()
  {
    $path = self::get_cwd();
    $config = cmsms()->GetConfig();
    if( self::test_invalid_path($path) ) {
      $path = self::get_default_cwd();
    }
    $url = $config['root_url'].'/'.$path;
    return $url;
  }

  public static function is_image_file($file)
  {
    // it'd be nice to check mime type here.
    $ext = substr(strrchr($file, '.'), 1);
    if( !$ext ) return FALSE;

    $tmp = array('gif','jpg','jpeg','png');
    if( in_array(strtolower($ext),$tmp) ) return TRUE;
    return FALSE;
  }

  public static function is_archive_file($file)
  {
    $tmp = array('.tar.gz','.tar.bz2','.zip','.tgz');
    foreach( $tmp as $t2 ) {
      if( endswith(strtolower($file),$t2) ) {
	return TRUE;
      }
    }
    return FALSE;
  }

  public static function get_file_list($path = '')
  {
    if( !$path ) $path = self::get_cwd();
    $advancedmode = self::check_advanced_mode();
    $filemod = cms_utils::get_module('FileManager');
    $showhiddenfiles=$filemod->GetPreference('showhiddenfiles','1');
    $result=array();
    $gCms = cmsms();
    $config = $gCms->GetConfig();

    // convert the cwd into a real path... slightly different for advanced mode.
    $realpath = self::join_path($config['root_path'],$path);

    $dir=@opendir($realpath);
    if (!$dir) return false;
    while ($file=readdir($dir)) {
      if ($file=='.') continue;
      if ($file=='..') {
	// can we go up.
	if( $path == self::get_default_cwd() ) continue;
      } else {
	if ($file[0]=='.' || $file[0] == '_' || $file[0] == '~') {
	  if (($showhiddenfiles!=1) || (!$advancedmode)) continue;
	}
      }
			
      if (substr($file,0,6)=='thumb_') {
	//Ignore thumbnail files of showing thumbnails is off
	if ($filemod->GetPreference('showthumbnails','1')=='1') continue;
      }
				
      // build the file info array.
      $fullname = self::join_path($realpath,$file);
      $info=array();
      $info['name']=$file;
      $info['dir'] = FALSE;
      $info['image'] = FALSE;
      $info['archive'] = FALSE;
      $info['mime'] = mime_content_type($fullname);
      $statinfo=stat($fullname);

      if (is_dir($fullname)) {
	$info['dir']=true;
	$info['ext']='';
	$info['fileinfo']=GetFileInfo($fullname,'',true);
      } else {
	$info['size']=$statinfo['size'];
	$info['date']=$statinfo['mtime'];
	$info['url']=self::join_path($config['root_url'], $path, $file);
	$info['url'] = str_replace('\\','/',$info['url']); // windoze both sucks, and blows.
	$explodedfile=explode('.', $file); $info['ext']=array_pop($explodedfile);
	$info['fileinfo']=GetFileInfo(self::join_path($realpath,$file),$info['ext'],false);
      }

      // test for archive
      $info['archive'] = self::is_archive_file($file);

      // test for image
      $info['image'] = self::is_image_file($file);

      if (function_exists('posix_getpwuid')) {
	$userinfo = @posix_getpwuid($statinfo['uid']);
	$info['fileowner']= isset($userinfo['name'])?$userinfo['name']:$filemod->Lang('unknown');
      } else {
	$info['fileowner']='N/A';
      }

      $info['writable']=is_writable(self::join_path($realpath,$file));
      if (function_exists('posix_getpwuid')) {
	$info['permissions']=$filemod->FormatPermissions($statinfo['mode'],$filemod->GetPreference('permissionstyle','xxx'));
      } else {
	if ($info['writable']) {
	  $info['permissions']='R';
	} else {
	  $info['permissions']='R';
	}
      }
			
      $result[]=$info;
    }
    
    $tmp = usort($result,'filemanager_utils::_FileManagerCompareFiles');
    return $result;
  }

  private static function _FileManagerCompareFiles($a,$b,$forcesort="") {
    $filemod = cms_utils::get_module('FileManager');
    $sortby=$filemod->GetPreference("sortby","nameasc");
    if ($forcesort!="") $sortby=$forcesort;
    if ($a["name"]=="..") return -1;
    if ($b["name"]=="..") return 1;
    /*print_r($a);
     print_r($b);*/
    //Handle if only one is a dir
    if ($a["dir"] XOR $b["dir"]) {
      if ($a["dir"]) return -1; else return 1;
    }
	  
    switch($sortby) {
    case "nameasc" : return strncasecmp($a["name"],$b["name"],strlen($a["name"]));
    case "namedesc" : return strncasecmp($b["name"],$a["name"],strlen($b["name"]));
    case "sizeasc" : {
      if ($a["dir"] && $b["dir"]) return self::_FileManagerCompareFiles($a,$b,"nameasc");
      return ($a["size"]>$b["size"]);
    }
    case "sizedesc" : {
      if ($a["dir"] && $b["dir"]) return self::_FileManagerCompareFiles($a,$b,"nameasc");
      return ($b["size"]>$a["size"]);
    }
    default : strncasecmp($a["name"],$b["name"],strlen($a["name"]));				
    }
    return 0;
  }


  // get post max size and give a portion of it to smarty for max chunk size.
  public static function str_to_bytes($val)
  {
    if(is_string($val) && $val != '')
      {
	$val = trim($val);
	$last = strtolower(substr($val, strlen($val/1), 1));
	switch($last)
	  {
	  case 'g':
	    $val *= 1024;
	  case 'm':
	    $val *= 1024;
	  case 'k':
	    $val *= 1024;
	  }
      }

    return (int) $val;
  }

  public static function get_dirlist()
  {
    $config = cmsms()->GetConfig();
    $mod = cms_utils::get_module('FileManager');
    $showhiddenfiles = $mod->GetPreference('showhiddenfiles');
    $startdir = $config['uploads_path'];
    $advancedmode = self::check_advanced_mode();
    if( $advancedmode ) {
      $startdir = $config['root_path'];
    }

    // now get a simple list of all of the directories we have 'write' access to.
    function fmutils_get_dirs($startdir,$prefix = '/') {
      $res = array();
      if( !is_dir($startdir) ) {
	return;
      }

      global $showhiddenfiles;
      $dh = opendir($startdir);
      while( false !== ($entry = readdir($dh)) ) {
	if( $entry == '.' ) continue;
	$full = filemanager_utils::join_path($startdir,$entry);
	if( !is_dir($full) ) continue;
	if( !$showhiddenfiles && ($entry[0] == '.' || $entry[0] == '_') ) {
	  continue;
	}

	if( $entry == '.svn' || $entry == '.git' ) continue;
	$res[$prefix.$entry] = $prefix.$entry;
	$tmp = fmutils_get_dirs($full,$prefix.$entry.'/');
	if( is_array($tmp) && count($tmp) ) {
	  $res = array_merge($res,$tmp);
	}
      }
      closedir($dh);
      return $res;
    }

    $output = fmutils_get_dirs($startdir,'/');
    if( is_array($output) && count($output) ) {
      $output['/'.basename($startdir)] = '/'.basename($startdir);
      ksort($output);
    }
    return $output;
  }
} // end of class

#
# EOF
#
?>
