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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

$error = "";

$title = "";
if (isset($_POST["title"])) $title = $_POST["title"];

$content = "";
if (isset($_POST["content"])) $content = $_POST["content"];

$head_tags = "";
if (isset($_POST["head_tags"])) $head_tags = $_POST["head_tags"];

$alias = "";
if ($config["auto_alias_content"])
{
	$alias = $title;
	$alias = strtolower(trim($alias));
	$alias = preg_replace("/\W+/", "-", $alias);
}
else
{
	if (isset($_POST["alias"])) $alias = $_POST["alias"];
}

$content_type = "content";
if (isset($_POST["content_type"])) $content_type = $_POST["content_type"];

$orig_content_type = "content";
if (isset($_POST["orig_content_type"])) $orig_content_type = $_POST["orig_content_type"];

$url = "";
if (isset($_POST["url"])) $url = $_POST["url"];

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

$parent_id = -1;
if (isset($_POST["parent_id"])) $parent_id = $_POST["parent_id"];

$owner_id = -1;
if (isset($_POST["owner_id"])) $owner_id = $_POST["owner_id"];

$orig_parent_id = -1;
if (isset($_POST["orig_parent_id"])) $orig_parent_id = $_POST["orig_parent_id"];

$preview = false;
if (isset($_POST["preview"])) $preview = true;

$content_change = false;
if (isset($_POST["content_change"]) && $_POST["content_change"] == 1) $content_change = true;

$submit = false;
if (isset($_POST["submitbutton"])) $submit = true;

$template_id = -1;
if (isset($_POST["template_id"])) $template_id = $_POST["template_id"];

$orig_item_order = -1;
if (isset($_POST["orig_item_order"])) $item_order = $_POST["orig_item_order"];

