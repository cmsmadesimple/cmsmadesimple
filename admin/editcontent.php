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

$orig_content_type = "content";
if (isset($_POST["orig_content_type"])) $orig_content_type = $_POST["orig_content_type"];

$url = "";
if ($content_type == "content") {
	if (isset($_POST["url"])) $url = strtolower(preg_replace("/[^A-Za-z0-9.]/","",$_POST["url"]));
}
else {
	if (isset($_POST["url"])) $url = $_POST["url"];
}

$menutext = "";
if (isset($_POST["menutext"])) $menutext = $_POST["menutext"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["editcontent"])) $active = 0;

$showinmenu = 1;
if (!isset($_POST["showinmenu"]) && isset($_POST["editcontent"])) $showinmenu = 0;

$order = 1;
if (isset($_POST["order"])) $order = $_POST["order"];

$page_id = -1;
if (isset($_POST["page_id"])) $page_id = $_POST["page_id"];
else if (isset($_GET["page_id"])) $page_id = $_GET["page_id"];

$section_id = -1;
if (isset($_POST["section_id"])) $section_id = $_POST["section_id"];

$owner_id = -1;
if (isset($_POST["owner_id"])) $owner_id = $_POST["owner_id"];

$orig_section_id = -1;
if (isset($_POST["orig_section_id"])) $orig_section_id = $_POST["orig_section_id"];

$preview = false;
if (isset($_POST["preview"])) $preview = true;

$content_change = false;
if (isset($_POST["content_change"]) && $_POST["content_change"] == 1) $content_change = true;

$submit = false;
if (isset($_POST["submitbutton"])) $submit = true;

$template_id = -1;
if (isset($_POST["template_id"])) $template_id = $_POST["template_id"];

if (isset($_POST["cancel"])) {
	redirect("listcontent.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Modify Any Content') || check_ownership($config, $userid, "", $page_id);
$adminaccess = $access;
if (!$access) {
	$access = check_authorship($config, $userid, $page_id);
}

if (get_preference($config, $userid, 'use_wysiwyg') == "1") {
	$tinymce_flag = "true";
	$templatepostback = " onchange=\"document.editform.content_change.value=1;tinyMCE.triggerSave();document.editform.submit()\"";
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
			if ($orig_section_id != $section_id) {
				$query = "SELECT max(item_order) + 1 AS item_order FROM ".$config->db_prefix."pages WHERE section_id = $section_id";
				$result = $dbnew->Execute($query);
				$row = $result->FetchRow();
				if (isset($row["item_order"])) {
					$order = $row["item_order"];	
				}
			}
			$query = "UPDATE ".$config->db_prefix."pages SET page_title=".$dbnew->qstr($title).", page_url=".$dbnew->qstr($url).", page_content=".$dbnew->qstr($content).", section_id=$section_id, template_id=$template_id, show_in_menu=$showinmenu, menu_text=".$dbnew->qstr($menutext).", active=$active, modified_date = now(), item_order=$order, page_type = ".$dbnew->qstr($content_type).", owner=$owner_id WHERE page_id = $page_id";
			$result = $dbnew->Execute($query);

			if ($result) {
				if ($adminaccess) {
					$query = "DELETE FROM ".$config->db_prefix."additional_users WHERE page_id = $page_id";
					$dbnew->Execute($query);
					if (isset($_POST["additional_editors"])) {
						foreach ($_POST["additional_editors"] as $addt_user_id) {
							$query = "INSERT INTO ".$config->db_prefix."additional_users (user_id, page_id) VALUES (".$addt_user_id.", ".$page_id.")";
							$dbnew->Execute($query);
						}
					}
				}
				#This is so pages will not cache the menu changes
				$query = "UPDATE ".$config->db_prefix."templates SET modified_date = now()";
				$dbnew->Execute($query);
				audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $page_id, $title, 'Edited Content');
				redirect("listcontent.php");
				return;
			}
			else {
				$error .= "<li>".$gettext->gettext("Error updating content!")."</li>";
			}
		}

	}
	else if ($page_id != -1 && !$preview && !$content_change) {

		$query = "SELECT * from ".$config->db_prefix."pages WHERE page_id = " . $page_id;
		$result = $dbnew->Execute($query);
		
		$row = $result->FetchRow();

		$title = $row["page_title"];
		$url = $row["page_url"];
		$content_type = $row["page_type"];
		$orig_content_type = $row["page_type"];
		$url = $row["page_url"];
		$content = $row["page_content"];
		$owner_id = $row["owner"];
		$section_id = $row["section_id"];
		$orig_section_id = $row["section_id"];
		$template_id = $row["template_id"];
		$active = $row["active"];
		$order = $row["item_order"];
		$showinmenu = $row["show_in_menu"];
		$menutext = $row["menu_text"];
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
		$dropdown2 .= ">".$row["template_name"]."</option>";
	}

	$dropdown2 .= "</select>";

	$owners = "<select name=\"owner_id\">";

    $query = "SELECT user_id, username FROM ".$config->db_prefix."users";
    $result = $dbnew->Execute($query);

    while($row = $result->FetchRow()) {
        $owners .= "<option value=\"".$row["user_id"]."\"";
		if ($row["user_id"] == $owner_id) {
			$owners .= " selected";
		}
		$owners .= ">".$row["username"]."</option>";
	}

	$owners .= "</select>";

    $addt_users = "";

    $query = "SELECT user_id, username FROM ".$config->db_prefix."users WHERE user_id <> " . $userid;
    $result = $dbnew->Execute($query);

    while($row = $result->FetchRow()) {
        $addt_users .= "<option value=\"".$row["user_id"]."\"";
		$query = "SELECT * from ".$config->db_prefix."additional_users WHERE user_id = ".$row["user_id"]." AND page_id = $page_id";
		$newresult = $dbnew->Execute($query);
		if ($newresult) {
			$addt_users .= " selected=\"true\"";
		}
		$addt_users .= ">".$row["username"]."</option>";
    }

	$ctdropdown = "<select name=\"content_type\" onchange=\"document.editform.content_change.value=1;document.editform.submit()\">";
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
	print "<h3>".$gettext->gettext("No Access to Edit this Page")."</h3>";
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

