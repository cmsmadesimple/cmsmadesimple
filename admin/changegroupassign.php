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

check_login($config);

$group_id="";
if (isset($_POST["group_id"])) $group_id = $_POST["group_id"];
else if (isset($_GET["group_id"])) $group_id = $_GET["group_id"];

$group_name="";

if (isset($_POST["cancel"])) {
	redirect("listgroups.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Modify Group Assignments');

if ($access) {

	$query = "SELECT group_name FROM ".$config->db_prefix."groups WHERE group_id = ".$group_id;
	$result = $dbnew->Execute($query);

	if ($result) {
		$row = $result->FetchRow();
		$group_name = $row[group_name];
	}

	if (isset($_POST["changeassign"])) {

		$query = "DELETE FROM ".$config->db_prefix."user_groups WHERE group_id = " . $group_id;
		$result = $dbnew->Execute($query);

		foreach ($_POST as $key=>$value) {
			if (strpos($key,"user-") == 0 && strpos($key,"user-") !== false) {
				$query = "INSERT INTO ".$config->db_prefix."user_groups (group_id, user_id, create_date, modified_date) VALUES (".$dbnew->qstr($group_id).", ".$dbnew->qstr(substr($key,5)).", now(), now())";
				$result = $dbnew->Execute($query);
			}
		}

		audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $group_id, $group_name, 'Changed Group Assignments');
		redirect("listgroups.php");
		return;

	}
}

include_once("header.php");

if (!$access) {
	print "<h3>".$gettext->gettext("No Access to Modify Group Assignments")."</h3>";
}
else {

?>
<h3>Users assigned to group: <?=$group_name?></h3>

<form method="post" action="changegroupassign.php">

<div class="adminform">

<?php

	$query = "SELECT * FROM ".$config->db_prefix."users ORDER BY username";
	$result = $dbnew->Execute($query);

	if ($result) {

		while($row = $result->FetchRow()) {

			$users[$row[username]] = false;
			$ids[$row[username]] = $row[user_id];
		}

	}

	$query = "SELECT u.user_id, u.username FROM ".$config->db_prefix."user_groups ug INNER JOIN ".$config->db_prefix."users u ON u.user_id = ug.user_id WHERE group_id = " . $group_id;
	$result = $dbnew->Execute($query);

	if ($result) {

		while($row = $result->FetchRow()) {

			$users[$row[username]] = true; 
			$ids[$row[username]] = $row[user_id];
		}

	}

	foreach ($users as $key => $value) {

		echo "<p>";
		echo "<input type=\"checkbox\"";
		echo ($value == true?" checked":"");
		echo " name=\"user-".$ids[$key]."\" value=\"1\">$key</p>\n";
	}

?>

<p><input type="hidden" name="group_id" value="<?=$group_id?>" /><input type="submit" name="changeassign" value="<?=$gettext->gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" /></p>

</div>

</form>

<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
