<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t;  -*-
# CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Functions that need to be in a global scope.  Mostly for utility and
 * shortcuts.
 *
 * @since 1.1
 */

//Defines
define("ROOT_DIR", dirname(dirname(__FILE__)));
define("DS", DIRECTORY_SEPARATOR);

//Load file location defines
require_once(ROOT_DIR.DS.'fileloc.php');

//So we can use them in __autoload
require_once(ROOT_DIR.DS.'lib'.DS.'classes'.DS.'class.cms_object.php');
require_once(ROOT_DIR.DS.'lib'.DS.'classes'.DS.'class.cms_config.php');
require_once(ROOT_DIR.DS.'lib'.DS.'classes'.DS.'class.cms_cache.php');

set_error_handler('cms_warning_handler', E_WARNING | E_USER_WARNING);

/**
 * The one and only autoload function for the system.  This basically allows us 
 * to remove a lot of the require_once BS and keep the file loading to as much 
 * of a minimum as possible.
 */
function cms_autoload($class_name)
{
	//$files = scan_classes();
	$files = CmsCache::get_instance()->call('scan_classes');
	
	//Fix references to older classes
	if ($class_name == 'CMSModule')
		$class_name = 'CmsModule';
		
	if (array_key_exists('class.' . underscore($class_name) . '.php', $files))
	{
		require($files['class.' . underscore($class_name) . '.php']);
	}
	else if (array_key_exists('class.' . underscore($class_name) . '.inc.php', $files))
	{
		require($files['class.' . underscore($class_name) . '.inc.php']);
	}
	else if (array_key_exists('class.' . strtolower($class_name) . '.php', $files))
	{
		require($files['class.' . strtolower($class_name) . '.php']);
	}
	else if (array_key_exists('class.' . strtolower($class_name) . '.inc.php', $files))
	{
		require($files['class.' . strtolower($class_name) . '.inc.php']);
	}
	else if (CmsContentOperations::load_content_type($class_name))
	{
	}
}

spl_autoload_register('cms_autoload');

function scan_classes()
{
	$dir = cms_join_path(ROOT_DIR,'lib','classes');
	if (!isset($GLOBALS['dirscan']))
	{
		$files = array();
		/*
		$time1 = microtime(true);
		*/
		scan_classes_recursive($dir, $files);
		/*
		$time2 = microtime(true);
		var_dump($time2 - $time1);
		*/
		$GLOBALS['dirscan'] = $files;
		return $files;
	}
	else
	{
		return $GLOBALS['dirscan'];
	}
}

function scan_classes_recursive($dir = '.', &$files)
{
	foreach(new DirectoryIterator($dir) as $file)
	{
		if (!$file->isDot() && $file->getFilename() != '.svn')
		{
			if ($file->isDir())
			{
				$newdir = $file->getPathname();
				scan_classes_recursive($newdir, $files);
			}
			else 
			{
				if (starts_with(basename($file->getPathname()), 'class.'))
					$files[basename($file->getPathname())] = $file->getPathname();
			}
		}
	}
	
	return $files;
}

/**
 * Returns the global CmsApplication singleton.  This is the equivalent of
 * global $gCms from days gone by.
 */
function cmsms()
{
	return CmsApplication::get_instance();
}

/**
 * Returns the global CmsApplication singleton.  This is the equivalent of
 * global $gCms from days gone by.
 */
function cms_global()
{
	return CmsApplication::get_instance();
}

/**
 * Returns a reference to the adodb(lite) connection singleton object.
 * Replaces the global $gCms; $db =& $gCms->GetDb(); routine.
 */
function cms_db()
{
	return CmsDatabase::get_instance();
}

function cms_orm($class = '')
{
	if ($class == '')
		return CmsObjectRelationalManager::get_instance();
	else
		return CmsObjectRelationalManager::get_instance()->$class;
}

/**
 * Returns a reference to the config object (used to be a hash).  Replaces
 * the global $gCms; $config =& $gCms->GetConfig() routine.
 */
function cms_config()
{
	return CmsConfig::get_instance();
}

/**
 * Returns a reference to the global smarty object.  Replaces
 * the global $gCms; $config =& $gCms->GetSmarty() routine.
 */
function cms_smarty()
{
	return CmsSmarty::get_instance();
}

/**
 * Joins a path together using proper directory separators
 * Taken from: http://www.php.net/manual/en/ref.dir.php
 *
 * @since 1.0
 */
