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

$template = "";
if (isset($_POST["template"])) $template = $_POST["template"];

$orig_template = "";
if (isset($_POST["orig_template"])) $orig_template = $_POST["orig_template"];

$content = "";
if (isset($_POST["content"])) $content = $_POST["content"];

$stylesheet = "";
if (isset($_POST["stylesheet"])) $stylesheet = $_POST["stylesheet"];

$encoding = "";
if (isset($_POST["encoding"])) $encoding = $_POST["encoding"];

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

$use_javasyntax = false;
if (get_preference($userid, 'use_javasyntax') == "1")$use_javasyntax = true;

if ($access)
{
	if (isset($_POST["edittemplate"]) && !$preview)
	{
		$validinfo = true;
		if ($template == "")
		{
			$error .= "<li>".lang('nofieldgiven', array(lang('name')))."</li>";
			$validinfo = false;
		}
		else if ($template != $orig_template)
		{
			$query = "SELECT template_id from ".cms_db_prefix()."templates WHERE template_name = " . $db->qstr($template);
			$result = $db->Execute($query);

			if ($result && $result->RowCount() > 0)
			{
				$error .= "<li>".lang('errortemplateinuse')."</li>";
				$validinfo = false;
			}
		}
		if ($content == "")
		{
			$error .= "<li>".lang('nofieldgiven', array(lang('content')))."</li>";
			$validinfo = false;
		}

		if ($validinfo)
		{
			$onetemplate = TemplateOperations::LoadTemplateByID($template_id);
			$onetemplate->name = $template;
			$onetemplate->content = $content;
			$onetemplate->stylesheet = $stylesheet;
			$onetemplate->encoding = $encoding;
			$onetemplate->active = $active;

			#Perform the edittemplate_pre callback
			foreach($gCms->modules as $key=>$value)
			{
				if (isset($gCms->modules[$key]['edittemplate_pre_function']))
				{
					call_user_func_array($gCms->modules[$key]['edittemplate_pre_function'], array($gCms, $groupobj));
				}
			}

			$result = $onetemplate->save();

			if ($result)
			{
				#Perform the edittemplate_post callback
				foreach($gCms->modules as $key=>$value)
				{
					if (isset($gCms->modules[$key]['edittemplate_post_function']))
					{
						call_user_func_array($gCms->modules[$key]['edittemplate_post_function'], array($gCms, $groupobj));
					}
				}
				audit($template_id, $onetemplate->name, 'Edited Template');
				redirect("listtemplates.php");
			}
			else
			{
				$error .= "<li>".lang('errorupdatingtemplate')."</li>";
			}
		}
	}
	else if ($template_id != -1 && !$preview)
	{
		$onetemplate = TemplateOperations::LoadTemplateByID($template_id);
		$template = $onetemplate->name;
		$orig_template = $onetemplate->name;
		$content = $onetemplate->content;
		$stylesheet = $onetemplate->stylesheet;
		$encoding = $onetemplate->encoding;
		$active = $onetemplate->active;
	}
}

include_once("header.php");

if (!$access)
{
	print "<h3>".lang('noaccessto', array(lang('edittemplate')))."</h3>";
}
else
{
	if ($error != "")
	{
		echo "<ul class=\"error\">".$error."</ul>";
	}

	if ($preview)
	{
		$data["title"] = "TITLE HERE";
		$data["content"] = "Test Content";
		#$data["template_id"] = $template_id;
		$data["stylesheet"] = $stylesheet;
		$data["encoding"] = $encoding;
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
				$data["stylesheet"] .= "\n".$cssline['css_text']."\n";
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

<form method="post" action="edittemplate.php" <?php if($use_javasyntax){echo 'onSubmit="textarea_submit(this, \'content,stylesheet\');"';} ?>>

<div class="adminform">

<h3><?php echo lang('edittemplate')?></h3>

<table width="100%" border="0">

	<tr>
		<td width="100">*<?php echo lang('name')?>:</td>
		<td><input type="text" name="template" maxlength="25" value="<?php echo $template?>"><input type="hidden" name="orig_template" value="<?php echo $orig_template?>"></td>
	</tr>
	<tr>
		<td>*<?php echo lang('content'); ?>:</td>
		<td><?php echo textarea_highlight($use_javasyntax, $content, "content", "syntaxHighlight", "HTML (Complex)", "", $encoding); ?></td>
	</tr>
	<tr>
		<td><?php echo lang('stylesheet')?>:</td>
		<td><?php echo textarea_highlight($use_javasyntax, $stylesheet, "stylesheet", "syntaxHighlight", "Java Properties", "", $encoding) ?></td>
	</tr>
	<tr>
		<td><?php echo lang('active')?>:</td>
		<td><input type="checkbox" name="active" <?php echo ($active == 1?"checked":"") ?>></td>
	</tr>
	<tr>
		<td><?php echo lang('encoding')?>:</td>
		<td><input type="text" name="encoding" maxlength="25" value="<?php echo $encoding?>"></td>
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
