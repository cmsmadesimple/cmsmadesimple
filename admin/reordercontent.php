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
#$Id: listcontent.php 2978 2006-06-30 17:57:34Z elijahlofgren $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");


check_login();
$userid = get_userid();
include_once("header.php");

require(cms_join_path($dirname,'lib', 'sllists','SLLists.class.php'));
$sortableLists = new SLLists( $config["root_url"].'/lib/scriptaculous');

function show_h(&$root, &$sortableLists, &$listArray)
{
  $content = &$root->getContent();
  
  if ($root->getLevel()==0)
    {
      $content->mId = 0;
    }
  else
    {
      echo '<li id="item_'.$content->mId.'">'."\n";
      echo $content->mName;
      //	print_r($content);
    }
  if ($root->getChildrenCount()>0)
    {
      $sortableLists->addList('parent'.$content->mId,'parent'.$content->mId.'ListOrder');
      $listArray[$content->mId] = 'parent'.$content->mId.'ListOrder';
      echo '<ul id="parent'.$content->mId.'" class="sortableList">'."\n";
      
      $children = &$root->getChildren();
      foreach ($children as $child)
	{
	  show_h($child, $sortableLists, $listArray);
	}
      echo "</ul>\n";
    }
  else 
    {
      echo "</li>\n";
    }
}
?>
<div class="pagecontainer">
<div class="pageoverflow">
<?php

$modifyall = check_permission($userid, 'Modify Any Page');

echo $themeObject->ShowHeader('reorderpages');

if ($modifyall)
  {
    $hierManager = &ContentManager::GetAllContentAsHierarchy(false);
  }
 else
   {
     $hierManager =& $gCms->GetHierarchyManager();
   }

$hierarchy = &$hierManager->getRootNode();


if ($hierarchy->hasChildren())
  {
    $listArray = array();
    show_h($hierarchy, $sortableLists, $listArray);
  }

$sortableLists->debug = true;
$sortableLists->printTopJS();

$sortableLists->printForm($_SERVER['PHP_SELF'], 'POST', 'Submit', 'button');

if(isset($_POST['sortableListsSubmitted']))
  {
    global $gCms;
    $hm =& $gCms->GetHierarchyManager();
    $order_changed = FALSE;
    foreach ($listArray AS $parent_id => $order)
      {
	$orderArray = SLLists::getOrderArray($_POST[$order], 'parent'.$parent_id);
	foreach($orderArray as $item)
	  {
	    $node =& $hm->sureGetNodeById($item['element']);
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
    if (TRUE == $order_changed) {
      ContentManager::SetAllHierarchyPositions();
    } 
  }

$sortableLists->printBottomJS();
echo '</div></div>';
include_once("footer.php");
?>
