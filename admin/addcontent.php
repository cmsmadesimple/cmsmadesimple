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

$content = "";
if (isset($_POST["content"])) $content = $_POST["content"];

$content_type = "content";
if (isset($_POST["content_type"])) $content_type = $_POST["content_type"];

$url = "";
if ($content_type == "content") {
	if (isset($_POST["url"])) $url = strtolower(preg_replace("/[^A-Za-z0-9.]/","",$_POST["url"]));
}
else {
	if (isset($_POST["url"])) $url = $_POST["url"];
}

$section_id = -1;
if (isset($_POST["section_id"])) $section_id = $_POST["section_id"];

$template_id = -1;
if (isset($_POST["template_id"])) $template_id = $_POST["template_id"];

$menutext = "";
if (isset($_POST["menutext"])) $menutext = $_POST["menutext"];

$preview = false;
if (isset($_POST["preview"])) $preview = true;

$submit = false;
if (isset($_POST["submitbutton"])) $submit = true;

$content_change = false;
if (isset($_POST["content_change"]) && $_POST["content_change"] == 1) $content_change = true;

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addcontent"])) $active = 0;

$showinmenu = 1;
if (!isset($_POST["showinmenu"]) && isset($_POST["addcontent"])) $showinmenu = 0;

if (isset($_POST["cancel"])) {
	redirect("listcontent.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Add Content');

$templatepostback = "";
if (get_preference($config, $userid, 'use_wysiwyg') == "1") {
	$tinymce_flag = "true";
	$templatepostback = " onchange=\"tinyMCE.triggerSave();document.addform.submit()\"";
}

if ($access) {

	if ($submit) {

		$validinfo = true;
		if ($title == "") {
			$validinfo = false;
			$error .= "<li>".$gettext->gettext("No title given!")."</li>";
		}
		if ($url == "") {
			$validinfo = false;
			$error .= "<li>".$gettext->gettext("No url given!")."</li>";
		}
		if ($content_type == "content" && $content == "") {
			$validinfo = false;
			$error .= "<li>".$gettext->gettext("No content entered!")."</li>";
		}
		if ($menutext == "") {
			$validinfo = false;
			$error .= "<li>".$gettext->gettext("No menu text given!")."</li>";
		}

		if ($validinfo) {
			$order = 1;
			$query = "SELECT max(item_order) + 1 as item_order FROM ".$config->db_prefix."pages WHERE section_id = $section_id";
			$result = $dbnew->Execute($query);
			$row = $result->FetchRow();
			if (isset($row["item_order"])) {
				$order = $row["item_order"];	
			}
			$query = "INSERT INTO ".$config->db_prefix."pages (page_title, page_url, page_content, page_type, section_id, template_id, owner, show_in_menu, menu_text, item_order, active, create_date, modified_date) VALUES (".$dbnew->qstr($title).",".$dbnew->qstr($url).",".$dbnew->qstr($content).",".$dbnew->qstr($content_type).", $section_id, $template_id, $userid, $showinmenu, ".$dbnew->qstr($menutext).", $order, $active, now(), now())";
			$result = $dbnew->Execute($query);
			if ($result) {
				$new_page_id = $dbnew->Insert_ID();
				if (isset($_POST["additional_editors"])) {
					foreach ($_POST["additional_editors"] as $addt_user_id) {
						$query = "INSERT INTO ".$config->db_prefix."additional_users (user_id, page_id) VALUES (".$addt_user_id.", ".$new_page_id.")";
						$dbnew->Execute($query);
					}
				}
				#This is so pages will not cache the menu changes
				$query = "UPDATE ".$config->db_prefix."templates SET modified_date = now()";
				$dbnew->Execute($query);
				audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $new_page_id, $title, 'Added Content');
				redirect("listcontent.php");
				return;
			}
			else {
				$error .= "<li>".$gettext->gettext("Error inserting page")."</li>";
			}
		}
	}

	$query = "SELECT section_id, section_name FROM ".$config->db_prefix."sections ORDER BY section_id";
	$result = $dbnew->Execute($query);

	$dropdown = "<select name=\"section_id\">";

	while($row = $result->FetchRow()) {

		$dropdown .= "<option value=\"".$row["section_id"]."\"";
		if ($row["section_id"] == $section_id) {
			$dropdown .= "selected";
		}
		$dropdown .= ">".$row["section_name"]."</option>";

	}

	$dropdown .= "</select>";

	$query = "SELECT template_id, template_name FROM ".$config->db_prefix."templates ORDER BY template_id";
	$result = $dbnew->Execute($query);

	$dropdown2 = "<select name=\"template_id\"$templatepostback>";

	while($row = $result->FetchRow()) {
		$dropdown2 .= "<option value=\"".$row["template_id"]."\"";
		if ($row["template_id"] == $template_id) {
			$dropdown2 .= "selected";
		}
		if ($template_id == -1) {
			$template_id = $row["template_id"];
		}
		$dropdown2 .= ">".$row["template_name"]."</option>";
	}

	$dropdown2 .= "</select>";

	$addt_users = "";

	$query = "SELECT user_id, username FROM ".$config->db_prefix."users WHERE user_id <> " . $userid;
	$result = $dbnew->Execute($query);

	while($row = $result->FetchRow()) {
		$addt_users .= "<option value=\"".$row["user_id"]."\">".$row["username"]."</option>";
	}

	$ctdropdown = "<select name=\"content_type\" onchange=\"document.addform.content_change.value=1;document.addform.submit()\">";
	foreach (get_page_types($config) as $key=>$value) {
		$ctdropdown .= "<option value=\"$key\"";
		if ($key == $content_type) {
			$ctdropdown .= " selected=\"true\"";
		}
		$ctdropdown .= ">".$value."</option>";
	}
	$ctdropdown .= "</select>";

}

include_once("header.php");

if (!$access) {
	echo "<h3>".$gettext->gettext("No Access to Add Content")."</h3>\n";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}

	if ($preview) {

		$data["title"] = $title;
		$data["content"] = $content;
		$data["template_id"] = $template_id;

		$query = "SELECT template_content, stylesheet FROM ".$config->db_prefix."templates WHERE template_id = ".$template_id;
		$result = $dbnew->Execute($query);
		if ($result) {
			$row = $result->FetchRow();
			$data["stylesheet"] = $row["stylesheet"];
			$data["template"] = $row["template_content"];
		} else {
			$data["stylesheet"] = "";
			$data["template"] = "{content}";
		}

		$tmpfname = tempnam($config->root_path . "/smarty/cms/cache/", "cmspreview");
		$handle = fopen($tmpfname, "w");
		fwrite($handle, serialize($data));
		fclose($handle);

?>
<h3><?=$gettext->gettext("Preview")?></h3>

<iframe name="previewframe" width="600" height="400" src="<?=$config->root_url?>/preview.php?tmpfile=<?=urlencode(str_replace("cmspreview","",basename($tmpfname)))?>">

</iframe>
<?php

	}

?>

<form method="post" action="addcontent.php" name="addform" id="addform">

<div class="adminform">

<h3><?=$gettext->gettext("Add Content")?></h3>

<table border="0">

	<tr>
		<td><?=$gettext->gettext("Content Type")?>:</td>
		<td><?=$ctdropdown?></td>
	</tr>
	<tr>
		<td>*<?=$gettext->gettext("Title")?>:</td>
		<td><input type="text" name="title" maxlength="25" value="<?=$title?>" /></td>
	</tr>
	<tr>
		<td>*<?=$gettext->gettext("URL")?>:</td>
		<td><input type="text" name="url" maxlength="255" value="<?=$url?>" /></td>
	</tr>
<?php if ($content_type == "content") { ?>
	<tr>
		<td>*<?=$gettext->gettext("Content")?>:</td>
		<td><textarea name="content" cols="90" rows="18"><?=$content?></textarea></td>
	</tr>
<?php } ?>
	<tr>
		<td><?=$gettext->gettext("Section")?>:</td>
		<td><?=$dropdown?></td>
	</tr>
<?php if ($content_type == "content") { ?>
	<tr>
		<td><?=$gettext->gettext("Template")?>:</td>
		<td><?=$dropdown2?></td>
	</tr>
<?php } ?>
	<tr>
		<td><?=$gettext->gettext("Additional Editors")?>:</td>
		<td><select name="additional_editors[]" multiple="true" size="5"><?=$addt_users?></select></td>
	</tr>
	<tr>
		<td>*<?=$gettext->gettext("Menu Text")?>:</td>
		<td><input type="text" name="menutext" maxlength="25" value="<?=$menutext?>" /></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Show in Menu")?>:</td>
		<td><input type="checkbox" name="showinmenu" <?=($showinmenu == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="content_change" value="0" /><input type="hidden" name="addcontent" value="true" /><?php if ($content_type == "content") { ?><input type="submit" name="preview" value="<?=$gettext->gettext("Preview")?>" /><?php } ?><input type="submit" name="submitbutton" value="<?=$gettext->gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>"></td>
	</tr>

</table>

</div>

</form>

<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
