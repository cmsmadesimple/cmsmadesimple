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

if (isset($_GET["css_id"]) && isset($_GET["id"]) && isset($_GET["type"])) {

	$css_id = $_GET["css_id"];
	$id = $_GET["id"];
	$type = $_GET["type"];

	$userid = get_userid();
	$access = check_permission($userid, 'Remove CSS association');

	if ($access) {

		if ($type == 'template') {
			# first we get the name of the template for logging
			$query = "SELECT template_name FROM ".cms_db_prefix()."templates WHERE template_id = '$id'";
			$result = $db->Execute($query);
			if ($result && $result->RowCount()) {
				$line = $result->FetchRow();
				$name = $line['template_name'];
			}
			else {
				$dodelete = false;
				$error = $gettext->gettext("Error getting the template name");
			}
		}

		if ($dodelete) {
			$query = "DELETE FROM ".cms_db_prefix()."css_assoc where assoc_css_id = '$css_id' AND assoc_type = '$type' AND assoc_to_id = '$id'";
			$result = $db->Execute($query);

			if ($result) {
				audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $id, $name, 'Deleted CSS association');
			}
			else {
				$dodelete = false;
				$error = $gettext->gettext("Error deleting CSS association!");
			}
		}
	}
	else {
		$dodelete = false;
		$error = $gettext->gettext("You do not have the right to remove CSS association");
	}

}
else {
	$dodelete = false;
	$error = $gettext->gettext("Some parameters were missing or invalid!");
}

if ($dodelete) {
	redirect("listcssassoc.php?id=$id&type=$type");
}
else {
	redirect("listcssassoc.php?id=$id&type=$type&message=$error");
}

# vim:ts=4 sw=4 noet
?>
