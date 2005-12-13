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

check_login();
$userid = get_userid();
include_once("header.php");

$expandImg = &$themeObject->DisplayImage('icons/system/expand.gif', lang('expand'),'','','systemicon');
$contractImg = &$themeObject->DisplayImage('icons/system/contract.gif', lang('contract'),'','','systemicon');
$downImg = &$themeObject->DisplayImage('icons/system/arrow-d.gif', lang('down'),'','','systemicon');
$upImg = &$themeObject->DisplayImage('icons/system/arrow-u.gif', lang('up'),'','','systemicon');
$viewImg = &$themeObject->DisplayImage('icons/system/view.gif', lang('view'),'','','systemicon');
$editImg = &$themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon');
$deleteImg = &$themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');

function show_h(&$root) {
  $content = &$root->getContent();
  echo "<li>".($root->getLevel()==0?"root":$content->Hierarchy())."</li>";
  if ($root->getChildrenCount()>0) {
    echo "<ul>";
    $children = &$root->getChildren();
    foreach ($children as $child) {
      show_h($child);
    }
    echo "</ul>";
  }
}

function display_hierarchy(&$root) {
  global $templates;
  global $users;
  global $modifyall;
  global $userid;
  global $mypages;
  global $currow;
  global $themeObject;
  global $image_true;
  global $image_false;
  global $config;
  global $page;
  global $indent;
  global $pagelist;
  global $expandImg;
  global $contractImg;
  global $downImg;
  global $upImg;
  global $viewImg;
  global $editImg;
  global $deleteImg;
  global $openedArray;
  
  $one = &$root->getContent();
  $children = &$root->getChildren();
  $thelist ="";
    
  if ($one) {
    if (!array_key_exists($one->TemplateId(), $templates)) {
  	 $templates[$one->TemplateId()] = TemplateOperations::LoadTemplateById($one->TemplateId());
  	}
  	
    if (!array_key_exists($one->Owner(), $users))	{
  		$users[$one->Owner()] = UserOperations::LoadUserById($one->Owner());
  	} 		
  }

  
  if (isset($one) && ($modifyall || check_ownership($userid,$one->Id()) || quick_check_authorship($one->Id(), $mypages))) {
        $thelist .= "<tr class=\"$currow\" onmouseover=\"this.className='".$currow.'hover'."';\" onmouseout=\"this.className='".$currow."';\">\n";
        $thelist .= "<td>";
        if ($one->ChildCount() > 0) {
          if (!in_array($one->Id(),$openedArray)) {
            $thelist .= "<a href=\"setexpand.php?content_id=".$one->Id()."&col=0&page=".$page."\">";
            $thelist .= $expandImg;
            $thelist .= "</a>";
          } else {
            $thelist .= "<a href=\"setexpand.php?content_id=".$one->Id()."&col=1&page=".$page."\">";
            $thelist .= $contractImg;
            $thelist .= "</a>";
          }
        }
        $thelist .= "</td><td>".$one->Hierarchy()."</td>\n";
        
        $thelist .= "<td>";
        
        if ($indent) {
          for ($i=1;$i < $root->getLevel();$i++) {
            $thelist .= "-&nbsp;&nbsp;&nbsp;";
          }
        } ## if indent

        $thelist .= "<a href=\"editcontent.php?content_id=".$one->Id()."&page=".$page."\">".$one->Name()."</a></td>\n";
  			if (isset($templates[$one->TemplateId()]->name) && $templates[$one->TemplateId()]->name) {
  						 $thelist .= "<td>".$templates[$one->TemplateId()]->name."</td>\n";
  			}	else {
	  				$thelist .= "<td>&nbsp;</td>\n";
	  		}

	  		$thelist .= "<td>".$one->FriendlyName()."</td>\n";
  
 				if ($one->Owner() > -1)	{
  				$thelist .= "<td>".$users[$one->Owner()]->username."</td>\n";
	  		}	else {
  						$thelist .= "<td>&nbsp;</td>\n";
  			}

  			if($one->Active()) {
  				$thelist .= "<td class=\"pagepos\">".($one->DefaultContent() == 1?$image_true:"<a href=\"listcontent.php?setinactive=".$one->Id()."\">".$image_true."</a>")."</td>\n";
  			}	else {
  					$thelist .= "<td class=\"pagepos\"><a href=\"listcontent.php?setactive=".$one->Id()."\">".$image_false."</a></td>\n";
  			}
  	
				if ($one->IsDefaultPossible() == TRUE) {
  						$thelist .= "<td class=\"pagepos\">".($one->DefaultContent() == true?$image_true:"<a href=\"listcontent.php?makedefault=".$one->Id()."\" onclick=\"return confirm('".lang("confirmdefault")."');\">".$image_false."</a>")."</td>\n";
  			}	else {
  						$thelist .= "<td>&nbsp;</td>";
  			}   
        
        // code for move up is simple
        if ($modifyall) {
        $thelist .= "<td>";
          $parentNode = &$root->getParentNode();
          if ($parentNode!=null) {
            $sameLevel = &$parentNode->getChildren();
            if (count($sameLevel)>1) {
              if ($parentNode->findChildNodeIndex($root)==0) { // first 
                $thelist .= "<a href=\"movecontent.php?direction=down&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
    						$thelist .= $downImg;
    						$thelist .= "</a>";
              } else if ($parentNode->findChildNodeIndex($root)==count($sameLevel)-1) { // last
                $thelist .= "<a href=\"movecontent.php?direction=up&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
    						$thelist .= $upImg;
    						$thelist .= "</a>";
              } else { // middle
                $thelist .= "<a href=\"movecontent.php?direction=down&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
    						$thelist .= $downImg;
    						$thelist .= "</a>&nbsp;<a href=\"movecontent.php?direction=up&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
    						$thelist .= $upImg;
    						$thelist .= "</a>";
              }
            }
          }
          $thelist .= "</td>";
        }
        // end of move code
        
        if ($config["query_var"] == "")	{
  						$thelist .= "<td class=\"pagepos\"><a href=\"".$config["root_url"]."/index.php/".$one->Id()."\" rel=\"external\">";
  						$thelist .= $viewImg;
              $thelist .= "</a></td>\n";
  			}	else if ($one->Alias() != "")	{
  						$thelist .= "<td class=\"pagepos\"><a href=\"".$config["root_url"]."/index.php?".$config['query_var']."=".$one->Alias()."\" rel=\"external\">";
                    	$thelist .= $viewImg;
                    	$thelist .= "</a></td>\n";
  			}	else {
  						$thelist .= "<td class=\"pagepos\"><a href=\"".$config["root_url"]."/index.php?".$config['query_var']."=".$one->Id()."\" rel=\"external\">";
              $thelist .= $viewImg;
              $thelist .= "</a></td>\n";
  			}
  			
        $thelist .= "<td class=\"pagepos\"><a href=\"editcontent.php?content_id=".$one->Id()."\">";
  			$thelist .= $editImg;;
        $thelist .= "</a></td>\n";
  			$thelist .= "<td class=\"pagepos\"><a href=\"deletecontent.php?content_id=".$one->Id()."\" onclick=\"return confirm('".lang('deleteconfirm')."');\">";
        $thelist .= $deleteImg;
        $thelist .= "</a></td>\n";
  			$thelist .= "</tr>\n";  	
				($currow == "row1"?$currow="row2":$currow="row1");
			}
	
  $pagelist[] = &$thelist;
  if (!isset($one) || in_array($one->Id(),$openedArray)) {
      foreach ($children as $child) { 
        display_hierarchy($child);
      }
  }
} // function display_hierarchy

