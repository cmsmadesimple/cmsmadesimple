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

function content_list_ajax()
{
	$objResponse = new xajaxResponse();
	$objResponse->clear("contentlist", "innerHTML");
	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	return $objResponse;
}

function check_modify_all($userid)
{
  return check_permission($userid, 'Modify Any Page') || check_permission($userid,'Manage All Content');
}

function setdefault($contentid)
{
  $gCms = cmsms();
	$userid = get_userid();
	
	$result = false;

	if (check_permission($userid,'Manage All Content'))
	{
		$hierManager = $gCms->GetHierarchyManager();
		$node = $hierManager->getNodeById($contentid);
		if (isset($node))
		{
			$value = $node->getContent();
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
		
		$db = $gCms->GetDb();
		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE default_content=1";
		$old_id = $db->GetOne($query);
		if (isset($old_id))
		{
			$node = $hierManager->getNodeById($old_id);
			if (isset($node))
			{
				$value = $node->getContent();
				if (isset($value))
				{
					$value->SetDefaultContent(false);
					$value->Save();
				}
			}
		}
		
		$node = $hierManager->getNodeById($contentid);
		if (isset($node))
		{
			$value = $node->getContent();
			if (isset($value))
			{
				$value->SetDefaultContent(true);
				$value->Save();
			}
		}

		$result = true;
		$contentops = $gCms->GetContentOperations();
		$contentops->ClearCache();
	}
	return $result;
}

function content_setdefault($contentid)
{
	$objResponse = new xajaxResponse();
	
	setdefault($contentid);

	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	$objResponse->script("$('#tr_$contentid').effect('highlight', [], 3000);");
	return $objResponse;
}

function content_setactive($contentid)
{
	$objResponse = new xajaxResponse();
	
	setactive($contentid);

	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	$objResponse->script("$('#tr_$contentid').effect('highlight', [], 3000);");
	return $objResponse;
}

function content_setinactive($contentid)
{
	$objResponse = new xajaxResponse();
	
	setactive($contentid, false);

	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	$objResponse->script("$('#tr_$contentid').effect('highlight', [], 3000);");
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
	$contentops = cmsms()->GetContentOperations();
	$all = $contentops->GetAllContent(false);
	$cs = '';
	foreach ($all as $thisitem)
	{
	  if( is_object($thisitem) && $thisitem instanceof ContentBase )
	    {
		if ($thisitem->HasChildren())
		{
			$cs .= $thisitem->Id().'=1.';
		}
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
	$objResponse->script("$('#tr_$contentid').effect('highlight', [], 3000);");
	return $objResponse;
}

function content_delete($contentid)
{
	$objResponse = new xajaxResponse();
	
	deletecontent($contentid);

	$objResponse->script("$('#tr_$contentid').effect('fade', [], 3000);");
	$objResponse->clear("contentlist", "innerHTML");
	$objResponse->assign("contentlist", "innerHTML", display_content_list());
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
	$userid = get_userid();
	
	$hierManager = cmsms()->GetHierarchyManager();
	
	// to activate a page, you must be admin, owner, or additional author
	$permission = (	check_ownership($userid, $contentid) ||
			check_authorship($userid, $contentid) ||
			check_permission($userid, 'Manage All Content')
	);

	if($permission)
	  {
		$node = $hierManager->getNodeById($contentid);
		$value = $node->getContent();
		$value->SetActive($active);
		$value->Save();
		$contentops = cmsms()->GetContentOperations();
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
	$objResponse->script("$('#tr_$contentid').effect('highlight', [], 3000);");

  // reset lock
  return $objResponse;

}

function movecontent($contentid, $parentid, $direction = 'down')
{
  $db = cmsms()->GetDb();
  $userid = get_userid();

  if (check_permission($userid, 'Manage All Content') ||
      (check_permission($userid, 'Reorder Content') && check_peer_authorship($userid,$contentid)) )
    {
      $order = 1;
      
      // Grab necessary info for fixing the item_order
      $query = "SELECT item_order FROM ".cms_db_prefix()."content WHERE content_id = ?";
      $order = $db->GetOne($query, array($contentid));
      
      $time = $db->DBTimeStamp(time());
      if ($direction == "down")
	{
	  $query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order - 1), modified_date = '.$time.' WHERE item_order = ? AND parent_id = ?';
	  // echo $query, $order + 1, $parent_id;
	  $db->Execute($query, array($order + 1, $parentid));
	  $query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order + 1), modified_date = '.$time.' WHERE content_id = ? AND parent_id = ?';
	  // echo $query, $content_id, $parent_id;
	  $db->Execute($query, array($contentid, $parentid));
	}
      else if ( ($direction == "up") && ($order > 1) )
	{
	  $query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order + 1), modified_date = '.$time.' WHERE item_order = ? AND parent_id = ?';
	  // echo $query;
	  $db->Execute($query, array($order - 1, $parentid));
	  $query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order - 1), modified_date = '.$time.' WHERE content_id = ? AND parent_id = ?';
	  // echo $query;
	  $db->Execute($query, array($contentid, $parentid));
	}

      //sleep(15); //waiting for updating DB. Better 5 but 15 is good for testing concurrent processes and work!
      $contentops = cmsms()->GetContentOperations();
      $contentops->SetAllHierarchyPositions();
      $contentops->ClearCache();
    }
}

