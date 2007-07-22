<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
	redirect("listcontent.php");
}

$gCms = cmsms();
$smarty = cms_smarty();
$contentops =& $gCms->GetContentOperations();
$templateops =& $gCms->GetTemplateOperations();

#Make sure we're logged in and get that user id
check_login();
$userid = get_userid();

//Need to know where we're submitting to
$smarty->assign('action', 'editcontent.php');

//See if some variables are returned
$content_id = coalesce_key($_REQUEST, 'content_id', '-1');
$page_type = coalesce_key($_REQUEST, 'page_type', 'content');
$orig_page_type = coalesce_key($_POST, 'orig_page_type', 'content');
$preview = array_key_exists('previewbutton', $_POST);
$submit = array_key_exists('submitbutton', $_POST);
$apply = array_key_exists('applybutton', $_POST);

require_once(dirname(dirname(__FILE__)) . '/lib/xajax/xajax.inc.php');
$xajax = new xajax();
$xajax->registerFunction('ajaxpreview');

$xajax->processRequests();

#See what kind of permissions we have
$access = check_ownership($userid, $content_id) || check_permission($userid, 'Modify Any Page');
$adminaccess = $access;
if (!$access)
	$access = check_authorship($userid, $content_id);

require_once("header.php");
CmsAdminTheme::inject_header_text($xajax->getJavascript('../lib/xajax')."\n");

#No access?  Just display an error and exit.
if (!$access) {
	$smarty->assign('error_message', lang('noaccessto',array(lang('addcontent'))));
	$smarty->display('pageerror.tpl');
	include_once('footer.php');
	exit;
}

function get_type($n)
{
	return $n->type;
}

function get_friendlyname($n)
{
	return $n->friendlyname;
}

function copycontentobj(&$page_object, $page_type)
{
	$contentops = cmsms()->GetContentOperations();

	$newcontenttype = strtolower($page_type);
	//$contentops->LoadContentType($newcontenttype);
	$tmpobj = $contentops->CreateNewContent($newcontenttype);

	$tmpobj->params = $page_object->params;
	$tmpobj->mProperties = $page_object->mProperties;

	$page_object = $tmpobj;
}

function &get_page_object(&$page_type, &$orig_page_type, $userid, $content_id, $params)
{
	global $gCms;

	$page_object = new StdClass();

	if (isset($params["serialized_content"]))
	{
		$page_object = unserialize_object($params["serialized_content"]);
		$page_object->update_parameters($params['content']);
		if (strtolower(get_class($page_object)) != $page_type)
		{
			copycontentobj($page_object, $page_type);
			$orig_page_type = $page_type;
		}
	}
	else
	{
		$page_object = cmsms()->GetContentOperations()->LoadContentFromId($content_id);
		$page_type = $page_object->type;
		$orig_page_type = $page_object->type;
		$page_object->last_modified_by = $userid;
	}
	
	return $page_object;
}

function create_preview(&$page_object)
{
	$config =& cmsms()->GetConfig();
	$templateobj = cmsms()->template->find_by_id($page_object->template_id);

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
	fwrite($handle, serialize(array($templateobj, $page_object)));
	fclose($handle);
	
	return $tmpfname;
}

function ajaxpreview($params)
{
	$content_id = coalesce_key($params, 'content_id', '-1');
	$page_type = coalesce_key($params, 'page_type', 'content');
	$orig_page_type = coalesce_key($params, 'orig_page_type', 'content');
	$userid = get_userid();
	
	$config =& cmsms()->GetConfig();

	$page_object = get_page_object($page_type, $orig_page_type, $userid, $content_id, $params);
	$tmpfname = create_preview($page_object);
	$url = $config["root_url"] . '/index.php?tmpfile=' . urlencode(basename($tmpfname));
	
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("previewframe", "src", $url);
	$objResponse->addAssign("serialized_content", "value", serialize_object($page_object));
	$count = 0;

	foreach (array("content", "advanced") as $tabname)
	{
		$objResponse->addScript("Element.removeClassName('".$tabname."', 'active');Element.removeClassName('".$tabname."_c', 'active');$('".$tabname."_c').style.display = 'none';");
	}
	$objResponse->addScript("Element.addClassName('preview', 'active');Element.addClassName('preview_c', 'active');$('preview_c').style.display = '';");

	return $objResponse->getXML();
}

//Get a working page object
$page_object = get_page_object($page_type, $orig_page_type, $userid, $content_id, $_REQUEST);

//Preview?
$smarty->assign('showpreview', false);
if ($preview)
{
	if (!$page_object->_call_validation())
	{
		$tmpfname = create_preview($page_object);
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
		if ($page_object->save())
		{
			$contentops->SetAllHierarchyPositions();
			if ($submit)
			{
				audit($page_object->id, $page_object->name, 'Edited Content');
				if ($submit)
					redirect('listcontent.php?message=contentupdated');
			}
		}
	}
}

//Set some page variables
$smarty->assign('header_name', $themeObject->ShowHeader('addcontent'));

//Setup the page object
$smarty->assign_by_ref('page_object', $page_object);
$smarty->assign('serialized_object', serialize_object($page_object));
$smarty->assign('orig_page_type', $orig_page_type);

//Can we preview?
$smarty->assign('can_preview', $page_object->preview);

//How about apply?
$smarty->assign('can_apply', true);

//Set the pagetypes
$smarty->assign('page_types', array_combine(array_map('get_type', $gCms->contenttypes), array_map('get_friendlyname', $gCms->contenttypes)));
$smarty->assign('selected_page_type', $page_type);

//Set the parent dropdown
$smarty->assign('show_parent_dropdown', $access);
$smarty->assign('parent_dropdown', $contentops->CreateHierarchyDropdown($page_object->id, $page_object->parent_id, 'content[parent_id]'));

//Se the template dropdown
$smarty->assign('template_names', $templateops->TemplateDropdown('content[template_id]', $page_object->template_id, 'onchange="document.contentform.submit()"'));

//Set the users
$userops =& $gCms->GetUserOperations();
$smarty->assign('show_owner_dropdown', false);
$smarty->assign('owner_dropdown', $userops->GenerateDropdown($page_object->owner_id, 'content[owner_id]'));

//Any included smarty templates for this page type?
$smarty->assign('include_templates', $page_object->edit_template($smarty));

//Other fields that aren't easily done with smarty
$smarty->assign('metadata_box', create_textarea(false, $page_object->metadata, 'content[metadata]', 'pagesmalltextarea', 'content_metadata', '', '', '80', '6'));

$smarty->display('addcontent.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>