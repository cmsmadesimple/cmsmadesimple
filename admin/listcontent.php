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
require_once(cms_join_path($dirname,'lib','html_entity_decode_utf8.php'));

check_login();
$userid = get_userid();
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
$thisurl=basename(__FILE__).$urlext;

include_once("../lib/classes/class.admintheme.inc.php");

define('XAJAX_DEFAULT_CHAR_ENCODING', $config['admin_encoding']);


require_once(dirname(dirname(__FILE__)) . '/lib/xajax/xajax_core/xajax.inc.php');
$xajax = new xajax();
$xajax->register(XAJAX_FUNCTION,'content_list_ajax');
$xajax->register(XAJAX_FUNCTION,'content_setactive');
$xajax->register(XAJAX_FUNCTION,'content_setinactive');
$xajax->register(XAJAX_FUNCTION,'content_setdefault');
$xajax->register(XAJAX_FUNCTION,'content_expandall');
$xajax->register(XAJAX_FUNCTION,'content_collapseall');
$xajax->register(XAJAX_FUNCTION,'content_toggleexpand');
$xajax->register(XAJAX_FUNCTION,'content_move');
$xajax->register(XAJAX_FUNCTION,'content_delete');
$xajax->register(XAJAX_FUNCTION,'reorder_display_list');
$xajax->register(XAJAX_FUNCTION,'reorder_process');
$xajax->processRequest();
$headtext = $xajax->getJavascript($config['root_url'] . '/lib/xajax')."\n";
include_once("header.php");

//echo '<a onclick="xajax_reorder_display_list();">Test</a>';

function content_list_ajax()
{
	$objResponse = new xajaxResponse();
	$objResponse->clear("contentlist", "innerHTML");
	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	return $objResponse;
}

function check_modify_all($userid)
{
  return check_permission($userid, 'Modify Any Page');
}

function setdefault($contentid)
{
	global $gCms;
	$userid = get_userid();
	
	$result = false;

	if (check_permission($userid,'Manage All Content'))
	{
		$hierManager =& $gCms->GetHierarchyManager();
		$node = &$hierManager->getNodeById($contentid);
		if (isset($node))
		{
			$value =& $node->getContent();
			if (isset($value))
			{
				if (!$value->Active())
				{
					#Modify the object inline
					$value->SetActive(true);
					$value->Save();
				}
			}
		}
		
		$db = &$gCms->GetDb();
		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE default_content=1";
		$old_id = $db->GetOne($query);
		if (isset($old_id))
		{
			$node = &$hierManager->getNodeById($old_id);
			if (isset($node))
			{
				$value =& $node->getContent();
				if (isset($value))
				{
					$value->SetDefaultContent(false);
					$value->Save();
				}
			}
		}
		
		$node = &$hierManager->getNodeById($contentid);
		if (isset($node))
		{
			$value =& $node->getContent();
			if (isset($value))
			{
				$value->SetDefaultContent(true);
				$value->Save();
			}
		}

		$result = true;
		global $gCms;
		$contentops =& $gCms->GetContentOperations();
		$contentops->ClearCache();
	}
	return $result;
}

function content_setdefault($contentid)
{
	$objResponse = new xajaxResponse();
	
	setdefault($contentid);

	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	$objResponse->script("new Effect.Highlight('tr_$contentid', { duration: 2.0 });");
	return $objResponse;
}

function content_setactive($contentid)
{
	$objResponse = new xajaxResponse();
	
	setactive($contentid);

	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	$objResponse->script("new Effect.Highlight('tr_$contentid', { duration: 2.0 });");
	return $objResponse;
}

function content_setinactive($contentid)
{
	$objResponse = new xajaxResponse();
	
	setactive($contentid, false);

	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	$objResponse->script("new Effect.Highlight('tr_$contentid', { duration: 2.0 });");
	return $objResponse;
}

function content_expandall()
{
	$objResponse = new xajaxResponse();
	
	expandall();

	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	return $objResponse;
}

function content_collapseall()
{
	$objResponse = new xajaxResponse();
	
	collapseall();

	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	return $objResponse;
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
			$cs .= $thisitem->Id().'=1.';
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

	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	$objResponse->script("new Effect.Highlight('tr_$contentid', { duration: 2.0 });");
	return $objResponse;
}

function content_delete($contentid)
{
	$objResponse = new xajaxResponse();
	
	deletecontent($contentid);

	$objResponse->script("new Effect.Fade('tr_$contentid', { afterFinish:function() { xajax_content_list_ajax(); } });");
	return $objResponse;
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
	
	$hierManager =& $gCms->GetHierarchyManager();
	
	// to activate a page, you must be admin, owner, or additional author
	$permission = (	check_ownership($userid, $contentid) ||
			check_authorship($userid, $contentid) ||
			check_permission($userid, 'Manage All Content')
	);

	if($permission)
	{
		$node = &$hierManager->getNodeById($contentid);
		$value =& $node->getContent();
		$value->SetActive($active);
		$value->Save();
		global $gCms;
		$contentops =& $gCms->GetContentOperations();
		$contentops->ClearCache();
	}
}

