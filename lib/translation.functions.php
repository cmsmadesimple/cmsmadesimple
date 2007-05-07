<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
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
 * Translation functions/classes
 *
 * @package CMS
 */

function lang()
{
	global $gCms;
	global $lang;
	global $nls;

	$name = '';
	$params = array();
	$realm = 'admin';

	if (func_num_args() > 0)
	{
		$name = func_get_arg(0);
		if (func_num_args() == 2 && is_array(func_get_arg(1)))
		{
			$params = func_get_arg(1);
		}
		else if (func_num_args() > 1)
		{
			$params = array_slice(func_get_args(), 1);
		}
	}
	else
	{
		return '';
	}

	//echo strtolower(get_encoding()) . ':' . strtolower($nls['encoding'][$gCms->current_language]);

	$result = '';

	if (isset($lang[$realm][$name]))
	{
		if (isset($params))
		{
			$result = vsprintf($lang[$realm][$name], $params);
		}
		else
		{
			$result = $lang[$realm][$name];
		}
	}
	else
	{
		$result = "--Add Me - $name --";
	}

	/*
	if (isset($gCms->current_language) && isset($gCms->config['admin_encoding']) && $gCms->config['admin_encoding'] != '' && isset($gCms->variables['convertclass']) && ($nls['encoding'][$gCms->current_language] != $gCms->config['admin_encoding']))
	{
		$class =& $gCms->variables['convertclass'];
		$result = $class->Convert($result, $nls['encoding'][$gCms->current_language], $gCms->config['admin_encoding']);
	}
	else if (isset($gCms->current_language) && (strtolower(get_encoding()) != strtolower($nls['encoding'][$gCms->current_language])) && isset($gCms->variables['convertclass']))
	{
		$class =& $gCms->variables['convertclass'];
		$result = $class->Convert($result, $nls['encoding'][$gCms->current_language], get_encoding());
	}
	*/

	if (isset($gCms->config['admin_encoding']) && isset($gCms->variables['convertclass']))
	{
		if (strtolower(get_encoding('', false)) != strtolower($gCms->config['admin_encoding']))
		{
			$class =& $gCms->variables['convertclass'];
			$result = $class->Convert($result, get_encoding('', false), $gCms->config['admin_encoding']);
		}
	}

	//return strtolower(get_encoding('', false)) . ':' . strtolower($gCms->config['admin_encoding']) . ' - ' . $result;
	return $result;
}

function get_encoding($charset='', $defaultoverrides=true)
{
	global $nls;
	global $current_language;
	global $config;
	global $gCms;
	$variables =& $gCms->variables;

	if ($charset != '')
	{
		return $charset;
	}
        else if (isset($variables['current_encoding']) && $variables['current_encoding'] != "" )
        {
	  return $variables['current_encoding'];
        }
	else if (isset($config['default_encoding']) && $config['default_encoding'] != "" && $defaultoverrides == true)
	{
		return $config['default_encoding'];
	}
	else if (isset($nls['encoding'][$current_language]))
	{
		return $nls['encoding'][$current_language];
	}
	else
	{
		return "UTF-8"; //can't hurt
	}
}


function set_encoding($charset)
{
  global $gCms;
  $variables =& $gCms->variables;
  
  if( $charset == '' ) 
    {
      if( isset($variables['current_encoding']) )
	unset($variables['current_encoding']);
      return;
    }
  $variables['current_encoding'] =  $charset;
}

// Returns true if $string is valid UTF-8 and false otherwise.
function is_utf8($string)
{
   // From http://w3.org/International/questions/qa-forms-utf-8.html
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
   )*$%xs', $string);
   
} // function is_utf8

# vim:ts=4 sw=4 noet
?>
