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

$alias = "";
if ($config["auto_alias_content"])
{
	$alias = $title;
	$alias=trim($alias);
	$alias = preg_replace("/\W+/", "-", $alias);
}
else
{
	if (isset($_POST["alias"])) $alias = $_POST["alias"];
}

$content_type = "content";
if (isset($_POST["content_type"])) $content_type = $_POST["content_type"];

$head_tags = "";
if (isset($_POST["head_tags"])) $head_tags = $_POST["head_tags"];

$url = "";
if (isset($_POST["url"])) $url = $_POST["url"];

$parent_id = -1;
if (isset($_POST["parent_id"])) $parent_id = $_POST["parent_id"];

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
$access = check_permission($userid, 'Add Content');

$templatepostback = "";
if (get_preference($userid, 'use_wysiwyg') == "1" && $content_type == "content") {
	$htmlarea_flag = true;
	$templatepostback = " onchange=\"document.addform.content.value=editor.getHTML();document.addform.submit()\"";
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
				$query = "SELECT page_id FROM ".cms_db_prefix()."pages WHERE page_alias = ".$db->qstr($alias);	
				$result = $db->Execute($query);
				if ($result && $result->RowCount() > 0)
				{
					$validinfo = false;
					$error .= "<li>".$gettext->gettext("Alias has already been used on another page!")."</li>";
				}
			}
		}

		if ($validinfo) {
			$order = 1;
			$query = "SELECT max(item_order) + 1 as item_order FROM ".cms_db_prefix()."pages WHERE parent_id = $parent_id";
			$result = $db->Execute($query);
			$row = $result->FetchRow();
			if (isset($row["item_order"])) {
				$order = $row["item_order"];	
			}
			$new_page_id = $db->GenID(cms_db_prefix()."pages_seq");
			$query = "INSERT INTO ".cms_db_prefix()."pages (page_id, page_title, page_url, page_content, page_type, parent_id, template_id, owner, show_in_menu, menu_text, item_order, active, page_alias, create_date, modified_date, head_tags) VALUES ($new_page_id, ".$db->qstr($title).",".$db->qstr($url).",".$db->qstr($content).",".$db->qstr($content_type).", $parent_id, $template_id, $userid, $showinmenu, ".$db->qstr($menutext).", $order, $active, ".$db->qstr($alias).",".$db->DBTimeStamp(time()).", ".$db->DBTimeStamp(time()).", ".$db->qstr($head_tags).")";
			$result = $db->Execute($query);
			if ($result) {
				if (isset($_POST["additional_editors"])) {
					foreach ($_POST["additional_editors"] as $addt_user_id) {
						$new_addt_id = $db->GenID(cms_db_prefix()."additional_users_seq");
						$query = "INSERT INTO ".cms_db_prefix()."additional_users (additional_user_id, user_id, page_id) VALUES ($new_addt_id, ".$addt_user_id.", ".$new_page_id.")";
						$db->Execute($query);
					}
				}
				audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $new_page_id, $title, 'Added Content');
				redirect("listcontent.php");
				return;
			}
			else {
				$error .= "<li>".$gettext->gettext("Error inserting page")."</li>";
			}
		}
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

	foreach ($content_array as $one) {

		$dropdown .= "<option value=\"".$one->page_id."\"";
		if ($one->page_id == $parent_id) {
			$dropdown .= "selected";
		}
		$dropdown .= ">".$one->hier." - ".$one->page_title."</option>";

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
		if ($template_id == -1) {
			$template_id = $row["template_id"];
		}
		$dropdown2 .= ">".$row["template_name"]."</option>";
	}

	$dropdown2 .= "</select>";

	$addt_users = "";

	$query = "SELECT user_id, username FROM ".cms_db_prefix()."users WHERE user_id <> " . $userid;
	$result = $db->Execute($query);

	while($row = $result->FetchRow()) {
		$addt_users .= "<option value=\"".$row["user_id"]."\">".$row["username"]."</option>";
	}

	$ctdropdown = "<select name=\"content_type\" onchange=\"document.addform.content_change.value=1;document.addform.submit()\" class=\"standard\">";
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
		
		$tmpfname = tempnam($config["previews_path"], "");
		$handle = fopen($tmpfname, "w");
		fwrite($handle, serialize($data));
		fclose($handle);

?>
<h3><?=$gettext->gettext("Preview")?></h3>

<iframe name="previewframe" width="90%" height="400" frameborder="0" src="<?=$config["root_url"]?>/preview.php?tmpfile=<?=urlencode(basename($tmpfname))?>" style="margin: 10px; border: 1px solid #8C8A8C;">

</iframe>
<?php

	}

?>

<form method="post" action="addcontent.php" name="addform" id="addform">

