<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
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

function smarty_function_tab_start($params,&$template)
{
	$smarty = $template->smarty;

	if( !isset($params['name']) ) return;

	$parms = array();
	foreach( $params as $key => $value )
	{
		switch( $key )
		{
			case 'name':
				$name = trim($value);
				break;
			case 'assign':
				break;
			default:
				$parms[$key] = trim($value);
		}
	}

	$out = cms_admin_tabs::start_tab($name,$parms);
	if( isset($params['assign']) )
	{
		$smarty->assign(trim($params['assign']),$out);
		return;
	}
	return $out;
}
?>