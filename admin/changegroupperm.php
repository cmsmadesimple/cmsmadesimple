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

$db = new DB($config);

if (isset($_POST["changeperm"])) {

	$query = "DELETE FROM ".$config->db_prefix."group_perms WHERE group_id = " . $group_id;
	$result = $db->query($query);

	foreach ($_POST as $key=>$value) {
		if (strpos($key,"perm-") == 0) {
			$query = "INSERT INTO ".$config->db_prefix."group_perms (group_id, permission_id, create_date, modified_date) VALUES (".mysql_real_escape_string($group_id).", ".mysql_real_escape_string(substr($key,5)).", now(), now())";
			$result = $db->query($query);
		}
	}

	$db->close();
	redirect("listgroups.php");
	return;

}

include_once("header.php");

?>
<h3>Permissions for group: Name</h3>

<form method="post" action="changegroupperm.php">
<?php

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

<p><input type="hidden" name="group_id" value="<?=$group_id?>" /><input type="submit" name="changeperm" value="Submit" /><input type="submit" name="cancel" value="Cancel" /></p>

</form>

<?php

include_once("footer.php");

?>