function deletecontent($contentid)
{
	$userid = get_userid();
	$mypages = author_pages($userid);

	$access = (check_permission($userid, 'Remove Pages') &&
	  (check_ownership($userid,$contentid) ||
	   quick_check_authorship($contentid,$mypages)))
	  || check_permission($userid, 'Manage All Content');
	
	$gCms = cmsms();
	$hierManager = $gCms->GetHierarchyManager();

	if ($access)
	{
		$node = $hierManager->getNodeById($contentid);
		if ($node)
		{
			$contentobj = $node->getContent(true);
			$childcount = 0;
			$parentid = -1;
			$parent = $node->getParent();
			if ($parent)
			{
			  $parentContent = $parent->getContent();
			  if (is_object($parentContent))
			    {
			      $parentid = $parentContent->Id();
			      $childcount = $parent->getChildrenCount();
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

				$contentops = $gCms->GetContentOperations();
				$contentops->SetAllHierarchyPositions();
				
				#See if this is the last child... if so, remove
				#the expand for it
				if ($childcount == 1 && $parentid > -1)
				{
					toggleexpand($parentid, true);
				}
				
				#Do the same with this page as well
				toggleexpand($contentid, true);
				
				audit($contentid, $title, lang_en('deleted_content'));
				
				$contentops->ClearCache();
			
				$_GET['message'] = 'contentdeleted';
			}
		}
	}
}

function show_h(&$root, &$sortableLists, &$listArray, &$output)
{
	$content = $root->getContent();
	if( !is_object($content) ) return;

	$contentops = cmsms()->GetContentOperations();

	$output .= '<li id="item_'.$content->mId.'">'."\n";
	$output .= '('.$contentops->CreateFriendlyHierarchyPosition($content->mHierarchy).') '.cms_htmlentities($content->mMenuText, '', '', true);

	if ($root->getChildrenCount()>0)
	{
		$sortableLists->addList('parent'.$content->mId,'parent'.$content->mId.'ListOrder');
		$listArray[$content->mId] = 'parent'.$content->mId.'ListOrder';
		$output .= '<ul id="parent'.$content->mId.'" class="sortableList">'."\n";

		$children = $root->getChildren(false,true);
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
	$gCms = cmsms();
	$config = $gCms->GetConfig();
	
	$userid = get_userid();
	
	$path = cms_join_path(dirname(dirname(__FILE__)), 'lib', 'sllists', 'SLLists.class.php');
	require($path);

	$sortableLists = new SLLists($config["root_url"].'/lib/scriptaculous');
	
	$hierManager = $gCms->GetHierarchyManager();
	$hierarchy = $hierManager->getRootNode();
	
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
	$gCms = cmsms();

	if (check_permission($userid,'Manage All Content'))
	{
		$config = $gCms->GetConfig();
		$db = $gCms->GetDb();
		$contentops = $gCms->GetContentOperations();
	    $hm = $contentops->GetAllContentAsHierarchy(false);
		$hierarchy = $hm->getRootNode();
	
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
				$node = $hm->sureGetNodeById($item['element']);
				if ($node != NULL)
				{
				    $one = $node->getContent();
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
			$contentops = $gCms->GetContentOperations();
			$contentops->SetAllHierarchyPositions();
			$contentops->ClearCache();
		}
	}
	
	$objResponse->assign("contentlist", "innerHTML", display_content_list());
	return $objResponse;
}


/** check to make sure that the user has authorship of all of the peers of the
 * specified page
 *
 * @author calguy1000
 * @since 1.10
 * @returns boolean
 */
function check_peer_authorship($userid,$contentid)
{
  if( $userid <= 0 || $contentid <= 0 ) return FALSE;
  if( check_permission($userid,'Modify Any Page') ) return TRUE;

  $hm = cmsms()->GetHierarchyManager();
  $node = $hm->getNodeById($contentid);
  if( !$node ) return FALSE;
  
  $parent = $node->getParentNode();
  if( !$node )
    {
      // no parent means that $contentid is at the root level
      $parent = $hm;
    }

  $children = $parent->getChildren();
  if( !$children ) return FALSE;

  $mypages = author_pages($userid);
  for( $i = 0; $i < count($children); $i++ )
    {
      $the_id = $children[$i]->getId();
      if( !check_ownership($userid,$the_id) && !quick_check_authorship($the_id,$mypages) )
	{
	  return FALSE;
	}
    }
  return TRUE;
}

function check_children(&$root, &$mypages, &$userid)
{
	$result = false;
	$content = $root->getContent();
	if (isset($content))
	{
		$result = in_array($content->Id(), $mypages, false);
		if (!$result)
		{
		  $children = $root->getChildren(false,true);
		  if( is_array($children) && count($children) > 0 )
		  {
		    foreach ($children as $child)
		      {
			$result = check_children($child, $mypages, $userid);
			if ($result)
			  break;
		      }
		  }
		}
	}
	return $result;
}

function display_hierarchy(&$root, &$userid, $modifyall, &$users, &$menupos, &$openedArray, &$pagelist, &$image_true, &$image_set_false, &$image_set_true, &$upImg, &$downImg, &$viewImg, &$editImg, &$copyImg, &$deleteImg, &$expandImg, &$contractImg, &$mypages, &$page, $columnstodisplay, $author_allpages )
{
  global $thisurl;
  global $urlext;
  global $currow;
  global $config;
  global $page;
  global $indent;
  
  if(empty($currow)) $currow = 'row1';
  
  $children = $root->getChildren(false,true);
  $one = $root->getContent();
  $thelist = '';

  if (!(isset($one) && $one != NULL))
    {
      return;
    }
  
  if (!array_key_exists($one->Owner(), $users))
    {
      $userops = cmsms()->GetUserOperations();
      $users[$one->Owner()] = $userops->LoadUserById($one->Owner());
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
	      $str = $one->mMenuText;
	      if( get_site_preference('listcontent_showtitle',0) )
		{
		  $str = $one->mName;
		}
	      if ($display == 'edit')
		$txt .= '<a href="editcontent.php'.$urlext.'&amp;content_id='.$one->mId.'&amp;page='.$page.'" title="'. cms_htmlentities($one->mName.' ('.$one->mAlias.')', '', '', true). '"><label for="multicontent-'.$one->mId.'">'. cms_htmlentities($str, '', '', true) . '</label></a>';
	      else
		$txt .= cms_htmlentities($str, '', '', true);
	    }
	  if( !empty($txt) ) $columns['page'] = $txt;
	}

      /* alias column */
      if( $columnstodisplay['alias'] )
	{
	  $columns['alias'] = '&nbsp;';
	  $txt = '';
	  if( $one->HasUsableLink() && $one->mAlias != '' )
	    {
	      $txt = $one->Alias();
	    }
	  if( !empty($txt) ) $columns['alias'] = $txt;
	}
       
      /* url column */
      if( $columnstodisplay['url'] )
	{
	  $columns['url'] = '&nbsp;';
	  $txt = '';
	  if( $one->HasUsableLink() && $one->URL() != '' )
	    {
	      $url = $one->URL();
	      if( strlen($url) > 30 )
		{
		  $url = '...'.substr($url,strlen($url)-27);
		}
	      $txt = $url;
	    }
	  if( !empty($txt) ) $columns['url'] = $txt;
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
	      $template = TemplateOperations::get_instance()->LoadTemplateById($one->TemplateId());
	      if( $template && check_permission($userid,'Modify Template') )
		{
		  $txt .= "<a href=\"edittemplate.php".$urlext."&amp;template_id=".$one->TemplateId()."&amp;from=content\">".cms_htmlentities($template->name, '', '', true)."</a>";
		}
   	      else if( $template )
		{
		  $txt .= $template->name;
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
	  if (check_permission($userid, 'Manage All Content') && $one->Type() != 'errorpage' )
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
	  if (check_permission($userid, 'Manage All Content') || $author_allpages )
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
	    if ($url != '' && $url != '#' && $one->IsViewable() && $one->Active())
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
	      ((check_permission($userid,'Add Pages') &&
		(check_ownership($userid, $one->Id()) || quick_check_authorship($one->Id(), $mypages)))
	       || check_permission($userid,'Manage All Content')) )
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
		  ((check_permission($userid, 'Remove Pages') &&
		    (check_ownership($userid,$one->Id()) || 
		     quick_check_authorship($one->Id(),$mypages)))
		   || check_permission($userid,'Manage All Content')) 
		 )
		{
		  //$txt .= "<a href=\"{$thisurl}&amp;deletecontent=".$one->Id()."\" onclick=\"confirm('".cms_html_entity_decode_utf8(lang('deleteconfirm', $one->mName), true)."');\">";
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
	  if ( (($structure == 1) || (($remove == 1) && ($editperms == 1))) &&
	       ($one->Type() != 'errorpage' ))
	    {
	      $txt .= '<input type="checkbox" id="multicontent-'.$one->Id().'" name="multicontent-'.$one->Id().'" />';
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

    $pagelist[] = $thelist;

    $indent = get_preference($userid, 'indent', true);

    if (in_array($one->Id(),$openedArray) && is_array($children) && count($children) )
    {
      // count through all the children and see if we can display the move column.
      $author_allpages = check_permission($userid,'Reorder Content') && check_peer_authorship($userid,$children[0]->getId());

      foreach ($children as $child)
        { 
	  display_hierarchy($child, $userid, $modifyall, $users, $menupos, $openedArray, $pagelist, $image_true, $image_set_false, $image_set_true, $upImg, $downImg, $viewImg, $editImg, $copyImg, $deleteImg, $expandImg, $contractImg, $mypages, $page, $columnstodisplay, $author_allpages);
        }
    }
} // function display_hierarchy

function display_content_list($themeObject = null)
{
  $gCms = cmsms();
	global $thisurl;
	global $urlext;

	check_login();
	$userid = get_userid();
	
	// setup which columns to display.
	$mypages = author_pages($userid);
	$columnstodisplay = array();
	$columnstodisplay['expand'] = 1;
	$columnstodisplay['hier'] = 1;
	$columnstodisplay['page'] = 1;
	$columnstodisplay['alias'] = get_site_preference('listcontent_showalias',1);
	$columnstodisplay['url'] = get_site_preference('listcontent_showurl',1);
	$columnstodisplay['template'] = 1;
	$columnstodisplay['friendlyname'] = 1;
	$columnstodisplay['owner'] = 1;
	$columnstodisplay['active'] = check_permission($userid, 'Manage All Content');
	$columnstodisplay['default'] = check_permission($userid, 'Manage All Content');
	$columnstodisplay['move'] = check_permission($userid, 'Manage All Content') || check_permission($userid,'Reorder Content');
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
		$themeObject = AdminTheme::GetThemeObject();

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
	$templateops = TemplateOperations::get_instance()->LoadTemplates();

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

        $hierarchy = $gCms->GetHierarchyManager();

	$rowcount = 0;
	if ($hierarchy->hasChildren())
	{
		$pagelist = array();

		$children = $hierarchy->getChildren(false,true);
		$author_allpages = check_permission($userid,'Reorder Content') && check_peer_authorship($userid,$children[0]->getId());

		foreach ($children as $child)
		{ 
		  display_hierarchy($child, $userid, check_modify_all($userid), $users, $menupos, $openedArray, $pagelist, $image_true, $image_set_false, $image_set_true, $upImg, $downImg, $viewImg, $editImg, $copyImg, $deleteImg, $expandImg, $contractImg, $mypages, $page, $columnstodisplay, $author_allpages );
		}
		$rowcount += count($pagelist);
		foreach ($pagelist as $item)
		{
			$thelist.=$item;
		}

		$thelist .= '</tbody>';
		$thelist .= "</table>\n";
	}


	$headoflist = '';

	$headoflist .= '<div class="pageoverflow"><p class="pageoptions">';
	if (check_permission($userid, 'Add Pages') || check_permission($userid,'Manage All Content'))
	{
	  $headoflist .=  '<a href="addcontent.php'.$urlext.'" class="pageoptions">';
	  $headoflist .= $themeObject->DisplayImage('icons/system/newobject.gif', lang('addcontent'),'','','systemicon').'</a>';
	  $headoflist .= ' <a class="pageoptions" href="addcontent.php'.$urlext.'">'.lang("addcontent").'</a>';
	}

	$headoflist .= '<a style="margin-left: 10px;" href="'.$thisurl.'&amp;expandall=1" onclick="xajax_content_expandall(); return false;">';
	$headoflist .= $themeObject->DisplayImage('icons/system/expandall.gif', lang('expandall'),'','','systemicon').'</a>';
	$headoflist .= ' <a class="pageoptions" href="'.$thisurl.'&amp;expandall=1" onclick="xajax_content_expandall(); return false;">'.lang("expandall").'</a>&nbsp;&nbsp;&nbsp;';
	$headoflist .= ' <a href="'.$thisurl.'&amp;collapseall=1" onclick="xajax_content_collapseall(); return false;">';
	$headoflist .= $themeObject->DisplayImage('icons/system/contractall.gif', lang('contractall'),'','','systemicon').'</a>';
	$headoflist .= ' <a class="pageoptions" href="'.$thisurl.'&amp;collapseall=1" onclick="xajax_content_collapseall(); return false;">'.lang("contractall").'</a>';

	if (check_permission($userid, 'Manage All Content'))
	{
	  $reorderurl = "ordercontent.php?".CMS_SECURE_PARAM_NAME."=".$_SESSION[CMS_USER_KEY];
	  $headoflist .= '&nbsp;&nbsp;&nbsp;<a href="'.$reorderurl.'">';
	  $headoflist .= $themeObject->DisplayImage('icons/system/reorder.gif', lang('reorderpages'),'','','systemicon').'</a>';
	  $headoflist .= ' <a class="pageoptions" href="'.$reorderurl.'">'.lang('reorderpages').'</a>';
	}

	$headoflist .='</p></div>';
	$headoflist .= '<form action="multicontent.php" method="post">';
	$headoflist .= '<div class="hidden" ><input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'"/></div>'."\n";
	$headoflist .= '<table cellspacing="0" class="pagetable">'."\n";
	$headoflist .= '<thead>';
	$headoflist .= "<tr>\n";

	// setup column titles.
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
	    $str = lang('menutext');
	    if( get_site_preference('listcontent_showtitle') )
	      {
		$str = lang('title');
	      }
	    $headoflist .= '<th class="pagew25" title="'.lang('lctitle_page').'">'.lang('page')." <em>({$str})</em></th>\n";
	  }
	if( $columnstodisplay['alias'] )
	  {
	    $headoflist .= '<th title="'.lang('lctitle_alias').'">'.lang('pagealias')."</th>\n";
	  }
	if( $columnstodisplay['url'] )
	  {
	    $headoflist .= '<th title="'.lang('lctitle_url').'">'.lang('url')."</th>\n";
	  }
	if( $columnstodisplay['template'] )
	  {
	    $headoflist .= '<th title="'.lang('lctitle_template').'">'.lang('template')."</th>\n";
	  }
	if( $columnstodisplay['friendlyname'] )
	  {
	    $headoflist .= "<th>".lang('type')."</th>\n";
	  }
	if( $columnstodisplay['owner'] )
	  {
	    $headoflist .= '<th title="'.lang('lctitle_owner').'">'.lang('owner')."</th>\n";
	  }
	if( $columnstodisplay['active'] )
	  {
	    $headoflist .= '<th title="'.lang('lctitle_active').'" class="pagepos">'.lang('active')."</th>\n";
	  }
	if( $columnstodisplay['default'] )
	  {
	    $headoflist .= '<th title="'.lang('lctitle_default').'" class="pagepos">'.lang('default')."</th>\n";
	  }
	if( $columnstodisplay['move'] )
	  {
	    $headoflist .= '<th title="'.lang('lctitle_move').'" class="move">'.lang('move')."</th>\n";
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
	    $headoflist .= '<th title="'.lang('lctitle_multiselect').'" class="checkbox"><label for="selectall">'.lang('toggle').'</label><br /><input id="selectall" type="checkbox" onclick="select_all();" /></th>'."\n"; // checkbox column
	  }
	$headoflist .= "</tr>\n";
	$headoflist .= '</thead>';
	$headoflist .= '<tbody>';

	ob_start();
	$opts = array();
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
	    bulkcontentoperations::register_function(lang('secure'),'secure');
	    bulkcontentoperations::register_function(lang('insecure'),'insecure');
	    bulkcontentoperations::register_function(lang('settemplate'),'settemplate');
	  }
	$opts = bulkcontentoperations::get_operation_list();
	if( !empty($opts) )
	  {
	    echo '<div class="pageoptions">'."\n";
	    echo '<div style="margin-top: 0; float: right; text-align: right">'."\n";
	    echo '<label for="multiaction">'.lang('selecteditems').'</label>:&nbsp;&nbsp;'; 
	    echo '<select name="multiaction" id="multiaction">';
	    foreach( $opts as $key => $value )
	      {
		echo '<option value="'.$key.'">'.$value.'</option>';
	      }
	    echo '</select>'."\n";
            echo '<input type="submit" accesskey="s" value="'.lang('submit').'"/></div></div>'."\n";
	  }
	/*    } */
?>
			<div style="float: left;">
<?php
	if (check_permission($userid, 'Add Pages')|| check_permission($userid,'Manage All Content'))
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
			$reorderurl = "ordercontent.php?".CMS_SECURE_PARAM_NAME."=".$_SESSION[CMS_USER_KEY];
			echo '&nbsp;&nbsp;&nbsp; <a class="pageoptions" href="'.$reorderurl.'">'.$image_reorder.'</a> <a class="pageoptions" href="'.$reorderurl.'">'.lang('reorderpages').'</a>';
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

$hierManager = cmsms()->GetHierarchyManager();

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
