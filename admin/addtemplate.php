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

$preview = false;
if (isset($_POST["preview"])) $preview = true;

$active = 1;
if (isset($_POST["active"]) && isset($_POST["addsection"])) $active = 0;

if (isset($_POST["cancel"])) {
	redirect("listtemplates.php");
	return;
}

$userid = get_userid();
$access = check_permission($config, $userid, 'Add Template');

if ($access) {

	if (isset($_POST["addtemplate"]) && !$preview) {

		$validinfo = true;

		if ($template == "") {
			$error .= "<li>".$gettext->gettext("No template name given!")."</li>";
			$validinfo = false;
		}
		if ($content == "") {
			$error .= "<li>".$gettext->gettext("No template content entered!")."</li>";
			$validinfo = false;
		}

		if ($validinfo) {
			$query = "INSERT INTO ".$config->db_prefix."templates (template_name, template_content, stylesheet, active, create_date, modified_date) VALUES (".$dbnew->qstr($template).", ".$dbnew->qstr($content).", ".$dbnew->qstr($stylesheet).", $active, now(), now());";
			$result = $dbnew->Execute($query);
			if ($result) {
				$new_template_id = $dbnew->Insert_ID();
				audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $new_template_id, $template, 'Added Template');
				redirect("listtemplates.php");
				return;
			}
			else {
				$error .= "<li>".$gettext->gettext("Error inserting template")."</li>";
			}
		}
	}
}

include_once("header.php");

if (!$access) {
	print "<h3>".$gettext->gettext("No Access to Add Templates")."</h3>";
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

		$tmpfname = tempnam('/tmp', "cmspreview");
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

<form method="post" action="addtemplate.php">

<div class="adminform">

<h3><?=$gettext->gettext("Add Template")?></h3>

<table border="0">

	<tr>
		<td><?=$gettext->gettext("Name")?>:</td>
		<td><input type="text" name="template" maxlength="25" value="<?=$template?>" /></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Content")?>:</td>
		<td><textarea name="content" cols="90" rows="18"><?=$content?></textarea></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Stylesheet")?>:</td>
		<td><textarea name="stylesheet" cols="90" rows="18"><?=$stylesheet?></textarea></td>
	</tr>
	<tr>
		<td><?=$gettext->gettext("Active")?>:</td>
		<td><input type="checkbox" name="active" <?=($active == 1?"checked":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addtemplate" value="true" /><input type="submit" name="preview" value="<?=$gettext->gettext("Preview")?>" /><input type="submit" value="<?=$gettext->gettext("Submit")?>" /><input type="submit" name="cancel" value="<?=$gettext->gettext("Cancel")?>" /></td>
	</tr>

</table>

</div>

</form>

<?php

}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
