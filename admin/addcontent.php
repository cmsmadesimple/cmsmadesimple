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

$title = "";
if (isset($_POST["title"])) $title = $_POST["title"];

$url = "";
if (isset($_POST["url"])) $url = $_POST["url"];

$content = "";
if (isset($_POST["content"])) $content = $_POST["content"];

$section_id = -1;
if (isset($_POST["section_id"])) $section_id = $_POST["section_id"];

$template_id = -1;
if (isset($_POST["template_id"])) $template_id = $_POST["template_id"];

$menutext = "";
if (isset($_POST["menutext"])) $menutext = $_POST["menutext"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addcontent"])) $active = 0;

$showinmenu = 1;
if (isset($_POST["showinmenu"]) && isset($_POST["showinmenu"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listcontent.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Add Content');

if ($access) {
	$db = new DB($config);

	if (isset($_POST["addcontent"])) {

		$query = "INSERT INTO ".$config->db_prefix."pages (page_title, page_url, page_content, section_id, template_id, owner, show_in_menu, menu_text, active, create_date, modified_date) VALUES ('".mysql_real_escape_string($title)."','".mysql_real_escape_string($url)."','".mysql_real_escape_string($content)."', $section_id, $template_id, 1, $showinmenu, '".mysql_real_escape_string($menutext)."', $active, now(), now())";
		$result = $db->query($query);
		if (mysql_affected_rows() > -1) {
			$db->close();
			redirect("listcontent.php");
			return;
		}
		else {
			echo "Error inserting page";
		}
		mysql_free_result($result);
	}

	$query = "SELECT section_id, section_name FROM ".$config->db_prefix."sections ORDER BY section_id";
	$result = $db->query($query);

	$dropdown = "<select name=\"section_id\">";

	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		$dropdown .= "<option value=\"".$row["section_id"]."\"";
		if ($row["section_id"] == $section_id) {
			$dropdown .= "selected";
		}
		$dropdown .= ">".$row["section_name"]."</option>";

	}

	$dropdown .= "</select>";

	mysql_free_result($result);

	$query = "SELECT template_id, template_name FROM ".$config->db_prefix."templates ORDER BY template_id";
	$result = $db->query($query);

	$dropdown2 = "<select name=\"template_id\">";

	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$dropdown2 .= "<option value=\"".$row["template_id"]."\"";
		if ($row["template_id"] == $template_id) {
			$dropdown2 .= "selected";
		}
		$dropdown2 .= ">".$row["template_name"]."</option>";
	}

	$dropdown2 .= "</select>";

	mysql_free_result($result);
	$db->close($link);
}

include_once("header.php");

if (!$access) {
	echo "<h3>No Access to Add Content</h3>\n";
}
else {

?>

<form method="post" action="addcontent.php">

<div class="adminform">

<h3>Add Content</h3>

<table border="0">

	<tr>
		<td>Title:</td>
		<td><input type="text" name="title" maxlength="25" value="<?=$title?>" /></td>
	</tr>
	<tr>
		<td>URL:</td>
		<td><input type="text" name="url" maxlength="255" value="<?=$url?>" /></td>
	</tr>
	<tr>
		<td>Content:</td>
		<td><textarea name="content" cols="90" rows="18"><?=$content?></textarea></td>
	</tr>
	<tr>
		<td>Section:</td>
		<td><?=$dropdown?></td>
	</tr>
	<tr>
		<td>Template:</td>
		<td><?=$dropdown2?></td>
	</tr>
	<tr>
		<td>Show in Menu:</td>
		<td><input type="checkbox" name="active" <?=($showinmenu == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>Menu Text:</td>
		<td><input type="text" name="menutext" maxlength="25" value="<?=$menutext?>" /></td>
	</tr>
	<tr>
		<td>Active:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addcontent" value="true" /><input type="submit" value="Submit" /><input type="submit" name="cancel" value="Cancel"></td>
	</tr>

</table>

</div>

</form>

<?php
}
include_once("footer.php");
?>
