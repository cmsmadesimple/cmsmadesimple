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

require_once("../include.php");

check_login($config);

$error="";

$section= "";
if (isset($_POST["section"])) $section = $_POST["section"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addsection"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listsections.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Add Section');

if ($access) {

	if (isset($_POST["addsection"])) {

		$validinfo = true;
		if ($section == "") {
			$validinfo = false;
			$error .= "<li>".$gettext->gettext("No Section title given!")."</li>";
		}

		if ($validinfo) {
			$order = 1;

			$query = "SELECT max(item_order) + 1 as item_order FROM ".$config->db_prefix."sections";
			$result = $dbnew->Execute($query);
			$row = $result->FetchRow();
			if (isset($row["item_order"])) {
				$order = $row["item_order"];	
			}

			$query = "INSERT INTO ".$config->db_prefix."sections (section_name, item_order, active, create_date, modified_date) VALUES (".$dbnew->qstr($section).", $order, $active, now(), now())";
			$result = $dbnew->Execute($query);
			if ($result) {
				$new_section_id = $dbnew->Insert_ID();
				audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $new_section_id, $section, 'Added Section');
				redirect("listsections.php");
				return;
			}
			else {
				$error .= "<li>".$gettext->gettext("Error inserting section")."</li>";
			}
		}
	}
}

include_once("header.php");

if (!$access) {
	print "<h3>".$gettext->gettext("No Access to Add Sections")."</h3>";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}

?>

<form method="post" action="addsection.php">

<div class="adminform">

<h3><?=$gettext->gettext("Add Section")?></h3>

<table border="0">

	<tr>
		<td>*<?=$gettext->gettext("Name")?>:</td>
		<td><input type="text" name="section" maxlength="255" value="<?=$section?>" /></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addsection" value="true" /><input type="submit" value="<?=$gettext->gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" /></td>
	</tr>

</table>

</div>

</form>

<?php
}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
