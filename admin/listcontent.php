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

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'cmsms.api.php');

check_login();

$cms_ajax = new CmsAjax();

$cms_ajax->register_function('content_setactive'); 
$cms_ajax->register_function('content_setinactive');
$cms_ajax->register_function('content_list_ajax');
$cms_ajax->register_function('content_setdefault');
$cms_ajax->register_function('content_expandall');
$cms_ajax->register_function('content_collapseall');
$cms_ajax->register_function('content_toggleexpand');
$cms_ajax->register_function('content_move');
$cms_ajax->register_function('content_move_new');
$cms_ajax->register_function('content_delete');
$cms_ajax->register_function('context_menu');
$cms_ajax->register_function('content_select');
$cms_ajax->register_function('content_new');
$cms_ajax->register_function('save_page');
$cms_ajax->register_function('check_url');
$cms_ajax->register_function('check_alias');

function check_modify_all($userid)
{
	return check_permission($userid, 'Modify Any Page');
}

$gCms = cmsms();
$smarty = cms_smarty();
$config = cms_config();
$contentops = $gCms->GetContentOperations();
$templateops = $gCms->GetTemplateOperations();

$urlext = '?' . CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY];
$smarty->assign('secure_name', CMS_SECURE_PARAM_NAME);
$smarty->assign('secure_key', $_SESSION[CMS_USER_KEY]);
$smarty->assign('urlext', $urlext);
$thisurl = basename(__FILE__) . $urlext;
$smarty->assign('thisurl', $thisurl);

$opt = array();
foreach (cms_orm('CmsTemplate')->find_all_by_active(true) as $tpl)
{
	$opt[$tpl->id] = $tpl->name;
}
$smarty->assign('template_items', $opt);

$opt = array();
$opt['CmsPage'] = 'Standard Page';
$opt['CmsErrorPage'] = 'Error Page';
$smarty->assign('page_types', $opt);

$opt = array();
$opt['CmsHtmlContentType'] = 'HTML Content';
$smarty->assign('content_types', $opt);

//include_once("../lib/classes/class.admintheme.inc.php");

$smarty->assign('theme_object', CmsAdminTheme::get_instance());
$cms_ajax->process_requests();

CmsAdminTheme::inject_header_text($cms_ajax->get_javascript()."\n");

if (isset($_GET["makedefault"]))
{
	setdefault($_GET['makedefault']);
	redirect('listcontent.php');
}

// check if we're activating a page 
if (isset($_GET["setactive"])) 
{ 
	setactive($_GET["setactive"], true); 
}

// perhaps we're deactivating a page instead? 
if (isset($_GET["setinactive"])) 
{ 
	setactive($_GET["setinactive"], false); 
}

if (isset($_GET['expandall']))
{
	expandall();
}

if (isset($_GET['collapseall']))
{
	collapseall();
}

if (isset($_GET['deletecontent']))
{
	deletecontent($_GET['deletecontent']);
	redirect('listcontent.php'.$urlext);
}

if (isset($_GET['direction']))
{
	movecontent($_GET['content_id'], $_GET['parent_id'], $_GET['direction']);
}

if (isset($_GET['col']) && isset($_GET['content_id']))
{
	toggleexpand($_GET['content_id'], $_GET['col']=='1'?true:false);
}

$userid = get_userid();
$smarty->assign('language', get_preference($userid, 'default_cms_language', 'en_US'));
$headtext = $cms_ajax->get_javascript();
include_once("header.php");
$cms_ajax->process_requests();

$smarty->assign('header_name', $themeObject->ShowHeader('currentpages'));

setup_smarty($themeObject);

set_bulk_actions();

$smarty->assign('content_list', display_content_list());

$smarty->display('listcontent.tpl');

include_once("footer.php");

