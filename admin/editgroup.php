<?php

require_once("../include.php");

$dropdown = "";

$group = "";
if (isset($_POST["group"])) $group = $_POST["group"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["editgroup"])) $active = 0;

$group_id = -1;
if (isset($_POST["group_id"])) $group_id = $_POST["group_id"];
else if (isset($_GET["group_id"])) $group_id = $_GET["group_id"];

$db = new DB($config);

if (isset($_POST["cancel"])) {
	redirect("listgroups.php");
	return;
}

if (isset($_POST["editgroup"])) {

	$query = "UPDATE ".$config->db_prefix."groups SET group_name='".mysql_real_escape_string($group)."', active=$active, modified_date = now() WHERE group_id = $group_id";
	$result = $db->query($query);

	if (mysql_affected_rows() > -1) {
		$db->close();
		redirect("listgroups.php");
		return;
	}
	else {
		echo "Error updating group";
		echo "<pre>query: $query</pre>";
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

?>

<form method="post" action="editgroup.php">

<div class="adminform">

<h3>Edit Group</h3>

<table border="0">

	<tr>
		<td>Name:</td>
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
