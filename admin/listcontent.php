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

include_once("header.php");

if (isset($_GET["message"])) {
    echo "<p class=\"error\">".$_GET["message"]."</p>";
}

?>
<h3><?php echo lang('currentpages')?></h3>
<?php

	$userid = get_userid();

	$modifyall = check_permission($userid, 'Modify Any Content');

	$content_array = ContentManager::GetAllContent(false);

	if ($modifyall)
	{
		if (isset($_GET["makedefault"]))
		{
			foreach ($content_array as $key=>$value_copy)
			{
				if ($value_copy->Id() == $_GET["makedefault"])
				{
					$value =& $content_array[$key];
					if ($value->mDefaultContent != true)
					{
						$value->mDefaultContent = true;
						$value->Save();
					}
				}
				else
				{
					$value =& $content_array[$key];
					if ($value->mDefaultContent != false)
					{
						$value->mDefaultContent = false;
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
		$permission = ($modifyall || check_ownership($userid,$_GET["setactive"]) || check_authorship($userid,$_GET["setactive"]));
      	if($permission) 
		{
			foreach ($content_array as $key=>$value_copy)
			{
				if ($value_copy->Id() == $_GET["setactive"])
				{
					#Modify the object inline
					$value =& $content_array[$key];
					$value->mActive = true;
					$value->Save();
				}
			}
    	}
    }

    // perhaps we're deactivating a page instead?
    if (isset($_GET["setinactive"])) 
	{
      	// to deactivate a page, you must be admin, owner, or additional author
      	$permission = ($modifyall || check_ownership($userid,$_GET["setinactive"]) || check_authorship($userid,$_GET["setinactive"]));
      	if($permission) 
		{
			foreach ($content_array as $key=>$value_copy)
			{
				if ($value_copy->Id() == $_GET["setinactive"])
				{
					#Modify the object inline
					$value =& $content_array[$key];
					$value->mActive = false;
					$value->Save();
				}
			}
	    }
    }

	$page = 1;
	if (isset($_GET['page']))$page = $_GET['page'];
	$limit = 20;
	if (count($content_array) > $limit)
	{
		echo "<div align=\"right\" class=\"clearbox\">".pagination($page, count($content_array), $limit)."</div>";
	}
	
	if (count($content_array))
	{
		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>&nbsp;</td>";
		echo "<td width=\"25%\">".lang('title')."</td>\n";
		echo "<td align=\"center\">".lang('template')."</td>\n";
		echo "<td align=\"center\">".lang('type')."</td>\n";
		// echo "<td align=\"center\">".lang('URL')."</td>\n";
		echo "<td align=\"center\">".lang('owner')."</td>\n";
		echo "<td align=\"center\">".lang('active')."</td>\n";
		echo "<td align=\"center\">".lang('default')."</td>\n";
		if ($modifyall)
		{
			echo "<td align=\"center\">".lang('move')."</td>\n";
		}
		echo "<td align=\"center\" width=\"16\">&nbsp;</td>\n";
		echo "<td align=\"center\" width=\"16\">&nbsp;</td>\n";
		echo "<td align=\"center\" width=\"16\">&nbsp;</td>\n";
		echo "</tr>\n";

		$count = 1;

		$currow = "row1";
		
		// construct true/false button images
		$image_true ="<img src=\"../images/cms/true.gif\" alt=\"".lang('true')."\" title=\"".lang('true')."\" border=\"0\" />";
		$image_false ="<img src=\"../images/cms/false.gif\" alt=\"".lang('false')."\" title=\"".lang('false')."\" border=\"0\" />";

		$counter = 0;

		#Setup array so we don't load more templates than we need to
		$templates = array();

		#Ditto with users
		$users = array();

		$menupos = array();

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

			if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit)
			{
				echo "<tr class=\"$currow\">\n";
				echo "<td>".$one->Hierarchy()."</td>\n";
				echo "<td><a href=\"editcontent.php?content_id=".$one->Id()."\">".$one->Name()."</a></td>\n";
				if ($templates[$one->TemplateId()]->name)
				{
					echo "<td>".$templates[$one->TemplateId()]->name."</td>\n";
				}
				else
				{
					echo "<td>&nbsp;</td>\n";
				}

				echo "<td align=\"center\">".$one->FriendlyName()."</td>\n";

				if ($one->Owner() > -1)
				{
					echo "<td align=\"center\">".$users[$one->Owner()]->username."</td>\n";
				}
				else
				{
					echo "<td>&nbsp;</td>\n";
				}

				if($one->Active())
				{
					echo "<td align=\"center\">".($one->DefaultContent() == 1?$image_true:"<a href=\"listcontent.php?setinactive=".$one->Id()."\">".$image_true."</a>")."</td>\n";
				}
				else 
				{
				  	echo "<td align=\"center\"><a href=\"listcontent.php?setactive=".$one->Id()."\">".$image_false."</a></td>\n";
				}

				if ($one->Type() == "content")
				{
					echo "<td align=\"center\">".($one->DefaultContent() == true?$image_true:"<a href=\"listcontent.php?makedefault=".$one->Id()."\" onclick=\"return confirm('".lang("confirmdefault")."');\">".$image_false."</a>")."</td>\n";
				}
				else
				{
					echo "<td>&nbsp;</td>";
				}

				if ($modifyall)
				{
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
					
					echo "<td align=\"center\">";
					if ($num_same_level > 1)
					{
						#echo "item_order: " . $item_order . " num_same_level:" . $num_same_level . "<br />";
						if ($item_order == 1 && $num_same_level)
						{
							echo "<a href=\"movecontent.php?direction=down&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">".
								"<img src=\"../images/cms/arrow-d.gif\" alt=\"".lang('down')."\" title=\"".lang('down')."\" border=\"0\" /></a>";
						}
						else if ($item_order == $num_same_level)
						{
							echo "<a href=\"movecontent.php?direction=up&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">".
								"<img src=\"../images/cms/arrow-u.gif\" alt=\"".lang('up')."\" title=\"".lang('up')."\" border=\"0\" /></a>";
						}
						else
						{
							echo "<a href=\"movecontent.php?direction=down&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">".
								"<img src=\"../images/cms/arrow-d.gif\" alt=\"".lang('down')."\" title=\"".lang('down')."\" border=\"0\" /></a>&nbsp;".
								"<a href=\"movecontent.php?direction=up&amp;content_id=".$one->Id()."&amp;parent_id=".$one->ParentId()."&amp;page=".$page."\">".
								"<img src=\"../images/cms/arrow-u.gif\" alt=\"".lang('up')."\" title=\"".lang('up')."\" border=\"0\" /></a>";
						}
					}
					echo "</td>\n";
				}
				if ($config["query_var"] == "")
				{
					echo "<td align=\"center\"><a href=\"".$config["root_url"]."/index.php/".$one->Id()."\" target=\"_blank\"><img src=\"../images/cms/view.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('view')."\" title=\"".lang('view')."\" /></a></td>\n";
				}
				else if ($one->Alias() != "")
				{
					echo "<td align=\"center\"><a href=\"".$config["root_url"]."/index.php?".$config['query_var']."=".$one->Alias()."\" target=\"_blank\"><img src=\"../images/cms/view.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('view')."\" title=\"".lang('view')."\" /></a></td>\n";
				}
				else
				{
					echo "<td align=\"center\"><a href=\"".$config["root_url"]."/index.php?".$config['query_var']."=".$one->Id()."\" target=\"_blank\"><img src=\"../images/cms/view.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('view')."\" title=\"".lang('view')."\" /></a></td>\n";
				}
				echo "<td align=\"center\"><a href=\"editcontent.php?content_id=".$one->Id()."\"><img src=\"../images/cms/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('edit')."\" title=\"".lang('edit')."\" /></a></td>\n";
				echo "<td align=\"center\"><a href=\"deletecontent.php?content_id=".$one->Id()."\" onclick=\"return confirm('".lang('deleteconfirm')."');\"><img src=\"../images/cms/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('delete')."\" title=\"".lang('delete')."\" /></a></td>\n";
				echo "</tr>\n";

				$count++;

				($currow == "row1"?$currow="row2":$currow="row1");
			}
			$counter++;
		} ## foreach
		echo "</table>\n";

	}
	else
	{
		echo "<p>".lang('noentries')."</p>";
	}

	if (check_permission($userid, 'Add Content'))
	{
?>

<div class="button"><a href="addcontent.php"><?php echo lang("addcontent")?></a></div>

<div class="collapseTitle"><a href="#help" onclick="expandcontent('helparea')"><?php echo lang('help')?>?</a></div>
<div id="helparea" class="expand">
<?php echo lang('helplistcontent')?>
<a name="help">&nbsp;</a>
</div>

<?php
	}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
