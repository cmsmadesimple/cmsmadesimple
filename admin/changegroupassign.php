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

	$db = new DB($config);

	if (isset($_POST["changeassign"])) {

		$query = "DELETE FROM ".$config->db_prefix."user_groups WHERE group_id = " . $group_id;
		$result = $db->query($query);

		foreach ($_POST as $key=>$value) {
			if (strpos($key,"user-") == 0) {
				$query = "INSERT INTO ".$config->db_prefix."user_groups (group_id, user_id, create_date, modified_date) VALUES (".mysql_escape_string($group_id).", ".mysql_escape_string(substr($key,5)).", now(), now())";
				$result = $db->query($query);
			}
		}

		$db->close();
		redirect("listgroups.php");
		return;

	}

	$query = "SELECT group_name FROM ".$config->db_prefix."groups WHERE group_id = ".$group_id;
        $result = $db->query($query);

        if (mysql_num_rows($result) > 0) {
                $row = mysql_fetch_array($result, MYSQL_ASSOC);
                $group_name = $row[group_name];                                         }

        mysql_free_result($result);
}

include_once("header.php");

if (!$access) {
	print "<h3>".GetText::gettext("No Access to Modify Group Assignments")."</h3>";
}
else {

?>
<h3>Users assigned to group: <?=$group_name?></h3>

<form method="post" action="changegroupassign.php">
<?php

        $query = "SELECT * FROM ".$config->db_prefix."users ORDER BY username";
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			$users[$row[username]] = false;
			$ids[$row[username]] = $row[user_id];
		}

	}

        mysql_free_result($result);

        $query = "SELECT u.user_id, u.username FROM ".$config->db_prefix."user_groups ug INNER JOIN ".$config->db_prefix."users u ON u.user_id = ug.user_id WHERE group_id = " . $group_id;
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			$users[$row[username]] = true; 
			$ids[$row[username]] = $row[user_id];
		}

	}

        mysql_free_result($result);

        $db->close();

	foreach ($users as $key => $value) {

		echo "<p>";
		echo "<input type=\"checkbox\"";
		echo ($value == true?" checked":"");
		echo " name=\"user-".$ids[$key]."\" value=\"1\">$key</p>\n";

	}

?>

<p><input type="hidden" name="group_id" value="<?=$group_id?>" /><input type="submit" name="changeassign" value="<?=GetText::gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=GetText::gettext("Cancel")?>" /></p>

</form>

<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