function content_move($contentid, $parentid, $direction)
{
  $objResponse = new xajaxResponse();

  $time = time();
  $tmp = get_site_preference('__listcontent_timelock__',0);
  if( (time() - $tmp) < 3 )
    {
      return $objResponse; // delay between requests
    }
  set_site_preference('__listcontent_timelock__',$time);
	
	movecontent($contentid, $parentid, $direction);

	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	$objResponse->script("new Effect.Highlight('tr_$contentid', { duration: 2.0 });");

  // reset lock
  return $objResponse;

}

function movecontent($contentid, $parentid, $direction = 'down')
{
	global $gCms;
	$db =& $gCms->GetDb();
	$userid = get_userid();

	if (check_permission($userid, 'Manage All Content'))
	{
		$order = 1;

		#Grab necessary info for fixing the item_order
		$query = "SELECT item_order FROM ".cms_db_prefix()."content WHERE content_id = ?";
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
			$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order + 1), modified_date = '.$time.' WHERE content_id = ? AND parent_id = ?';
			#echo $query, $content_id, $parent_id;
			$db->Execute($query, array($contentid, $parentid));
		}
		else if ( ($direction == "up") && ($order > 1) )
		{
			$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order + 1), modified_date = '.$time.' WHERE item_order = ? AND parent_id = ?';
			#echo $query;
			$db->Execute($query, array($order - 1, $parentid));
			$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order - 1), modified_date = '.$time.' WHERE content_id = ? AND parent_id = ?';
			#echo $query;
			$db->Execute($query, array($contentid, $parentid));
		}

		//sleep(15); //waiting for updating DB. Better 5 but 15 is good for testing concurrent processes and work!
		$contentops =& $gCms->GetContentOperations();
		$contentops->SetAllHierarchyPositions();
		$contentops->ClearCache();
	}

}

