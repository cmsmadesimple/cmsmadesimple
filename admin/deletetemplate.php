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
$template_id = -1;
if (isset($_GET["template_id"])) {

	$template_id = $_GET["template_id"];
	$template_name = "";
	$userid = get_userid();
	$access = check_permission($userid, 'Remove Template');

	if ($access) {

		$query = "SELECT template_name FROM ".cms_db_prefix()."templates WHERE template_id = ".$template_id;
		$result = $db->Execute($query);

		if ($result && $result->RowCount()) {
			$row = $result->FetchRow();
			$template_name = $row['template_name'];
		}

		$query = "SELECT count(*) AS count FROM ".cms_db_prefix()."pages WHERE template_id = $template_id";
		$result = $db->Execute($query);
		$row = $result->FetchRow();
		if (isset($row["count"]) && $row["count"] > 0) {
			$dodelete = false;
		}

		if ($dodelete) {
			$query = "DELETE FROM ".cms_db_prefix()."templates where template_id = $template_id";
			$result = $db->Execute($query);
			audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $template_id, $template_name, 'Deleted Template');
		}
	}
}

if ($dodelete) {
	redirect("listtemplates.php");
}
else {
	redirect("listtemplates.php?message=".$gettext->gettext("Template still being used by content pages.  Please remove those first."));
}

# vim:ts=4 sw=4 noet
?>
