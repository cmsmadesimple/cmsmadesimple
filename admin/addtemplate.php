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

if (isset($_POST["cancel"]))
{
	redirect("listtemplates.php");
}

$gCms = cmsms();
$smarty = cms_smarty();
$smarty->assign('action', 'addtemplate.php');

$contentops = $gCms->GetContentOperations();
$templateops = $gCms->GetTemplateOperations();

#Make sure we're logged in and get that user id
check_login();
$userid = get_userid();
$access = check_permission($userid, 'Add Templates');

require_once("header.php");

$dflt_content='
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
<title>{sitename} - {title}</title>
{metadata}
{stylesheet}
</head>
<body>

<!-- start header -->
<div id="header">
  <h1>{sitename}</h1>
</div>
<!-- end header -->

<!-- start menu -->
<div id="menu">
  {menu}
</div>
<!-- end menu -->

<!-- start content -->
<div id="content">
  <h1>{title}</h1>
  {content}
</div>
<!-- end content -->

</body>
</html>
';

$preview = array_key_exists('previewbutton', $_POST);
$submit = array_key_exists('submitbutton', $_POST);
$apply = array_key_exists('applybutton', $_POST);

function &get_template_object()
{
	$template_object = new CmsTemplate();
	if (isset($_REQUEST['template']))
		$template_object->update_parameters($_REQUEST['template']);
	return $template_object;
}

function create_preview(&$template_object)
{
	$config =& cmsms()->GetConfig();
	cmsms()->GetContentOperations()->LoadContentType('Content');
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
$template_object = get_template_object($userid);

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
			$smarty->assign('previewfname', $config["root_url"] . '/index.php?tmpfile=' . urlencode(basename($tmpfname)));
		}
	}
}
else if ($access)
{
	if ($submit || $apply)
	{
		if ($template_object->save())
		{
			if ($submit)
			{
				audit($template_object->id, $template_object->name, 'Added Template');
				redirect("listtemplates.php");
			}
		}
	}
}

//Add the header
$smarty->assign('header_name', $themeObject->ShowHeader('addtemplate'));

//Can we preview?
$smarty->assign('can_preview', true);

//How about apply?
$smarty->assign('can_apply', false);

//Setup the template object
$smarty->assign_by_ref('template_object', $template_object);

$smarty->assign('content_box', create_textarea(false, $template_object->content, 'template[content]', 'pagebigtextarea', '', $template_object->encoding));
$smarty->assign('encoding_dropdown', create_encoding_dropdown('template[encoding]', $template_object->encoding));

$smarty->display('addtemplate.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