function deletecontent($contentid)
{
	$userid = get_userid();
	$access = check_permission($userid, 'Remove Pages') || check_permission($userid, 'Manage All Content');
	
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
				$contentobj->Delete();

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
	if( !is_object($content) ) return;

	global $gCms;
	$contentops =& $gCms->GetContentOperations();

	$output .= '<li id="item_'.$content->mId.'">'."\n";
	$output .= '('.$contentops->CreateFriendlyHierarchyPosition($content->mHierarchy).') '.cms_htmlentities($content->mMenuText, '', '', true);

	if ($root->getChildrenCount()>0)
	{
		$sortableLists->addList('parent'.$content->mId,'parent'.$content->mId.'ListOrder');
		$listArray[$content->mId] = 'parent'.$content->mId.'ListOrder';
		$output .= '<ul id="parent'.$content->mId.'" class="sortableList">'."\n";

		$children = &$root->getChildren(false,true);
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

function reorder_display_list()
{
	$objResponse = new xajaxResponse();
	global $gCms;
	$config =& $gCms->GetConfig();
	
	$userid = get_userid();
	
	$path = cms_join_path(dirname(dirname(__FILE__)), 'lib', 'sllists', 'SLLists.class.php');
	require($path);

	$sortableLists = new SLLists($config["root_url"].'/lib/scriptaculous');
	
	$hierManager =& $gCms->GetHierarchyManager();
	$hierarchy = &$hierManager->getRootNode();
	
	$listArray = array();
	$output = '';
	
	$sortableLists->addList('parent0','parent0ListOrder');
	$listArray[0] = 'parent0ListOrder';
	$output .= '<ul id="parent0" class="sortableList">'."\n";
	
	foreach ($hierarchy->getChildren(false,true) as $child)
	{
		show_h($child, $sortableLists, $listArray, $output);
	}
	
	$output .= '</ul>';

	ob_start();
	//$sortableLists->printTopJS();
	$sortableLists->printForm($_SERVER['PHP_SELF'], 'POST', lang('submit'), 'button', 'sortableListForm', lang('cancel'), $output);
	$contents = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	$sortableLists->printBottomJs();
	$script = ob_get_contents();
	ob_end_clean();
	
	$objResponse->assign("contentlist", "innerHTML", $contents);
	$objResponse->script($script);

	return $objResponse;
}

function reorder_process($get)
{
	$userid = get_userid();
	$objResponse = new xajaxResponse();

	if (check_permission($userid,'Manage All Content'))
	{
		global $gCms;
		$config =& $gCms->GetConfig();
		$db =& $gCms->GetDb();
		$contentops =& $gCms->GetContentOperations();
	    $hm = $contentops->GetAllContentAsHierarchy(false);
		$hierarchy = &$hm->getRootNode();
	
		require(cms_join_path(dirname(dirname(__FILE__)), 'lib', 'sllists','SLLists.class.php'));
		$sortableLists = new SLLists( $config["root_url"].'/lib/scriptaculous');
	
		$listArray = array();
		$output = '';
		
		$sortableLists->addList('parent0','parent0ListOrder');
		$listArray[0] = 'parent0ListOrder';
		$output .= '<ul id="parent0" class="sortableList">'."\n";

		foreach ($hierarchy->getChildren(false,true) as $child)
		{
			show_h($child, $sortableLists, $listArray, $output);
		}
		
		$output .= '</ul>';
	
		$order_changed = FALSE;
		foreach ($listArray AS $parent_id => $order)
		{
			$orderArray = SLLists::getOrderArray($get[$order], 'parent'.$parent_id);
			foreach($orderArray as $item)
			{
				$node =& $hm->sureGetNodeById($item['element']);
				if ($node != NULL)
				{
				    $one =& $node->getContent();
				    // Only update if order has changed.
				    if ($one->ItemOrder() != $item['order'])
				    {
					$order_changed = TRUE;
					$query = 'UPDATE '.cms_db_prefix().'content SET item_order = ? WHERE content_id = ?';
					$db->Execute($query, array($item['order'], $item['element']));
				    }
				}
			}
		}
		if (TRUE == $order_changed) {
			global $gCms;
			$contentops =& $gCms->GetContentOperations();
			$contentops->SetAllHierarchyPositions();
			$contentops->ClearCache();
		}
	}
	
	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	return $objResponse;
}

function check_children(&$root, &$mypages, &$userid)
{
	$result = false;
	$content =& $root->getContent();
	if (isset($content))
	{
		$result = in_array($content->Id(), $mypages, false);
		if (!$result)
		{
		  $children =& $root->getChildren(false,true);
			foreach ($children as $child)
			{
				$result = check_children($child, $mypages, $userid);
				if ($result)
					break;
			}
		}
	}
	return $result;
}

function display_hierarchy(&$root, &$userid, $modifyall, &$templates, &$users, &$menupos, &$openedArray, &$pagelist, &$image_true, &$image_set_false, &$image_set_true, &$upImg, &$downImg, &$viewImg, &$editImg, &$copyImg, &$deleteImg, &$expandImg, &$contractImg, &$mypages, &$page, $columnstodisplay)
{
  global $thisurl;
  global $urlext;
  global $currow;
  global $config;
  global $page;
  global $indent;
  
  if(empty($currow)) $currow = 'row1';
  
  $children =& $root->getChildren(false,true);
  $one =& $root->getContent();
  $thelist = '';

  if (!(isset($one) && $one != NULL))
    {
      return;
    }
  
  if (!array_key_exists($one->TemplateId(), $templates))
    {
      global $gCms;
      $templateops =& $gCms->GetTemplateOperations();
      $templates[$one->TemplateId()] = $templateops->LoadTemplateById($one->TemplateId());
    }
  
  if (!array_key_exists($one->Owner(), $users))
    {
      global $gCms;
      $userops =& $gCms->GetUserOperations();
      $users[$one->Owner()] =& $userops->LoadUserById($one->Owner());
    }
  
  $display = 'none';
  if (check_modify_all($userid) || check_ownership($userid, $one->Id()) || quick_check_authorship($one->Id(), $mypages))
    {
      $display = 'edit';
    }
  else if (check_children($root, $mypages, $userid))
    {
      $display = 'view';
    }
  else if (check_permission($userid, 'Manage All Content'))
    {
      $display = 'structure';
    }
  
  $columns = array();
  if ($display != 'none')
    {
      $thelist .= "<tr id=\"tr_".$one->Id()."\" class=\"$currow\" onmouseover=\"this.className='".$currow.'hover'."';\" onmouseout=\"this.className='".$currow."';\">\n";
      
      /* expand/collapse column */
      $columns['expand'] = '&nbsp;';
      if( $columnstodisplay['expand'] )
	{
	  $txt = '';
	  if ($root->hasChildren())
	    {
	      if (!in_array($one->Id(),$openedArray))
		{
		  $txt .= "<a href=\"{$thisurl}&amp;content_id=".$one->Id()."&amp;col=0&amp;page=".$page."\" onclick=\"xajax_content_toggleexpand(".$one->Id().", 'false'); return false;\">";
		  $txt .= $expandImg;
		  $txt .= "</a>";
		}
	      else
		{
		  $txt .= "<a href=\"{$thisurl}&amp;content_id=".$one->Id()."&amp;col=1&amp;page=".$page."\" onclick=\"xajax_content_toggleexpand(".$one->Id().", 'true'); return false;\">";
		  $txt .= $contractImg;
		  $txt .= "</a>";
		}
	    }
	  if( !empty($txt) ) $columns['expand'] = $txt;
	}
	  

      /* hierarchy column */
      if( $columnstodisplay['hier'] )
	{
	  $columns['hier'] = $one->Hierarchy();
	}


      /* page column */
      if( $columnstodisplay['page'] )
	{
	  $columns['page'] = '&nbsp;';
	  $txt = '';
	  if( $one->mMenuText != CMS_CONTENT_HIDDEN_NAME )
	    {
	      if ($indent)
		{
		  for ($i=0;$i < $root->getLevel();$i++)
		    {
		      $txt .= "-&nbsp;&nbsp;&nbsp;";
		    }
		} 
	      if ($display == 'edit')
		$txt .= '<a href="editcontent.php'.$urlext.'&amp;content_id='.$one->mId.'&amp;page='.$page.'" title="'. cms_htmlentities($one->mName.' ('.$one->mAlias.')', '', '', true). '">'. cms_htmlentities($one->mMenuText, '', '', true) . '</a>';
	      else
		$txt .= cms_htmlentities($one->mMenuText, '', '', true);
	    }
	  if( !empty($txt) ) $columns['page'] = $txt;
	}

      
      /* template column */
      if( $columnstodisplay['template'] )
	{
	  $columns['template'] = '&nbsp;';
	  $txt = '';
	  if( $one->Type() != 'pagelink' && 
	      $one->Type() != 'link' && 
	      $one->Type() != 'sectionheader' && 
	      $one->Type() != 'separator' )
	    {
	      if (isset($templates[$one->TemplateId()]->name) && $templates[$one->TemplateId()]->name &&
		  check_permission($userid,'Modify Templates'))
		{
		  $txt .= "<a href=\"edittemplate.php".$urlext."&amp;template_id=".$one->TemplateId()."&amp;from=content\">".cms_htmlentities($templates[$one->TemplateId()]->name, '', '', true)."</a>";
		}
	      else
		{
		  $txt .= $templates[$one->TemplateId()]->name;
		}
	    }
	  if( !empty($txt) )
	    {
	      $columns['template'] = $txt;
	    }
	}


      /* friendly name column */
      if( $columnstodisplay['friendlyname'] )
	{
	  $columns['friendlyname'] = $one->FriendlyName();
	}


      /* owner column */
      if( $columnstodisplay['owner'] )
	{
	  $columns['owner'] = '&nbsp;';
	  if( $one->Owner() > - 1 )
	    {
	      $columns['owner'] = $users[$one->Owner()]->username;
	    }
	}


      /* active column */
      if( $columnstodisplay['active'] )
	{
	  $columns['active'] = '&nbsp;';
	  $txt = '';
	  if (check_permission($userid, 'Manage All Content'))
	    {
	      if($one->Active())
		{
		  $txt = ($one->DefaultContent()?$image_true:"<a href=\"{$thisurl}&amp;setinactive=".$one->Id()."\" onclick=\"xajax_content_setinactive(".$one->Id().");return false;\">".$image_set_false."</a>");
		}
	      else
		{
		  $txt = "<a href=\"{$thisurl}&amp;setactive=".$one->Id()."\" onclick=\"xajax_content_setactive(".$one->Id().");return false;\">".$image_set_true."</a>";
		}
	    }
	  if( !empty($txt) )
	    {
	      $columns['active'] = $txt;
	    }
	}


      /* default content */
      if( $columnstodisplay['default'] )
	{
	  $columns['default'] = '&nbsp;';
	  $txt = '';
	  if (check_permission($userid,'Manage All Content'))
	    {
	      if ($one->IsDefaultPossible())
		{
		  $txt = ($one->DefaultContent()?$image_true:"<a href=\"{$thisurl}&amp;makedefault=".$one->Id()."\" onclick=\"if(confirm('".cms_html_entity_decode_utf8(lang("confirmdefault", $one->mName), true)."')) xajax_content_setdefault(".$one->Id().");return false;\">".$image_set_true."</a>");
		}
	    }
	  if( !empty($txt) )
	    {
	      $columns['default'] = $txt;
	    }
	}


      /* move column */
      if( $columnstodisplay['move'] )
	{
	  // code for move up is simple
	  $columns['move'] = '&nbsp;';
	  $txt = '';
	  if (check_permission($userid, 'Manage All Content'))
	    {
	      $sameLevel = $root->getSiblingCount();
	      if ($sameLevel>1)
		{
		  if (($one->ItemOrder() - 1) <= 0) #first
		    { 
		      $txt .= "<a onclick=\"xajax_content_move(".$one->Id().", ".$one->ParentId().", 'down'); return false;\" href=\"{$thisurl}&amp;direction=down&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
		      $txt .= $downImg;
		      $txt .= "</a>&nbsp;&nbsp;";
		    }
		  else if (($one->ItemOrder() - 1) == $sameLevel-1) #last
		    {
		      $txt .= "&nbsp;&nbsp;<a class=\"move_up\" onclick=\"xajax_content_move(".$one->Id().", ".$one->ParentId().", 'up'); return false;\" href=\"{$thisurl}&amp;direction=up&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
		      $txt .= $upImg;
		      $txt .= "</a>";
		    }
		  else #middle
		    {
		      $txt .= "<a onclick=\"xajax_content_move(".$one->Id().", ".$one->ParentId().", 'down'); return false;\" href=\"{$thisurl}&amp;direction=down&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
		      $txt .= $downImg;
		      $txt .= "</a>&nbsp;<a onclick=\"xajax_content_move(".$one->Id().", ".$one->ParentId().", 'up'); return false;\" href=\"{$thisurl}&amp;direction=up&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
		      $txt .= $upImg;
		      $txt .= "</a>";
		    }
		}
	      
	      // $txt .= '<input clsss="hidden" type="text" name="order-'. $one->Id().'" value="'.$one->ItemOrder().'" class="order" />';
	    }
	  if( !empty($txt) )
	    {
	      $columns['move'] = $txt;
	    }
	  // end of move code
	}
	

      /* view column */
      if( $columnstodisplay['view'] )
	{
	  $columns['view'] = '&nbsp;';
	  $txt = '';
	  {
	    $url = $one->GetURL();
	    if ($url != '' && $url != '#' && $one->Type() != 'pagelink' && $one->Type() != 'link' && $one->Type() != 'sectionheader' && $one->Active())
	      {
		$txt .= "<a href=\"".$url."\" rel=\"external\" target=\"_blank\">";
		$txt .= $viewImg."</a>";
	      }
	  }
	  if( !empty($txt) )
	    {
	      $columns['view'] = $txt;
	    }
	}


      /* copy column */
      if( $columnstodisplay['copy'] )
	{
	  $columns['copy'] = '&nbsp;';
	  $txt = '';
	  if( $one->IsCopyable() && 
	      (check_permission($userid,'Add Pages') || check_permission($userid,'Manage All Content')) &&
	      (check_ownership($userid, $one->Id()) || quick_check_authorship($one->Id(), $mypages)) )
	    {
	      $txt .= '<a href="copycontent.php'.$urlext.'&amp;content_id='.$one->Id().'">';
	      $txt .= $copyImg."</a>";
	    }
	  if( !empty($txt) )
	    {
	      $columns['copy'] = $txt;
	    }
	}


      /* edit column */
      if( $columnstodisplay['edit'] )
	{
	  $columns['edit'] = '&nbsp;';
	  $txt = '';
	  if (check_modify_all($userid) || 
	      check_ownership($userid, $one->Id()) || 
	      quick_check_authorship($one->Id(), $mypages) ||
	      check_permission($userid, 'Manage All Content'))
	    {
	      // edit link
	      $txt .= "<a href=\"editcontent.php".$urlext."&amp;content_id=".$one->Id()."\">";
	      $txt .= $editImg;
	      $txt .= "</a>";
	    }
	  if( !empty($txt) )
	    {
	      $columns['edit'] = $txt;
	    }
	}


      /* delete column */
      if( $columnstodisplay['delete'] )
	{
	  $columns['delete'] = '&nbsp;';
	  $txt = '';
	  if ($one->DefaultContent() != true)
	    {
	      if ($root->getChildrenCount() == 0 && 
		  (check_permission($userid, 'Remove Pages') || check_permission($userid,'Manage All Content')) )
		{
		  $txt .= "<a href=\"{$thisurl}&amp;deletecontent=".$one->Id()."\" onclick=\"if (confirm('".cms_html_entity_decode_utf8(lang('deleteconfirm', $one->mName), true)."')) xajax_content_delete(".$one->Id()."); return false;\">";
		  $txt .= $deleteImg;
		  $txt .= "</a>";
		}
	    }
	  if( !empty($txt) )
	    {
	      $columns['delete'] = $txt;
	    }
	}
	    
      if( $columnstodisplay['multiselect'] )
	{
	  /* multiselect */
	  $columns['multiselect'] = '&nbsp;';
	  $txt = '';

	  $remove    = check_permission($userid, 'Remove Pages')?1:0;
	  $structure = check_permission($userid, 'Manage All Content')?1:0;
	  $editperms = (check_permission($userid, 'Modify Any Page') ||
			quick_check_authorship($one->Id(),$mypages) ||
			check_ownership($userid,$one->Id()))?1:0;
	  if ( ($structure == 1) || (($remove == 1) && ($editperms == 1)) )
	    {
	      $txt .= '<input type="checkbox" name="multicontent-'.$one->Id().'" />';
	    }
	  if( !empty($txt) )
	    {
	      $columns['multiselect'] = $txt;
	    }
	}
	  
      /* done */
      foreach( $columns as $name => $value )
	{
	  if( !$columnstodisplay[$name] ) continue;

	  switch( $name )
	    {
	    case 'edit':
	    case 'default':
	    case 'view':
	    case 'copy':
	    case 'delete':
	    case 'active':
	      $thelist .= '<td class="pagepos">'.$value."</td>\n";
	      break;
	      
	    case 'move':
	      $thelist .= '<td class="move">'.$value."</td>\n";
	      break;

	    case 'multiselect':
	      $thelist .= '<td class="checkbox">'.$value."</td>\n";
	      break;
	      
	    default:
	      $thelist .= '<td>'.$value."</td>\n";
	      break;
	    }
	}
      $thelist .= "</tr>\n";
      ($currow == "row1"?$currow="row2":$currow="row1");
    }

    $pagelist[] = &$thelist;

    $indent = get_preference($userid, 'indent', true);

    if (in_array($one->Id(),$openedArray))
    {
        foreach ($children as $child)
        { 
	  display_hierarchy($child, $userid, $modifyall, $templates, $users, $menupos, $openedArray, $pagelist, $image_true, $image_set_false, $image_set_true, $upImg, $downImg, $viewImg, $editImg, $copyImg, $deleteImg, $expandImg, $contractImg, $mypages, $page, $columnstodisplay);
        }
    }
} // function display_hierarchy

function display_content_list($themeObject = null)
{
	global $gCms;
	global $thisurl;
	global $urlext;

	check_login();
	$userid = get_userid();
	
	$mypages = author_pages($userid);
	$columnstodisplay = array();
	$columnstodisplay['expand'] = 1;
	$columnstodisplay['hier'] = 1;
	$columnstodisplay['page'] = 1;
	$columnstodisplay['template'] = 1;
	$columnstodisplay['friendlyname'] = 1;
	$columnstodisplay['owner'] = 1;
	$columnstodisplay['active'] = check_permission($userid, 'Manage All Content');
	$columnstodisplay['default'] = check_permission($userid, 'Manage All Content');
	$columnstodisplay['move'] = check_permission($userid, 'Manage All Content');
	$columnstodisplay['view'] = 1;
	$columnstodisplay['copy'] = check_permission($userid,'Add Pages') || check_permission($userid,'Manage All Content');
	$columnstodisplay['edit'] = 1;
	$columnstodisplay['delete'] = check_permission($userid, 'Remove Pages') || check_permission($userid,'Manage All Content');
	$columnstodisplay['multiselect'] = check_permission($userid, 'Remove Pages') ||
	  check_permission($userid,'Manage All Content') || check_permission($userid,'Modify Any Page');
	
	$page = 1;
	if (isset($_GET['page']))
		$page = $_GET['page'];
	//$limit = get_preference($userid, 'paging', 0);
	$limit = 0; //Took out pagination

	$thelist = '';
	$count = 0;

	$currow = "row1";
	
	if ($themeObject == null)
		$themeObject =& AdminTheme::GetThemeObject();

	// construct true/false button images
	$image_true = $themeObject->DisplayImage('icons/system/true.gif', lang('true'),'','','systemicon');
	$image_set_false = $themeObject->DisplayImage('icons/system/true.gif', lang('setfalse'),'','','systemicon');
	$image_set_true = $themeObject->DisplayImage('icons/system/false.gif', lang('settrue'),'','','systemicon');
	$expandImg = $themeObject->DisplayImage('icons/system/expand.gif', lang('expand'),'','','systemicon');
	$contractImg = $themeObject->DisplayImage('icons/system/contract.gif', lang('contract'),'','','systemicon');
	$downImg = $themeObject->DisplayImage('icons/system/arrow-d.gif', lang('down'),'','','systemicon');
	$upImg = $themeObject->DisplayImage('icons/system/arrow-u.gif', lang('up'),'','','systemicon');
	$viewImg = $themeObject->DisplayImage('icons/system/view.gif', lang('view'),'','','systemicon');
	$editImg = $themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon');
	$copyImg = $themeObject->DisplayImage('icons/system/copy.gif', lang('copy'),'','','systemicon');
	$deleteImg = $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');

	#Setup array so we don't load more templates than we need to
	$templates = array();

	#Ditto with users
	$users = array();

	$menupos = array();
	
	$openedArray=array();
	if (get_preference($userid, 'collapse', '') != '')
	{
		$tmp  = explode('.',get_preference($userid, 'collapse'));
		foreach ($tmp as $thisCol)
		{
			$colind = substr($thisCol,0,strpos($thisCol,'='));
			if ($colind!="")
				$openedArray[] = $colind;
		}
	}

        $hierManager =& $gCms->GetHierarchyManager();
	$hierarchy = &$hierManager->getRootNode();

	$rowcount = 0;
	if ($hierarchy->hasChildren())
	{
		$pagelist = array();
		foreach ($hierarchy->getChildren(false,true) as $child)
		{ 
		  display_hierarchy($child, $userid, check_modify_all($userid), $templates, $users, $menupos, $openedArray, $pagelist, $image_true, $image_set_false, $image_set_true, $upImg, $downImg, $viewImg, $editImg, $copyImg, $deleteImg, $expandImg, $contractImg, $mypages, $page, $columnstodisplay);
		}
		$rowcount += count($pagelist);
		foreach ($pagelist as $item)
		{
			$thelist.=$item;
		}

		$thelist .= '<tr class="invisible"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
<td>&nbsp;</td><td><input type="submit" name="reorderpages" value="'.lang('reorderpages').'" /></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';

		$thelist .= '</tbody>';
		$thelist .= "</table>\n";
	}

	$headoflist = '';

	$headoflist .= '<div class="pageoverflow"><p class="pageoptions">';
	if ((check_permission($userid, 'Add Pages') && count($mypages)) || check_permission($userid,'Manage All Content'))
	{
	  $headoflist .=  '<a href="addcontent.php'.$urlext.'" class="pageoptions">';
	  $headoflist .= $themeObject->DisplayImage('icons/system/newobject.gif', lang('addcontent'),'','','systemicon').'</a>';
	  $headoflist .= ' <a class="pageoptions" href="addcontent.php'.$urlext.'">'.lang("addcontent").'</a>';
	}

	if (check_permission($userid, 'Manage All Content'))
	{
	  $headoflist .= '&nbsp;&nbsp;&nbsp;<a href="'.$thisurl.'&amp;error=jsdisabled" class="pageoptions" onclick="xajax_reorder_display_list();return false;">';
	  $headoflist .= $themeObject->DisplayImage('icons/system/reorder.gif', lang('reorderpages'),'','','systemicon').'</a>';
	  $headoflist .= ' <a href="'.$thisurl.'&amp;error=jsdisabled" class="pageoptions" onclick="xajax_reorder_display_list();return false;">'.lang('reorderpages').'</a>';
	}

	$headoflist .='</p></div>';
	$headoflist .= '<form action="multicontent.php" method="post">';
	$headoflist .= '<div class="hidden" ><input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'"/></div>'."\n";
	$headoflist .= '<table cellspacing="0" class="pagetable">'."\n";
	$headoflist .= '<thead>';
	$headoflist .= "<tr>\n";

	if( $columnstodisplay['expand'] )
	  {
	    $headoflist .= "<th>&nbsp;</th>";
	  }
	if( $columnstodisplay['hier'] )
	  {
	    $headoflist .= "<th>&nbsp;</th>";
	  }
	if( $columnstodisplay['page'] )
	  {
	    $headoflist .= "<th class=\"pagew25\">".lang('page')."</th>\n";
	  }
	if( $columnstodisplay['template'] )
	  {
	    $headoflist .= "<th>".lang('template')."</th>\n";
	  }
	if( $columnstodisplay['friendlyname'] )
	  {
	    $headoflist .= "<th>".lang('type')."</th>\n";
	  }
	if( $columnstodisplay['owner'] )
	  {
	    $headoflist .= "<th>".lang('owner')."</th>\n";
	  }
	if( $columnstodisplay['active'] )
	  {
	    $headoflist .= "<th class=\"pagepos\">".lang('active')."</th>\n";
	  }
	if( $columnstodisplay['default'] )
	  {
	    $headoflist .= "<th class=\"pagepos\">".lang('default')."</th>\n";
	  }
	if( $columnstodisplay['move'] )
	  {
	    $headoflist .= "<th class=\"move\">".lang('move')."</th>\n";
	  }
	if( $columnstodisplay['view'] )
	  {
	    $headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
	  }
	if( $columnstodisplay['copy'] )
	  {
	    $headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
	  }
	if( $columnstodisplay['edit'] )
	  {
	    $headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
	  }
	if( $columnstodisplay['delete'] )
	  {
	    $headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
	  }
	if( $columnstodisplay['multiselect'] )
	  {
	    $headoflist .= "<th class=\"checkbox\"><input id=\"selectall\" type=\"checkbox\" onclick=\"select_all();\" /></th>\n"; // checkbox column
	  }
	$headoflist .= "</tr>\n";
	$headoflist .= '</thead>';
	$headoflist .= '<tbody>';

	ob_start();
	$opts = array();
	if( check_permission($userid, 'Remove Pages') || check_permission($userid, 'Manage All Content') )
	  {
	    $opts['delete'] = lang('delete');
	  }
	if (check_permission($userid, 'Manage All Content')) 
	  {
	    $opts['active'] = lang('active');
	    $opts['inactive'] = lang('inactive');
	    $opts['setcachable'] = lang('cachable');
	    $opts['setnoncachable'] = lang('noncachable');
	    $opts['showinmenu'] = lang('showinmenu');
	    $opts['hidefrommenu'] = lang('hidefrommenu');
	  }
	if (check_permission($userid, 'Modify Any Page') || check_permission($userid, 'Manage All Content'))
	  {
	    $opts['settemplate'] = lang('settemplate');
	  }
	if( !empty($opts) )
	  {
	    echo '<div class="pageoptions">'."\n";
	    echo '<div style="margin-top: 0; float: right; text-align: right">'."\n";
	    echo lang('selecteditems').':&nbsp;&nbsp;'; 
	    echo '<select name="multiaction">';
	    foreach( $opts as $key => $value )
	      {
		echo '<option value="'.$key.'">'.$value.'</option>';
	      }
	    echo '</select>'."\n";
            echo '<input type="submit" value="'.lang('submit').'"/></div></div>'."\n";
	  }
	/*    } */
?>
			<div style="float: left;">
<?php
	if ((check_permission($userid, 'Add Pages') && count($mypages)) || check_permission($userid,'Manage All Content'))
	{
?>
			<a href="addcontent.php<?php echo $urlext ?>" class="pageoptions">
<?php 
            echo $themeObject->DisplayImage('icons/system/newobject.gif', lang('addcontent'),'','','systemicon').'</a>';
            echo ' <a class="pageoptions" href="addcontent.php'.$urlext.'">'.lang("addcontent");
?>
			</a>
<?php 
    } 
?>
		<a style="margin-left: 10px;" href="'.$thisurl.'&amp;expandall=1" onclick="xajax_content_expandall(); return false;">
<?php 
			echo $themeObject->DisplayImage('icons/system/expandall.gif', lang('expandall'),'','','systemicon').'</a>';
		echo ' <a class="pageoptions" href="'.$thisurl.'&amp;expandall=1" onclick="xajax_content_expandall(); return false;">'.lang("expandall");
?>
			</a>&nbsp;&nbsp;&nbsp;
		<a href="<?php echo $thisurl ?>&amp;collapseall=1" onclick="xajax_content_collapseall(); return false;">
<?php 
			echo $themeObject->DisplayImage('icons/system/contractall.gif', lang('contractall'),'','','systemicon').'</a>';
		echo ' <a class="pageoptions" href="'.$thisurl.'&amp;collapseall=1" onclick="xajax_content_collapseall(); return false;">'.lang("contractall").'</a>';
		if (check_permission($userid, 'Manage All Content'))
		{
			$image_reorder = $themeObject->DisplayImage('icons/system/reorder.gif', lang('reorderpages'),'','','systemicon');
			echo '&nbsp;&nbsp;&nbsp; <a class="pageoptions" href="'.$thisurl.'&amp;error=jsdisabled" onclick="xajax_reorder_display_list();return false;">'.$image_reorder.'</a> <a class="pageoptions" href="'.$thisurl.'&amp;error=jsdisabled" onclick="xajax_reorder_display_list();return false;">'.lang('reorderpages').'</a>';
		}
?>
			</div>

			<br />

			<div class="clearb"></div>
<?php
	$footer = ob_get_contents();
	ob_end_clean();

	return $headoflist . $thelist . $footer .'</form></div>';
}

echo $themeObject->ShowMessage('', 'message');
echo $themeObject->ShowErrors('' ,'error');
?>
<div class="pagecontainer">
<?php

$hierManager =& $gCms->GetHierarchyManager();

if (isset($_GET["makedefault"]))
{
	setdefault($_GET['makedefault']);
	redirect($thisurl);
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
	redirect($thisurl);
}

if (isset($_GET['direction']))
{
	movecontent($_GET['content_id'], $_GET['parent_id'], $_GET['direction']);
}

if (isset($_GET['col']) && isset($_GET['content_id']))
{
	toggleexpand($_GET['content_id'], $_GET['col']=='1'?true:false);
}

echo '<div class="pageoverflow">';
echo $themeObject->ShowHeader('currentpages').'</div>';
echo '<div id="contentlist">'.display_content_list($themeObject).'</div>';

?>

		<p class="pageback"><a class="pageback" href="<?php echo $themeObject->BackUrl(); ?>">&#171; <?php echo lang('back')?></a></p>
		<script type="text/javascript">
		//<![CDATA[
		function select_all()
		{
	        checkboxes = document.getElementsByTagName("input");
                elem = document.getElementById('selectall');
		state = elem.checked;
	        for (i=0; i<checkboxes.length ; i++)
	        {
	                if (checkboxes[i].type == "checkbox") 
			  {
			    checkboxes[i].checked=state;
			  }
	        }
		}
		//]]>
		</script>
		<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
