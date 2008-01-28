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

$language = get_preference($userid, 'default_cms_language', 'en_US');
$smarty->assign('language', $language);

//Need to know where we're submitting to
$smarty->assign('action', 'editcontent.php');

//See if some variables are returned
$start_tab = coalesce_key($_REQUEST, 'start_tab', isset($_REQUEST['permission_add_submit']) ? '3' : '1');
$content_id = coalesce_key($_REQUEST, 'content_id', '-1');
$page_type = coalesce_key($_REQUEST, 'page_type', 'content');
$orig_page_type = coalesce_key($_POST, 'orig_page_type', 'content');
$current_language = coalesce_key($_POST, 'current_language', $language);
$orig_current_language = coalesce_key($_POST, 'orig_current_language', $language);
$preview = array_key_exists('previewbutton', $_POST);
$submit = array_key_exists('submitbutton', $_POST);
$apply = array_key_exists('applybutton', $_POST);

$dateformat = get_preference(get_userid(),'date_format_string','%x %X');

define('XAJAX_DEFAULT_CHAR_ENCODING', $config['admin_encoding']);
require_once(dirname(dirname(__FILE__)) . '/lib/xajax/xajax.inc.php');
$xajax = new xajax();
$xajax->registerFunction('ajaxpreview');
$xajax->registerFunction('change_block_type');

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
	$contentops = cmsms()->GetContentOperations();

	$newcontenttype = strtolower($page_type);
	//$contentops->LoadContentType($newcontenttype);
	$tmpobj = $contentops->CreateNewContent($newcontenttype);

	$tmpobj->params = $page_object->params;
	$tmpobj->mProperties = $page_object->mProperties;

	$page_object = $tmpobj;
}