<?php if ($content_type == "content") { ?>
<h3><?=$gettext->gettext("Add Content")?></h3>
<div class="adminform">
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" summary="" style="margin-top: 0; border: solid 1px #8C8A8C; padding: 5px 5px 10px 5px;" id="padform">
				<tr valign="top">
					<td valign="top">
						<?=$gettext->gettext("Content Type")?>:<?=$ctdropdown?>
						<?=$gettext->gettext("Title")?>:&nbsp;<input type="text" name="title" maxlength="80" value="<?=$title?>" />
						<span style="white-space: nowrap"><?=$gettext->gettext("Menu Text")?>:&nbsp;<input type="text" name="menutext" maxlength="25" value="<?=$menutext?>" /></span>
						<span style="white-space: nowrap"><?=$gettext->gettext("Page Alias")?>:&nbsp;<input type="text" name="alias" maxlength="65" value="<?=$alias?>" /></span>
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
	<div style="line-height: .8em; padding-top: 1em; font-weight: bold;"><?=$gettext->gettext("Head Tags") ?></div>
	<textarea rows="4" name="head_tags"><?=$head_tags ?></textarea>

	<table border="0" cellpadding="0" cellspacing="0" summary="">
		<tr valign="top">
			<td valign="top" style="padding-right: 10px;">
				<div style="line-height: .8em; padding-top: 1em; font-weight: bold;"><?=$gettext->gettext("Status") ?></div>
				<div style="border: solid 1px #8C8A8C; height: 8em; padding: 7px 5px 5px 5px;">
					<table width="100%" border="0"cellpadding="0" cellspacing="0" summary="" style=" vertical-align: middle;">
						<tr valign="top">
							<td valign="top"><?=$gettext->gettext("Show in Menu")?>:</td>
							<td><input type="checkbox" name="showinmenu" <?=($showinmenu == 1?"checked":"")?> /></td>
						</tr>
						<tr valign="top" style="padding-top: 5px;">
							<td valign="top"><?=$gettext->gettext("Active")?>:</td>
							<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /> </td>
						</tr>
						<tr>
							<td colspan="2"><?=$gettext->gettext("Parent")?>:&nbsp;<?=$dropdown?></td>
						</tr>
					</table>
				</div>
			</td>
			<?php //if ($adminaccess) { ?>
			<td valign="top">
					<div style="line-height: .8em; padding-top: 1em; font-weight: bold;"><?=$gettext->gettext("Permission") ?></div>
					<div style="border: solid 1px #6F8341; height: 8em; padding: 7px 5px 5px 5px;">
					<!--<?=$gettext->gettext("Owner")?>:&nbsp;<?=$owners?><br>-->
					<div style="text-align: center;"><?=$gettext->gettext("Additional Editors")?>:<br><select name="additional_editors[]" multiple="true" size="3"><?=$addt_users?></select></div>
					</div>
			</td>
			<?php // } ?>
		</tr>
	</table>
</div>
<br>
<input type="hidden" name="orig_section_id" value="<?=$orig_section_id?>" />
<input type="hidden" name="content_change" value="0" />
<input type="hidden" name="addcontent" value="true" />
<input type="submit" name="preview" value="<?=$gettext->gettext("Preview")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
<input type="submit" name="submitbutton" value="<?=$gettext->gettext("Submit")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
<input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
</div>

<?php }elseif ($content_type == "separator") { ?>
<h3><?=$gettext->gettext("Add Separator")?></h3>
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
		<td><input type="checkbox" name="showinmenu" <?=($showinmenu == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="content_change" value="0" />
			<input type="hidden" name="addcontent" value="true" />
			<input type="submit" name="submitbutton" value="<?=$gettext->gettext("Submit")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
			<input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" /></td>
	</tr>
</table>
</div>



<?php }elseif ($content_type == "link") { ?>
<h3><?=$gettext->gettext("Add Link")?></h3>
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
		<td><input type="text" name="menutext" maxlength="25" value="<?=$menutext?>" class="standard"/></td>
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
		<td><input type="checkbox" name="showinmenu" <?=($showinmenu == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="content_change" value="0" />
			<input type="hidden" name="addcontent" value="true" />
			<input type="submit" name="submitbutton" value="<?=$gettext->gettext("Submit")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
			<input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" /></td>
	</tr>
</table>
</div>
<?php } ?>

</form>

<div class="collapseTitle"><a href="#help" onClick="expandcontent('helparea')" style="cursor:hand; cursor:pointer"><?=$gettext->gettext("Help") ?>?</a></div>
<div id="helparea" class="expand">
<p><?php echo $gettext->gettext("This page allows you to create new content.  Select a content type and fill in the required fields and you have your new content setup.  If you select to show it in the menu, and you are using one of the automated menu tools {dhtmlmenu} or {bulletmenu} then the new content will show up automatically in the menu.") ?></p>
<p><?php echo $gettext->gettext("You can set your preference for a WYSIWYG editor by clicking on the User Preferences link on the menu.  By setting this to false you can create more complex content, or simply cut and paste in existing content from another site.</p>") ?>
<a name="help">&nbsp;</a>
</div>


<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
