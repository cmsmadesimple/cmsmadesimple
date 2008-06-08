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
$cms_ajax->register_function('content_delete');
$cms_ajax->register_function('context_menu');

function check_modify_all($userid)
{
	return check_permission($userid, 'Modify Any Page');
}

$gCms = cmsms();
$smarty = cms_smarty();
$config = cms_config();
$contentops = $gCms->GetContentOperations();
$templateops = $gCms->GetTemplateOperations();

//include_once("../lib/classes/class.admintheme.inc.php");

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
	redirect('listcontent.php');
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
include_once("header.php");

$smarty->assign('header_name', $themeObject->ShowHeader('currentpages'));

setup_smarty($themeObject);

$smarty->assign('content_list', $smarty->fetch('listcontent-tree.tpl'));

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
	$userid = get_userid();
	$themeObject = CmsAdminTheme::get_theme_for_user($userid);
	$smarty = cms_smarty();
	setup_smarty($themeObject);
	return $smarty->fetch('listcontent-tree.tpl');
}

function content_list_ajax()
{
	$resp = new CmsAjaxResponse();

	$resp->modify_html('#contentlist', display_content_list());
	$resp->script('set_context_menu();');
	
	return $resp->get_result();
}

function content_setactive($contentid) 
{	
	$resp = new CmsAjaxResponse();
	
	setactive($contentid, true);
	
	$resp->modify_html('#contentlist', display_content_list());
	$resp->script('set_context_menu();');
	$resp->script("$('#content_span_{$contentid}').highlight('#ff0', 1500);");
	
	return $resp->get_result();
}

function content_setinactive($contentid) 
{ 	
	$resp = new CmsAjaxResponse();
	
	setactive($contentid, false);
	
	$resp->modify_html('#contentlist', display_content_list());
	$resp->script('set_context_menu();');
	$resp->script("$('#content_span_{$contentid}').highlight('#ff0', 1500);");
	
	return $resp->get_result();
}

