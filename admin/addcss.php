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

$error = "";

$css_text = "";
if (isset($_POST["css_text"])) $css_text = $_POST["css_text"];

$css_name = "";
if (isset($_POST["css_name"])) $css_name = $_POST["css_name"];

if (isset($_POST["cancel"])) {
	redirect("listcss.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Add CSS');

if ($access) {

	if (isset($_POST["addcss"])) {

		$validinfo = true;

		if ($css_name == "") {
			$error .= "<li>".$gettext->gettext("No CSS name given!")."</li>";
			$validinfo = false;
		} else {
			$query = "SELECT css_id from ".cms_db_prefix()."css WHERE css_name = " . $db->qstr($css_name);
			$result = $db->Execute($query);

			if ($result && $result->RowCount() > 0) {
				$error .= "<li>".$gettext->gettext("CSS name already in use!")."</li>";
				$validinfo = false;
			}
		}
		if ($css_text == "") {
			$error .= "<li>".$gettext->gettext("No CSS content entered!")."</li>";
			$validinfo = false;
		}

		if ($validinfo) {
			$new_css_id = $db->GenID(cms_db_prefix()."css_seq");
			$query = "INSERT INTO ".cms_db_prefix()."css (css_id, css_name, css_text, create_date, modified_date) VALUES ($new_css_id, ".$db->qstr($css_name).", ".$db->qstr($css_text).", ".$db->DBTimeStamp(time()).", ".$db->DBTimeStamp(time()).")";
			$result = $db->Execute($query);
			if ($result) {
				audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $new_css_id, $css_name, 'Added CSS');
				redirect("listcss.php");
				return;
			}
			else {
				$error .= "<li>".$gettext->gettext("Error inserting CSS")."$query</li>";
			}
		}
	}
}

include_once("header.php");

if (!$access) {
	print "<h3>".$gettext->gettext("No Access to Add CSS")."</h3>";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="addcss.php">

<div class="adminform">

<h3><?=$gettext->gettext("Add CSS")?></h3>

<table border="0">

	<tr>
		<td><?=$gettext->gettext("Name")?>:</td>
		<td><input type="text" name="css_name" maxlength="25" value="<?=$css_name?>" /></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Content")?>:</td>
		<td><textarea name="css_text" cols="90" rows="18"><?=$css_text?></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addcss" value="true" /><input type="submit" value="<?=$gettext->gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" /></td>
	</tr>

</table>

</div>

</form>

<?php

}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