function setup_smarty($themeObject)
{
	$smarty = cms_smarty();
	$userid = get_userid();

	//Set permissions
	set_permissions($smarty);
	
	//Setup the images
	$smarty->assign('newobject_image', $themeObject->DisplayImage('icons/system/newobject.gif', lang('addcontent'),'','','systemicon'));
	$smarty->assign('reorder_image', $themeObject->DisplayImage('icons/system/reorder.gif', lang('reorderpages'),'','','systemicon'));
	$smarty->assign('expandall_image', $themeObject->DisplayImage('icons/system/expandall.gif', lang('expandall'),'','','systemicon'));
	$smarty->assign('collapseall_image', $themeObject->DisplayImage('icons/system/contractall.gif', lang('contractall'),'','','systemicon'));
	$smarty->assign('true_image', $themeObject->DisplayImage('icons/system/true.gif', lang('true'),'','','systemicon'));
	$smarty->assign('setfalse_image', $themeObject->DisplayImage('icons/system/true.gif', lang('setfalse'),'','','systemicon'));
	$smarty->assign('settrue_image', $themeObject->DisplayImage('icons/system/false.gif', lang('settrue'),'','','systemicon'));
	$smarty->assign('expand_image', $themeObject->DisplayImage('icons/system/expand.gif', lang('expand'),'','','systemicon'));
	$smarty->assign('contract_image', $themeObject->DisplayImage('icons/system/contract.gif', lang('contract'),'','','systemicon'));
	$smarty->assign('down_image', $themeObject->DisplayImage('icons/system/arrow-d.gif', lang('down'),'','','systemicon'));
	$smarty->assign('up_image', $themeObject->DisplayImage('icons/system/arrow-u.gif', lang('up'),'','','systemicon'));
	$smarty->assign('view_image', $themeObject->DisplayImage('icons/system/view.gif', lang('view'),'','','systemicon'));
	$smarty->assign('edit_image', $themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon'));
	$smarty->assign('delete_image', $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon'));

	//Handle any expand/collapse nodes
	$opened_items = array();
	if (get_preference($userid, 'collapse', '') != '')
	{
		$tmp  = explode('.',get_preference($userid, 'collapse'));
		foreach ($tmp as $thisCol)
		{
			$colind = substr($thisCol,0,strpos($thisCol,'='));
			if ($colind != '')
				$opened_items[] = $colind;
		}
	}
	$smarty->assign_by_ref('opened_items', $opened_items);

	//Set some page variables
	$smarty->assign('content', cmsms()->GetHierarchyManager()->getRootNode());

	$smarty->assign('addcontent_url','editcontent.php');
	
	set_bulk_actions();
	
	$smarty->assign('bulk_content_ops', bulkcontentoperations::get_operation_list());
}

function set_permissions(&$smarty)
{
	$userid = get_userid();

	$smarty->assign('add_pages', check_permission($userid, 'Add Pages'));
	$smarty->assign('modify_page_structure', check_permission($userid, 'Modify Page Structure'));
	$smarty->assign('check_modify_all', check_permission($userid, 'Modify Any Page'));
	$smarty->assign('remove_pages', check_permission($userid, 'Remove Pages'));
	$smarty->assign('mypages', array()); //TODO: Fix me!
}

function display_content_list()
{
	//$userid = get_userid();
	//$themeObject = CmsAdminTheme::get_theme_for_user($userid);
	$admin_theme = CmsAdminTheme::get_instance();
	$smarty = cms_smarty();
	setup_smarty($admin_theme);
	return $smarty->fetch('listcontent-tree.tpl');
}

function content_list_ajax()
{
	$resp = new CmsAjaxResponse();

	$resp->replace_html('#contentlist', display_content_list());
	//$resp->script('set_context_menu();');
	
	return $resp->get_result();
}

function content_setactive($contentid) 
{	
	$resp = new CmsAjaxResponse();
	
	setactive($contentid, true);
	
	$resp->replace_html('#contentlist', display_content_list());
	//$resp->script('set_context_menu();');
	$resp->script("$('#content_span_{$contentid}').highlight('#ff0', 1500);");
	
	return $resp->get_result();
}

function content_setinactive($contentid) 
{ 	
	$resp = new CmsAjaxResponse();
	
	setactive($contentid, false);
	
	$resp->replace_html('#contentlist', display_content_list());
	//$resp->script('set_context_menu();');
	$resp->script("$('#content_span_{$contentid}').highlight('#ff0', 1500);");
	
	return $resp->get_result();
}

function set_bulk_actions()
{
	$userid = get_userid();
	//
	// This is the start of the output
	//
	if( check_permission($userid, 'Remove Pages') || check_permission($userid, 'Manage All Content') )
	{
		bulkcontentoperations::register_function(lang('delete'),'delete');
	}
	if (check_permission($userid, 'Manage All Content'))
	{
		bulkcontentoperations::register_function(lang('active'),'active');
		bulkcontentoperations::register_function(lang('inactive'),'inactive');
		bulkcontentoperations::register_function(lang('cachable'),'setcachable');
		bulkcontentoperations::register_function(lang('noncachable'),'setnoncachable');
		bulkcontentoperations::register_function(lang('showinmenu'),'showinmenu');
		bulkcontentoperations::register_function(lang('hidefrommenu'),'hidefrommenu');
	}
	if (check_permission($userid, 'Modify Any Page') || check_permission($userid, 'Manage All Content'))
	{
		bulkcontentoperations::register_function(lang('settemplate'),'settemplate');
	}
}

function setdefault($page_id)
{
	$userid = get_userid();
	
	$result = false;

	if (check_modify_all($userid))
	{
		$old = cms_orm('CmsPage')->find_by_default_content(1);
		if ($old)
		{
			$old->default_content = false;
			$old->save();
		}
		
		$new = cms_orm('CmsPage')->load($page_id);
		if ($new)
		{
			$new->active = true;
			$new->default_content = true;
			$new->save();
		}

		$result = true;
		CmsCache::clear();
	}
	return $result;
}

function content_setdefault($page_id)
{
	$resp = new CmsAjaxResponse();
	
	setdefault($page_id);

	$resp->replace_html('#contentlist', display_content_list());
	$resp->script("$('#tr_{$page_id} > td').highlight('#ff0', 1500);");
	//$resp->script('set_context_menu();');

	return $resp->get_result();
}

function content_expandall()
{
	$resp = new CmsAjaxResponse();
	
	expandall();

	$resp->replace_html('#contentlist', display_content_list());
	//$resp->script('set_context_menu();');

	return $resp->get_result();
}

function content_collapseall()
{
	$resp = new CmsAjaxResponse();
	
	collapseall();

	$resp->replace_html('#contentlist', display_content_list());
	//$resp->script('set_context_menu();');

	return $resp->get_result();
}

function context_menu($content_id)
{
	$smarty = cms_smarty();
	$resp = new CmsAjaxResponse();
	
	set_permissions($smarty);
	
	$content = cms_orm('content')->find_by_id(substr($content_id, strlen('content_span_')));
	$smarty->assign_by_ref('current', $content);

	$resp->replace_html('#context_menu', $smarty->fetch('listcontent-context_menu.tpl'));
	
	return $resp->get_result();	
}

function expandall()
{
	$userid = get_userid();
	//$all = cmsms()->GetContentOperations()->GetAllContent(false);
	$all = cms_orm('CmsPage')->find_all(array('order' => 'lft ASC'));
	$cs = '';
	foreach ($all as &$thisitem)
	{
		if ($thisitem->has_children())
		{
			$cs .= $thisitem->id . '=1.';
		}
	}
	set_preference($userid, 'collapse', $cs);
}

function collapseall()
{
	$userid = get_userid();
	set_preference($userid, 'collapse', '');
}

function content_toggleexpand($contentid, $collapse)
{
	$resp = new CmsAjaxResponse();
	
	toggleexpand($contentid, $collapse=='true'?true:false);

	$resp->replace_html('#contentlist', display_content_list());
	$resp->script("$('#tr_{$contentid} > td').highlight('#ff0', 1500);");
	//$resp->script('set_context_menu();');

	return $resp->get_result();
}

function content_delete($contentid)
{
	$resp = new CmsAjaxResponse();
	
	deletecontent($contentid);

	//$objResponse->addScript("new Effect.Fade('tr_$contentid', { afterFinish:function() { xajax_content_list_ajax(); } });");
	//$objResponse->addScript("$('#tr_{$contentid}').Highlight(500, '#f00', function() { xajax_content_list_ajax(); });");
	$resp->replace_html('#contentlist', display_content_list());
	//$resp->script('set_context_menu();');
	
	return $resp->get_result();
}

function toggleexpand($contentid, $collapse = false)
{
	$userid = get_userid();
	$openedArray=array();
	if (get_preference($userid, 'collapse', '') != '')
	{
		$tmp  = explode('.',get_preference($userid, 'collapse'));
		foreach ($tmp as $thisCol)
		{
			$colind = substr($thisCol,0,strpos($thisCol,'='));
			$openedArray[$colind] = 1;
		}
	}
	if ($collapse)
	{
		$openedArray[$contentid] = 0;
	}
	else
	{
		$openedArray[$contentid] = 1;
	}
	$cs = '';
	foreach ($openedArray as $key=>$val)
	{
		if ($val == 1)
		{
			$cs .= $key.'=1.';
		}
	}
	set_preference($userid, 'collapse', $cs);
}

function setactive($contentid, $active = true)
{
	$userid = get_userid();
	
	// to activate a page, you must be admin, owner, or additional author
	$permission = (check_modify_all($userid) || check_permission($userid, 'Modify Page Structure'));

	if($permission)
	{
		$value = cms_orm('CmsPage')->find_by_id($contentid);
		if ($value)
		{
			$value->active = $active;
			$value->save();
		}
	}
	
	CmsCache::clear();
}

function content_move($contentid, $parentid, $direction)
{	
	$resp = new CmsAjaxResponse();
	
	movecontent($contentid, $parentid, $direction);

	$resp->replace_html('#contentlist', display_content_list());
	$resp->script("$('#tr_{$contentid} > td').highlight('#ff0', 1500);");
	//$resp->script('set_context_menu();');

	return $resp->get_result();
}

function content_move_new($id, $ref_id, $type)
{	
	$resp = new CmsAjaxResponse();
	
	$result = false;
	
	//$resp->script('alert("'.$id.', '.$ref_id.', '.$type.'");');
	if ($type == 'inside')
	{
		$child_id = str_replace('phtml_', '', $id);
		$parent_id = str_replace('phtml_', '', $ref_id);
		
		$obj = cms_orm('CmsPage')->find_by_id($child_id);
		if ($obj)
		{
			$obj->parent_id = $parent_id;
			$result = $obj->save();
		}
	}
	else if ($type == 'after' || $type == 'before')
	{
		$child_id = str_replace('phtml_', '', $id);
		$target_id = str_replace('phtml_', '', $ref_id);
		
		$obj = cms_orm('CmsPage')->find_by_id($child_id);
		$target_obj = cms_orm('CmsPage')->find_by_id($target_id);
		if ($obj && $target_obj)
		{
			$result = $obj->move_before_or_after($target_obj, $type);
		}
	}
	
	if ($result)
		$resp->script('successful = true;');
	else
		$resp->script('successful = false;');


	return $resp->get_result();
}

function content_select($html_id)
{
	$smarty = cms_smarty();
	$resp = new CmsAjaxResponse();
	
	//var_dump($html_id);
	
	if ($html_id == 'multiple' || $html_id == 'none')
	{
		$smarty->assign_by_ref('reason_for_not_showing', $html_id);
	}
	else
	{
		$id = str_replace('phtml_', '', $html_id);
		$page = cms_orm('CmsPage')->load($id);
		$smarty->assign_by_ref('page', $page);
	}
	
	$resp->replace_html('#contentsummary', $smarty->fetch('listcontent-summary.tpl'));
	//$resp->script('setup_observers();');

	return $resp->get_result();
}

function content_new()
{
	$smarty = cms_smarty();
	$resp = new CmsAjaxResponse();
	
	$page = new CmsPage();
	$smarty->assign_by_ref('page', $page);
	
	$resp->script('prepare_add_content()');
	$resp->replace_html('#contentsummary', $smarty->fetch('listcontent-summary.tpl'));
	//$resp->script('setup_observers();');

	return $resp->get_result();
}

function movecontent($contentid, $parentid, $direction = 'down')
{
	$userid = get_userid();

	if (check_modify_all($userid) || check_permission($userid, 'Modify Page Structure'))
	{
		$content = cms_orm('CmsPage')->find_by_id($contentid);
		
		if ($content != null)
		{
			$content->shift_position($direction);
		}
	}
}

function deletecontent($contentid)
{
  $userid = get_userid();
  $access = check_permission($userid, 'Remove Pages') || check_permission($userid, 'Modify Page Structure');
  
  if (!$access)
    {
      $_GET['error'] = 'permissiondenied';
      return;
    }

  $content_obj = CmsContentOperations::load_content_from_id($contentid);
  if( !$content_obj )
    {
      $_GET['error'] = 'errorgettingcontent';
      return;
    }

  $res = $content_obj->delete();
  if( !$res )
    {
      $_GET['error'] = 'errordeletingcontent';
      return;
    }
  $_GET['message'] = 'contentdeleted';
}

function save_page($params)
{
	$ajax = new CmsAjaxResponse();
	$admin_theme = CmsAdminTheme::get_instance();
	
	$smarty = cms_smarty();
	
	if (isset($params['save']) || isset($params['apply']))
	{
		$page = cms_orm('CmsPage')->load($params['page']);
		$result = '';
		if ($page)
		{
			$page->update_parameters($params['page']);
			$valid = !$page->check_not_valid();
			if ($valid)
			{
				//Ok, page validates.  Let's handle the content (if there is)
				if (isset($params['block_type']) && is_array($params['block_type']))
				{
					foreach ($params['block_type'] as $block_name => $content_type)
					{
						$content_obj = cms_orm('CmsContentBase')->load($params['block'][$block_name], $content_type);
						if ($content_obj)
						{
							$content_obj->update_parameters($params['block'][$block_name]);
							if ($content_obj->save())
							{
								$page->params['blocks'][$block_name]['id'] = $content_obj->id;
							}
						}
					}
				}
				
				if ($page->save())
				{
					$admin_theme->add_message('Page Saved');
					$ajax->replace_html('.pagemessagecontainer', $admin_theme->display_messages());
					$ajax->script('$(".pagemessage").fadeOut(3500)');
					CmsCache::clear();
					if (isset($params['save']))
					{
						$ajax->script('prepare_add_content()');
						$smarty->assign('reason_for_not_showing', 'none');
						$ajax->replace_html('#contentsummary', $smarty->fetch('listcontent-summary.tpl'));
					}
					$ajax->replace_html('#contentlist', display_content_list());
					return $ajax->get_result();
				}
			}
			
			$admin_theme->add_error('Error Saving Page');
			$ajax->replace_html('div.pageerrorcontainer', $admin_theme->display_errors());
			$ajax->script('$(".pageerror").fadeOut(3500)');
		}
	}
	else if (isset($params['cancel']))
	{
		$ajax->script('prepare_add_content()');
		$smarty->assign('reason_for_not_showing', 'none');
		$ajax->replace_html('#contentsummary', $smarty->fetch('listcontent-summary.tpl'));
	}
	return $ajax->get_result();
}

function check_alias($params)
{
	$ajax = new CmsAjaxResponse();
	$count = cms_orm('CmsPage')->find_count(array('conditions' => array('content_alias = ? AND id <> ?', $params['alias'], $params['page_id'])));
	if ($params['alias'] == '')
	{
		$ajax->replace_html('#alias_ok', 'Empty');
		$ajax->script('$("#alias_ok").attr("style", "color: red;")');
	}
	else if ($count > 0 || $params['alias'] == '')
	{
		$ajax->replace_html('#alias_ok', 'Used');
		$ajax->script('$("#alias_ok").attr("style", "color: red;")');
	}
	else
	{
		$ajax->replace_html('#alias_ok', 'Ok');
		$ajax->script('$("#alias_ok").attr("style", "color: green;")');
	}
	//$ajax->script('reset_main_content();');
	return $ajax->get_result();
}

function check_url($params)
{
	$ajax = new CmsAjaxResponse();
	$count = cms_orm('CmsPage')->find_count(array('conditions' => array('url_text = ? AND id != ?', $params['alias'], $params['page_id'])));
	if ($params['alias'] == '')
	{
		$ajax->replace_html('#url_text_ok', 'Empty');
		$ajax->script('$("#url_text_ok").attr("style", "color: red;")');
	}
	else if ($count > 0 || $params['alias'] == '')
	{
		$ajax->replace_html('#url_text_ok', 'Used');
		$ajax->script('$("#url_text_ok").attr("style", "color: red;")');
	}
	else
	{
		$ajax->replace_html('#url_text_ok', 'Ok');
		$ajax->script('$("#url_text_ok").attr("style", "color: green;")');
	}
	//$ajax->script('reset_main_content();');
	return $ajax->get_result();
}


# vim:ts=4 sw=4 noet
?>