<?php

require_once("../include.php");

$template = "";
if (isset($_POST["template"])) $template = $_POST["template"];

$content = "";
if (isset($_POST["content"])) $content = $_POST["content"];

$active = 1;
if (isset($_POST["active"]) && isset($_POST["addsection"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listtemplates.php");
	return;
}

$db = new DB($config);

if (isset($_POST["addtemplate"])) {

	$query = "INSERT INTO ".$config->db_prefix."templates (template_name, template_content, active, create_date, modified_date) VALUES ('".mysql_escape_string($section)."', '".mysql_escape_string($content)."', $active, now(), now());";
	$result = $db->query($query);
	if (mysql_affected_rows() > -1) {
		$db->close();
		redirect("listtemplates.php");
		return;
	}
	else {
		echo "Error inserting template";
	}
}

$db->close();

?>

<form method="post" action="addtemplate.php">

<div class="adminform">

<h3>Add Template</h3>

<table border="0">

	<tr>
		<td>Name:</td>
		<td><input type="text" name="template" maxlength="25" value="<?=$template?>" /></td>
	</tr>
	<tr>
		<td>Content:</td>
		<td><textarea name="content" cols="90" rows="18"><?=$content?></textarea></td>
	</tr>
	<tr>
		<td>Active:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addtemplate" value="true" /><input type="submit" value="Submit" /><input type="submit" name="cancel" value="Cancel" /></td>
	</tr>

</table>

</div>

</form>
