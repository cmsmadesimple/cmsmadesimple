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

$dropdown = "";

$group = "";
if (isset($_POST["group"])) $group = $_POST["group"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["editgroup"])) $active = 0;

$group_id = -1;
if (isset($_POST["group_id"])) $group_id = $_POST["group_id"];
else if (isset($_GET["group_id"])) $group_id = $_GET["group_id"];

if (isset($_POST["cancel"])) {
	redirect("listgroups.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Modify Group');

if ($access) {

	$db = new DB($config);

	if (isset($_POST["editgroup"])) {

		$validinfo = true;
		if ($group == "") {
			$validinfo = false;
			$error .= "<li>No group name given!</li>";
		}

		if ($validinfo) {
			$query = "UPDATE ".$config->db_prefix."groups SET group_name='".mysql_escape_string($group)."', active=$active, modified_date = now() WHERE group_id = $group_id";
			$result = $db->query($query);

			if (mysql_affected_rows() > -1) {
				$db->close();
				redirect("listgroups.php");
				return;
			}
			else {
				$error .= "<li>Error updating group</li>";
			}
		}

	}
	else if ($group_id != -1) {

		$query = "SELECT * from ".$config->db_prefix."groups WHERE group_id = " . $group_id;
		$result = $db->query($query);
		
		$row = mysql_fetch_array($result, MYSQL_ASSOC);

		$group = $row["group_name"];
		$active = $row["active"];

		mysql_free_result($result);

	}

	$db->close();

}

include_once("header.php");

if (!$access) {
	print "<h3>No Access to Edit Groups</h3>";
}
else {
	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="editgroup.php">

<div class="adminform">

<h3>Edit Group</h3>

<table border="0">

	<tr>
		<td>*Name:</td>
		<td><input type="text" name="group" maxlength="25" value="<?=$group?>" /></td>
	</tr>
	<tr>
		<td>Active:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="group_id" value="<?=$group_id?>" /><input type="hidden" name="editgroup" value="true" /><input type="submit" value="Submit" /><input type="submit" name="cancel" value="Cancel" /></td>
	</tr>

</table>

</div>

</form>

<?php

}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
