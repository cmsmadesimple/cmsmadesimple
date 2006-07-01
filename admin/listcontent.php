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

include_once("../lib/classes/class.admintheme.inc.php");

require_once(dirname(dirname(__FILE__)) . '/lib/xajax/xajax.inc.php');
$xajax = new xajax();
$xajax->registerFunction('content_list_ajax');
$xajax->registerFunction('content_setactive');
$xajax->registerFunction('content_setinactive');
$xajax->registerFunction('content_setdefault');
$xajax->registerFunction('content_expandall');
$xajax->registerFunction('content_collapseall');
$xajax->registerFunction('content_toggleexpand');
$xajax->registerFunction('content_delete');

$xajax->processRequests();
$headtext = $xajax->getJavascript('../lib/xajax')."\n";
$headtext .= '<script type="text/javascript" src="../lib/scriptaculous/prototype.js"></script>';
$headtext .= '<script type="text/javascript" src="../lib/scriptaculous/scriptaculous.js"></script>';

include_once("header.php");

//echo '<a onclick="xajax_content_list_ajax();">Test</a>';

function content_list_ajax()
{
	$objResponse = new xajaxResponse();
	$objResponse->addClear("contentlist", "innerHTML");
	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	return $objResponse->getXML();
}

function check_modify_all($userid)
{
	return check_permission($userid, 'Modify Any Page');
}

function setdefault($contentid)
{
	global $gCms;
	$userid = get_userid();

	if (check_modify_all($userid))
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
		ContentManager::SetDefaultContent($contentid);
		$result = true;
	}
	return $result;
}

function content_setdefault($contentid)
{
	$objResponse = new xajaxResponse();
	
	setdefault($contentid);

	$objResponse->addClear("contentlist", "innerHTML");
	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	return $objResponse->getXML();
}

function content_setactive($contentid)
{
	$objResponse = new xajaxResponse();
	
	setactive($contentid);

	$objResponse->addClear("contentlist", "innerHTML");
	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	return $objResponse->getXML();
}

function content_setinactive($contentid)
{
	$objResponse = new xajaxResponse();
	
	setactive($contentid, false);

	$objResponse->addClear("contentlist", "innerHTML");
	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	return $objResponse->getXML();
}

function content_expandall()
{
	$objResponse = new xajaxResponse();
	
	expandall();

	$objResponse->addClear("contentlist", "innerHTML");
	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	return $objResponse->getXML();
}

function content_collapseall()
{
	$objResponse = new xajaxResponse();
	
	collapseall();

	$objResponse->addClear("contentlist", "innerHTML");
	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	return $objResponse->getXML();
}

function expandall()
{
	$userid = get_userid();
	$all = ContentManager::GetAllContent(false);
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

	$objResponse->addClear("contentlist", "innerHTML");
	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
	return $objResponse->getXML();
}

function content_delete($contentid)
{
	$objResponse = new xajaxResponse();
	
	deletecontent($contentid);

	$objResponse->addClear("contentlist", "innerHTML");
	$objResponse->addAssign("contentlist", "innerHTML", display_content_list());
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
	
	$hierManager =& $gCms->GetHierarchyManager();
	
	// to activate a page, you must be admin, owner, or additional author
	$permission = (check_modify_all($userid) || 
			check_ownership($userid, $contentid) ||
			check_authorship($userid, $contentid));

	if($permission)
	{
		$node = &$hierManager->getNodeById($contentid);
		$value =& $node->getContent();
		$value->SetActive($active);
		$value->Save();
	}
}

function deletecontent($contentid)
{
	$userid = get_userid();
	$access = check_permission($userid, 'Remove Pages');

	if ($access)
	{
		$contentobj = ContentManager::LoadContentFromId($contentid);

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
			ContentManager::SetAllHierarchyPositions();
			audit($contentid, $title, 'Deleted Content');
			
			$_GET['message'] = 'contentdeleted';
		}
	}
}

