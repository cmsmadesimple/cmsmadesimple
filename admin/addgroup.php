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

$error = "";

$group= "";
if (isset($_POST["group"])) $group = $_POST["group"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addgroup"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listgroups.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Add Group');

if ($access) {
	if (isset($_POST["addgroup"])) {

		$db = new DB($config);

		$validinfo = true;
		if ($group == "") {
			$error .= "<li>".GetText::gettext("No group name given!")."</li>";
			$validinfo = false;
		}

		if ($validinfo) {
			$query = "INSERT INTO ".$config->db_prefix."groups (group_name, active, create_date, modified_date) VALUES ('".$db->escapestring($group)."', $active, now(), now())";
			$result = $db->query($query);
			if ($db->rowsaffected()) {
				$new_group_id = $db->insertid();
				$db->close();
				audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $new_group_id, $group, 'Added Group');
				redirect("listgroups.php");
				return;
			}
			else {
				$error .= "<li>".GetText::gettext("Error inserting group!")."</li>";
			}
		}

		$db->close();
	}
}

include_once("header.php");

if (!$access) {
	print "<h3>".GetText::gettext("No Access to Add Groups")."</h3>";
}
else {
	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="addgroup.php">

<div class="adminform">

<h3><?=GetText::gettext("Add Group")?></h3>

<table border="0">

	<tr>
		<td>*<?=GetText::gettext("Name")?>:</td>
		<td><input type="text" name="group" maxlength="255" value="<?=$group?>" /></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addgroup" value="true" /><input type="submit" value="<?=GetText::gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=GetText::gettext("Cancel")?>" /></td>
	</tr>

</table>

</div>

</form>

<?php
}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
