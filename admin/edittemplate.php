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

$error = "";

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

		$validinfo = true;
		if ($template == "") {
			$error .= "<li>No template name given!</li>";
			$validinfo = false;
		}
		if ($content == "") {
			$error .= "<li>No template content given!</li>";
			$validinfo = false;
		}

		if ($validinfo) {
			$query = "UPDATE ".$config->db_prefix."templates SET template_name = '".mysql_escape_string($template)."', template_content = '".mysql_escape_string($content)."', stylesheet = '".mysql_escape_string($stylesheet)."', active = $active, modified_date = now() WHERE template_id = $template_id";
			$result = $db->query($query);

			if (mysql_affected_rows() > -1) {
				$db->close();
				redirect("listtemplates.php");
				return;
			}
			else {
				$error .= "<li>Error updating template!</li>";
			}
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

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}

?>

<form method="post" action="edittemplate.php">

<div class="adminform">

<h3>Edit Template</h3>

<table border="0">

	<tr>
		<td>*Template Name:</td>
		<td><input type="text" name="template" maxlength="25" value="<?=$template?>" /></td>
	</tr>
	<tr>
		<td>*Content:</td>
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

# vim:ts=4 sw=4 noet
?>
