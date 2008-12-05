<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.sf.net
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
#$Id$

/**
 * Misc functions
 *
 * @package CMS
 */

/**
 * Enter description here...
 *
 * @param unknown $val
 * @param integer $quote_style
 * @return unknown
 * 
 * $quote_style may be one of:
 *     ENT_COMPAT   : Will convert double-quotes and leave single-quotes alone. 
 *     ENT_QUOTES   : Will convert both double and single quotes. 
 *     ENT_NOQUOTES : Will leave both double and single quotes unconverted. 
 */
function cms_htmlentities($val, $param=ENT_QUOTES, $charset="UTF-8")
{
	if ($val == "")
	{
		return "";
	}
	$val = str_replace( "&#032;", " ", $val ); 
	$val = str_replace( "&"            , "&amp;"         , $val ); 
	$val = str_replace( "<!--"         , "&#60;&#33;--"  , $val ); 
	$val = str_replace( "-->"          , "--&#62;"       , $val ); 
	$val = preg_replace( "/<script/i"  , "&#60;script"   , $val ); 
	$val = str_replace( ">"            , "&gt;"          , $val ); 
	$val = str_replace( "<"            , "&lt;"          , $val ); 
	$val = str_replace( "\""           , "&quot;"        , $val ); 
	$val = preg_replace( "/\\$/"      , "&#036;"        , $val ); 
	$val = str_replace( "!"            , "&#33;"         , $val ); 
	$val = str_replace( "'"            , "&#39;"         , $val ); 
	 
	return $val;
}

/**
 * Display $var nicely to the $gCms->errors array if $config['debug'] is set
 *
 * @param mixed $var
 * @param string $title
 */
function debug_buffer($var, $title="")
{
	CmsProfiler::get_instance()->mark(print_r($var));
}

function filespec_is_excluded( $file, $excludes )
{
	// strip the path from the file
	foreach( $excludes as $excl )
	{
		if( @preg_match( "/".$excl."/i", basename($file) ) )
		{
			return true;
		}
	}
	return false;
}

/**
 * Check the permissions of a directory recursively to make sure that
 * we have write permission to all files
 * &param  path      start path
*/
function is_directory_writable( $path )
{
	if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }     
	$result = true;
	if( $handle = @opendir( $path ) )
	{
		while( false !== ( $file = readdir( $handle ) ) )
		{
			if( $file == '.' || $file == '..' )
			{
				continue;
			}

			$p = $path.$file;

			if( !@is_writable( $p ) )
			{
				return false;
			}

			if( @is_dir( $p ) )
			{
				$result = is_directory_writable( $p );
				if( !$result )
				{
					return false;
				}
			}
		}
		@closedir( $handle );
	}
	else
	{
		return false;
	}

	return true;
}

/**
* Return an array containing a list of files in a directory
* performs a recursive serach
* @param  path      start path
* @param  maxdepth  how deep to browse (-1=unlimited)
* @param  mode      "FULL"|"DIRS"|"FILES"
* @param  d         for internal use only
**/
function get_recursive_file_list ( $path , $excludes, $maxdepth = -1 , $mode = "FULL" , $d = 0 )
{
	if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }     
	$dirlist = array () ;
	if ( $mode != "FILES" ) { $dirlist[] = $path ; }
	if ( $handle = opendir ( $path ) )
	{
		while ( false !== ( $file = readdir ( $handle ) ) )
		{
			$excluded = filespec_is_excluded( $file, $excludes );
			if ( $file != '.' && $file != '..' && $excluded == false )
			{
				$file = $path . $file ;
				if ( ! @is_dir ( $file ) ) { if ( $mode != "DIRS" ) { $dirlist[] = $file ; } }
				elseif ( $d >=0 && ($d < $maxdepth || $maxdepth < 0) )
				{
					$result = get_recursive_file_list ( $file . '/' , $excludes, $maxdepth , $mode , $d + 1 ) ;
					$dirlist = array_merge ( $dirlist , $result ) ;
				}
			}
		}
		closedir ( $handle ) ;
	}
	if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
	return ( $dirlist ) ;
}

