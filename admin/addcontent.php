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
			$error .= "<li>".lang('nofieldgiven', array(lang('title')))."</li>";
		}
		if ($url == "" && $content_type == "link") {
			$validinfo = false;
			$error .= "<li>".lang('nofieldgiven', array(lang('url')))."</li>";
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
				$query = "SELECT page_id FROM ".cms_db_prefix()."pages WHERE page_alias = ".$db->qstr($alias);	
				$result = $db->Execute($query);
				if ($result && $result->RowCount() > 0)
				{
					$validinfo = false;
					$error .= "<li>".lang('aliasalreadyused')."</li>";
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
				$error .= "<li>".lang('errorinsertingpage')."</li>";
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

	if ($result && $result->RowCount() > 0) {
		while($row = $result->FetchRow()) {
			$addt_users .= "<option value=\"".$row["user_id"]."\">".$row["username"]."</option>";
		}
	}else{
		$addt_users = "<option>&nbsp;</option>";
	}

	$ctdropdown = "<select name=\"content_type\" onchange=\"document.addform.content_change.value=1;document.addform.submit()\" class=\"standard\">";
	foreach (get_page_types() as $key=>$value) {
		$ctdropdown .= "<option value=\"$key\"";
		if ($key == $content_type) {
			$ctdropdown .= " selected ";
		}
		$ctdropdown .= ">".$value."</option>";
	}
	$ctdropdown .= "</select>";

}


include_once("header.php");

if (!$access) {
	echo "<h3>".lang('noaccessto',array(lang('addcontent')))."</h3>\n";
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
<h3><?php echo lang('preview')?></h3>

<iframe name="previewframe" width="90%" height="400" frameborder="0" src="<?php echo $config["root_url"]?>/preview.php?tmpfile=<?php echo urlencode(basename($tmpfname))?>" style="margin: 10px; border: 1px solid #8C8A8C;">

</iframe>
<?php

	}

?>

<form method="post" action="addcontent.php" name="addform" id="addform">

<?php if ($content_type == "content") { ?>
<h3><?php echo lang('addcontent')?></h3>
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
		<td style="padding-top: 10px;"><strong><?php echo lang('content') ?></strong><br><textarea id="content" name="content" style="width:100%" cols="80" rows="24"><?php echo $content?></textarea></td>
	</tr>
</table>

<div class="collapseTitle"><a href="#advanced" onClick="expandcontent('advanced')" style="cursor:hand; cursor:pointer"><?php echo lang('advanced') ?></a></div>
<div id="advanced" class="expand">
	<a name="advanced">&nbsp;</a>
	<div style="line-height: .8em; padding-top: 1em; font-weight: bold;"><?php echo lang('headtags') ?></div>
	<textarea rows="4" cols="80" name="head_tags"><?php echo $head_tags ?></textarea>

	<table border="0" cellpadding="0" cellspacing="0" summary="">
		<tr valign="top">
			<td valign="top" style="padding-right: 10px;">
				<div style="line-height: .8em; padding-top: 1em; font-weight: bold;"><?php echo lang('status') ?></div>
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
						<tr>
							<td colspan="2"><?php echo lang('parent')?>:&nbsp;<?php echo $dropdown?></td>
						</tr>
					</table>
				</div>
			</td>
			<?php //if ($adminaccess) { ?>
			<td valign="top">
					<div style="line-height: .8em; padding-top: 1em; font-weight: bold;"><?php echo lang('permission') ?></div>
					<div style="border: solid 1px #6F8341; height: 8em; padding: 7px 5px 5px 5px;">
					<!--<?php echo lang('owner')?>:&nbsp;<?php echo $owners?><br>-->
					<div style="text-align: center;"><?php echo lang('additionaleditors')?>:<br><select name="additional_editors[]" multiple size="3"><?php echo $addt_users ?></select></div>
					</div>
			</td>
			<?php // } ?>
		</tr>
	</table>
</div>
<br>
<input type="hidden" name="orig_section_id" value="<?php echo $orig_section_id?>">
<input type="hidden" name="content_change" value="0">
<input type="hidden" name="addcontent" value="true">
<input type="submit" name="preview" value="<?php echo lang('preview')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
<input type="submit" name="submitbutton" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
</div>

<?php }elseif ($content_type == "separator") { ?>
<h3><?php echo lang('addseparator')?></h3>
<div class="adminformSmall">
<input type="hidden" name="template_id" value="1">
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
		<td><?php echo lang('contenttype')?>:</td>
		<td><?php echo $ctdropdown?></td>
	<tr>
		<td><?php echo lang('parent')?>:</td>
		<td><?php echo $dropdown?></td>
	</tr>
	<tr>
		<td><?php echo lang('additionaleditors')?>:</td>
		<td><select name="additional_editors[]" multiple size="5"><?php echo $addt_users?></select></td>
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
			<input type="hidden" name="content_change" value="0">
			<input type="hidden" name="addcontent" value="true">
			<input type="submit" name="submitbutton" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
			<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>
</table>
</div>



<?php }elseif ($content_type == "link") { ?>
<h3><?php echo lang('addlink')?></h3>
<div class="adminformSmall">
<input type="hidden" name="template_id" value="1">
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="">
	<tr>
		<td><?php echo lang('contenttype')?>:</td>
		<td><?php echo $ctdropdown?></td>
	<tr>
		<td>*<?php echo lang('title')?>:</td>
		<td><input type="text" name="title" maxlength="80" value="<?php echo $title?>" class="standard"></td>
	</tr>
	<tr>
		<td>*<?php echo lang('menutext')?>:</td>
		<td><input type="text" name="menutext" maxlength="25" value="<?php echo $menutext?>" class="standard"></td>
	</tr>
	<tr>
		<td>*<?php echo lang('url')?>:</td>
		<td><input type="text" name="url" maxlength="65" value="<?php echo $url?>" class="standard"></td>
	</tr>
	<tr>
		<td><?php echo lang('parent')?>:</td>
		<td><?php echo $dropdown?></td>
	</tr>
	<tr>
		<td><?php echo lang('additionaleditors')?>:</td>
		<td><select name="additional_editors[]" multiple size="5"><?php echo $addt_users?></select></td>
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
			<input type="hidden" name="content_change" value="0">
			<input type="hidden" name="addcontent" value="true">
			<input type="submit" name="submitbutton" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
			<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>
</table>
</div>
<?php } ?>

</form>

<div class="collapseTitle"><a href="#help" onClick="expandcontent('helparea')" style="cursor:hand; cursor:pointer"><?php echo lang('help') ?>?</a></div>
<div id="helparea" class="expand">
<?php echo lang('helpaddcontent')?>
<a name="help">&nbsp;</a>
</div>


<?php

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
