<?php

require_once("../include.php");

check_login($config);

$section= "";
if (isset($_POST["section"])) $section = $_POST["section"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addsection"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listsections.php");
	return;
}

$db = new DB($config);

if (isset($_POST["addsection"])) {

	$query = "INSERT INTO ".$config->db_prefix."sections (section_name, active, create_date, modified_date) VALUES ('".mysql_real_escape_string($section)."', $active, now(), now())";
	$result = $db->query($query);
	if (mysql_affected_rows() > -1) {
		$db->close();
		redirect("listsections.php");
		return;
	}
	else {
		echo "Error inserting section";
	}
}

$db->close();

include_once("header.php");

?>

<form method="post" action="addsection.php">

<div class="adminform">

<h3>Add Section</h3>

<table border="0">

	<tr>
		<td>Name:</td>
		<td><input type="text" name="section" maxlength="255" value="<?=$section?>" /></td>
	</tr>
	<tr>
		<td>Active:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addsection" value="true" /><input type="submit" value="Submit" /><input type="submit" name="cancel" value="Cancel" /></td>
	</tr>

</table>

</div>

</form>

<?php

include_once("footer.php");

?>
