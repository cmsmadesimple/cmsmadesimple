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

$doadd = true;

if (isset($_POST["css_id"]) && isset($_POST["id"]) && isset($_POST["type"])) {

	$css_id = $_POST["css_id"];
	$id		= $_POST["id"];
	$type	= $_POST["type"];

	$userid = get_userid();
	$access = check_permission($userid, 'Add CSS association');

	if ($access) {

		# first check if this association already exists
		$query = "SELECT * FROM ".cms_db_prefix()."css_assoc WHERE assoc_to_id = '$id' AND assoc_type = '$type' AND assoc_css_id = '$css_id'";
		$result = $db->Execute($query);

		if ($result && $result->RowCount() > 0) {
			$error = $gettext->gettext("This CSS association already exists");
			$doadd = false;
		}

		# we get the name of the element (for logging)
		if ($type == "template" && $doadd) {
			$query = "SELECT template_name FROM ".cms_db_prefix()."templates WHERE template_id = '$id'";
			$result = $db->Execute($query);
			
			if ($result && $result->RowCount() > 0) {
				$line = $result->FetchRow();
				$name = $line["template_name"];
			}
			else {
				$doadd = false;
				$error = $gettext->gettext("The template is not valid !");
			}
		}

		# everything is ok, we can insert the element.
		if ($doadd) {
			$query = "INSERT INTO ".cms_db_prefix()."css_assoc (assoc_to_id,assoc_css_id,assoc_type,create_date,modified_date)
				VALUES ('$id','$css_id','$type',".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
			$result = $db->Execute($query);

			if ($result) {
				audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $id, $name, 'Added CSS association');
			}
			else {
				$doadd = false;
				$error = $gettext->gettext("Error creating CSS association!");
			}
		}
	}
	else {
		$doadd = false;
		$error = $gettext->gettext("You do not have the right to create CSS associations");
	}
}
else {
	$doadd = false;
	$error = $gettext->gettext("Some informations where missing!");
}

if ($doadd) {
	redirect("listcssassoc.php?id=$id&type=$type");
}
else {
	redirect("listcssassoc.php?id=$id&type=$type&message=$error");
}

# vim:ts=4 sw=4 noet
?>
