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
$access = check_permission($userid, 'Modify Group');

if ($access) {

	if (isset($_POST["editgroup"])) {

		$validinfo = true;
		if ($group == "") {
			$validinfo = false;
			$error .= "<li>".$gettext->gettext("No group name given!")."</li>";
		}

		if ($validinfo) {
			$query = "UPDATE ".cms_db_prefix()."groups SET group_name=".$db->qstr($group).", active=$active, modified_date = ".$db->DBTimeStamp(time())." WHERE group_id = $group_id";
			$result = $db->Execute($query);

			if ($result) {
				audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $group_id, $group, 'Edited Group');
				redirect("listgroups.php");
				return;
			}
			else {
				$error .= "<li>".$gettext->gettext("Error updating group")."</li>";
			}
		}

	}
	else if ($group_id != -1) {

		$query = "SELECT * from ".cms_db_prefix()."groups WHERE group_id = " . $group_id;
		$result = $db->Execute($query);
		
		$row = $result->FetchRow();

		$group = $row["group_name"];
		$active = $row["active"];
	}
}

include_once("header.php");

if (!$access) {
	print "<h3>".$gettext->gettext("No Access to Edit Groups")."</h3>";
}
else {
	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="editgroup.php">

<div class="adminformSmall">

<h3><?=$gettext->gettext("Edit Group")?></h3>

<table border="0" align="center">

	<tr>
		<td align="right"><?=$gettext->gettext("Name")?>:</td>
		<td><input type="text" name="group" maxlength="25" value="<?=$group?>"></td>
	</tr>
	<tr>
		<td align="right"><?=$gettext->gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="hidden" name="group_id" value="<?=$group_id?>"><input type="hidden" name="editgroup" value="true">
		<input type="submit" value="<?=$gettext->gettext("Submit")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		<input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>

</table>

</div>

</form>

<?php

}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
