<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
 * Loads appropriate language file if necessary.
 *
 * @since		1.0
 * @package CMS
 * @license GPL
 */

/**
 * Loads appropriate language file if necessary. Returns translated string value for a module string.
 * Included in the module class when needed.
 *
 * @param mixed $modinstance pointer to the module instance
 * @since		1.0
 * @return string translated 
 */
function cms_module_Lang(&$modinstance)
{
	$gCms = cmsms();

	$name = '';
	$params = array();

	if (func_num_args() > 0)
	{
		
		$name = func_get_arg(1);
		if (func_num_args() == 3 && is_array(func_get_arg(2)))
		{
			$params = func_get_arg(2);
		}
		else if (func_num_args() > 2)
		{
			$params = array_slice(func_get_args(), 2);
		}
	}
	else
	{
		return '';
	}

	$ourlang = cms_current_language();
	if ($modinstance->curlang != '') $ourlang = $modinstance->curlang;

	#Load the language if it's not loaded
	if (!isset($modinstance->langhash[$ourlang]) || !is_array($modinstance->langhash[$ourlang]) || 
	    (is_array($modinstance->langhash[$ourlang]) && count(array_keys($modinstance->langhash[$ourlang])) == 0))
	{
		$dir = $gCms->config['root_path'];

		$lang = array();

		//First load the default language to remove any "Missing Languagestrings"
		if (@is_file("$dir/modules/".$modinstance->GetName()."/lang/".$modinstance->DefaultLanguage()."/".$modinstance->DefaultLanguage().".php"))
		{
			include("$dir/modules/".$modinstance->GetName()."/lang/".$modinstance->DefaultLanguage()."/".$modinstance->DefaultLanguage().".php");
		}
		else if (@is_file("$dir/modules/".$modinstance->GetName()."/lang/".$modinstance->DefaultLanguage().".php"))
		{
			include("$dir/modules/".$modinstance->GetName()."/lang/".$modinstance->DefaultLanguage().".php");
		}

		//Now load the other language if necessary
		if (count($lang) == 0 || $modinstance->DefaultLanguage() != $ourlang)
		{
			if (@is_file("$dir/modules/".$modinstance->GetName()."/lang/$ourlang/$ourlang.php"))
			{
				include("$dir/modules/".$modinstance->GetName()."/lang/$ourlang/$ourlang.php");
			}
			else if (@is_file("$dir/modules/".$modinstance->GetName()."/lang/ext/$ourlang.php"))
			{
				include("$dir/modules/".$modinstance->GetName()."/lang/ext/$ourlang.php");
			}
			else if (@is_file("$dir/modules/".$modinstance->GetName()."/lang/$ourlang.php"))
			{
				include("$dir/modules/".$modinstance->GetName()."/lang/$ourlang.php");
			}
			else if (count($lang) == 0)
			{
				if (@is_file("$dir/modules/".$modinstance->GetName()."/lang/".$modinstance->DefaultLanguage()."/".$modinstance->DefaultLanguage().".php"))
				{
					include("$dir/modules/".$modinstance->GetName()."/lang/".$modinstance->DefaultLanguage()."/".$modinstance->DefaultLanguage().".php");
				}
				else if (@is_file("$dir/modules/".$modinstance->GetName()."/lang/".$modinstance->DefaultLanguage().".php"))
				{
					include("$dir/modules/".$modinstance->GetName()."/lang/".$modinstance->DefaultLanguage().".php");
				}
			}
			else
			{
				# Sucks to be here...  Don't use Lang unless there are language files...
				# Get ready for a lot of Missing Languagestrings
			}
		}

		# try to load an admin modifiable version of the lang file if one exists
		if( @is_file("$dir/module_custom/".$modinstance->GetName()."/lang/$ourlang.php") )
		  {
		    include("$dir/module_custom/".$modinstance->GetName()."/lang/$ourlang.php");
		  }
		else if( @is_file("$dir/module_custom/".$modinstance->GetName()."/lang/".$modinstance->DefaultLanguage().".php") )
		{
			include("$dir/module_custom/".$modinstance->GetName()."/lang/".$modinstance->DefaultLanguage().".php");
		}

		$modinstance->langhash[$ourlang] = &$lang;
	}

	$result = '';

	if (isset($modinstance->langhash[$ourlang][$name]))
	{
		if (count($params))
		{
			$result = @vsprintf($modinstance->langhash[$ourlang][$name], $params);
		}
		else
		{
			$result = $modinstance->langhash[$ourlang][$name];
		}
	}
	else
	{
		$result = "-- Missing Languagestring - module:".$modinstance->GetName()." string:$name--";
	}

	if (isset($gCms->config['admin_encoding']) && isset($gCms->variables['convertclass']))
	{
		if (strtolower(get_encoding('', false)) != strtolower($gCms->config['admin_encoding']))
		{
			$class =& $gCms->variables['convertclass'];
			$result = $class->Convert($result, get_encoding('', false), $gCms->config['admin_encoding']);
		}
	}

	return $result;
}

?>