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

$group_id="";
if (isset($_POST["group_id"])) $group_id = $_POST["group_id"];
else if (isset($_GET["group_id"])) $group_id = $_GET["group_id"];

$group_name="";

if (isset($_POST["cancel"])) {
	redirect("listgroups.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Group Assignments');

if ($access) {

	$query = "SELECT group_name FROM ".cms_db_prefix()."groups WHERE group_id = ".$group_id;
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {
		$row = $result->FetchRow();
		$group_name = $row['group_name'];
	}

	if (isset($_POST["changeassign"])) {

		$query = "DELETE FROM ".cms_db_prefix()."user_groups WHERE group_id = " . $group_id;
		$result = $db->Execute($query);

		foreach ($_POST as $key=>$value) {
			if (strpos($key,"user-") == 0 && strpos($key,"user-") !== false) {
				$query = "INSERT INTO ".cms_db_prefix()."user_groups (group_id, user_id, create_date, modified_date) VALUES (".$db->qstr($group_id).", ".$db->qstr(substr($key,5)).", ".$db->DBTimeStamp(time()).", ".$db->DBTimeStamp(time()).")";
				$result = $db->Execute($query);
			}
		}

		audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $group_id, $group_name, 'Changed Group Assignments');
		redirect("listgroups.php");
		return;

	}
}

include_once("header.php");

if (!$access) {
	print "<h3>".$gettext->gettext("No Access to Modify Group Assignments")."</h3>";
}
else {

	$gettext->setVar('group_name', $group_name);

?>
<h3><?=$gettext->gettext('Users assigned to group: ${group_name}')?></h3>

<form method="post" action="changegroupassign.php">

<div class="adminformSmall">



<?php

	$query = "SELECT * FROM ".cms_db_prefix()."users ORDER BY username";
	$result = $db->Execute($query);

	if ($result && $result->RowCount()) {

		while($row = $result->FetchRow()) {

			$users[$row['username']] = false;
			$ids[$row['username']] = $row['user_id'];
		}

	}

	$query = "SELECT u.user_id, u.username FROM ".cms_db_prefix()."user_groups ug INNER JOIN ".cms_db_prefix()."users u ON u.user_id = ug.user_id WHERE group_id = " . $group_id;
	$result = $db->Execute($query);

	if ($result && $result->RowCount()) {

		while($row = $result->FetchRow()) {

			$users[$row['username']] = true; 
			$ids[$row['username']] = $row['user_id'];
		}

	}
	echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" summary=\"\" align=\"center\">";
	foreach ($users as $key => $value) {

		echo "<tr><td>";
		echo "<input type=\"checkbox\"";
		echo ($value == true?" checked":"");
		echo " name=\"user-".$ids[$key]."\" value=\"1\">$key</td></tr>\n";
	}

?>

<tr><td><br><input type="hidden" name="group_id" value="<?=$group_id?>" /><input type="submit" name="changeassign" value="<?=$gettext->gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" /></td></tr>
</table>
</div>

</form>

<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
