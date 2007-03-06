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
#$Id$

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

$userid = "";
if ($_SESSION['cmsms_user_id'])
{
 	$userid = $_SESSION['cmsms_user_id'];
}

$username= "";
if ($_SESSION['login_user_username'])
{
 	$username = $_SESSION['login_user_username'];
}

audit($userid, $username, 'User Logout');

#unset($_SESSION['cms_admin_user_id']);
#setcookie('cms_admin_user_id', '', time() - 3600);
#setcookie('cms_passhash', '', time() - 3600);

#Perform the logout_post callback
foreach($gCms->modules as $key=>$value)
{
	if ($gCms->modules[$key]['installed'] == true &&
		$gCms->modules[$key]['active'] == true)
	{
		$gCms->modules[$key]['object']->LogoutPost();
	}
}

#Now call the event
CmsEvents::SendEvent('Core', 'LogoutPost');

$_SESSION['logout_user_now'] = "1";
redirect("login.php");

# vim:ts=4 sw=4 noet
?>