$openedArray=array();
if (get_preference($userid, 'collapse', '') != '')	{
	$tmp  = explode('.',get_preference($userid, 'collapse'));
	foreach ($tmp as $thisCol) {
		$colind = substr($thisCol,0,strpos($thisCol,'='));
		if ($colind!="") $openedArray[] = $colind;
		}
}

if (isset($_GET["message"])) {
	$message = preg_replace('/\</','',$_GET['message']);
	echo '<div class="pagemcontainer"><p class="pagemessage">'.$message.'</p></div>';
}

?>
<div class="pagecontainer">
	<div class="pageoverflow">
<?php

	$modifyall = check_permission($userid, 'Modify Any Page');
	if ($modifyall) {
		if (isset($_GET["makedefault"])) {
			ContentManager::SetDefaultContent($_GET["makedefault"]);
		}
	}

  $mem1=memory_get_usage();
	$hierManager = &ContentManager::GetAllContentAsHierarchy(false,$openedArray);
	$hierarchy = &$hierManager->getRootNode();
  $mem2=memory_get_usage();
  $nbNodes = count($hierManager->index);
  //echo ($mem2-$mem1)." bytes used for hierarchy of $nbNodes nodes";

	$mypages = author_pages($userid);

    // check if we're activating a page
    if (isset($_GET["setactive"])) {
      // to activate a page, you must be admin, owner, or additional author
		  $permission = ($modifyall || 
			check_ownership($userid,$_GET["setactive"]) ||
			check_authorship($userid,$_GET["setactive"]));

  		if($permission) {
        $node = &$hierManager->getNodeById($_GET["setactive"]);
			  $value = $node->getContent();
				#Modify the object inline
				$value->SetActive(true);
				$value->Save();
      }
    }

    // perhaps we're deactivating a page instead?
    if (isset($_GET["setinactive"])) {
     	// to deactivate a page, you must be admin, owner, or additional author
     	$permission = ($modifyall || 
			check_ownership($userid,$_GET["setinactive"]) || 
			check_authorship($userid,$_GET["setinactive"]));
     	if($permission) {
        $node = &$hierManager->getNodeById($_GET["setinactive"]);
	   	  $value = $node->getContent();
				#Modify the object inline
				$value->SetActive(false);
				$value->Save();
      }
    }

	$page = 1;
	if (isset($_GET['page'])) $page = $_GET['page'];
	$limit = get_preference($userid, 'paging', 0);

	$thelist = '';
	$count = 0;

	$currow = "row1";
		
	// construct true/false button images
    $image_true = $themeObject->DisplayImage('icons/system/true.gif', lang('true'),'','','systemicon');
    $image_false = $themeObject->DisplayImage('icons/system/false.gif', lang('false'),'','','systemicon');

	$counter = 0;

	#Setup array so we don't load more templates than we need to
	$templates = array();

	#Ditto with users
	$users = array();

	$menupos = array();

	$indent = get_preference($userid, 'indent', true);
	if ($hierarchy->hasChildren()){
   display_hierarchy($hierarchy);
   foreach ($pagelist as $item) {
    $thelist.=$item;
   }
  	$thelist .= '</tbody>';
		$thelist .= "</table>\n";
	}
	
  $counter = 1;	
	if (! $counter)
	{
		$thelist = "<p>".lang('noentries')."</p>";
	}

	$headoflist = '';
	
	if ($limit != 0 && $counter > $limit)
	{
		$headoflist .= "<p class=\"pageshowrows\">".pagination($page, $counter, $limit)."</p>";
	}

	$headoflist .= '<p class="pageheader">'.lang('currentpages').'</p></div>';
	if ($counter)
	{
		
		$headoflist .= '<table cellspacing="0" class="pagetable">'."\n";
		$headoflist .= '<thead>';
		$headoflist .= "<tr>\n";
		$headoflist .= "<th>&nbsp;</th>";
		$headoflist .= "<th>&nbsp;</th>";
		$headoflist .= "<th class=\"pagew25\">".lang('title')."</th>\n";
		$headoflist .= "<th>".lang('template')."</th>\n";
		$headoflist .= "<th>".lang('type')."</th>\n";
		$headoflist .= "<th>".lang('owner')."</th>\n";
		$headoflist .= "<th class=\"pagepos\">".lang('active')."</th>\n";
		$headoflist .= "<th class=\"pagepos\">".lang('default')."</th>\n";
		if ($modifyall)
		{
			$headoflist .= "<th class=\"pagepos\">".lang('move')."</th>\n";
		}
		$headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
		$headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
		$headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
		$headoflist .= "</tr>\n";
		$headoflist .= '</thead>';
		$headoflist .= '<tbody>';
	}
	echo $headoflist . $thelist;
	
	if (check_permission($userid, 'Add Pages'))
	{
?>
	<div class="pageoptions">
		<p class="pageoptions">
			<a href="addcontent.php">
				<?php 
					echo $themeObject->DisplayImage('icons/system/newobject.gif', lang('addcontent'),'','','systemicon').'</a>';
					echo ' <a class="pageoptions" href="addcontent.php">'.lang("addcontent");
				?>
			</a>&nbsp;&nbsp;&nbsp;
			<a href="setexpand.php?expandall=1">
				<?php 
					echo $themeObject->DisplayImage('icons/system/expandall.gif', lang('expandall'),'','','systemicon').'</a>';
					echo ' <a class="pageoptions" href="setexpand.php?expandall=1">'.lang("expandall");
				?>
			</a>&nbsp;&nbsp;&nbsp;
			<a href="setexpand.php?collapseall=1">
				<?php 
					echo $themeObject->DisplayImage('icons/system/contractall.gif', lang('contractall'),'','','systemicon').'</a>';
					echo ' <a class="pageoptions" href="setexpand.php?collapseall=1">'.lang("contractall");
				?>
			</a>
		</p>
	</div>
</div>
<p class="pageback"><a class="pageback" href="<?php echo $themeObject->BackUrl(); ?>">&#171; <?php echo lang('back')?></a></p>
<?php
	}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
