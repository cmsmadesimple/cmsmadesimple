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

$group_name = "";

if (isset($_POST["cancel"])) {
	redirect("listgroups.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Modify Permissions');

if ($access) {

	$db = new DB($config);

	if (isset($_POST["changeperm"])) {

		$query = "DELETE FROM ".$config->db_prefix."group_perms WHERE group_id = " . $group_id;
		$result = $db->query($query);

		foreach ($_POST as $key=>$value) {
			if (strpos($key,"perm-") == 0) {
				$query = "INSERT INTO ".$config->db_prefix."group_perms (group_id, permission_id, create_date, modified_date) VALUES (".mysql_escape_string($group_id).", ".mysql_escape_string(substr($key,5)).", now(), now())";
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
		$group_name = $row[group_name];
	}

	mysql_free_result($result);
}

include_once("header.php");

if (!$access) {
	print "<h3>".GetText::gettext("No Access to Modify Group Permissions")."</h3>";
}
else {

	GetText::setVar('group_name', $group_name);

?>
<h3><?=GetText::gettext('Permissions for group: ${group_name}')?></h3>

<form method="post" action="changegroupperm.php">
<?php

	GetText::reset();

	$query = "SELECT permission_id, permission_name, permission_text FROM ".$config->db_prefix."permissions ORDER BY permission_name";
	$result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			$perms[$row[permission_name]] = false;
			$ids[$row[permission_name]] = $row[permission_id];
		}

	}

	mysql_free_result($result);

	$query = "SELECT p.permission_name FROM ".$config->db_prefix."group_perms g INNER JOIN ".$config->db_prefix."permissions p ON p.permission_id = g.permission_id WHERE g.group_id = " . $group_id;

	$result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			$tmp = $row[permission_name];
			$perms[$tmp] = true;
		}

	}

	mysql_free_result($result);

	$db->close();

	foreach ($perms as $key => $value) {

		echo "<p>";
		echo "<input type=\"checkbox\"";
		echo ($value == true?" checked":"");
		echo " name=\"perm-".$ids[$key]."\" value=\"1\">$key</p>\n";

	}

?>

<p><input type="hidden" name="group_id" value="<?=$group_id?>" /><input type="submit" name="changeperm" value="<?=GetText::gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=GetText::gettext("Cancel")?>" /></p>

</form>

<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