function show_h(&$root)
{
	$content = &$root->getContent();
	echo "<li>".($root->getLevel()==0?"root":$content->Hierarchy())."</li>";
	if ($root->getChildrenCount()>0)
	{
		echo "<ul>";
		$children = &$root->getChildren();
		foreach ($children as $child)
		{
			show_h($child);
		}
		echo "</ul>";
	}
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
			$children =& $root->getChildren();
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

function display_hierarchy(&$root, &$userid, $modifyall, &$templates, &$users, &$menupos, &$openedArray, &$pagelist, &$image_true, &$image_set_false, &$image_set_true, &$upImg, &$downImg, &$viewImg, &$editImg, &$deleteImg, &$expandImg, &$contractImg, &$mypages, &$page)
{
	global $currow;
	global $config;
	global $page;
	global $indent;

	$one =& $root->getContent();
	$children =& $root->getChildren();
	$thelist = '';

	if ($one)
	{
		if (!array_key_exists($one->TemplateId(), $templates))
		{
			$templates[$one->TemplateId()] = TemplateOperations::LoadTemplateById($one->TemplateId());
		}

		if (!array_key_exists($one->Owner(), $users))
		{
			$users[$one->Owner()] = UserOperations::LoadUserById($one->Owner());
		} 		
	}

	$display = 'none';
	if (isset($one))
	{
		if (check_modify_all($userid) || check_ownership($userid, $one->Id()) || quick_check_authorship($one->Id(), $mypages))
		{
			$display = 'edit';
		}
		else if (check_children($root, $mypages, $userid))
		{
			$display = 'view';
		}
	}

	if ($display != 'none')
	{
		$thelist .= "<tr class=\"$currow\" onmouseover=\"this.className='".$currow.'hover'."';\" onmouseout=\"this.className='".$currow."';\">\n";
		$thelist .= "<td>";
		if ($one->ChildCount() > 0)
		{
			if (!in_array($one->Id(),$openedArray))
			{
				$thelist .= "<a href=\"listcontent.php?content_id=".$one->Id()."&amp;col=0&amp;page=".$page."\" onclick=\"xajax_content_toggleexpand(".$one->Id().", 'false'); return false;\">";
				$thelist .= $expandImg;
				$thelist .= "</a>";
			}
			else
			{
				$thelist .= "<a href=\"listcontent.php?content_id=".$one->Id()."&amp;col=1&amp;page=".$page."\" onclick=\"xajax_content_toggleexpand(".$one->Id().", 'true'); return false;\">";
				$thelist .= $contractImg;
				$thelist .= "</a>";
			}
		}
		$thelist .= "</td><td>";
		if (check_modify_all($userid))
		  {
		    $pos = strrpos($one->Hierarchy(), '.');
		    if ($pos != false)
		      {
			$thelist .= substr($one->Hierarchy(), 0, $pos)."\n";
		      }
		    
		    $thelist .= '<input type="text" name="order-'. $one->Id().'" value="'.$one->ItemOrder().'" class="order" /> '."\n";
		  }
		else
		  {
		   $thelist .= $one->Hierarchy();
		  }
		 $thelist .= "</td>\n";
		$thelist .= "<td>";

		if ($indent)
		{
			for ($i=1;$i < $root->getLevel();$i++)
			{
				$thelist .= "-&nbsp;&nbsp;&nbsp;";
			}
		} ## if indent

		if ($display == 'edit')
			$thelist .= "<a href=\"editcontent.php?content_id=".$one->Id()."&amp;page=".$page."\">".$one->Name()."</a></td>\n";
		else
			$thelist .= $one->Name()."</td>\n";


		if (isset($templates[$one->TemplateId()]->name) && $templates[$one->TemplateId()]->name)
		{
			$thelist .= "<td><a href=\"edittemplate.php?template_id=".$one->TemplateId()."&amp;from=content\">".$templates[$one->TemplateId()]->name."</a></td>\n";
		}
		else
		{
			$thelist .= "<td>&nbsp;</td>\n";
		}

		$thelist .= "<td>".$one->FriendlyName()."</td>\n";

		if ($one->Owner() > -1)
		{
			$thelist .= "<td>".$users[$one->Owner()]->username."</td>\n";
		}
		else
		{
			$thelist .= "<td>&nbsp;</td>\n";
		}

		if ($display == 'edit')
		{
			if($one->Active())
			{
				$thelist .= "<td class=\"pagepos\">".($one->DefaultContent() == 1?$image_true:"<a href=\"listcontent.php?setinactive=".$one->Id()."\" onclick=\"xajax_content_setinactive(".$one->Id().");return false;\">".$image_set_false."</a>")."</td>\n";
			}
			else
			{
				$thelist .= "<td class=\"pagepos\"><a href=\"listcontent.php?setactive=".$one->Id()."\" onclick=\"xajax_content_setactive(".$one->Id().");return false;\">".$image_set_true."</a></td>\n";
			}
		}
		else
		{
			$thelist .= "<td>&nbsp;</td>\n";
		}

		if ($one->IsDefaultPossible() == TRUE && $display == 'edit')
		{
			$thelist .= "<td class=\"pagepos\">".($one->DefaultContent() == true?$image_true:"<a href=\"listcontent.php?makedefault=".$one->Id()."\" onclick=\"if(confirm('".lang("confirmdefault")."')) xajax_content_setdefault(".$one->Id().");return false;\">".$image_set_true."</a>")."</td>\n";
		}
		else
		{
			$thelist .= "<td>&nbsp;</td>";
		}   

		// code for move up is simple
		if (check_modify_all($userid))
		{
			$thelist .= "<td>";
			$parentNode = &$root->getParentNode();
			if ($parentNode!=null)
			{
				$sameLevel = &$parentNode->getChildren();
				if (count($sameLevel)>1)
				{
					if ($parentNode->findChildNodeIndex($root)==0) #first
					{ 
						$thelist .= "<a href=\"movecontent.php?direction=down&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
						$thelist .= $downImg;
						$thelist .= "</a>";
					}
					else if ($parentNode->findChildNodeIndex($root)==count($sameLevel)-1) #last
					{
						$thelist .= "<a href=\"movecontent.php?direction=up&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
						$thelist .= $upImg;
						$thelist .= "</a>";
					}
					else #middle
					{
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

		$url = $one->GetURL();
		if ($url != '')
		{
			$thelist .= "<td class=\"pagepos\"><a href=\"".$one->GetURL()."\" rel=\"external\">";
			$thelist .= $viewImg;
			$thelist .= "</a></td>\n";
		}
		else
		{
			$thelist .= '<td>&nbsp;</td>' . "\n";
		}

		if ($display == 'edit')
		{
			$thelist .= "<td class=\"pagepos\"><a href=\"editcontent.php?content_id=".$one->Id()."\">";
			$thelist .= $editImg;;
			$thelist .= "</a></td>\n";
	        if ($one->DefaultContent() != true) {
				if (count($children) == 0)
				{
					$thelist .= "<td class=\"pagepos\"><a href=\"listcontent.php?deletecontent=".$one->Id()."\" onclick=\"if (confirm('".lang('deleteconfirm')."')) xajax_content_delete(".$one->Id()."); return false;\">";
					$thelist .= $deleteImg;
					$thelist .= "</a></td>\n";
				}
				else
				{
					$thelist .= '<td>&nbsp;</td>' . "\n";
				}
				$thelist .= '<td class="pagepos"><input type="checkbox" name="multicontent-'.$one->Id().'" /></td>';
			}
			else
			{
				$thelist .= '<td>&nbsp;</td>' . "\n";
				$thelist .= '<td>&nbsp;</td>' . "\n";
			}
			$thelist .= "</tr>\n";  	
		}
		else
		{
			$thelist .= '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>' . "\n";
		}
		($currow == "row1"?$currow="row2":$currow="row1");
	}

	$pagelist[] = &$thelist;
	if (!isset($one) || in_array($one->Id(),$openedArray))
	{
		foreach ($children as $child)
		{ 
			display_hierarchy($child, $userid, $modifyall, $templates, $users, $menupos, $openedArray, $pagelist, $image_true, $image_set_false, $image_set_true, $upImg, $downImg, $viewImg, $editImg, $deleteImg, $expandImg, $contractImg, $mypages, $page);
		}
	}
} // function display_hierarchy

function display_content_list($themeObject = null)
{
	check_login();
	$userid = get_userid();
	
	$mypages = author_pages($userid);
	
	$page = 1;
	if (isset($_GET['page']))
		$page = $_GET['page'];
	$limit = get_preference($userid, 'paging', 0);

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
	$deleteImg = $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');

	$counter = 0;

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

	if (check_modify_all($userid))
		$hierManager = &ContentManager::GetAllContentAsHierarchy(false,$openedArray);
	else
		$hierManager =& $gCms->GetHierarchyManager();

	$hierarchy = &$hierManager->getRootNode();

	$indent = get_preference($userid, 'indent', true);
	if ($hierarchy->hasChildren())
	{
		$pagelist = array();
		display_hierarchy($hierarchy, $userid, check_modify_all($userid), $templates, $users, $menupos, $openedArray, $pagelist, $image_true, $image_set_false, $image_set_true, $upImg, $downImg, $viewImg, $editImg, $deleteImg, $expandImg, $contractImg, $mypages, $page);
		foreach ($pagelist as $item)
		{
			$thelist.=$item;
		}
		$thelist .= '</tbody>';
		$thelist .= "</table>\n";
	}

	$counter = 1;	
	if (!$counter)
	{
		$thelist = "<p>".lang('noentries')."</p>";
	}

	$headoflist = '<div class="pageoverflow">';


	if ($limit != 0 && $counter > $limit)
	{
		$headoflist .= "<p class=\"pageshowrows\">".pagination($page, $counter, $limit)."</p>";
	}

	$headoflist .= $themeObject->ShowHeader('currentpages').'</div>';
	//$headoflist .= $themeObject->ShowHeader('currentpages');
	if (check_permission($userid, 'Add Pages'))
	{
		$headoflist .=  '<p class="pageoptions"><a href="addcontent.php">';
		$headoflist .= $themeObject->DisplayImage('icons/system/newobject.gif', lang('addcontent'),'','','systemicon').'</a>';
		$headoflist .= ' <a class="pageoptions" href="addcontent.php">'.lang("addcontent").'</a></p>';
	}
	$headoflist .= '<form action="multicontent.php" method="post">';
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
		if (check_modify_all($userid))
		{
			$headoflist .= "<th class=\"pagepos\">".lang('move')."</th>\n";
		}
		$headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
		$headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
		$headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
		$headoflist .= "<th class=\"pageicon\">&nbsp;</th>\n";
		$headoflist .= "</tr>\n";
		$headoflist .= '</thead>';
		$headoflist .= '<tbody>';
	}
	return $headoflist . $thelist;
}


if (FALSE == empty($_GET['message'])) {
    echo $themeObject->ShowMessage(lang($_GET['message']));
}
if (FALSE == empty($_GET['error'])) {
    echo $themeObject->ShowErrors(lang($_GET['error']));
}

?>
<div class="pagecontainer">
<?php

$hierManager =& $gCms->GetHierarchyManager();

if (isset($_GET["makedefault"]))
{
	setdefault($_GET['makedefault']);
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
}

if (isset($_GET['col']) && isset($_GET['content_id']))
{
	toggleexpand($_GET['content_id'], $_GET['col']=='1'?true:false);
}

echo '<div id="contentlist">'.display_content_list($themeObject).'</div>';

?>
		<div class="pageoptions">
		<p class="pageoptions">
		<span style="float: left;">
<?php
if (check_permission($userid, 'Add Pages'))
{
	?>
		<a href="addcontent.php">
		<?php 
		echo $themeObject->DisplayImage('icons/system/newobject.gif', lang('addcontent'),'','','systemicon').'</a>';
	echo ' <a class="pageoptions" href="addcontent.php">'.lang("addcontent");
	?>
		</a>&nbsp;&nbsp;&nbsp;
<?php } ?>
	<a href="listcontent.php?expandall=1" onclick="xajax_content_expandall(); return false;">
		<?php 
		echo $themeObject->DisplayImage('icons/system/expandall.gif', lang('expandall'),'','','systemicon').'</a>';
	echo ' <a class="pageoptions" href="listcontent.php?expandall=1" onclick="xajax_content_expandall(); return false;">'.lang("expandall");
	?>
		</a>&nbsp;&nbsp;&nbsp;
	<a href="listcontent.php?collapseall=1" onclick="xajax_content_collapseall(); return false;">
		<?php 
		echo $themeObject->DisplayImage('icons/system/contractall.gif', lang('contractall'),'','','systemicon').'</a>';
	echo ' <a class="pageoptions" href="listcontent.php?collapseall=1" onclick="xajax_content_collapseall(); return false;">'.lang("contractall");
	?>
		</a>
		</span>
		<span style="margin-right: 30px; float: right; text-align: right">
		<a href="javascript:selectall();"><?php echo lang('selectall'); ?></a>
		<script type="text/javascript">
		/*<![CDATA[*/
		function selectall()
		{
	        checkboxes = document.getElementsByTagName("input");
	        for (i=0; i<checkboxes.length ; i++)
	        {
	                if (checkboxes[i].type == "checkbox") checkboxes[i].checked=true;
	        }
		}
		/*]]>*/
		</script>

		<?php echo lang('multi_page_actions'); ?>: <select name="multiaction">
	        <option value="reorder"><?php echo lang('reorder') ?></option>
		<option value="delete"><?php echo lang('delete') ?></option>
		<option value="active"><?php echo lang('active') ?></option>
		<option value="inactive"><?php echo lang('inactive') ?></option>
		</select>
		<input type="submit" value="<?php echo lang('submit') ?>" />
		</span>
		<br />
		</p>
		</div>
		</form>
		</div>
		<p class="pageback"><a class="pageback" href="<?php echo $themeObject->BackUrl(); ?>">&#171; <?php echo lang('back')?></a></p>
		<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
