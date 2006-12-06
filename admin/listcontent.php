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

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'xajax' . DIRECTORY_SEPARATOR . 'xajax.inc.php');
$xajax = new xajax();
$xajax->registerFunction('content_list_ajax');
$xajax->registerFunction('content_setactive');
$xajax->registerFunction('content_setinactive');
$xajax->registerFunction('content_setdefault');
$xajax->registerFunction('content_expandall');
$xajax->registerFunction('content_collapseall');
$xajax->registerFunction('content_toggleexpand');
$xajax->registerFunction('content_move');
$xajax->registerFunction('content_delete');
$xajax->registerFunction('reorder_display_list');
$xajax->registerFunction('reorder_process');

function check_modify_all($userid)
{
	return check_permission($userid, 'Modify Any Page');
}

$gCms = cmsms();
$smarty = smarty();
$contentops =& $gCms->GetContentOperations();
$templateops =& $gCms->GetTemplateOperations();

include_once("../lib/classes/class.admintheme.inc.php");

$xajax->processRequests();
$headtext = $xajax->getJavascript($config['root_url'] . '/lib/xajax')."\n";

if (isset($_GET["makedefault"]))
{
	setdefault($_GET['makedefault']);
	redirect('listcontent.php');
}

// check if we're activating a page
if (isset($_GET["setactive"]))
{
	setactive($_GET["setactive"]);
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
include_once("header.php");

$smarty->assign('header_name', $themeObject->ShowHeader('currentpages'));

setup_smarty($themeObject);

$smarty->assign('content_list', $smarty->fetch('listcontent-tree.tpl'));

$smarty->display('listcontent.tpl');

include_once("footer.php");

function setup_smarty($themeObject)
{
	$smarty = smarty();
	$userid = get_userid();

	//Set permissions
	$smarty->assign('add_pages', check_permission($userid, 'Add Pages'));
	$smarty->assign('modify_page_structure', check_permission($userid, 'Modify Page Structure'));
	$smarty->assign('check_modify_all', check_permission($userid, 'Modify Any Page'));
	$smarty->assign('remove_pages', check_permission($userid, 'Remove Pages'));
	$smarty->assign('mypages', author_pages($userid));
	
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

function display_content_list()
{
	$userid = get_userid();
	$themeObject = AdminTheme::get_theme_for_user($userid);
	$smarty = smarty();
	setup_smarty($themeObject);
	return $smarty->fetch('listcontent-tree.tpl');
}

function content_list_ajax()
{
	$objResponse = new xajaxResponse();
	$objResponse->addClear("contentlist", "innerHTML");
	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	return $objResponse->getXML();
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
		cmsms()->GetContentOperations()->ClearCache();
	}
	return $result;
}

function content_setdefault($contentid)
{
	$objResponse = new xajaxResponse();
	
	setdefault($contentid);

	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	$objResponse->addScript("new Effect.Highlight('tr_$contentid', { duration: 2.0 });");
	return $objResponse->getXML();
}

function content_setactive($contentid)
{
	$objResponse = new xajaxResponse();
	
	setactive($contentid);

	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	$objResponse->addScript("new Effect.Highlight('tr_$contentid', { duration: 2.0 });");
	return $objResponse->getXML();
}

function content_setinactive($contentid)
{
	$objResponse = new xajaxResponse();
	
	setactive($contentid, false);

	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	$objResponse->addScript("new Effect.Highlight('tr_$contentid', { duration: 2.0 });");
	return $objResponse->getXML();
}

function content_expandall()
{
	$objResponse = new xajaxResponse();
	
	expandall();

	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	return $objResponse->getXML();
}

function content_collapseall()
{
	$objResponse = new xajaxResponse();
	
	collapseall();

	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	return $objResponse->getXML();
}

function expandall()
{
	$userid = get_userid();
	global $gCms;
	$contentops =& $gCms->GetContentOperations();
	$all = $contentops->GetAllContent(false);
	$cs = '';
	foreach ($all as $thisitem)
	{
		if ($thisitem->HasChildren())
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
	$objResponse = new xajaxResponse();
	
	toggleexpand($contentid, $collapse=='true'?true:false);

	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	$objResponse->addScript("new Effect.Highlight('tr_$contentid', { duration: 2.0 });");
	return $objResponse->getXML();
}

function content_delete($contentid)
{
	$objResponse = new xajaxResponse();
	
	deletecontent($contentid);

	$objResponse->addScript("new Effect.Fade('tr_$contentid', { afterFinish:function() { xajax_content_list_ajax(); } });");
	return $objResponse->getXML();
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
	global $gCms;
	$userid = get_userid();
	
	// to activate a page, you must be admin, owner, or additional author
	$permission = (check_modify_all($userid) || 
			check_ownership($userid, $contentid) ||
			check_authorship($userid, $contentid) ||
			check_permission($userid, 'Modify Page Structure')
	);

	if($permission)
	{
		$value = cmsms()->content->find_by_id($contentid);
		if ($value)
		{
			$value->active = $active;
			$value->save();
			cmsms()->GetContentOperations()->ClearCache();
		}
	}
}

function content_move($contentid, $parentid, $direction)
{
	$objResponse = new xajaxResponse();
	
	movecontent($contentid, $parentid, $direction);

	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	$objResponse->addScript("new Effect.Highlight('tr_$contentid', { duration: 2.0 });");
	return $objResponse->getXML();
}

function movecontent($contentid, $parentid, $direction = 'down')
{
	global $gCms;
	$db =& $gCms->GetDb();
	$userid = get_userid();

	if (check_modify_all($userid) || check_permission($userid, 'Modify Page Structure'))
	{
		$order = 1;

		#Grab necessary info for fixing the item_order
		$query = "SELECT item_order FROM ".cms_db_prefix()."content WHERE id = ?";
		$result = $db->Execute($query, array($contentid));
		$row = $result->FetchRow();
		if (isset($row["item_order"]))
		{
			$order = $row["item_order"];
		}

		$time = $db->DBTimeStamp(time());
		if ($direction == "down")
		{
			$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order - 1), modified_date = '.$time.' WHERE item_order = ? AND parent_id = ?';
			#echo $query, $order + 1, $parent_id;
			$db->Execute($query, array($order + 1, $parentid));
			$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order + 1), modified_date = '.$time.' WHERE id = ? AND parent_id = ?';
			#echo $query, $content_id, $parent_id;
			$db->Execute($query, array($contentid, $parentid));
		}
		else if ($direction == "up")
		{
			$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order + 1), modified_date = '.$time.' WHERE item_order = ? AND parent_id = ?';
			#echo $query;
			$db->Execute($query, array($order - 1, $parentid));
			$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order - 1), modified_date = '.$time.' WHERE id = ? AND parent_id = ?';
			#echo $query;
			$db->Execute($query, array($contentid, $parentid));
		}

		cmsms()->GetContentOperations()->SetAllHierarchyPositions();
		cmsms()->GetContentOperations()->ClearCache();
	}
}

function deletecontent($contentid)
{
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

# vim:ts=4 sw=4 noet
?>