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
$contentops = $gCms->GetContentOperations();
$templateops = $gCms->GetTemplateOperations();

#Make sure we're logged in and get that user id
check_login();
$userid = get_userid();
$user = CmsLogin::get_current_user();

$language = CmsMultiLanguage::get_user_default_language($user);
$smarty->assign('language', $language);

//Need to know where we're submitting to
$smarty->assign('action', 'addcontent.php');

//See if some variables are returned
$page_type = coalesce_key($_REQUEST, 'page_type', 'content');
$orig_page_type = coalesce_key($_POST, 'orig_page_type', 'content');
$current_language = coalesce_key($_POST, 'current_language', $language);
$orig_current_language = coalesce_key($_POST, 'orig_current_language', $language);
$preview = array_key_exists('previewbutton', $_POST);
$submit = array_key_exists('submitbutton', $_POST);
$apply = array_key_exists('applybutton', $_POST);

$cms_ajax = new CmsAjax();

$cms_ajax->register_function('ajaxpreview');
$cms_ajax->register_function('change_block_type');

$cms_ajax->process_requests();

#See what kind of permissions we have
$access = (check_permission($userid, 'Add Pages') || check_permission($userid, 'Modify Page Structure'));

require_once("header.php");
CmsAdminTheme::inject_header_text($cms_ajax->get_javascript()."\n");

#No access?  Just display an error and exit.
if (!$access) {
	$smarty->assign('error_message', lang('noaccessto',array(lang('addcontent'))));
	$smarty->display('elements/pageerror.tpl');
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
	global $gCms;
	$contentops =& $gCms->GetContentOperations();

	$newcontenttype = strtolower($page_type);
	CmsContentOperations::LoadContentType($newcontenttype);
	$tmpobj = CmsContentOperations::CreateNewContent($newcontenttype);

	$tmpobj->params = $page_object->params;
	$tmpobj->mProperties = $page_object->mProperties;

	$page_object = $tmpobj;
}

function &get_page_object(&$page_type, &$orig_page_type, $userid, $params, $lang = 'en_US')
{
	$page_object = new StdClass();

	if (isset($params["serialized_content"]))
	{
		$page_object = unserialize_object($params["serialized_content"]);
		
		//Do this first -- so alias gets set
		$page_object->set_property_value('name', $_REQUEST['name'], $lang);
		$page_object->set_property_value('menu_text', $_REQUEST['menu_text'], $lang);

		$page_object->update_parameters($params['content'], $lang);
		if (strtolower(get_class($page_object)) != $page_type)
		{
			copycontentobj($page_object, $page_type);
			$orig_page_type = $page_type;
		}
	}
	else
	{
		$page_object = CmsContentOperations::CreateNewContent($page_type);
		$page_object->owner_id = $userid;
		$page_object->active = TRUE;
		$page_object->show_in_menu = TRUE;
		$page_object->last_modified_by = $userid;
		$page_object->parent_id = coalesce_key($_REQUEST, 'parent_id', '-1');
	}
	
	return $page_object;
}

