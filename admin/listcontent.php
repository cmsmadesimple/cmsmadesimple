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
if (isset($_GET["message"])) {
	$message = preg_replace('/\</','',$_GET['message']);
	echo '<div class="pagemcontainer"><p class="pagemessage">'.$message.'</p></div>';
}

?>
<div class="pagecontainer">
	<div class="pageoverflow">
<?php

	$modifyall = check_permission($userid, 'Modify Any Page');
	$content_array = ContentManager::GetAllContent(false);
	$mypages = author_pages($userid);

	if ($modifyall)
	{
		if (isset($_GET["makedefault"]))
		{
			foreach ($content_array as $key=>$value_copy)
			{
				if ($value_copy->Id() == $_GET["makedefault"])
				{
					$value =& $content_array[$key];
					if ($value->DefaultContent() != true)
					{
						$value->SetDefaultContent(true);
						$value->Save();
					}
				}
				else
				{
					$value =& $content_array[$key];
					if ($value->DefaultContent() != false)
					{
						$value->SetDefaultContent(false);
						$value->Save();
					}
				}
			}
		}
	}
    // check if we're activating a page
    if (isset($_GET["setactive"])) 
	{
      	// to activate a page, you must be admin, owner, or additional author
		$permission = ($modifyall || 
			check_ownership($userid,$_GET["setactive"]) ||
			check_authorship($userid,$_GET["setactive"]));

		if($permission) 
		{
			foreach ($content_array as $key=>$value_copy)
			{
				if ($value_copy->Id() == $_GET["setactive"])
				{
					#Modify the object inline
					$value =& $content_array[$key];
					$value->SetActive(true);
					$value->Save();
				}
			}
    	}
    }

    // perhaps we're deactivating a page instead?
    if (isset($_GET["setinactive"])) 
	{
      	// to deactivate a page, you must be admin, owner, or additional author
      	$permission = ($modifyall || 
			check_ownership($userid,$_GET["setinactive"]) || 
			check_authorship($userid,$_GET["setinactive"]));
      	if($permission) 
		{
			foreach ($content_array as $key=>$value_copy)
			{
				if ($value_copy->Id() == $_GET["setinactive"])
				{
					#Modify the object inline
					$value =& $content_array[$key];
					$value->SetActive(false);
					$value->Save();
				}
			}
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
    $collapsing = false;
    $collapse_depth = -1;
	if (count($content_array))
	{
		foreach ($content_array as $one)
		{
			if (!array_key_exists($one->TemplateId(), $templates))
			{
				$templates[$one->TemplateId()] = TemplateOperations::LoadTemplateById($one->TemplateId());
			}

			if (!array_key_exists($one->Owner(), $users))
			{
				$users[$one->Owner()] = UserOperations::LoadUserById($one->Owner());
			}

             // check that permissions are good before showing -- and counting:
            if ($modifyall || 
				check_ownership($userid,$one->Id()) || 
				quick_check_authorship($one->Id(), $mypages))
			{
                $depth = count(split('\.', $one->Hierarchy()));
                if ($collapsing && $depth > $collapse_depth)
                    {
                    continue;
                    }
                else
                    {
                    $collapsing = false;
                    }
			if ($limit == 0 || ( ($counter)  < $page*$limit && ($counter)  >= ($page*$limit)-$limit))
				{
  			    	$thelist .= "<tr class=\"$currow\" onmouseover=\"this.className='".$currow.'hover'."';\" onmouseout=\"this.className='".$currow."';\">\n";
                    $thelist .= "<td>";
                    if ($one->ChildCount() > 0)
                        {
                        if (!isset($openedArray[$one->Id()])) //$one->Collapsed()
                            {
                            $thelist .= "<a href=\"setexpand.php?content_id=".$one->Id()."&amp;col=0&amp;page=".$page."\">";
                            $thelist .= $themeObject->DisplayImage('icons/system/expand.gif', lang('expand'),'','','systemicon');
                            $thelist .= "</a>";
                            $collapsing = true;
                            // was collapse_depth=1
                            $collapse_depth = $depth;
                            }
                        else
                            {
                            $thelist .= "<a href=\"setexpand.php?content_id=".$one->Id()."&amp;col=1&amp;page=".$page."\">";
                            $thelist .= $themeObject->DisplayImage('icons/system/contract.gif', lang('contract'),'','','systemicon');
                            $thelist .= "</a>";
                            }
                        }
                    $thelist .= "</td><td>".$one->Hierarchy()."</td>\n";
                	$thelist .= "<td>";
                    if ($indent)
                        {
                        for ($i=1;$i < $depth;$i++)
                            {
                            $thelist .= "-&nbsp;&nbsp;&nbsp;";
                            }
                        }

                    $thelist .= "<a href=\"editcontent.php?content_id=".$one->Id()."&amp;page=".$page."\">".$one->Name()."</a></td>\n";
  					if (isset($templates[$one->TemplateId()]->name) && $templates[$one->TemplateId()]->name)
  					{
  						 $thelist .= "<td>".$templates[$one->TemplateId()]->name."</td>\n";
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

  					if($one->Active())
  					{
  						$thelist .= "<td class=\"pagepos\">".($one->DefaultContent() == 1?$image_true:"<a href=\"listcontent.php?setinactive=".$one->Id()."\">".$image_true."</a>")."</td>\n";
  					}
  					else 
  					{
  				  		$thelist .= "<td class=\"pagepos\"><a href=\"listcontent.php?setactive=".$one->Id()."\">".$image_false."</a></td>\n";
  					}
  	
					if ($one->IsDefaultPossible() == TRUE)
					{
  						$thelist .= "<td class=\"pagepos\">".($one->DefaultContent() == true?$image_true:"<a href=\"listcontent.php?makedefault=".$one->Id()."\" onclick=\"return confirm('".lang("confirmdefault")."');\">".$image_false."</a>")."</td>\n";
  					}
  					else
  					{
  						$thelist .= "<td>&nbsp;</td>";
  					}
  
					if ($modifyall)
					{
  						$thelist .= "<td class=\"pagepos\">";
						
  						#Figure out some variables real quick
  						$depth = count(split('\.', $one->Hierarchy()));
  
  						$item_order = substr($one->Hierarchy(), strrpos($one->Hierarchy(), '.'));
  						if ($item_order == '')
  						{
  							$item_order = $one->Hierarchy();
  						}
  
  						#Remove any rogue dots
  						$item_order = trim($item_order, ".");
  
  						$num_same_level = 0;
  	
						#TODO: Handle depth correctly yet
  						foreach ($content_array as $another)
  						{
  							#Are they the same level?
  							if (count(split('\.', $another->Hierarchy())) == $depth)
  							{
  								#Make sure it's not top level
  								if (count(split('\.', $another->Hierarchy())) > 1)
  								{
  									#So only pages with the same parents count
  									if (substr($another->Hierarchy(), 0, strrpos($another->Hierarchy(), '.')) == substr($one->Hierarchy(), 0, strrpos($another->Hierarchy(), '.')))
  									{
  										$num_same_level++;
  									}
  								}
  								else
  								{
  									#It's top level, just increase the count
  									$num_same_level++;	
  								}
  							}
  						}
  						
  						if ($num_same_level > 1)
  						{
  							#$thelist .= "item_order: " . $item_order . " num_same_level:" . $num_same_level . "<br />";
  							if ($item_order == 1 && $num_same_level)
  							{
  								$thelist .= "<a href=\"movecontent.php?direction=down&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
  								$thelist .= $themeObject->DisplayImage('icons/system/arrow-d.gif', lang('down'),'','','systemicon');
  								$thelist .= "</a>";
   							}
  							else if ($item_order == $num_same_level)
  							{
  								$thelist .= "<a href=\"movecontent.php?direction=up&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
  								$thelist .= $themeObject->DisplayImage('icons/system/arrow-u.gif', lang('up'),'','','systemicon');
  								$thelist .= "</a>";
  							}
  							else
  							{
  								$thelist .= "<a href=\"movecontent.php?direction=down&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
  								$thelist .= $themeObject->DisplayImage('icons/system/arrow-d.gif', lang('down'),'','','systemicon');
  								$thelist .= "</a>&nbsp;<a href=\"movecontent.php?direction=up&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">";
  								$thelist .= $themeObject->DisplayImage('icons/system/arrow-u.gif', lang('up'),'','','systemicon');
  								$thelist .= "</a>";
  							}
  						}
  					}

					if ($modifyall)
  						$thelist .= "</td>\n";
				
  					if ($config["query_var"] == "")
  					{
  						$thelist .= "<td class=\"pagepos\"><a href=\"".$config["root_url"]."/index.php/".$one->Id()."\" rel=\"external\">";
  						$thelist .= $themeObject->DisplayImage('icons/system/view.gif', lang('view'),'','','systemicon');
                    	$thelist .= "</a></td>\n";
  					}
  					else if ($one->Alias() != "")
  					{
  						$thelist .= "<td class=\"pagepos\"><a href=\"".$config["root_url"]."/index.php?".$config['query_var']."=".$one->Alias()."\" rel=\"external\">";
                    	$thelist .= $themeObject->DisplayImage('icons/system/view.gif', lang('view'),'','','systemicon');
                    	$thelist .= "</a></td>\n";
  					}
  					else
  					{
  						$thelist .= "<td class=\"pagepos\"><a href=\"".$config["root_url"]."/index.php?".$config['query_var']."=".$one->Id()."\" rel=\"external\">";
                    	$thelist .= $themeObject->DisplayImage('icons/system/view.gif', lang('view'),'','','systemicon');
                    	$thelist .= "</a></td>\n";
  					}
  					$thelist .= "<td class=\"pagepos\"><a href=\"editcontent.php?content_id=".$one->Id()."\">";
  					$thelist .= $themeObject->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon');
                	$thelist .= "</a></td>\n";
  					$thelist .= "<td class=\"pagepos\"><a href=\"deletecontent.php?content_id=".$one->Id()."\" onclick=\"return confirm('".lang('deleteconfirm')."');\">";
                	$thelist .= $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');
                	$thelist .= "</a></td>\n";
  					$thelist .= "</tr>\n";
  	
  					$count++;
  
  					($currow == "row1"?$currow="row2":$currow="row1");
					}
				$counter++;
				}
			} ## foreach
		
		$thelist .= '</tbody>';
		$thelist .= "</table>\n";

	}
	
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
