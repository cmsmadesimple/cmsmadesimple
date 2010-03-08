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

function smarty_function_get_user($params, &$smarty)
{
	if (!isset($params['id']) || $params['id'] < 0)
	{
		//var_dump('not found');
		return;
	}

	$to = 'user';
	if (isset($params['to']))
	{
		$to = $params['to'];
	}

	if ($smarty->get_template_vars('users') == null)
	{
		$smarty->assign('users', array());
	}

	$users = $smarty->get_template_vars('users');
	if (!array_key_exists($params['id'], $users))
	{
		$users[$params['id']] = cms_orm('CmsUser')->find_by_id($params['id']);
	}

	$smarty->assign('users', $users);
	$smarty->assign_by_ref($to, $users[$params['id']]);
}

?>