function &get_page_object(&$page_type, &$orig_page_type, $userid, $content_id, $params, $lang = 'en_US')
{
	global $gCms;

	$page_object = new StdClass();

	if (isset($params["serialized_content"]))
	{
		$page_object = unserialize_object($params["serialized_content"]);
		
		//Do this first -- so alias gets set
		$page_object->set_property_value('name', $_REQUEST['name'], $lang);
		$page_object->set_property_value('menu_text', $_REQUEST['menu_text'], $lang);

		$page_object->update_parameters($params['content'], $lang, get_magic_quotes_gpc());
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

function load_permissions($params, $page_object)
{
	$smarty = cms_smarty();

	$permission_defns = CmsAcl::get_permission_definitions('Core', 'Page');
	$permission_list = array();

	if (isset($params["serialized_permissions_list"]))
	{
		$permission_list = unserialize_object($params["serialized_permissions_list"]);
		$permission_defns = unserialize_object($params["serialized_permissions_defns"]);

		if (isset($params['permission_add_submit']))
		{
			for ($x = 0; $x < count($permission_defns); $x++)
			{
				if ($params['permission_id'] == $permission_defns[$x]['id'])
				{
					$add_me = true;

					$group_id = -1;
					$gorup_name = lang('Everyone');
					
					if (isset($params['group_id']) && $params['group_id'] > -1)
					{
						$group = cms_orm()->cms_group->find_by_id($params['group_id']);
						if ($group)
						{
							$group_id = $group->id;
							$group_name = $group->name;
						}
						else
						{
							$add_me = false;
						}
					}
					
					$permission_defns[$x]['entries'][] = array('has_access' => $params['permission_allow'] == 1 ? lang('true') : lang('false'), 'object_name' => $page_object->get_property_value('menu_text', $current_language) . ' - ' . $page_object->hierarchy, 'group_id' => $group_id, 'group_name' => $group_name, 'object_id' => $page_object->id);
				}
			}
		}
		else
		{
			foreach ($params as $k=>$v)
			{
				if (starts_with($k, 'delete_permission'))
				{
					list($blah, $group_id, $permission_id) = explode('-', $k);
					if ($group_id && $permission_id)
					{
						for ($x = 0; $x < count($permission_defns); $x++)
						{
							if ($permission_id == $permission_defns[$x]['id'])
							{
								for ($y = 0; $y < count($permission_defns[$x]['entries']); $y++)
								{
									if ($permission_defns[$x]['entries'][$y]['group_id'] == $group_id)
									{
										unset($permission_defns[$x]['entries'][$y]);
										break;
									}
								}
							}
						}
					}
				}
			}
		}
	}
	else
	{
		for ($x = 0; $x < count($permission_defns); $x++)
		{
			$permission_list[$permission_defns[$x]['id']] = $permission_defns[$x]['name'];
			$entries = CmsAcl::get_permissions('Core', 'Page', $permission_defns[$x]['name'], $page_object->id, true);
			foreach ($entries as &$oneentry)
			{
				$oneentry['object_name'] = '';
				if ($oneentry['object_id'] == 1)
				{
					$oneentry['object_name'] = lang('root');
				}
				else
				{
					$content = cms_orm()->content->find_by_id($oneentry['object_id']);
					if ($content != null)
					{
						$oneentry['object_name'] = $content->get_property_value('menu_text', $current_language) . ' - ' . $content->hierarchy;
					}
				}
			}
			$permission_defns[$x]['entries'] = $entries;
		}
	}
	
	$smarty->assign('permission_defns', $permission_defns);
	$smarty->assign('permission_list', $permission_list);
	$smarty->assign("serialized_permissions_list", serialize_object($permission_list));
	$smarty->assign("serialized_permissions_defns", serialize_object($permission_defns));
	
	return $permission_defns;
}

function save_permissions($params, $page_object, $permission_defns)
{
	for ($x = 0; $x < count($permission_defns); $x++)
	{
		$dfn = $permission_defns[$x];
		for ($y = 0; $y < count($permission_defns[$x]['entries']); $y++)
		{
			$perm = $permission_defns[$x]['entries'][$y];
			if ($perm['object_id'] == $page_object->id)
			{
				CmsAcl::set_permission('Core', 'Page', $dfn['name'], $page_object->id, $perm['group_id'], $perm['has_access'] == 'True' ? 1 : 0);
			}
		}
	}
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

function change_block_type($params, $block_id, $new_block_type)
{
	$content_id = coalesce_key($params, 'content_id', '-1');
	$page_type = coalesce_key($params, 'page_type', 'content');
	$orig_page_type = coalesce_key($params, 'orig_page_type', 'content');
	
	$userid = get_userid();
	$config = cms_config();
	$smarty = cms_smarty();
	
	$language = get_preference($userid, 'default_cms_language', 'en_US');
	$orig_current_language = coalesce_key($params, 'orig_current_language', $language);

	$page_object = get_page_object($page_type, $orig_page_type, $userid, $content_id, $params, $orig_current_language);
	$type_param = $block_id . '-block-type';
	$div_id = 'content-form-' . $block_id;
	$page_object->$type_param = $new_block_type;
	
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("serialized_content", "value", serialize_object($page_object));
	
	$smarty->_compile_source('metadata template', $page_object->create_block_type($block_id), $_compiled);
	@ob_start();
	$smarty->_eval('?>' . $_compiled);
	$result = @ob_get_contents();
	@ob_end_clean();
	
	$objResponse->addAssign($div_id, 'innerHTML', $result);
	
	return $objResponse->getXML();
}

function ajaxpreview($params)
{
	$content_id = coalesce_key($params, 'content_id', '-1');
	$page_type = coalesce_key($params, 'page_type', 'content');
	$orig_page_type = coalesce_key($params, 'orig_page_type', 'content');
	$userid = get_userid();
	
	$language = get_preference($userid, 'default_cms_language', 'en_US');
	$orig_current_language = coalesce_key($params, 'orig_current_language', $language);
	
	$config =& cmsms()->GetConfig();

	$page_object = get_page_object($page_type, $orig_page_type, $userid, $content_id, $params, $orig_current_language);
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
$page_object = get_page_object($page_type, $orig_page_type, $userid, $content_id, $_REQUEST, $orig_current_language);

//Load permissions (from db or serialized versions) and put them into smarty for dispaly on the template
$permission_defns = load_permissions($_REQUEST, $page_object);

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
			$contentops->set_all_hierarchy_positions($page_object->lft);
			save_permissions($params, $page_object, $permission_defns);
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

//Language related stuff
$smarty->assign('languages', CmsMultiLanguage::get_enabled_languages_as_hash());
$smarty->assign('current_language', $current_language);
$smarty->assign('orig_current_language', $current_language); //orig_current_language should match current_language now that saves and stuff are done

//Can we preview?
$smarty->assign('can_preview', $page_object->preview);

//Set the pagetypes
$smarty->assign('page_types', array_combine(array_map('get_type', $gCms->contenttypes), array_map('get_friendlyname', $gCms->contenttypes)));
$smarty->assign('selected_page_type', $page_type);

//Set the parent dropdown
$smarty->assign('show_parent_dropdown', $access);
$smarty->assign('parent_dropdown', $contentops->CreateHierarchyDropdown($page_object->id, $page_object->parent_id, 'content[parent_id]'));

//Se the template dropdown
$smarty->assign('template_names', $templateops->TemplateDropdown('content[template_id]', $page_object->template_id, 'onchange="document.contentform.submit()"'));

$smarty->assign('group_dropdown', CmsGroup::get_groups_for_dropdowm(true));

//Name and menu text are multilang, use the parameters
$smarty->assign('name', $page_object->get_property_value('name', $current_language));
$smarty->assign('menu_text', $page_object->get_property_value('menu_text', $current_language));

//Set the users
$userops =& $gCms->GetUserOperations();
$smarty->assign('show_owner_dropdown', false);
$smarty->assign('owner_dropdown', $userops->GenerateDropdown($page_object->owner_id, 'content[owner_id]'));

//Any included smarty templates for this page type?
$smarty->assign('include_templates', $page_object->edit_template($smarty, $current_language));

//Other fields that aren't easily done with smarty
$smarty->assign('metadata_box', create_textarea(false, $page_object->metadata, 'content[metadata]', 'pagesmalltextarea', 'content_metadata', '', '', '80', '6'));

$smarty->assign('start_tab', $start_tab);

$ExtraButtons = array(
	array(
		'name'    => 'applybutton',
		'class'   => 'positive apply',
		'image'   => '',
		'caption' => lang('apply'),
	)
);

if ($page_object->preview)
{
	$ExtraButtons[] = array(
			'name'    => 'previewbutton',
			'class'   => 'positive preview',
			'image'   => '',
			'caption' => lang('preview'),
	);
}

$smarty->assign('DisplayButtons', $ExtraButtons);

$smarty->display('addcontent.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>