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
#
#$Id$

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
require_once("../lib/classes/class.template.inc.php");

check_login();

$error = "";

$template = "";
if (isset($_POST["template"])) $template = $_POST["template"];

$content = "";
if (isset($_POST["content"])) $content = $_POST["content"];

$stylesheet = "";
if (isset($_POST["stylesheet"])) $stylesheet = $_POST["stylesheet"];

$encoding = "";
if (isset($_POST["encoding"])) $encoding = $_POST["encoding"];

$preview = false;
if (isset($_POST["preview"])) $preview = true;

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["addsection"])) $active = 0;

if (isset($_POST["cancel"]))
{
	redirect("listtemplates.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Add Template');

$use_javasyntax = false;
if (get_preference($userid, 'use_javasyntax') == "1")$use_javasyntax = true;

if ($access)
{
	if (isset($_POST["addtemplate"]) && !$preview)
	{
		$validinfo = true;

		if ($template == "")
		{
			$error .= "<li>".lang("nofieldgiven",array(lang('name')))."</li>";
			$validinfo = false;
		}
		else
		{
			$query = "SELECT template_id from ".cms_db_prefix()."templates WHERE template_name = " . $db->qstr($template);
			$result = $db->Execute($query);

			if ($result && $result->RowCount() > 0)
			{
				$error .= "<li>".lang('templateexists')."</li>";
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
			$newtemplate = new Template();
			$newtemplate->name = $template;
			$newtemplate->content = $content;
			$newtemplate->stylesheet = $stylesheet;
			$newtemplate->encoding = $encoding;
			$newtemplate->active = $active;

			#Perform the addtemplate_pre callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->AddTemplatePre($newtemplate);
				}
			}

			$result = $newtemplate->save();

			if ($result)
			{
				#Perform the addtemplate_post callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->AddTemplatePost($newtemplate);
					}
				}

				audit($newtemplate->id, $template, 'Added Template');
				redirect("listtemplates.php");
				return;
			}
			else
			{
				$error .= "<li>".lang('errorinsertingtemplate')."$query</li>";
			}
		}
	}
}

include_once("header.php");

if (!$access)
{
	print "<h3>".lang('noaccessto', array(lang('addtemplate')))."</h3>";
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
		$data["template"] = $content;
		$data["encoding"] = $encoding;

		$tmpfname = '';
		if (is_writable($config["previews_path"]))
		{
			$tmpfname = tempnam($config["previews_path"], "cmspreview");
		}
		else
		{
			$tmpfname = tempnam(dirname(dirname(__FILE__)) . '/tmp/cache', "cmspreview");
		}
		$handle = fopen($tmpfname, "w");
		fwrite($handle, serialize($data));
		fclose($handle);

?>
<h3><?php echo lang('preview')?></h3>

<iframe name="previewframe" width="90%" height="400" src="<?php echo $config["root_url"]?>/preview.php?tmpfile=<?php echo urlencode(basename($tmpfname))?>" style="margin: 10px; border: 1px solid #8C8A8C;">

</iframe>
<?php

	}
?>

<form method="post" action="addtemplate.php" <?php if($use_javasyntax){echo 'onSubmit="textarea_submit(this, \'content,stylesheet\');"';} ?>>

<h3><?php echo lang('addtemplate')?></h3>

<div class="adminform">

<table width="100%" border="0">

	<tr>
		<td width="100">*<?php echo lang('name')?>:</td>
		<td><input type="text" name="template" maxlength="25" value="<?php echo $template?>" /></td>
	</tr>
	<tr>
		<td>*<?php echo lang('content')?>:</td>
		<td><?php echo create_textarea(false, $content, 'content', 'syntaxHighlight', 'content', $encoding)?></td>
	</tr>
	<tr>
		<td><?php echo lang('stylesheet')?>:</td>
		<td><?php echo create_textarea(false, $stylesheet, 'stylesheet', 'syntaxHighlight', 'stylesheet', $encoding)?></td>
	</tr>
	<tr>
		<td><?php echo lang('encoding')?>:</td>
		<td><input type="text" name="encoding" maxlength="25" value="<?php echo $encoding?>" /></td>
	</tr>
	<tr>
		<td><?php echo lang('active')?>:</td>
		<td><input type="checkbox" name="active" <?php echo ($active == 1?"checked=\"checked\"":"")?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="addtemplate" value="true"/>
		<input type="submit" name="preview" value="<?php echo lang('preview')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
		<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" /></td>
	</tr>

</table>

</div>

</form>

<h4 onclick="expandcontent('helparea')" style="cursor:hand; cursor:pointer"><?php echo lang('help') ?>?</h4>
<div id="helparea" class="expand">
<?php echo lang("helpaddtemplate")?>
</div>
<?php

}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
