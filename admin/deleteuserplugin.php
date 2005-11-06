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

check_login();

$userplugin_id = -1;
if (isset($_GET["userplugin_id"])) {

	$userplugin_id = $_GET["userplugin_id"];
	$userplugin_name = "";
	$userid = get_userid();
	$access = check_permission($userid, 'Modify Code Blocks');

	if ($access) {

		$query = "SELECT userplugin_name FROM ".cms_db_prefix()."userplugins WHERE userplugin_id = ?";
		$result = $db->Execute($query, array($userplugin_id));

		if ($result && $result->RowCount()) {
			$row = $result->FetchRow();
			$userplugin_name = $row['userplugin_name'];
		}

		$query = "DELETE FROM ".cms_db_prefix()."userplugins where userplugin_id = ?";
		$result = $db->Execute($query,array($userplugin_id));
		audit($userplugin_id, $userplugin_name, 'Deleted User Defined Tag');
	}
}

redirect("listusertags.php");

# vim:ts=4 sw=4 noet
?>
