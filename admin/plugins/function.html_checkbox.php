<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
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

function smarty_function_html_checkbox($params, &$smarty)
{
	echo '<input type="hidden"';
	if (isset($params['name']))
		echo ' name="'.$params['name'].'"';
	if (isset($params['unchecked_value'])) {
		echo ' value="'.$params['unchecked_value'].'"';
	} else {
		echo ' value="0"';
	}
	echo ' />';

	echo '<input type="checkbox"';
	if (isset($params['id']))
		echo ' id="'.$params['id'].'"';
	if (isset($params['name']))
		echo ' name="'.$params['name'].'"';
	if (isset($params['checked_value'])) {
		echo ' value="'.$params['checked_value'].'"';
	} else {
		echo ' value="1"';
	}
	if (isset($params['selected']) && $params['selected'] == true) {
		echo ' checked="checked"';
	}
	echo ' />';
}

?>
