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
require_once("../lib/classes/class.template.inc.php");

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

$password_protect = false;
if (isset($_POST['password_protect']))$password_protect = $_POST['password_protect'];

$submit = false;
if (isset($_POST["submitbutton"])) $submit = true;

$template_id = -1;
if (isset($_POST["template_id"]))
{
	$template_id = $_POST["template_id"];
	$onetemplate = TemplateOperations::LoadTemplateByID($template_id);
	header("Content-Type: text/html; charset=" . (isset($onetemplate->encoding)?$onetemplate->encoding:get_encoding()));
	$charsetsent = true;
}

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

$use_javasyntax = false;

if ($access) {

	if ($submit) {

		$validinfo = true;

		if ($title == "" && $content_type != "separator" && $content_type != "sectionheader") {
			$validinfo = false;
			$error .= "<li>".lang('nofieldgiven',array(lang('title')))."</li>";
		}
		if ($url == "" && $content_type == "link") {
			$validinfo = false;
			$error .= "<li>".lang('nofieldgiven',array(lang('url')))."</li>";
		}
		if ($content_type == "content" && $content == "") {
			$validinfo = false;
			$error .= "<li>".lang('nofieldgiven', array(lang('content')))."</li>";
		}
		if ($menutext == "" && $content_type != "separator") {
			$validinfo = false;
			$error .= "<li>".lang('nofieldgiven', array(lang('menutext')))."</li>";
		}
		if ($alias != "")
		{
			if (preg_match('/^\d+$/', $alias))
			{
				$validinfo = false;
				$error .= "<li>".lang('aliasnotaninteger')."</li>";
			}
			else if (!preg_match('/^[\d\w\-]+$/', $alias))
			{
				$validinfo = false;
				$error .= "<li>".lang('aliasmustbelettersandnumbers')."</li>";
			}
			else
			{
				$query = "SELECT page_id FROM ".cms_db_prefix()."pages WHERE page_id <> $page_id AND page_alias = ".$db->qstr($alias);
				$result = $db->Execute($query);
				if ($result && $result->RowCount() > 0)
				{
					$validinfo = false;
					$error .= "<li>".lang('aliasalreadyused')."</li>";
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
			$query1 = "UPDATE ".cms_db_prefix()."pages SET page_title=".$db->qstr($title).", page_url=".$db->qstr($url).", page_content=".$db->qstr($content).", parent_id=$parent_id, template_id=$template_id, show_in_menu=$showinmenu, menu_text=".$db->qstr($menutext).", active=$active, modified_date = ".$db->DBTimeStamp(time()).", item_order=$order, page_type = ".$db->qstr($content_type).", owner=$owner_id, page_alias=".$db->qstr($alias).", head_tags=".$db->qstr($head_tags).", password_protected=".$password_protect." WHERE page_id = $page_id";
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
							$query = "INSERT INTO ".cms_db_prefix()."additional_users (additional_users_id, user_id, page_id) VALUES (".$new_addt_id.", ".$addt_user_id.", ".$page_id.")";
							$db->Execute($query);
						}
					}
					$query = "DELETE FROM ".cms_db_prefix()."frontend_users WHERE page_id = $page_id";
					$db->Execute($query);
					if(isset($_POST['frontend_access'])){
						foreach($_POST["frontend_access"] as $frontend_user_id){
							$new_user_id = $db->GenID(cms_db_prefix()."frontend_users_seq");
							$query = "INSERT INTO ".cms_db_prefix()."frontend_users (frontend_users_id, user_id, page_id) VALUES (".$new_user_id.", ".$frontend_user_id.", ".$page_id.")";
							$db->Execute($query);
						}
					}
				}
				set_all_pages_hierarchy_position();
				audit($page_id, $title, 'Edited Content');
				redirect("listcontent.php");
				return;
			}
			else {
				$error .= "<li>".lang('errorupdatingcontent')."</li>";
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
		$password_protect = $row['password_protected'];

		//Get encoding of template
		$onetemplate = TemplateOperations::LoadTemplateByID($template_id);
		header("Content-Type: text/html; charset=" . (isset($onetemplate->encoding)?$onetemplate->encoding:get_encoding()));
		$charsetsent = true;
	}

	$templatepostback = "";
	if (get_preference($userid, 'use_wysiwyg') == "1" && $content_type == "content") {
		$htmlarea_flag = true;
		$templatepostback = " onchange=\"document.editform.content_change.value=1;document.editform.content.value=editor.getHTML();document.editform.submit()\"";
        $use_javasyntax = false;
    }else if (get_preference($userid, 'use_javasyntax') == "1" && $content_type == "content"){
        $use_javasyntax = true;
    }

    $content_array = array();
    $content_array = db_get_menu_items();
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
	print "<h3>".lang('noaccessto', array(lang('editpage')))."</h3>";
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
<h3><?php echo lang('preview')?></h3>

<iframe name="previewframe" width="90%" height="400" frameborder="0" src="<?php echo $config["root_url"] ?>/preview.php?tmpfile=<?php echo urlencode(basename($tmpfname))?>" style="margin: 10px; border: 1px solid #8C8A8C;">

</iframe>
<?php

	}

