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

if (isset($_GET["css_id"])) {

	$css_id		= $_GET["css_id"];
	$css_name	= "";

	$userid		= get_userid();
	$access		= check_permission($userid, 'Remove CSS');

	if ($access) {

		# first we get the name of the css for logging
		$query = "SELECT css_name FROM ".cms_db_prefix()."css WHERE css_id = '$css_id'";
		$result = $db->Execute($query);
		if ($result && $result->RowCount()) {
			$row = $result->FetchRow();
			$css_name = $row['css_name'];
		}
		else {
			$dodelete = false;
			$error = $gettext->gettext("Error getting the CSS name");
		}

		if ($dodelete) {

			# then we check if this CSS has associations
			$query = "SELECT * FROM ".cms_db_prefix()."css_assoc WHERE assoc_css_id = '$css_id'";
			$result = $db->Execute($query);
			if ($result && $result->RowCount()) {
				$dodelete = false;
				$error =  $gettext->gettext("This CSS is still used by templates or pages. Please remove those association first!");
			}
		}

		# everything should be ok
		if ($dodelete) {
			$query = "DELETE FROM ".cms_db_prefix()."css where css_id = '$css_id'";
			$result = $db->Execute($query);

			if ($result) {
				audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $css_id, $css_name, 'Deleted CSS');
			}
			else {
				$dodelete = false;
				$error = $gettext->gettext("Error deleting CSS!");
			}
		}
	}
	else {
		$dodelete = false;
		$error = $gettext->gettext("You do not have the right to delete CSS");
	}
}
else {
	$dodelete = false;
	$error = $gettext->gettext("The CSS ID is not valid!");
}

if ($dodelete) {
	redirect("listcss.php");
}
else {
	redirect("listcss.php?message=$error");
}

# vim:ts=4 sw=4 noet
?>
