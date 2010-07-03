<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
if (!isset($gCms)) die("Can't call actions directly!");

if (isset($params['submitprefs']) )
	{
		if( !$this->Permissions->check('Manage Site Preferences') )
			die('permission denied');

		$password_minlength = (int)coalesce_key($params,'password_minlength',6);
		$username_minlength = (int)coalesce_key($params,'username_minlength',4);
		
		if( $password_minlength == 0 )
			{
				$smarty->assign('error_msg',$this->Lang('Invalid value for password length'));
			}
		else
			{
				$this->set_preference('password_minlength',$password_minlength);
			}

		if( $username_minlength == 0 )
			{
				$smarty->assign('error_msg',$this->Lang('Invalid value for username length'));
			}
		else
			{
				$this->set_preference('username_minlength',$username_minlength);
			}
	}

// todo: check permissions here
$groups = cms_orm('CmsGroup')->find_all();
$smarty->assign('groups',$groups);

$users = cms_orm('CmsUser')->find_all();
$smarty->assign('users',$users);

$smarty->assign('active_tab_for_modules',coalesce_key($params,'selected_tab','users'));
$smarty->assign('form_action','defaultadmin');

echo $this->Template->process('defaultadmin.tpl',$id,$return_id);

# 
# EOF
#
?>
