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

/**
 * Translation functions/classes
 *
 * @package CMS
 */

function lang($name, $params = array(), $realm='admin')
{
	global $lang;

	if (isset($lang[$realm][$name]))
	{
		if (isset($params))
		{
			return vsprintf($lang[$realm][$name], $params);
		}
		else
		{
			return $lang[$realm][$name];
		}
	}
	else
	{
		return "--Add Me - $name --";
	}
}

function get_encoding($charset='')
{
	global $nls;
	global $current_language;
	global $config;

	if ($charset != '')
	{
		return $charset;
	}
	else if (isset($config['default_encoding']) && isset($config['default_encoding']) != "")
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

# vim:ts=4 sw=4 noet
?>
