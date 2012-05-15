<?php
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
#$Id: translation.functions.php 287 2010-08-30 14:15:42Z calguy1000 $


/**
 * Translation functions/classes
 *
 * @package CMS
 */

function ilang()
{
	global $lang;

	$name = '';
	$params = array();

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

	$result = '';

	if (isset($lang[$name]))
	{
		if (count($params))
		{
			$result = vsprintf($lang[$name], $params);
		}
		else
		{
			$result = $lang[$name];
		}
	}
	else
	{
		$result = "-- Missing Languagestring - $name --";
	}

	return $result;
}

// function get_encoding($charset='', $defaultoverrides=true)
// {
// 	return "UTF-8";
// }

# vim:ts=4 sw=4 noet
?>
