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

$error = "";

$dropdown = "";

$section = "";
if (isset($_POST["section"])) $section = $_POST["section"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["editsection"])) $active = 0;

$section_id = -1;
if (isset($_POST["section_id"])) $section_id = $_POST["section_id"];
else if (isset($_GET["section_id"])) $section_id = $_GET["section_id"];

if (isset($_POST["cancel"])) {
	redirect("listsections.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Modify Section');

if ($access) {
	$db = new DB($config);

	if (isset($_POST["editsection"])) {

		$validinfo = true;

		if ($section == "") {
			$validinfo = false;
			$error .= "<li>No Section name given!</li>";
		}

		if ($validinfo) {

			$query = "UPDATE ".$config->db_prefix."sections SET section_name='".mysql_escape_string($section)."', active=$active, modified_date = now() WHERE section_id = $section_id";
			$result = $db->query($query);

			if (mysql_affected_rows() > -1) {
				#This is so pages will not cache the menu changes
				$query = "UPDATE ".$config->db_prefix."templates SET modified_date = now()";
				$db->query($query);
				$db->close();
				redirect("listsections.php");
				return;
			}
			else {
				$error .= "<li>Error updating section</li>";
			}
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
}

include_once("header.php");

if (!$access) {
	print "<h3>No Access to Edit Sections</h3>";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="editsection.php">

<div class="adminform">

<h3>Edit Content</h3>

<table border="0">

	<tr>
		<td>*Name:</td>
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

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