function recursive_delete( $dirname )
{
	// all subdirectories and contents:
	if(is_dir($dirname))$dir_handle=opendir($dirname);
	while($file=readdir($dir_handle))
	{
		if($file!="." && $file!="..")
		{
			if(!is_dir($dirname."/".$file))
			{
				if( !@unlink ($dirname."/".$file) )
				{
					closedir( $dir_handle );
					return false;
				}
			}
			else 
			{
				recursive_delete($dirname."/".$file);
			}
		}
	}
	closedir($dir_handle);
	if( ! @rmdir($dirname) )
	{
		return false;
	}
	return true;
}

function chmod_r( $path, $mode )
{
	if( !is_dir( $path ) )
	return chmod( $path, $mode );

	$dh = @opendir( $path );
	if( !$dh ) return FALSE;

	while( $file = readdir( $dh ) )
	{
		if( $file == '.' || $file == '..' ) continue;

		$p = $path.DIRECTORY_SEPARATOR.$file;
		if( is_dir( $p ) )
		{
			if( !@chmod_r( $p, $mode ) )
			{
				closedir( $dh );
				return false;
			}
		}
		else if( !is_link( $p ) )
		{
			if( !@chmod( $p, $mode ) )
			{
				closedir( $dh );
				return false;
			}
		}
	}
	@closedir( $dh );
	return @chmod( $path, $mode );
}

function copyr($source, $dest)
{
    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }
 
    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest);
    }
    
    // If the source is a symlink
    if (is_link($source)) {
        $link_dest = readlink($source);
        return symlink($link_dest, $dest);
    }
 
    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }
 
        // Deep copy directories
        if ($dest !== "$source/$entry") {
            copyr("$source/$entry", "$dest/$entry");
        }
    }
 
    // Clean up
    $dir->close();
    return true;
}

function serialize_object(&$object)
{
	return base64_encode(serialize($object));
}

function unserialize_object(&$serialized)
{
	return unserialize(base64_decode($serialized));
}

function show_mem($string = '')
{
	var_dump($string . ' -- ' . memory_get_usage());
}

function showmem($string = '')
{
	return show_mem($string);
}

function munge_string_to_url($alias, $tolower = false)
{
	// replacement.php is encoded utf-8 and must be the first modification of alias
	//include(dirname(__FILE__) . '/replacement.php');
	$alias = str_replace($toreplace, $replacement, $alias);
	
	// lowercase only on empty aliases
	if ($tolower == true)
	{
		$alias = strtolower($alias);
	}
		
	$alias = preg_replace("/[^\w-]+/", "-", $alias);
	$alias = trim($alias, '-');

	return $alias;
}

/**
 * Returns all parameters sent that are destined for the module with
 * the given $id
 */
function get_module_parameters($id)
{
  $params = array();
  
  if ($id != '')
    {
      foreach ($_REQUEST as $key=>$value)
	{
	  if (strpos($key, (string)$id) !== FALSE && strpos($key, (string)$id) == 0)
	    {
	      $key = str_replace($id, '', $key);
	      if( $key == 'id' || $key == 'returnid' )
		{
		  $value = (int)$value;
		}
	      $params[$key] = $value;
	    }
	}
    }

  return $params;
}


