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
#if (isset($_POST["url"])) $url = $_POST["url"];
if (isset($_POST["url"])) $url = strtolower(preg_replace("/[^A-Za-z0-9.]/","",$_POST["url"]));

$content = "";
if (isset($_POST["content"])) $content = $_POST["content"];

$section_id = -1;
if (isset($_POST["section_id"])) $section_id = $_POST["section_id"];

$template_id = -1;
if (isset($_POST["template_id"])) $template_id = $_POST["template_id"];

$menutext = "";
if (isset($_POST["menutext"])) $menutext = $_POST["menutext"];

$preview = false;
if (isset($_POST["preview"])) $preview = true;

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addcontent"])) $active = 0;

$showinmenu = 1;
if (!isset($_POST["showinmenu"]) && isset($_POST["addcontent"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listcontent.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Add Content');

if ($access) {
	$db = new DB($config);

	if (isset($_POST["addcontent"]) && !$preview) {

		$validinfo = true;
		if ($title == "") {
			$validinfo = false;
			$error .= "<li>".GetText::gettext("No title given!")."</li>";
		}
		if ($url == "") {
			$validinfo = false;
			$error .= "<li>".GetText::gettext("No url given!")."</li>";
		}
		if ($content == "") {
			$validinfo = false;
			$error .= "<li>".GetText::gettext("No content entered!")."</li>";
		}
		if ($menutext == "") {
			$validinfo = false;
			$error .= "<li>".GetText::gettext("No menu text given!")."</li>";
		}

		if ($validinfo) {
			$order = 1;
			$query = "SELECT max(item_order) + 1 as item_order FROM ".$config->db_prefix."pages WHERE section_id = $section_id";
			$result = $db->query($query);
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			if (isset($row["item_order"])) {
				$order = $row["item_order"];	
			}
			mysql_free_result($result);
			$query = "INSERT INTO ".$config->db_prefix."pages (page_title, page_url, page_content, section_id, template_id, owner, show_in_menu, menu_text, item_order, active, create_date, modified_date) VALUES ('".mysql_escape_string($title)."','".mysql_escape_string($url)."','".mysql_escape_string($content)."', $section_id, $template_id, $userid, $showinmenu, '".mysql_escape_string($menutext)."', $order, $active, now(), now())";
			$result = $db->query($query);
			if (mysql_affected_rows() > -1) {
				$new_page_id = mysql_insert_id();
				if (isset($_POST["additional_editors"])) {
					foreach ($_POST["additional_editors"] as $addt_user_id) {
						$query = "INSERT INTO ".$config->db_prefix."additional_users (user_id, page_id) VALUES (".$addt_user_id.", ".$new_page_id.")";
						$db->query($query);
					}
				}
				#This is so pages will not cache the menu changes
				$query = "UPDATE ".$config->db_prefix."templates SET modified_date = now()";
				$db->query($query);
				$db->close();
				redirect("listcontent.php");
				return;
			}
			else {
				$error .= "<li>".GetText::gettext("Error inserting page")."</li>";
			}
		}
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

	$addt_users = "";

	$query = "SELECT user_id, username FROM ".$config->db_prefix."users WHERE user_id <> " . $userid;
	$result = $db->query($query);

	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$addt_users .= "<option value=\"".$row["user_id"]."\">".$row["username"]."</option>";
	}

	mysql_free_result($result);
	$db->close();
}

include_once("header.php");

if (!$access) {
	echo "<h3>".GetText::gettext("No Access to Add Content")."</h3>\n";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}

	if ($preview) {

		$data["content"] = $content;
		$data["template_id"] = $template_id;

		$db = new DB($config);
		$query = "SELECT template_content, stylesheet FROM ".$config->db_prefix."templates WHERE template_id = ".$template_id;
		#echo $query;
		$result = $db->query($query);
		if (mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$data["stylesheet"] = $row["stylesheet"];
			$data["template"] = $row["template_content"];
		} else {
			$data["stylesheet"] = "";
			$data["template"] = "{content}";
		}
		mysql_free_result($result);
		$db->close();

		$tmpfname = tempnam($config->root_path . "/smarty/cms/cache/", "cmspreview");
		$handle = fopen($tmpfname, "w");
		fwrite($handle, serialize($data));
		fclose($handle);

?>
<h3><?=GetText::gettext("Preview")?></h3>

<iframe name="previewframe" width="600" height="400" src="<?=$config->root_url?>/preview.php?tmpfile=<?=urlencode(str_replace("cmspreview","",basename($tmpfname)))?>">

</iframe>
<?php

	}

?>

<form method="post" action="addcontent.php">

<div class="adminform">

<h3><?=GetText::gettext("Add Content")?></h3>

<table border="0">

	<tr>
		<td>*<?=GetText::gettext("Title")?>:</td>
		<td><input type="text" name="title" maxlength="25" value="<?=$title?>" /></td>
	</tr>
	<tr>
		<td>*<?=GetText::gettext("URL")?>:</td>
		<td><input type="text" name="url" maxlength="255" value="<?=$url?>" /></td>
	</tr>
	<tr>
		<td>*<?=GetText::gettext("Content")?>:</td>
		<td><textarea name="content" cols="90" rows="18"><?=$content?></textarea></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Section")?>:</td>
		<td><?=$dropdown?></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Template")?>:</td>
		<td><?=$dropdown2?></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Additional Editors")?>:</td>
		<td><select name="additional_editors[]" multiple="true" size="5"><?=$addt_users?></select></td>
	</tr>
	<tr>
		<td>*<?=GetText::gettext("Menu Text")?>:</td>
		<td><input type="text" name="menutext" maxlength="25" value="<?=$menutext?>" /></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Show in Menu")?>:</td>
		<td><input type="checkbox" name="showinmenu" <?=($showinmenu == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addcontent" value="true" /><input type="submit" name="preview" value="<?=GetText::gettext("Preview")?>" /><input type="submit" name="submit" value="<?=GetText::gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=GetText::gettext("Cancel")?>"></td>
	</tr>

</table>

</div>

</form>

<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