<form method="post" action="editcontent.php" name="editform" id="editform">

<div class="adminform">

<h3><?=$gettext->gettext("Edit Content")?></h3>

<table border="0">

	<tr>
		<td><?=$gettext->gettext("Content Type")?>:</td>
		<td><?=$ctdropdown?><input type="hidden" name="orig_content_type" value="<?=$orig_content_type?>" /></td>
	</tr>
	<tr>
		<td>*<?=$gettext->gettext("Title")?>:</td>
		<td><input type="text" name="title" maxlength="255" value="<?=$title?>" /></td>
	</tr>
	<tr>
		<td>*<?=$gettext->gettext("URL")?>:</td>
		<td><input type="text" name="url" maxlength="255" value="<?=$url?>" /></td>
	</tr>
<?php if ($content_type == "content") { ?>
	<tr>
		<td>*<?=$gettext->gettext("Content")?>:</td>
		<td><textarea name="content" cols="90" rows="18"><?=htmlentities($content)?></textarea></td>
	</tr>
<?php } ?>
	<tr>
		<td><?=$gettext->gettext("Section")?>:</td>
		<td><?=$dropdown?><input type="hidden" name="orig_section_id" value="<?=$orig_section_id?>" /></td>
	</tr>
<?php if ($content_type == "content") { ?>
	<tr>
		<td><?=$gettext->gettext("Template")?>:</td>
		<td><?=$dropdown2?></td>
	</tr>
<?php } ?>
<?php if ($adminaccess) { ?>
	<tr>
		<td><?=$gettext->gettext("Owner")?>:</td>
		<td><?=$owners?></td>
	</tr>
    <tr> 
		<td><?=$gettext->gettext("Additional Editors")?>:</td>
		<td><select name="additional_editors[]" multiple="true" size="5"><?=$addt_users?></select></td>
	</tr>
<?php } ?>
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
		<td><input type="hidden" name="content_change" value="0" /><input type="hidden" name="order" value="<?=$order?>" /><input type="hidden" name="page_id" value="<?=$page_id?>" /><input type="hidden" name="editcontent" value="true" /><?php if ($content_type == "content") { ?><input type="submit" name="preview" value="<?=$gettext->gettext("Preview")?>" /><?php } ?><input type="submit" name="submitbutton" value="<?=$gettext->gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>"></td>
	</tr>

</table>

</div>

</form>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