?>

<form method="post" action="editcontent.php" name="editform" id="editform" <?php if($use_javasyntax){echo 'onSubmit="textarea_submit(this, \'content,head_tags\');"';} ?>>

<?php if ($content_type == "content") { ?>
<h3><?php echo lang('editcontent')?></h3>
<div class="adminform">
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" summary="" style="margin-top: 0; border: solid 1px #8C8A8C; padding: 5px 5px 10px 5px;" id="padform">
				<tr valign="top">
					<td valign="top">
						<?php echo lang('contenttype')?>:<?php echo $ctdropdown?>
						<?php echo lang('title')?>:&nbsp;<input type="text" name="title" maxlength="80" value="<?php echo $title?>">
						<span style="white-space: nowrap"><?php echo lang('menutext')?>:&nbsp;<input type="text" name="menutext" maxlength="25" value="<?php echo $menutext?>"></span>
						<span style="white-space: nowrap"><?php echo lang('pagealias')?>:&nbsp;<input type="text" name="alias" maxlength="65" value="<?php echo $alias?>"></span>
						<span style="white-space: nowrap"><?php echo lang('template')?>:&nbsp;<?php echo $dropdown2?></span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top: 10px;"><strong><?php echo lang('content') ?></strong><br>
        <?php echo textarea_highlight($use_javasyntax, $content, "content", "syntaxHighlight", "HTML (Complex)", "content"); ?>
        </td>
	</tr>
</table>

<div class="collapseTitle"><a href="#advanced" onClick="expandcontent('advanced')" style="cursor:hand; cursor:pointer"><?php echo lang('advanced') ?></a></div>
<div id="advanced" class="expand">
	<a id="advanced">&nbsp;</a>
	<div style="line-height: .8em; padding-top: 1em; font-weight: bold;"><?php echo lang('headtags') ?>
		<?php echo textarea_highlight($use_javasyntax, $head_tags, "head_tags"); ?></textarea>
	</div>

	<table border="0" cellpadding="0" cellspacing="0" summary="">
		<tr valign="top">
			<td valign="top" style="padding-right: 10px;">
				<div style="line-height: .8em; padding-top: 1em; margin-bottom: 1em; font-weight: bold;"><?php echo lang('status')?></div>
				<div style="border: solid 1px #8C8A8C; height: 8em; padding: 7px 5px 5px 5px;">
					<table width="100%" border="0"cellpadding="0" cellspacing="0" summary="" style=" vertical-align: middle;">
						<tr valign="top">
							<td valign="top"><?php echo lang('showinmenu')?>:</td>
							<td><input type="checkbox" name="showinmenu" <?php echo ($showinmenu == 1?"checked":"")?>></td>
						</tr>
						<tr valign="top" style="padding-top: 5px;">
							<td valign="top"><?php echo lang('active')?>:</td>
							<td><input type="checkbox" name="active" <?php echo ($active == 1?"checked":"")?>> </td>
						</tr>
						</tr>
							<td colspan="2"><?php echo lang('parent')?>:&nbsp;<?php echo $dropdown?></td>
						</tr>
					</table>
				</div>
			</td>
			<td valign="top" style="padding-right: 10px;">
					<div style="line-height: .8em; padding-top: 1em; margin-bottom: 1em; font-weight: bold;"><?php echo lang('permission')?></div>
					<div style="border: solid 1px #8C8A8C; height: 8em; padding: 7px 5px 5px 5px;">
					<?php if ($adminaccess) { ?>
							<?php echo lang('owner')?>:&nbsp;<?php echo $owners?>
					<?php } ?>
					<div style="text-align: center; padding-top: 5px;"><?php echo lang('additionaleditors')?>:<br><select name="additional_editors[]" multiple="true" size="3"><?php echo $addt_users?></select></div>
					</div>
			</td>
			<td valign="top">
					<div style="line-height: .8em; padding-top: 1em; margin-bottom: 1em; font-weight: bold;">Password Protect</div>
					<div style="border: solid 1px #8C8A8C; height: 8em; padding: 7px 5px 5px 5px;">
					<div style="text-align: center; padding-top: 5px;">Password Protect:<input type="checkbox" name="password_protect" value="1" <?php echo ($password_protect == 1?"checked":"") ?>><br><select name="frontend_access[]" multiple="true" size="3"><?php echo $addt_users ?></select></div></div>
			</td>
		</tr>
	</table>
    <div id="advanced" class="expand">&nbsp;</div>
</div>

<br>
<input type="hidden" name="orig_parent_id" value="<?php echo $orig_parent_id?>">
<input type="hidden" name="content_change" value="0">
<input type="hidden" name="order" value="<?php echo $order?>">
<input type="hidden" name="page_id" value="<?php echo $page_id?>">
<input type="hidden" name="editcontent" value="true">
<input type="hidden" name="orig_item_order" value="<?php echo $orig_item_order?>">
<input type="submit" name="preview" value="<?php echo lang('preview')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
<input type="submit" name="submitbutton" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">

<?php }elseif ($content_type == "separator") { ?>
<h3><?php echo lang('editseparator')?></h3>
<div class="adminformSmall">
<input type="hidden" name="template_id" value="1">
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
		<td><?php echo lang('contenttype')?>:</td>
		<td><?php echo $ctdropdown?></td>
	</tr>
	<tr>
		<td><?php echo lang('parent')?>:</td>
		<td><?php echo $dropdown?></td>
	</tr>
	<tr>
		<td><?php echo lang('additionaleditors')?>:</td>
		<td><select name="additional_editors[]" multiple="true" size="5"><?php echo $addt_users?></select></td>
	</tr>
	<tr>
		<td><?php echo lang('showinmenu')?>:</td>
		<td><input type="checkbox" name="showinmenu" <?php echo ($showinmenu == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td><?php echo lang('active')?>:</td>
		<td><input type="checkbox" name="active" <?php echo ($active == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
<input type="hidden" name="orig_parent_id" value="<?php echo $orig_parent_id?>">
			<input type="hidden" name="content_change" value="0">
			<input type="hidden" name="order" value="<?php echo $order?>">
			<input type="hidden" name="page_id" value="<?php echo $page_id?>">
			<input type="hidden" name="editcontent" value="true">
			<input type="hidden" name="orig_item_order" value="<?php echo $orig_item_order?>">
			<input type="submit" name="submitbutton" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
			<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>
</table>
<div id="advanced" class="expand">&nbsp;</div>
</div>



<?php } else if ($content_type == "link" || $content_type == 'News') { ?>
<h3><?php echo lang('editlink')?></h3>
<div class="adminformSmall">
<input type="hidden" name="template_id" value="1">
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
		<td><?php echo lang('contenttype')?>:</td>
		<td><?php echo $ctdropdown?></td>
	</tr>
	<tr>
		<td>*<?php echo lang('title')?>:</td>
		<td><input type="text" name="title" maxlength="80" value="<?php echo $title?>" class="standard"></td>
	</tr>
	<tr>
		<td>*<?php echo lang('menutext')?>:</td>
		<td><input type="text" name="menutext" maxlength="25" value="<?php echo $menutext?>" class="standard"></td>
	</tr>
	<?php if ($content_type == 'link') { ?>
	<tr>
		<td>*<?php echo lang('url')?>:</td>
		<td><input type="text" name="url" maxlength="65" value="<?php echo $url?>" class="standard"></td>
	</tr>
	<?php } ?>
	<?php if ($content_type == 'News') { ?>
	<tr>
		<td><?php echo lang('template')?>:</td>
		<td><?php echo $dropdown2?></td>
	</tr>
	<?php } ?>
	<tr>
		<td><?php echo lang('parent')?>:</td>
		<td><?php echo $dropdown?></td>
	</tr>
	<tr>
		<td><?php echo lang('additionaleditors')?>:</td>
		<td><select name="additional_editors[]" multiple="true" size="5"><?php echo $addt_users?></select></td>
	</tr>
	<tr>
		<td><?php echo lang('showinmenu')?>:</td>
		<td><input type="checkbox" name="showinmenu" <?php echo ($showinmenu == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td><?php echo lang('active')?>:</td>
		<td><input type="checkbox" name="active" <?php echo ($active == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="orig_parent_id" value="<?php echo $orig_parent_id?>">
			<input type="hidden" name="content_change" value="0">
		    <input type="hidden" name="order" value="<?php echo $order?>">
			<input type="hidden" name="page_id" value="<?php echo $page_id?>">
			<input type="hidden" name="editcontent" value="true">
			<input type="hidden" name="orig_item_order" value="<?php echo $orig_item_order?>">
		<input type="submit" name="submitbutton" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>
</table>
<div id="advanced" class="expand">&nbsp;</div>
</div>

<?php } else if ($content_type == "sectionheader") { ?>
<h3><?php echo lang('editlink')?></h3>
<div class="adminformSmall">
<input type="hidden" name="template_id" value="1">
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
		<td><?php echo lang('contenttype')?>:</td>
		<td><?php echo $ctdropdown?></td>
	</tr>
	<tr>
		<td>*<?php echo lang('menutext')?>:</td>
		<td><input type="text" name="menutext" maxlength="25" value="<?php echo $menutext?>" class="standard"></td>
	</tr>
	<tr>
		<td><?php echo lang('parent')?>:</td>
		<td><?php echo $dropdown?></td>
	</tr>
	<tr>
		<td><?php echo lang('showinmenu')?>:</td>
		<td><input type="checkbox" name="showinmenu" <?php echo ($showinmenu == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td><?php echo lang('active')?>:</td>
		<td><input type="checkbox" name="active" <?php echo ($active == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="orig_parent_id" value="<?php echo $orig_parent_id?>">
			<input type="hidden" name="content_change" value="0">
		    <input type="hidden" name="order" value="<?php echo $order?>">
			<input type="hidden" name="page_id" value="<?php echo $page_id?>">
			<input type="hidden" name="editcontent" value="true">
			<input type="hidden" name="orig_item_order" value="<?php echo $orig_item_order?>">
		<input type="submit" name="submitbutton" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>
</table>
<div id="advanced" class="expand">&nbsp;</div>
</div>

<?php } ?>
</div>
</form>

<div class="collapseTitle"><a href="#help" onClick="expandcontent('helparea')" style="cursor:hand; cursor:pointer"><?php echo lang('help') ?>?</a></div>
<div id="helparea" class="expand">
<?php echo lang('helpeditcontent')?>
<a name="help">&nbsp;</a>
</div>

<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