function setdefault($contentid)
{
	$userid = get_userid();
	
	$result = false;

	if (check_modify_all($userid))
	{
		$old = cmsms()->content->find_by_default_content(1);
		if ($old)
		{
			$old->default_content = false;
			$old->save();
		}
		
		$new = cmsms()->content->find_by_id($contentid);
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

function content_setdefault($contentid)
{
	$resp = new CmsAjaxResponse();
	
	setdefault($contentid);

	$resp->modify_html('#contentlist', display_content_list());
	$resp->script("$('#tr_{$contentid} > td').highlight('#ff0', 1500);");
	$resp->script('set_context_menu();');

	return $resp->get_result();
}

function content_expandall()
{
	$resp = new CmsAjaxResponse();
	
	expandall();

	$resp->modify_html('#contentlist', display_content_list());
	$resp->script('set_context_menu();');

	return $resp->get_result();
}

function content_collapseall()
{
	$resp = new CmsAjaxResponse();
	
	collapseall();

	$resp->modify_html('#contentlist', display_content_list());
	$resp->script('set_context_menu();');

	return $resp->get_result();
}

function context_menu($content_id)
{
	$smarty = cms_smarty();
	$resp = new CmsAjaxResponse();
	
	set_permissions($smarty);
	
	$content = cms_orm('content')->find_by_id(substr($content_id, strlen('content_span_')));
	$smarty->assign_by_ref('current', $content);

	$resp->modify_html('#context_menu', $smarty->fetch('listcontent-context_menu.tpl'));
	
	return $resp->get_result();	
}

function expandall()
{
	$userid = get_userid();
	//$all = cmsms()->GetContentOperations()->GetAllContent(false);
	$all = cmsms()->content->find_all(array('order' => 'hierarchy ASC'));
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

	$resp->modify_html('#contentlist', display_content_list());
	$resp->script("$('#tr_{$contentid} > td').highlight('#ff0', 1500);");
	$resp->script('set_context_menu();');

	return $resp->get_result();
}

function content_delete($contentid)
{
	$resp = new CmsAjaxResponse();
	
	deletecontent($contentid);

	//$objResponse->addScript("new Effect.Fade('tr_$contentid', { afterFinish:function() { xajax_content_list_ajax(); } });");
	//$objResponse->addScript("$('#tr_{$contentid}').Highlight(500, '#f00', function() { xajax_content_list_ajax(); });");
	$resp->modify_html('#contentlist', display_content_list());
	$resp->script('set_context_menu();');
	
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
	$permission = (check_modify_all($userid) || check_permission($userid, 'Modify Page Structure')
	);

	if($permission)
	{
		$value = cmsms()->content->find_by_id($contentid);
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

	$resp->modify_html('#contentlist', display_content_list());
	$resp->script("$('#tr_{$contentid} > td').highlight('#ff0', 1500);");
	$resp->script('set_context_menu();');

	return $resp->get_result();
}

function movecontent($contentid, $parentid, $direction = 'down')
{
	$userid = get_userid();

	if (check_modify_all($userid) || check_permission($userid, 'Modify Page Structure'))
	{
		$content = cms_orm()->content->find_by_id($contentid);
		
		if ($content != null)
		{
			$content->shift_position($direction);
		}
	}
}

function deletecontent($contentid)
{
	//TODO: Rewrite me
	$userid = get_userid();
	$access = check_permission($userid, 'Remove Pages') || check_permission($userid, 'Modify Page Structure');
	
	global $gCms;
	$hierManager =& $gCms->GetHierarchyManager();

	if ($access)
	{
		$node = &$hierManager->getNodeById($contentid);
		if ($node)
		{
			$contentobj =& $node->getContent();
			$childcount = 0;
			$parentid = -1;
			if (isset($node->parentNode))
			{
				$parent =& $node->parentNode;
				if (isset($parent))
				{
					$parentContent =& $parent->getContent();
					if (isset($parentContent))
					{
						$parentid = $parentContent->Id();
						$childcount = $parent->getChildrenCount();
					}
				}
			}

			if ($contentobj)
			{
				$title = $contentobj->Name();
	
				#Check for children
				if ($contentobj->HasChildren())
				{
					$_GET['error'] = 'errorchildcontent';
				}
	
				#Check for default
				if ($contentobj->DefaultContent())
				{
					$_GET['error'] = 'errordefaultpage';
				}
			
				$title = $contentobj->Name();
				$contentobj->delete();

				$contentops =& $gCms->GetContentOperations();
				$contentops->SetAllHierarchyPositions();
				
				#See if this is the last child... if so, remove
				#the expand for it
				if ($childcount == 1 && $parentid > -1)
				{
					toggleexpand($parentid, true);
				}
				
				#Do the same with this page as well
				toggleexpand($contentid, true);
				
				audit($contentid, $title, 'Deleted Content');
				
				$contentops->ClearCache();
			
				$_GET['message'] = 'contentdeleted';
			}
		}
	}
}

function show_h(&$root, &$sortableLists, &$listArray, &$output)
{
	$content = &$root->getContent();

	$contentops = cmsms()->GetContentOperations();

	$output .= '<li id="item_'.$content->id.'">'."\n";
	$output .= '('.$contentops->CreateFriendlyHierarchyPosition($content->hierarchy).') '.$content->name;

	if ($root->getChildrenCount()>0)
	{
		$sortableLists->addList('parent'.$content->id,'parent'.$content->id.'ListOrder');
		$listArray[$content->id] = 'parent'.$content->id.'ListOrder';
		$output .= '<ul id="parent'.$content->id.'" class="sortableList">'."\n";

		$children = &$root->getChildren();
		foreach ($children as $child)
		{
			show_h($child, $sortableLists, $listArray, $output);
		}
		$output .= "</ul>\n";
	}
	else 
	{
		$output .= "</li>\n";
	}
}

# vim:ts=4 sw=4 noet
?>