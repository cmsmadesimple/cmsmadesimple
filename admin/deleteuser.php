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
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

$dodelete = true;

$user_id = -1;
if (isset($_GET["user_id"]))
{
	$user_id = $_GET["user_id"];
	$user_name = "";
	$userid = get_userid();
	$access = check_permission($userid, 'Remove Users');

	if ($access)
	{
		$oneuser = CmsUserOperations::load_user_by_id($user_id);
		$user_name = $oneuser->name;
		$ownercount = CmsUserOperations::count_page_ownership_by_id($user_id);

		if ($ownercount > 0)
		{
			$dodelete = false;
		}

		if ($dodelete)
		{
			$oneuser->delete();

			audit($user_id, $user_name, 'Deleted User');
		}
	}
}

if ($dodelete == true)
{
	redirect("listusers.php");
}
else
{
	redirect("listusers.php?message=".lang('erroruserinuse'));
}

# vim:ts=4 sw=4 noet
?>