if (isset($_POST["cancel"])) {
	redirect("listcontent.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Any Content') || check_ownership($userid, "", $page_id);
$adminaccess = $access;
if (!$access) {
	$access = check_authorship($userid, $page_id);
}


if ($access) {

	if ($submit) {

		$validinfo = true;

		if ($title == "" && $content_type != "separator") {
			$validinfo = false;
			$error .= "<li>".$gettext->gettext("No title given!")."</li>";
		}
		if ($url == "" && $content_type == "link") {
			$validinfo = false;
			$error .= "<li>".$gettext->gettext("No url given!")."</li>";
		}
		if ($content_type == "content" && $content == "") {
			$validinfo = false;
			$error .= "<li>".$gettext->gettext("No content entered!")."</li>";
		}
		if ($menutext == "" && $content_type != "separator") {
			$validinfo = false;
			$error .= "<li>".$gettext->gettext("No menu text given!")."</li>";
		}
		if ($alias != "")
		{
			if (preg_match('/^\d+$/', $alias))
			{
				$validinfo = false;
				$error .= "<li>".$gettext->gettext("Alias cannot be an integer!")."</li>";
			}
			else if (!preg_match('/^[\d\w\-]+$/', $alias))
			{
				$validinfo = false;
				$error .= "<li>".$gettext->gettext("Alias must be all letters and numbers!")."</li>";
			}
			else
			{
				$query = "SELECT page_id FROM ".cms_db_prefix()."pages WHERE page_id <> $page_id AND page_alias = ".$db->qstr($alias);
				$result = $db->Execute($query);
				if ($result && $result->RowCount() > 0)
				{
					$validinfo = false;
					$error .= "<li>".$gettext->gettext("Alias has already been used on another page!")."</li>";
				}
			}
		}

		if ($validinfo) {
			if ($orig_parent_id != $parent_id) {
				$query = "SELECT max(item_order) + 1 AS item_order FROM ".cms_db_prefix()."pages WHERE parent_id = $parent_id";
				$result = $db->Execute($query);
				$row = $result->FetchRow();
				if (isset($row["item_order"])) {
					$order = $row["item_order"];	
				} else {
					$order = 1;
				}

			}
			$query1 = "UPDATE ".cms_db_prefix()."pages SET page_title=".$db->qstr($title).", page_url=".$db->qstr($url).", page_content=".$db->qstr($content).", parent_id=$parent_id, template_id=$template_id, show_in_menu=$showinmenu, menu_text=".$db->qstr($menutext).", active=$active, modified_date = ".$db->DBTimeStamp(time()).", item_order=$order, page_type = ".$db->qstr($content_type).", owner=$owner_id, page_alias=".$db->qstr($alias).", head_tags=".$db->qstr($head_tags)." WHERE page_id = $page_id";
			$result1 = $db->Execute($query1);

			if ($orig_parent_id != $parent_id) {
				$query2 = "UPDATE ".cms_db_prefix()."pages SET item_order = item_order - 1 WHERE parent_id = " . $orig_parent_id . " AND item_order > " . $item_order;
				$result2 = $db->Execute($query2);
				if (!$result2) {
					$result1 = null;
				}
			}

			if ($result1) {
				if ($adminaccess) {
					$query = "DELETE FROM ".cms_db_prefix()."additional_users WHERE page_id = $page_id";
					$db->Execute($query);
					if (isset($_POST["additional_editors"])) {
						foreach ($_POST["additional_editors"] as $addt_user_id) {
							$new_addt_id = $db->GenID(cms_db_prefix()."additional_users_seq");
							$query = "INSERT INTO ".cms_db_prefix()."additional_users (additional_users_id, user_id, page_id) VALUES ($new_addt_id, ".$addt_user_id.", ".$page_id.")";
							$db->Execute($query);
						}
					}
				}
				audit((isset($_SESSION["cms_admin_user_id"])?$_SESSION["cms_admin_user_id"]:""), (isset($_SESSION["cms_admin_username"])?$_SESSION["cms_admin_username"]:""), $page_id, $title, 'Edited Content');
				redirect("listcontent.php");
				return;
			}
			else {
				$error .= "<li>".$gettext->gettext("Error updating content!")."</li>";
			}
		}

	}
	else if ($page_id != -1 && !$preview && !$content_change) {

		$query = "SELECT * from ".cms_db_prefix()."pages WHERE page_id = " . $page_id;
		$result = $db->Execute($query);
		
		$row = $result->FetchRow();

		$page_id = $row["page_id"];
		$title = $row["page_title"];
		$alias = $row["page_alias"];
		$url = $row["page_url"];
		$content_type = $row["page_type"];
		$head_tags = $row["head_tags"];
		$orig_content_type = $row["page_type"];
		$url = $row["page_url"];
		$content = $row["page_content"];
		$owner_id = $row["owner"];
		$parent_id = $row["parent_id"];
		$orig_parent_id = $row["parent_id"];
		$template_id = $row["template_id"];
		$active = $row["active"];
		$order = $row["item_order"];
		$showinmenu = $row["show_in_menu"];
		$menutext = $row["menu_text"];
		$orig_item_order = $row["item_order"];
	}

	$templatepostback = "";
	if (get_preference($userid, 'use_wysiwyg') == "1" && $content_type == "content") {
		$htmlarea_flag = true;
		$templatepostback = " onchange=\"document.editform.content_change.value=1;document.editform.content.value=editor.getHTML();document.editform.submit()\"";
	}

    $content_array = array();
    $content_array = db_get_menu_items("content_hierarchy");
		foreach ($content_array as $one ) {
			if (strlen($one->page_title) > 20) {
				$ddsize = "style=\"width: 250px;\"";
				break;
			}else{
				$ddsize = "class=\"standard\"";
			}
		}
    $dropdown = "<select name=\"parent_id\" ".(isset($ddsize)?$ddsize:"").">";
    $dropdown .="<option value=\"0\"";
    if ($parent_id == "0") {
        $dropdown .= " selected";
    }
    $dropdown .= ">None</option>";

	$showparent = true;
    foreach ($content_array as $one) {
		if ($one->page_id == $page_id) {
			$showparent = false;
			$rememberlevel = $one->level;
		}
		if ($showparent == false && $one->level == $rememberlevel && $one->page_id != $page_id) {
			$showparent = true;
		}
		if ($showparent && $one->page_type != "separator") {
			$dropdown .= "<option value=\"".$one->page_id."\"";
			if ($one->page_id == $parent_id) {
				$dropdown .= "selected";
			}
			$dropdown .= ">".$one->hier." - ".$one->page_title."</option>";
		} ## if
    }

    $dropdown .= "</select>";

	$query = "SELECT template_id, template_name FROM ".cms_db_prefix()."templates ORDER BY template_id";
	$result = $db->Execute($query);

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

    $query = "SELECT user_id, username FROM ".cms_db_prefix()."users";
    $result = $db->Execute($query);

    while($row = $result->FetchRow()) {
        $owners .= "<option value=\"".$row["user_id"]."\"";
		if ($row["user_id"] == $owner_id) {
			$owners .= " selected";
		}
		$owners .= ">".$row["username"]."</option>";
	}

	$owners .= "</select>";

    $addt_users = "";

    $query = "SELECT user_id, username FROM ".cms_db_prefix()."users WHERE user_id <> " . $userid;
    $result = $db->Execute($query);

    while($row = $result->FetchRow()) {
        $addt_users .= "<option value=\"".$row["user_id"]."\"";
		$query = "SELECT * from ".cms_db_prefix()."additional_users WHERE user_id = ".$row["user_id"]." AND page_id = $page_id";
		$newresult = $db->Execute($query);
		if ($newresult && $newresult->RowCount() > 0) {
			$addt_users .= " selected=\"true\"";
		}
		$addt_users .= ">".$row["username"]."</option>";
    }

	$ctdropdown = "<select name=\"content_type\" onchange=\"document.editform.content_change.value=1;document.editform.submit()\" class=\"standard\">";
	foreach (get_page_types() as $key=>$value) {
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

		$query = "SELECT template_content, stylesheet FROM ".cms_db_prefix()."templates WHERE template_id = ".$template_id;
		$result = $db->Execute($query);
		if ($result && $result->RowCount() > 0) {
			$row = $result->FetchRow();
			$data["stylesheet"] = $row["stylesheet"];
			$data["template"] = $row["template_content"];
		} else {
			$data["stylesheet"] = "";
			$data["template"] = "{content}";
		}

		# add linked CSS if any
		$cssquery = "SELECT css_text FROM ".cms_db_prefix()."css, ".cms_db_prefix()."css_assoc
			WHERE	css_id		= assoc_css_id
			AND		assoc_type	= 'template'
			AND		assoc_to_id = '$template_id'";
		$cssresult = $db->Execute($cssquery);
		if ($cssresult && $cssresult->RowCount() > 0)
		{
			while ($cssline = $cssresult->FetchRow())
			{
				$data["stylesheet"] .= "\n".$cssline[css_text]."\n";
			}
		}

		$tmpfname = tempnam($config["previews_path"], "cmspreview");
		$handle = fopen($tmpfname, "w");
		fwrite($handle, serialize($data));
		fclose($handle);

?>
<h3><?=$gettext->gettext("Preview")?></h3>

<iframe name="previewframe" width="90%" height="400" frameborder="0" src="<?=$config["root_url"] ?>/preview.php?tmpfile=<?=urlencode(basename($tmpfname))?>" style="margin: 10px; border: 1px solid #8C8A8C;">

</iframe>
<?php

	}

?>

<form method="post" action="editcontent.php" name="editform" id="editform">

<?php if ($content_type == "content") { ?>
<h3><?=$gettext->gettext("Edit Content")?></h3>
<div class="adminform">
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" summary="" style="margin-top: 0; border: solid 1px #8C8A8C; padding: 5px 5px 10px 5px;" id="padform">
				<tr valign="top">
					<td valign="top">
						<?=$gettext->gettext("Content Type")?>:<?=$ctdropdown?>
						<?=$gettext->gettext("Title")?>:&nbsp;<input type="text" name="title" maxlength="80" value="<?=$title?>">
						<span style="white-space: nowrap"><?=$gettext->gettext("Menu Text")?>:&nbsp;<input type="text" name="menutext" maxlength="25" value="<?=$menutext?>"></span>
						<span style="white-space: nowrap"><?=$gettext->gettext("Page Alias")?>:&nbsp;<input type="text" name="alias" maxlength="65" value="<?=$alias?>"></span>
						<span style="white-space: nowrap"><?=$gettext->gettext("Template")?>:&nbsp;<?=$dropdown2?></span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top: 10px;"><strong><?=$gettext->gettext("Content") ?></strong><br><textarea id="content" name="content" style="width:100%" cols="80" rows="24"><?=$content?></textarea></td>
	</tr>
</table>

<div class="collapseTitle"><a href="#advanced" onClick="expandcontent('advanced')" style="cursor:hand; cursor:pointer"><?=$gettext->gettext("Advanced") ?></a></div>
<div id="advanced" class="expand">
	<a id="advanced">&nbsp;</a>
	<div style="line-height: .8em; padding-top: 1em; font-weight: bold;"><?=$gettext->gettext("Head Tags") ?>
		<textarea rows="4" name="head_tags"><?=$head_tags ?></textarea>
	</div>

	<table border="0" cellpadding="0" cellspacing="0" summary="">
		<tr valign="top">
			<td valign="top" style="padding-right: 10px;">
				<div style="line-height: .8em; padding-top: 1em; margin-bottom: 1em; font-weight: bold;"><?=$gettext->gettext("Status")?></div>
				<div style="border: solid 1px #8C8A8C; height: 8em; padding: 7px 5px 5px 5px;">
					<table width="100%" border="0"cellpadding="0" cellspacing="0" summary="" style=" vertical-align: middle;">
						<tr valign="top">
							<td valign="top"><?=$gettext->gettext("Show in Menu")?>:</td>
							<td><input type="checkbox" name="showinmenu" <?=($showinmenu == 1?"checked":"")?>></td>
						</tr>
						<tr valign="top" style="padding-top: 5px;">
							<td valign="top"><?=$gettext->gettext("Active")?>:</td>
							<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?>> </td>
						</tr>
							<td colspan="2"><?=$gettext->gettext("Parent")?>:&nbsp;<?=$dropdown?></td>
						</tr>
					</table>
				</div>
			</td>
			<?php //if ($adminaccess) { ?>
			<td valign="top">
					<div style="line-height: .8em; padding-top: 1em; margin-bottom: 1em; font-weight: bold;"><?=$gettext->gettext("Permission")?></div>
					<div style="border: solid 1px #8C8A8C; height: 8em; padding: 7px 5px 5px 5px;">
					<!--<?=$gettext->gettext("Owner")?>:&nbsp;<?=$owners?><br>-->
					<div style="text-align: center; padding-top: 5px;"><?=$gettext->gettext("Additional Editors")?>:<br><select name="additional_editors[]" multiple="true" size="3"><?=$addt_users?></select></div>
					</div>
			</td>
			<?php // } ?>
		</tr>
	</table>
</div>

<br>
<input type="hidden" name="orig_parent_id" value="<?=$orig_parent_id?>">
<input type="hidden" name="content_change" value="0">
<input type="hidden" name="order" value="<?=$order?>">
<input type="hidden" name="page_id" value="<?=$page_id?>">
<input type="hidden" name="editcontent" value="true">
<input type="hidden" name="orig_item_order" value="<?=$orig_item_order?>">
<input type="submit" name="preview" value="<?=$gettext->gettext("Preview")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
<input type="submit" name="submitbutton" value="<?=$gettext->gettext("Submit")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
<input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">

<?php }elseif ($content_type == "separator") { ?>
<h3><?=$gettext->gettext("Edit Separator")?></h3>
<div class="adminformSmall">
<input type="hidden" name="template_id" value="1">
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
		<td><?=$gettext->gettext("Content Type")?>:</td>
		<td><?=$ctdropdown?></td>
	<tr>
		<td><?=$gettext->gettext("Parent")?>:</td>
		<td><?=$dropdown?></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Additional Editors")?>:</td>
		<td><select name="additional_editors[]" multiple="true" size="5"><?=$addt_users?></select></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Show in Menu")?>:</td>
		<td><input type="checkbox" name="showinmenu" <?=($showinmenu == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
<input type="hidden" name="orig_parent_id" value="<?=$orig_parent_id?>">
			<input type="hidden" name="content_change" value="0">
			<input type="hidden" name="order" value="<?=$order?>">
			<input type="hidden" name="page_id" value="<?=$page_id?>">
			<input type="hidden" name="editcontent" value="true">
			<input type="hidden" name="orig_item_order" value="<?=$orig_item_order?>">
			<input type="submit" name="submitbutton" value="<?=$gettext->gettext("Submit")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
			<input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>
</table>
</div>



<?php }elseif ($content_type == "link") { ?>
<h3><?=$gettext->gettext("Edit Link")?></h3>
<div class="adminformSmall">
<input type="hidden" name="template_id" value="1">
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
		<td><?=$gettext->gettext("Content Type")?>:</td>
		<td><?=$ctdropdown?></td>
	<tr>
		<td>*<?=$gettext->gettext("Title")?>:</td>
		<td><input type="text" name="title" maxlength="80" value="<?=$title?>" class="standard"></td>
	</tr>
	<tr>
		<td>*<?=$gettext->gettext("Menu Text")?>:</td>
		<td><input type="text" name="menutext" maxlength="25" value="<?=$menutext?>" class="standard"></td>
	</tr>
	<tr>
		<td>*<?=$gettext->gettext("URL")?>:</td>
		<td><input type="text" name="url" maxlength="65" value="<?=$url?>" class="standard"></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Parent")?>:</td>
		<td><?=$dropdown?></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Additional Editors")?>:</td>
		<td><select name="additional_editors[]" multiple="true" size="5"><?=$addt_users?></select></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Show in Menu")?>:</td>
		<td><input type="checkbox" name="showinmenu" <?=($showinmenu == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="orig_parent_id" value="<?=$orig_parent_id?>">
			<input type="hidden" name="content_change" value="0">
		    <input type="hidden" name="order" value="<?=$order?>">
			<input type="hidden" name="page_id" value="<?=$page_id?>">
			<input type="hidden" name="editcontent" value="true">
			<input type="hidden" name="orig_item_order" value="<?=$orig_item_order?>">
		<input type="submit" name="submitbutton" value="<?=$gettext->gettext("Submit")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		<input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>
</table>
</div>

<?php } ?>
</div>
</form>

<div class="collapseTitle"><a href="#help" onClick="expandcontent('helparea')" style="cursor:hand; cursor:pointer"><?=$gettext->gettext("Help") ?>?</a></div>
<div id="helparea" class="expand">
<?php
	echo "<p>".$gettext->gettext("This page allows you to change the actual content of your page, the parent of this piece of content used for generating the menu. You can also change the text that actually appears in the menu, the title of the page, change the owner, and assign additional editors to the selected piece of content.")."</p>";
	echo "<p>".$gettext->gettext("If you choose to use the page alias, the links created by the automated menu will be more readable using the page alias as the argument rather than the internally defined page id number.")."</p>";
?>
<a name="help">&nbsp;</a>
</div>

<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
