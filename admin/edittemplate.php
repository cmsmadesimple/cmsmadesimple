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

$template = "";
if (isset($_POST["template"])) $template = $_POST["template"];

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
$access = check_permission($config, $userid, 'Modify Template');

if ($access) {

	$db = new DB($config);

	if (isset($_POST["edittemplate"]) && !$preview) {

		$validinfo = true;
		if ($template == "") {
			$error .= "<li>".GetText::gettext("No template name given!")."</li>";
			$validinfo = false;
		}
		if ($content == "") {
			$error .= "<li>".GetText::gettext("No template content given!")."</li>";
			$validinfo = false;
		}

		if ($validinfo) {
			$query = "UPDATE ".$config->db_prefix."templates SET template_name = '".$db->escapestring($template)."', template_content = '".$db->escapestring($content)."', stylesheet = '".$db->escapestring($stylesheet)."', active = $active, modified_date = now() WHERE template_id = $template_id";
			$result = $db->query($query);

			if ($db->rowsaffected()) {
				$db->close();
				audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $template_id, $template, 'Edited Template');
				redirect("listtemplates.php");
				return;
			}
			else {
				$error .= "<li>".GetText::gettext("Error updating template!")."</li>";
			}
		}

	}
	else if ($template_id != -1 && !$preview) {

		$query = "SELECT * from ".$config->db_prefix."templates WHERE template_id = " . $template_id;
		$result = $db->query($query);
		
		$row = $db->getresulthash($result);

		$template = $row["template_name"];
		$content = $row["template_content"];
		$stylesheet = $row["stylesheet"];
		$active = $row["active"];

		$db->freeresult($result);

	}

	$db->close($link);

}

include_once("header.php");

if (!$access) {
	print "<h3>".GetText::gettext("No Access To Edit Templates")."</h3>";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}

	if ($preview) {

		$data["content"] = "Test Content";
		#$data["template_id"] = $template_id;
		$data["stylesheet"] = $stylesheet;
		$data["template"] = $content;

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

<form method="post" action="edittemplate.php">

<div class="adminform">

<h3><?=GetText::gettext("Edit Template")?></h3>

<table border="0">

	<tr>
		<td>*<?=GetText::gettext("Template Name")?>:</td>
		<td><input type="text" name="template" maxlength="25" value="<?=$template?>" /></td>
	</tr>
	<tr>
		<td>*<?=GetText::gettext("Content")?>:</td>
		<td><textarea name="content" cols="90" rows="18"><?=htmlentities($content)?></textarea></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Stylesheet")?>:</td>
		<td><textarea name="stylesheet" cols="90" rows="18"><?=htmlentities($stylesheet)?></textarea></td>
	</tr>
	<tr>
		<td><?=GetText::gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="template_id" value="<?=$template_id?>" /><input type="hidden" name="edittemplate" value="true" /><input type="submit" name="preview" value="<?=GetText::gettext("Preview")?>" /><input type="submit" value="<?=GetText::gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=GetText::gettext("Cancel")?>"></td>
	</tr>

</table>

</div>

</form>

<?php

}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