function can_users_upload()
{
  # first, check to see if safe mode is enabled
  # if it is, then check to see the owner of the index.php, moduleinterface.php
  # and the uploads and modules directory.  if they all match, then we
  # can upload files.
  # if safe mode is off, then we just have to check the permissions.
  global $gCms;
  $file_index = $gCms->config['root_path'].DIRECTORY_SEPARATOR.'index.php';
  $dir_uploads = $gCms->config['uploads_path'];

  $stat_index = @stat($file_index);
  $stat_uploads = @stat($dir_uploads);

  if( $stat_index == FALSE || $stat_uploads == FALSE )
    {
      // couldn't get some necessary information.
      return false;
    }

  $safe_mode = ini_get_boolean('safe_mode');
  if( $safe_mode )
    {
      // we're in safe mode.
      if( $stat_index[4] != $stat_uploads[4] )
	{
	  return FALSE;
	}
    }

  // now check to see if we can write to the directories
  if( !is_writable( $dir_modules ) )
    {
      return FALSE;
    }
  if( !is_writable( $dir_uploads ) )
    {
      return FALSE;
    }
  
  // It all worked.
  return TRUE;
}

function can_admin_upload()
{
  # first, check to see if safe mode is enabled
  # if it is, then check to see the owner of the index.php, moduleinterface.php
  # and the uploads and modules directory.  if they all match, then we
  # can upload files.
  # if safe mode is off, then we just have to check the permissions.
  global $gCms;
  $file_index = $gCms->config['root_path'].DIRECTORY_SEPARATOR.'index.php';
  $file_moduleinterface = $gCms->config['root_path'].DIRECTORY_SEPARATOR.
    $gCms->config['admin_dir'].DIRECTORY_SEPARATOR.'moduleinterface.php';
  $dir_uploads = $gCms->config['uploads_path'];
  $dir_modules = $gCms->config['root_path'].DIRECTORY_SEPARATOR.'modules';

  $stat_index = @stat($file_index);
  $stat_moduleinterface = @stat($file_moduleinterface);
  $stat_uploads = @stat($dir_uploads);
  $stat_modules = @stat($dir_modules);

  $my_uid = @getmyuid();

  if( $my_uid === FALSE || $stat_index == FALSE || 
      $stat_moduleinterface == FALSE || $stat_uploads == FALSE ||
      $stat_modules == FALSE )
    {
      // couldn't get some necessary information.
      return FALSE;
    }

  $safe_mode = ini_get_boolean('safe_mode');
  if( $safe_mode )
    {

      // we're in safe mode.
      if( ($stat_moduleinterface[4] != $stat_modules[4]) ||
	  ($stat_moduleinterface[4] != $stat_uploads[4]) ||
	  ($my_uid != $stat_moduleinterface[4]) )
	{
	  // owners don't match
	  return FALSE;
	}
    }

  // now check to see if we can write to the directories
  if( !is_writable( $dir_modules ) )
    {
      return FALSE;
    }
  if( !is_writable( $dir_uploads ) )
    {
      return FALSE;
    }
  
  // It all worked.
  return TRUE;
}

function interpret_permissions($perms)
{
  $owner = array();
  $group = array();
  $other = array();

  if( $perms & 0400 )
    {
      $owner[] = lang('read');
    }
  if( $perms & 0200 )
    {
      $owner[] = lang('write');
    }
  if( $perms & 0100 )
    {
      $owner[] = lang('execute');
    }

  if( $perms & 0040 )
    {
      $group[] = lang('read');
    }
  if( $perms & 0020 )
    {
      $group[] = lang('write');
    }
  if( $perms & 0010 )
    {
      $group[] = lang('execute');
    }

  if( $perms & 0004 )
    {
      $other[] = lang('read');
    }
  if( $perms & 0002 )
    {
      $other[] = lang('write');
    }
  if( $perms & 0001 )
    {
      $other[] = lang('execute');
    }

  return array($owner,$group,$other);
}

function ini_get_boolean($str)
{
  $val1 = ini_get($str);
  $val2 = strtolower($val1);

  $ret = 0;
  if( $val2 == 1 || $val2 == '1' || $val2 == 'yes' || $val2 == 'true' || $val2 == 'on' )
     $ret = 1;
  return $ret;
}

function GetModuleParameters($id)
{
	return get_module_parameters($id);
}

# vim:ts=4 sw=4 noet
?>