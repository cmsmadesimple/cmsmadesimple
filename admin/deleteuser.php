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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

$dodelete = true;

$user_id = -1;
if (isset($_GET["user_id"])) {

	$user_id = $_GET["user_id"];
	$user_name = "";
	$userid = get_userid();
	$access = check_permission($userid, 'Remove User');

	if ($access) {

		$query = "SELECT username FROM ".cms_db_prefix()."users WHERE user_id = ".$user_id;
		$result = $db->Execute($query);

		if ($result && $result->RowCount()) {
			$row = $result->FetchRow();
			$user_name = $row['username'];
		}

		$query = "SELECT count(*) AS count FROM ".cms_db_prefix()."pages WHERE owner = $user_id";
		$result = $db->Execute($query);
		$row = $result->FetchRow();
		if (isset($row["count"]) && $row["count"] > 0) {
			$dodelete = false;
		}

		if ($dodelete) {
			$query = "DELETE FROM ".cms_db_prefix()."additional_users where user_id = $user_id";
			$result = $db->Execute($query);
			$query = "DELETE FROM ".cms_db_prefix()."users where user_id = $user_id";
			$result = $db->Execute($query);
			audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $user_id, $user_name, 'Deleted User');
		}
	}
}

if ($dodelete == true) {
	redirect("listusers.php");
}
else {
	redirect("listusers.php?message=".lang('erroruserinuse'));
}

# vim:ts=4 sw=4 noet
?>
