<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

if (isset($_POST["cancel"]))
{
	redirect("listtemplates.php");
}

$gCms = cmsms();
$smarty = cms_smarty();
$smarty->assign('action', 'edittemplate.php');

// Make sure we have permissions for this page
$userid = get_userid();
$access = check_permission($userid, 'Modify Templates');

require_once("header.php");

$preview = array_key_exists('previewbutton', $_POST);
$submit = array_key_exists('submitbutton', $_POST);
$apply = array_key_exists('applybutton', $_POST);
$template_id = coalesce_key($_REQUEST, 'template_id', '-1');

function &get_template_object($template_id)
{
	$template_object = cmsms()->cms_template->find_by_id($template_id);
	
	if (isset($_REQUEST['template']))
		$template_object->update_parameters($_REQUEST['template']);

	return $template_object;
}

function create_preview($template_object)
{
	$config = cms_config();
	$page_object = Content::create_preview_object($template_object);

	$tmpfname = '';
	if (is_writable($config["previews_path"]))
	{
		$tmpfname = tempnam($config["previews_path"], "cmspreview");
	}
	else
	{
		$tmpfname = tempnam(TMP_CACHE_LOCATION, "cmspreview");
	}
	$handle = fopen($tmpfname, "w");
	fwrite($handle, serialize(array($template_object, $page_object)));
	fclose($handle);
	
	return $tmpfname;
}

//Get a working page object
$template_object = get_template_object($template_id);

//Preview?
$smarty->assign('showpreview', false);
if ($preview)
{
	if (!$template_object->_call_validation())
	{
		$tmpfname = create_preview($template_object);
		if ($tmpfname != '')
		{
			$smarty->assign('showpreview', true);
			$smarty->assign('previewfname', CmsConfig::get("root_url") . '/index.php?tmpfile=' . urlencode(basename($tmpfname)));
		}
	}
}
else if ($access)
{
	if ($submit || $apply)
	{
		if ($template_object->save())
		{
			audit($template_object->id, $template_object->name, 'Edited Template');
			if ($submit)
				CmsResponse::redirect("listtemplates.php");
		}
	}
}

//Add the header
$smarty->assign('header_name', $themeObject->ShowHeader('edittemplate'));

//Setup the template object
$smarty->assign_by_ref('template_object', $template_object);

$smarty->assign('content_box', create_textarea(false, $template_object->content, 'template[content]', 'pagebigtextarea', '', $template_object->encoding));

//extra buttons
$ExtraButtons = array(
		      array(
			    'name'    => 'previewbutton',
			    'class'   => 'preview',
			    'caption' => lang('preview'),
			    ),
			   array(
			    'name'    => 'applybutton',
			    'class'   => 'apply',
			    'caption' => lang('apply'),
			    ),
		      );

$smarty->assign('DisplayButtons', $ExtraButtons);

$smarty->display('addtemplate.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>