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

$error="";

$section= "";
if (isset($_POST["section"])) $section = $_POST["section"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addsection"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listsections.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Add Section');

if ($access) {
	$db = new DB($config);

	if (isset($_POST["addsection"])) {

		$validinfo = true;
		if ($section == "") {
			$validinfo = false;
			$error .= "<li>No Section title given!</li>";
		}

		if ($validinfo) {
			$query = "INSERT INTO ".$config->db_prefix."sections (section_name, active, create_date, modified_date) VALUES ('".mysql_real_escape_string($section)."', $active, now(), now())";
			$result = $db->query($query);
			if (mysql_affected_rows() > -1) {
				$db->close();
				redirect("listsections.php");
				return;
			}
			else {
				$error .= "<li>Error inserting section</li>";
			}
		}
	}

	$db->close();
}

include_once("header.php");

if (!$access) {
	print "<h3>No Access to Add Sections</h3>";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}

?>

<form method="post" action="addsection.php">

<div class="adminform">

<h3>Add Section</h3>

<table border="0">

	<tr>
		<td>*Name:</td>
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
}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
