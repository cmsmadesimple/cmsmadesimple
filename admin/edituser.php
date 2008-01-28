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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

if (isset($_POST["cancel"]))
{
	redirect("listusers.php");
}

$gCms = cmsms();
$smarty = cms_smarty();
$smarty->assign('action', 'edituser.php');

// Make sure we have permissions for this page
$userid = get_userid();
$access = check_permission($userid, 'Modify Users');

require_once("header.php");

$submit = array_key_exists('submitbutton', $_POST);

function get_user_object($user_id)
{
	$user_object = cmsms()->cms_user->find_by_id($user_id);
	if (isset($_REQUEST['user']))
	{
		$user_object->update_parameters($_REQUEST['user']);
	
		//Handle password separately -- too much room for error
		//to put all the login in the CmsUser class
		if ($_REQUEST['password'] != '')
		{
			if ($_REQUEST['password'] == $_REQUEST['passwordagain'])
			{
				$user_object->set_password($_REQUEST['password']);
			}
			else
			{
				//Add validation error about passwords not matching
				$user_object->add_validation_error(lang("Passwords don't match"));
			}
		}
	}
	
	return $user_object;
}

//Get a working page object
$user_id = coalesce_key($_REQUEST, 'user_id', $userid);
$user_object = get_user_object($user_id);

if ($access)
{
	if ($submit)
	{
		if ($user_object->save())
		{
			if ($submit)
			{
				audit($user_object->id, $user_object->name, 'Edited user');
				redirect("listusers.php");
			}
		}
	}
}

//Add the header
$smarty->assign('header_name', $themeObject->ShowHeader('edituser'));

//Setup the user object
$smarty->assign_by_ref('user_object', $user_object);

$smarty->display('adduser.tpl');

include_once("footer.php");
?>