<?php

require_once("../include.php");

check_login($config);

$template = "";
if (isset($_POST["template"])) $template = $_POST["template"];

$content = "";
if (isset($_POST["content"])) $content = $_POST["content"];

$stylesheet = "";
if (isset($_POST["stylesheet"])) $stylesheet = $_POST["stylesheet"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["edittemplate"])) $active = 0;

$template_id = -1;
if (isset($_POST["template_id"])) $template_id = $_POST["template_id"];
else if (isset($_GET["template_id"])) $template_id = $_GET["template_id"];

if (isset($_POST["cancel"])) {
	redirect("listtemplates.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Modify Template');

if ($access) {

	$db = new DB($config);

	if (isset($_POST["edittemplate"])) {

		$query = "UPDATE ".$config->db_prefix."templates SET template_name = '".mysql_real_escape_string($template)."', template_content = '".mysql_real_escape_string($content)."', stylesheet = '".mysql_real_escape_string($stylesheet)."', active = $active, modified_date = now() WHERE template_id = $template_id";
		$result = $db->query($query);

		if (mysql_affected_rows() > -1) {
			$db->close();
			redirect("listtemplates.php");
			return;
		}
		else {
			echo "Error updating template";
			echo "<pre>query: $query</pre>";
		}

	}
	else if ($template_id != -1) {

		$query = "SELECT * from ".$config->db_prefix."templates WHERE template_id = " . $template_id;
		$result = $db->query($query);
		
		$row = mysql_fetch_array($result, MYSQL_ASSOC);

		$template = $row["template_name"];
		$content = $row["template_content"];
		$stylesheet = $row["stylesheet"];
		$active = $row["active"];

		mysql_free_result($result);

	}

	$db->close($link);

}

include_once("header.php");

if (!$access) {
	print "<h3>No Access To Edit Templates</h3>";
}
else {

?>

<form method="post" action="edittemplate.php">

<div class="adminform">

<h3>Edit Template</h3>

<table border="0">

	<tr>
		<td>Template Name:</td>
		<td><input type="text" name="template" maxlength="25" value="<?=$template?>" /></td>
	</tr>
	<tr>
		<td>Content:</td>
		<td><textarea name="content" cols="90" rows="18"><?=$content?></textarea></td>
	</tr>
	<tr>
		<td>Stylesheet:</td>
		<td><textarea name="stylesheet" cols="90" rows="18"><?=$stylesheet?></textarea></td>
	</tr>
	<tr>
		<td>Active:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="template_id" value="<?=$template_id?>" /><input type="hidden" name="edittemplate" value="true" /><input type="submit" value="Submit" /><input type="submit" name="cancel" value="Cancel"></td>
	</tr>

</table>

</div>

</form>

<?php

}
include_once("footer.php");

?>