function cms_join_path()
{
 	$num_args = func_num_args();
	$args = func_get_args();
	$path = $args[0];

	if( $num_args > 1 )
	{
		for ($i = 1; $i < $num_args; $i++)
		{
			$path .= DS.$args[$i];
		}
	}

	return $path;
}

function starts_with($str, $sub)
{
	return ( substr( $str, 0, strlen( $sub ) ) == $sub );
}

function startswith( $str, $sub )
{
	return starts_with($str, $sub);
}

function ends_with( $str, $sub )
{
	return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}

function endswith( $str, $sub )
{
	return ends_with($str, $sub);
}

/**
 * Returns given $lower_case_and_underscored_word as a camelCased word.
 * Take from cakephp (http://cakephp.org)
 * Licensed under the MIT License
 *
 * @param string $lower_case_and_underscored_word Word to camelize
 * @return string Camelized word. likeThis.
 */
function camelize($lowerCaseAndUnderscoredWord)
{
	$replace = str_replace(" ", "", ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord)));
	return $replace;
}

/**
 * Returns an underscore-syntaxed ($like_this_dear_reader) version of the $camel_cased_word.
 * Take from cakephp (http://cakephp.org)
 * Licensed under the MIT License
 *
 * @param string $camel_cased_word Camel-cased word to be "underscorized"
 * @return string Underscore-syntaxed version of the $camel_cased_word
 */
function underscore($camelCasedWord)
{
	$replace = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
	return $replace;
}

/**
 * Returns a human-readable string from $lower_case_and_underscored_word,
 * by replacing underscores with a space, and by upper-casing the initial characters.
 * Take from cakephp (http://cakephp.org)
 * Licensed under the MIT License
 *
 * @param string $lower_case_and_underscored_word String to be made more readable
 * @return string Human-readable string
 */
function humanize($lowerCaseAndUnderscoredWord)
{
	$replace = ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord));
	return $replace;
}

/**
 * Looks through the hash given.  If a key named val1 exists, then it's value is 
 * returned.  If not, then val2 is returned.  Furthermore, passing one of the php
 * filter ids (http://www.php.net/manual/en/ref.filter.php) will filter the 
 * returned value.
 *
 * @param array The has to parse through
 * @param string The key to look for
 * @param mixed The value to return if the key isn't found
 * @param integer An optional filter id to pass the returned value through
 * @param array Optional parameters for the filter_var call
 * @return mixed The result of the coalesce
 * @author Ted Kulp
 * @since 1.1
 **/
function coalesce_key($array, $val1, $val2, $filter = -1, $filter_options = array())
{
	if (isset($array[$val1]))
	{
		if ($filter > -1)
			return filter_var($array[$val1], $filter, $filter_options);
		else
			return $array[$val1];
	}
	return $val2;
}

/**
 * Finds all keys_to_remove in the hash, removes them,
 * and returns the new hash.
 *
 * @param array The original hash
 * @param array The names of the keys to remove
 * @return array The result of the key removal
 * @author Ted Kulp
 * @since 1.1
 */
function remove_keys($array, $keys_to_remove)
{
	if (is_array($array))
	{
		foreach ($keys_to_remove as $k)
		{
			if (array_key_exists($k, $array))
			{
				unset($array[$k]);
			}
		}
	}
	
	return $array;
}

/**
 * Global wrapper for CmsResponse::redirect()
 *
 * @param $to The url to redirect to
 *
 * @return void
 * @author Ted Kulp
 **/
function redirect($to)
{
	CmsResponse::redirect($to);
}

function lang()
{
	$ary = func_get_args();
	return call_user_func_array(array('CmsLanguage', 'translate'), $ary);
}

/**
 * Returns the currently configured database prefix.
 *
 * @since 0.4
 */
function cms_db_prefix()
{
	return CmsDatabase::get_prefix();
}

/**
 * Returns whether or not we're in debug mode currently.
 *
 * @return boolean
 * @author Ted Kulp
 */
function in_debug()
{
	return CmsConfig::get("debug") == true;
}

/**
 * Error handler so that we can swallow warning messages unless the
 * debug flag is on.
 *
 * @author Ted Kulp
 */
function cms_warning_handler($errno, $errstr, $errfile = '', $errline = -1, $errcontext = array())
{
	if (in_debug())
	{
		trigger_error($errstr, E_USER_WARNING);
	}
}

?>