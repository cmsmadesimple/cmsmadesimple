<?php
#CMS - CMS Made Simple
#(c)2016 by Robert Campbell (calguy1000@cmsmadesimple.org)
#Visit our homepage at: http://www.cmsmadesimple.org
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

function smarty_function_page_selector($params,&$template)
{
    $value = (isset($params['value']) ) ? (int) $params['value'] : 0;
    $name = (isset($params['name']) ) ? trim($params['name']) : 'parent_id';
    $allowcurrent = (isset($params['allowcurrent']) ) ? cms_to_bool($params['allowcurrent']) : 0;
    $allow_all = (isset($params['allowall']) ) ? cms_to_bool($params['allowall']) : 0;
    $for_child = (isset($params['for_child']) ) ? cms_to_bool($params['for_child']) : 0;

    $out = \ContentOperations::get_instance()->CreateHierarchyDropdown('',$value,$name,$allowcurrent,0,0,$allow_all,$for_child);
	if( isset($params['assign']) )	{
		$smarty->assign(trim($params['assign']),$out);
		return;
	}
	return $out;
}
