<?php
# CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
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

/**
 * The one and only autoload function for the system.  This basically allows us 
 * to remove a lot of the require_once BS and keep the file loading to as much 
 * of a minimum as possible.
 */
function __autoload($class_name)
{
	$dirname = dirname(dirname(__FILE__));
	
	//Fix references to older classes
	if ($class_name == 'CMSModule')
		$class_name = 'CmsModule';
	else if ($class_name == 'Events')
		$class_name = 'CmsEvents';
	else if ($class_name == 'ContentBase')
		$class_name = 'CmsContentBase';

	//We do this in order of importance...  classes first
	if (file_exists(cms_join_path($dirname,'lib','classes','class.' . strtolower($class_name) . '.php')))
	{
		require_once(cms_join_path($dirname,'lib','classes','class.' . strtolower($class_name) . '.php'));
	}
	else if (file_exists(cms_join_path($dirname,'lib','classes','class.' . strtolower($class_name) . '.inc.php')))
	{
		require_once(cms_join_path($dirname,'lib','classes','class.' . strtolower($class_name) . '.inc.php'));
	}
	else if (file_exists(cms_join_path($dirname,'lib','classes','class.' . underscore($class_name) . '.php')))
	{
		require_once(cms_join_path($dirname,'lib','classes','class.' . underscore($class_name) . '.php'));
	}
	else if (file_exists(cms_join_path($dirname,'lib','classes','class.' . underscore($class_name) . '.inc.php')))
	{
		require_once(cms_join_path($dirname,'lib','classes','class.' . underscore($class_name) . '.inc.php'));
	}
	else if (CmsContentOperations::load_content_type($class_name))
	{
	}
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
 * Returns a reference to the adodb(lite) connection singleton object.
 * Replaces the global $gCms; $db =& $gCms->GetDb(); routine.
 */
function cms_db()
{
	return CmsDatabase::get_instance();
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
			$path .= DIRECTORY_SEPARATOR.$args[$i];
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
 * returned.  If not, then val2 is returned.
 *
 * @return mixed The result of the coalesce
 * @author Ted Kulp
 * @since 1.1
 **/
function coalesce_key($array, $val1, $val2)
{
	if (isset($array[$val1]))
	{
		return $array[$val1];
	}
	return $val2;
}

?>