function create_preview(&$page_object)
{
	$config =& cmsms()->GetConfig();
	$templateops = cmsms()->GetTemplateOperations();
	$templateobj = $templateops->LoadTemplateById($page_object->template_id);

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

function change_block_type($params, $block_id, $new_block_type)
{
	$page_type = coalesce_key($params, 'page_type', 'content');
	$orig_page_type = coalesce_key($params, 'orig_page_type', 'content');
	
	$userid = get_userid();
	$config = cms_config();
	$smarty = cms_smarty();
	
	$language = get_preference($userid, 'default_cms_language', 'en_US');
	$orig_current_language = coalesce_key($params, 'orig_current_language', $language);

	$page_object = get_page_object($page_type, $orig_page_type, $userid, $params, $orig_current_language);
	$type_param = $block_id . '-block-type';
	$div_id = 'content-form-' . $block_id;
	$page_object->$type_param = $new_block_type;
	
	$resp = new CmsAjaxResponse();

	$resp->modify_attribute("#serialized_content", "value", serialize_object($page_object));
	
	$smarty->_compile_source('metadata template', $page_object->create_block_type($block_id), $_compiled);
	@ob_start();
	$smarty->_eval('?>' . $_compiled);
	$result = @ob_get_contents();
	@ob_end_clean();
	
	$resp->modify_html("#$div_id", $result);
	
	return $resp->get_result();
}

function ajaxpreview($params)
{
	$page_type = coalesce_key($params, 'page_type', 'content');
	$orig_page_type = coalesce_key($params, 'orig_page_type', 'content');
	$userid = get_userid();
	
	$language = get_preference($userid, 'default_cms_language', 'en_US');
	$orig_current_language = coalesce_key($params, 'orig_current_language', $language);
	
	$config =& cmsms()->GetConfig();

	$page_object = get_page_object($page_type, $orig_page_type, $userid, $params, $orig_current_language);
	$tmpfname = create_preview($page_object);
	$url = $config["root_url"] . '/index.php?tmpfile=' . urlencode(basename($tmpfname));
	
	$resp = new CmsAjaxResponse();

	$resp->modify_attribute("#previewframe", "src", $url);
	$resp->modify_attribute("#serialized_content", "value", serialize_object($page_object));

	return $resp->get_result();
}

//Get a working page object
$page_object = get_page_object($page_type, $orig_page_type, $userid, $_REQUEST, $orig_current_language);

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
				audit($page_object->id, $page_object->name, 'Added Content');
				if ($submit)
					redirect('listcontent.php?message=contentadded');
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

//Language related stuff
$smarty->assign('languages', CmsMultiLanguage::get_enabled_languages_as_hash());
$smarty->assign('current_language', $current_language);
$smarty->assign('orig_current_language', $current_language); //orig_current_language should match current_language now that saves and stuff are done

//Can we preview?
$smarty->assign('can_preview', $page_object->preview);

//How about apply?
$smarty->assign('can_apply', false);

//Set the pagetypes
$smarty->assign('page_types', array_combine(array_map('get_type', $gCms->contenttypes), array_map('get_friendlyname', $gCms->contenttypes)));
$smarty->assign('selected_page_type', $page_type);

//Set the parent dropdown
$smarty->assign('show_parent_dropdown', $access);
$smarty->assign('parent_dropdown', $contentops->CreateHierarchyDropdown('', $page_object->parent_id, 'content[parent_id]'));

//Se the template dropdown
$smarty->assign('template_names', $templateops->TemplateDropdown('content[template_id]', $page_object->template_id, 'onchange="document.contentform.submit()"'));

//Set the users
$userops = $gCms->GetUserOperations();
$smarty->assign('show_owner_dropdown', false);
$smarty->assign('owner_dropdown', $userops->GenerateDropdown($page_object->owner_id, 'content[owner_id]'));

//Name and menu text are multilang, use the parameters
$smarty->assign('name', $page_object->get_property_value('name'), $current_language);
$smarty->assign('menu_text', $page_object->get_property_value('menu_text'), $current_language);

//Any included smarty templates for this page type?
$smarty->assign('include_templates', $page_object->add_template($smarty, $current_language));

//Other fields that aren't easily done with smarty
$smarty->assign('metadata_box', create_textarea(false, $page_object->metadata, 'content[metadata]', 'pagesmalltextarea', 'content_metadata', '', '', '80', '6'));

$smarty->assign('attribute_defns', cms_orm('CmsAttributeDefinition')->find_all_by_module_and_extra_attr('Core', 'Content'));

//extra buttons
$ExtraButtons = array(
	array(
		'name'    => 'preview',
		'class'   => 'positive preview',
		'image'   => '',
		'caption' => lang('preview'),
		'onclick' => "$('#page_tabs').tabsClick(4);return false;"
	),
);

$smarty->assign('DisplayButtons', $ExtraButtons);

$smarty->display('addcontent.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>