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

$template = "";
if (isset($_POST["template"])) $template = $_POST["template"];

$orig_template = "";
if (isset($_POST["orig_template"])) $orig_template = $_POST["orig_template"];

$content = "";
if (isset($_POST["content"])) $content = $_POST["content"];

$stylesheet = "";
if (isset($_POST["stylesheet"])) $stylesheet = $_POST["stylesheet"];

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["edittemplate"])) $active = 0;

$preview = false;
if (isset($_POST["preview"])) $preview = true;

$template_id = -1;
if (isset($_POST["template_id"])) $template_id = $_POST["template_id"];
else if (isset($_GET["template_id"])) $template_id = $_GET["template_id"];

if (isset($_POST["cancel"])) {
	redirect("listtemplates.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Template');

if ($access) {

	if (isset($_POST["edittemplate"]) && !$preview) {

		$validinfo = true;
		if ($template == "") {
			$error .= "<li>".lang('nofieldgiven', array(lang('name')))."</li>";
			$validinfo = false;
		} else if ($template != $orig_template) {
			$query = "SELECT template_id from ".cms_db_prefix()."templates WHERE template_name = " . $db->qstr($template);
			$result = $db->Execute($query);

			if ($result && $result->RowCount() > 0) {
				$error .= "<li>".lang('errortemplateinuse')."</li>";
				$validinfo = false;
			}
		}
		if ($content == "") {
			$error .= "<li>".lang('nofieldgiven', array(lang('content')))."</li>";
			$validinfo = false;
		}

		if ($validinfo) {
			$query = "UPDATE ".cms_db_prefix()."templates SET template_name = ".$db->qstr($template).", template_content = ".$db->qstr($content).", stylesheet = ".$db->qstr($stylesheet).", active = $active, modified_date = ".$db->DBTimeStamp(time())." WHERE template_id = $template_id";
			$result = $db->Execute($query);

			if ($result) {
				audit(get_userid(), (isset($_SESSION["cms_admin_username"])?$_SESSION["cms_admin_username"]:""), $template_id, $template, 'Edited Template');
				redirect("listtemplates.php");
				return;
			}
			else {
				$error .= "<li>".lang('errorupdatingtemplate')."</li>";
			}
		}

	}
	else if ($template_id != -1 && !$preview) {

		$query = "SELECT * from ".cms_db_prefix()."templates WHERE template_id = " . $template_id;
		$result = $db->Execute($query);
		
		$row = $result->FetchRow();

		$template = $row["template_name"];
		$orig_template = $row["template_name"];
		$content = $row["template_content"];
		$stylesheet = $row["stylesheet"];
		$active = $row["active"];

	}
}

include_once("header.php");

if (!$access) {
	print "<h3>".lang('noaccessto', array(lang('edittemplate')))."</h3>";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}

	if ($preview) {

		$data["title"] = "TITLE HERE";
		$data["content"] = "Test Content";
		#$data["template_id"] = $template_id;
		$data["stylesheet"] = $stylesheet;
		$data["template"] = $content;

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

<iframe name="previewframe" width="100%" height="400" src="<?php echo $config["root_url"]?>/preview.php?tmpfile=<?php echo urlencode(basename($tmpfname))?>">

</iframe>
<?php

	}

?>

<form method="post" action="edittemplate.php">

<div class="adminform">

<h3><?php echo lang('edittemplate')?></h3>

<table width="100%" border="0">

	<tr>
		<td width="100">*<?php echo lang('name')?>:</td>
		<td><input type="text" name="template" maxlength="25" value="<?php echo $template?>"><input type="hidden" name="orig_template" value="<?php echo $orig_template?>"></td>
	</tr>
	<tr>
		<td>*<?php echo lang('content')?>:</td>
		<td><textarea name="content" cols="90" rows="18"><?php echo htmlentities($content)?></textarea></td>
	</tr>
	<tr>
		<td><?php echo lang('stylesheet')?>:</td>
		<td><textarea name="stylesheet" cols="90" rows="18"><?php echo htmlentities($stylesheet)?></textarea></td>
	</tr>
	<tr>
		<td><?php echo lang('active')?>:</td>
		<td><input type="checkbox" name="active" <?php echo ($active == 1?"checked":"")?>></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="template_id" value="<?php echo $template_id?>"><input type="hidden" name="edittemplate" value="true"><input type="submit" name="preview" value="<?php echo lang('preview')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>

</table>

</div>

</form>

<?php

}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
