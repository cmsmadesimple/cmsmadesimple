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

function show_h(&$root, &$sortableLists)
{
  $content = &$root->getContent();
  
  if ($root->getLevel()==0)
    {
      //	echo "<li>\n";
      //   echo	    "root";
    }
  else
    {
      echo '<li id="item_'.$content->mId.'">'."\n";
      echo 'h: '.$content->Hierarchy();
      echo ' id: '.$content->mId.' '.$content->mName;
      //	print_r($content);
       }
  if ($root->getChildrenCount()>0)
    {
      if ($root->getLevel()==0)
	{
	  $sortableLists->addList('content0','content0ListOrder');
	  echo '<ul id="content0" class="sortableList">'."\n";
	}
      else
	{
	  $sortableLists->addList('content'.$content->mId,'content'.$content->mId.'ListOrder');
	  echo '<ul id="content'.$content->mId.'" class="sortableList">'."\n";
	}
      $children = &$root->getChildren();
      foreach ($children as $child)
	{
	  show_h($child, $sortableLists);
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
    show_h($hierarchy, $sortableLists);
  }

$sortableLists->debug = true;
$sortableLists->printTopJS();

$sortableLists->printForm($_SERVER['PHP_SELF'], 'POST', 'Submit', 'button');

if(isset($_POST['sortableListsSubmitted'])) {
	?>
	<br><br>
	The update statements below would update the database with the new order:<br><br>
	<div style="margin-left:40px;">
	<?
	$orderArray = SLLists::getOrderArray($_POST['categoriesListOrder'],'categories');
	foreach($orderArray as $item) {
		$sql = "UPDATE categories set orderid=".$item['order']." WHERE carid=".$item['element'];
		echo $sql.'<br>';
	}
	?>
	</div>
	<?
}
?>
<?php
$sortableLists->printBottomJS();
echo '</div></div>';
include_once("footer.php");
?>
