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

$title = "";
if (isset($_POST["title"])) $title = $_POST["title"];

$url = "";
if (isset($_POST["url"])) $url = $_POST["url"];

$content = "";
if (isset($_POST["content"])) $content = $_POST["content"];

$menutext = "";
if (isset($_POST["menutext"])) $menutext = $_POST["menutext"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["editcontent"])) $active = 0;

$showinmenu = 1;
if (!isset($_POST["showinmenu"]) && isset($_POST["showinmenu"])) $showinmenu = 0;

$page_id = -1;
if (isset($_POST["page_id"])) $page_id = $_POST["page_id"];
else if (isset($_GET["page_id"])) $page_id = $_GET["page_id"];

$section_id = -1;
if (isset($_POST["section_id"])) $section_id = $_POST["section_id"];

$template_id = -1;
if (isset($_POST["template_id"])) $template_id = $_POST["template_id"];

if (isset($_POST["cancel"])) {
	redirect("listcontent.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Modify Any Content') || check_ownership($config, $userid, "", $page_id);

if ($access) {
	$db = new DB($config);

	if (isset($_POST["editcontent"])) {

		$validinfo = true;

		if ($title == "") {
			$validinfo = false;
			$error .= "<li>No title given!</li>";
		}
		if ($url == "") {
			$validinfo = false;
			$error .= "<li>No url given!</li>";
		}
		if ($content == "") {
			$validinfo = false;
			$error .= "<li>No content entered!</li>";
		}
		if ($menutext == "") {
			$validinfo = false;
			$error .= "<li>No menu text given!</li>";
		}

		if ($validinfo) {
			$query = "UPDATE ".$config->db_prefix."pages SET page_title='".mysql_escape_string($title)."', page_url='".mysql_escape_string($url)."', page_content='".mysql_escape_string($content)."', section_id=$section_id, template_id=$template_id, owner=1, show_in_menu=$showinmenu, menu_text='".mysql_escape_string($menutext)."', active=$active, modified_date = now() WHERE page_id = $page_id";
			$result = $db->query($query);

			if (mysql_affected_rows() > -1) {
				$db->close();
				redirect("listcontent.php");
				return;
			}
			else {
				$error .= "<li>Error updating content</li>";
			}
		}

	}
	else if ($page_id != -1) {


		$query = "SELECT * from ".$config->db_prefix."pages WHERE page_id = " . $page_id;
		$result = $db->query($query);
		
		$row = mysql_fetch_array($result, MYSQL_ASSOC);

		$title = $row["page_title"];
		$url = $row["page_url"];
		$content = $row["page_content"];
		$section_id = $row["section_id"];
		$template_id = $row["template_id"];
		$active = $row["active"];
		$showinmenu = $row["show_in_menu"];
		$menutext = $row["menu_text"];

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
	$db->close();
}

include_once("header.php");

if (!$access) {
	print "<h3>No Access to Edit this Page</h3>";
}
else {
	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="editcontent.php">

<div class="adminform">

<h3>Edit Content</h3>

<table border="0">

	<tr>
		<td>*Title:</td>
		<td><input type="text" name="title" maxlength="255" value="<?=$title?>" /></td>
	</tr>
	<tr>
		<td>*URL:</td>
		<td><input type="text" name="url" maxlength="255" value="<?=$url?>" /></td>
	</tr>
	<tr>
		<td>*Content:</td>
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
		<td>*Menu Text:</td>
		<td><input type="text" name="menutext" maxlength="25" value="<?=$menutext?>" /></td>
	</tr>
	<tr>
		<td>Show in Menu:</td>
		<td><input type="checkbox" name="showinmenu" <?=($showinmenu == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>Active:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="page_id" value="<?=$page_id?>" /><input type="hidden" name="editcontent" value="true" /><input type="submit" value="Submit" /><input type="submit" name="cancel" value="Cancel"></td>
	</tr>

</table>

</div>

</form>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
