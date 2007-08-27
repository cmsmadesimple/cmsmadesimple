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
