<?php

require_once("../config.php");

$dropdown = "";

$section = "";
if (isset($_POST["section"])) $section = $_POST["section"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["editsection"])) $active = 0;

$section_id = -1;
if (isset($_POST["section_id"])) $section_id = $_POST["section_id"];
else if (isset($_GET["section_id"])) $section_id = $_GET["section_id"];

$db = new DB($config);

if (isset($_POST["cancel"])) {
	redirect("listsections.php");
	return;
}

if (isset($_POST["editsection"])) {

	$query = "UPDATE ".$config->db_prefix."sections SET section_name='".mysql_real_escape_string($section)."', active=$active, modified_date = now() WHERE section_id = $section_id";
	$result = $db->query($query);

	if (mysql_affected_rows() > -1) {
		$db->close();
		redirect("listsections.php");
		return;
	}
	else {
		echo "Error updating section";
		echo "<pre>query: $query</pre>";
	}

}
else if ($section_id != -1) {

	$query = "SELECT * from ".$config->db_prefix."sections WHERE section_id = " . $section_id;
	$result = $db->query($query);
	
	$row = mysql_fetch_array($result, MYSQL_ASSOC);

	$section = $row["section_name"];
	$active = $row["active"];

	mysql_free_result($result);

}

$db->close();

?>

<form method="post" action="editsection.php">

<div class="adminform">

<h3>Edit Content</h3>

<table border="0">

	<tr>
		<td>Name:</td>
		<td><input type="text" name="section" maxlength="25" value="<?=$section?>" /></td>
	</tr>
	<tr>
		<td>Active:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="section_id" value="<?=$section_id?>" /><input type="hidden" name="editsection" value="true" /><input type="submit" value="Submit" /><input type="submit" name="cancel" value="Cancel" /></td>
	</tr>

</table>

</div>

</form>
