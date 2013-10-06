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

function smarty_function_cms_langstring($params,&$template)
{
	$smarty = $template->smarty;

	$string	= isset($params['string']) ? $params['string'] : '';

	if( $string == '' )
	{
		echo "Error! The required <b>string</b> parameter is missing in the cms_langstring tag!";
		return;
	}

	$out = lang_by_realm('cms_langstring',$string);
	return $out;
}

/*
 * Template Example: {cms_langstring string='foo'}
 * PHP File Example: lang_by_realm('cms_langstring',$foo)
 *
 * Look into /lib/lang/cms_langstring/en_US.php for all available global languagestrings
 * Using this string means less work translating strings for each seperate module...
 */

?>