<?php

require_once("../include.php");

check_login($config);

$group_id="";
if (isset($_POST["group_id"])) $group_id = $_POST["group_id"];
else if (isset($_GET["group_id"])) $group_id = $_GET["group_id"];

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
				$query = "INSERT INTO ".$config->db_prefix."user_groups (group_id, user_id, create_date, modified_date) VALUES (".mysql_real_escape_string($group_id).", ".mysql_real_escape_string(substr($key,5)).", now(), now())";
				$result = $db->query($query);
			}
		}

		$db->close();
		redirect("listgroups.php");
		return;

	}
}

include_once("header.php");

if (!$access) {
	print "<h3>No Access to Modify Group Assignments</h3>";
}
else {

?>
<h3>Users assigned to group: Name</h3>

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

<p><input type="hidden" name="group_id" value="<?=$group_id?>" /><input type="submit" name="changeassign" value="Submit" /><input type="submit" name="cancel" value="Cancel" /></p>

</form>

<?php

}

include_once("footer.php");

?>
