<?php

require_once("../include.php");

check_login($config);

$group= "";
if (isset($_POST["group"])) $group = $_POST["group"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addgroup"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listgroups.php");
	return;
}

$db = new DB($config);

if (isset($_POST["addgroup"])) {

	$query = "INSERT INTO ".$config->db_prefix."groups (group_name, active, create_date, modified_date) VALUES ('".mysql_real_escape_string($group)."', $active, now(), now())";
	$result = $db->query($query);
	if (mysql_affected_rows() > -1) {
		$db->close();
		redirect("listgroups.php");
		return;
	}
	else {
		echo "Error inserting group";
	}
}

$db->close();

include_once("header.php");

?>

<form method="post" action="addgroup.php">

<div class="adminform">

<h3>Add Group</h3>

<table border="0">

	<tr>
		<td>Name:</td>
		<td><input type="text" name="group" maxlength="255" value="<?=$group?>" /></td>
	</tr>
	<tr>
		<td>Active:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addgroup" value="true" /><input type="submit" value="Submit" /><input type="submit" name="cancel" value="Cancel" /></td>
	</tr>

</table>

</div>

</form>

<?php
include_once("footer.php");
?>
