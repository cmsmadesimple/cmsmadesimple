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

// security
if (!isset($gCms)) die("Can't call actions directly!");

$user = CmsLogin::get_current_user();
if (!CmsAcl::check_core_permission('Modify Users',$user) || !isset($params['uid']))
	{ 
		$uid = $user->id;
	}
else
	{
		$uid = (int)$params['uid'];
	}

// setup

$groups = cms_orm('CmsGroup')->find_all();
$user = cms_orm('CmsUser')->load($uid);

// handle form operations
if( isset($params['cancel']) )
	{
		$this->Redirect->module_url(array('action' => 'defaultadmin', 'selected_tab' => 'users'));
	}
else if( isset( $params['submit']) )
	{
		$user->name = trim(coalesce_key($params,'name',''));
		$user->first_name = trim(coalesce_key($params,'first_name',''));
		$user->last_name = trim(coalesce_key($params,'last_name',''));
		$user->email = trim(coalesce_key($params,'email',''));
		$user->openid = trim(coalesce_key($params,'openid',''));
		$user->active = trim(coalesce_key($params,'active',1));

		// don't allow admin user to be deactivated
		if( $user->id == 1 ) $user->active = 1;

		// these fields are extra and used only for validation
		$user->min_username_length = $this->Preference->get('username_minlength',4);
		$user->min_password_length = $this->Preference->get('password_minlength',4);
		if( !empty($params['password']) )
			{
				$user->clear_password = trim(coalesce_key($params,'password',''));
				$user->clear_repeat_password = trim(coalesce_key($params,'repeat',''));
			}

		// this will encode the password
		if( !empty($params['password']) )
			{
				$user->set_password($params['password']);
			}

		// save
		if( $user->save() )
			{
				foreach( $groups as $onegroup )
					{
						if( isset($params['groups'][$onegroup->id]) &&
							$params['groups'][$onegroup->id] == 1)
							{
								$onegroup->add_user($user);
							}
						else
							{
								$onegroup->remove_user($user);
							}
					}

				$this->Redirect->module_url(array('action' => 'defaultadmin', 'selected_tab' => 'users'));
			}
		foreach( $groups as $onegroup )
			{
				if( isset($params['groups'][$onegroup->id]) &&
					$params['groups'][$onegroup->id] == 1)
					{
						$onegroup->member = 1;
					}
				else
					{
						$onegroup->member = 0;
					}
			}	

		$smarty->assign('password',$user->clear_password);
		$smarty->assign('repeat',$user->clear_repeat_password);
	}

// lazy method.... adjust the groups array with a selected field
// so that we can tell if that user is a member
foreach( $groups as $onegroup )
{
	$onegroup->member = 0;
	foreach( $user->groups as $onechild )
	{
		if( $onechild instanceof CmsGroup )
		{
			if( $onechild->id == $onegroup->id )
			{
				$onegroup->member = 1;
			}
		}
	}
}
$smarty->assign('user',$user);
$smarty->assign('groups',$groups);
$smarty->assign('module_action','admin_edituser');
echo $this->Template->process('edituser.tpl',$id,$return_id);

# 
# EOF
#
?>
