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

	if ($modifyall) {
		if (isset($_GET["makedefault"])) {
			$query = "UPDATE ".cms_db_prefix()."pages SET default_page = 0";
			$result = $db->Execute($query);

			$query = "UPDATE ".cms_db_prefix()."pages SET default_page = 1 WHERE page_id = ".$_GET["makedefault"];
			$result = $db->Execute($query);
		}
	}

	$content_array = db_get_menu_items();
	if (count($content_array)) {

		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>&nbsp;</td>";
		echo "<td width=\"25%\">".lang('title')."</td>\n";
		echo "<td>".lang('template')."</td>\n";
		echo "<td align=\"center\">".lang('type')."</td>\n";
//		echo "<td align=\"center\">".lang('URL')."</td>\n";
		echo "<td align=\"center\">".lang('owner')."</td>\n";
		echo "<td align=\"center\">".lang('active')."</td>\n";
		echo "<td align=\"center\">".lang('default')."</td>\n";
		if ($modifyall) {
			echo "<td align=\"center\">".lang('move')."</td>\n";
		}
		echo "<td align=\"center\" width=\"16\">&nbsp;</td>\n";
		echo "<td align=\"center\" width=\"16\">&nbsp;</td>\n";
		echo "<td align=\"center\" width=\"16\">&nbsp;</td>\n";
		echo "</tr>\n";

		$count = 1;

		$currow = "row1";

		$types = get_page_types();

		foreach ($content_array as $one) {

			echo "<tr class=\"$currow\">\n";
			echo "<td>".$one->hier."</td>\n";
			echo "<td><a href=\"editcontent.php?page_id=".$one->page_id."&amp;parent_id=".$one->parent_id."\">".$one->page_title."</a></td>\n";
			echo "<td>".$one->template_name."</td>\n";
			echo "<td align=\"center\">".$types[$one->page_type]."</td>\n";
//			echo "<td>".($types[$one->page_type]=="Link"?$one->page_url:"&nbsp;")."</td>\n";
			echo "<td align=\"center\">".$one->username."</td>\n";
			echo "<td align=\"center\">".($one->active == 1?lang('true'):lang('false'))."</td>\n";
			echo "<td align=\"center\">".($one->default_page == 1?lang('true'):"<a href=\"listcontent.php?makedefault=".$one->page_id."\" onclick=\"return confirm('".lang("Are you sure you want to set site\'s default page?")."');\">".lang('false')."</a>")."</td>\n";

			if ($modifyall) {
				echo "<td align=\"center\">";
				if ($one->num_same_level > 1) {
					if ($one->item_order == 1 && $one->num_same_level) {
						echo "<a href=\"movecontent.php?direction=down&amp;page_id=".$one->page_id."&parent_id=".$one->parent_id."\">".
							"<img src=\"../images/cms/arrow-d.gif\" alt=\"".lang('down')."\" title=\"".lang('down')."\" border=\"0\"></a>";
					} else if ($one->item_order == $one->num_same_level) {
						echo "<a href=\"movecontent.php?direction=up&amp;page_id=".$one->page_id."&amp;parent_id=".$one->parent_id."\">".
							"<img src=\"../images/cms/arrow-u.gif\" alt=\"".lang('up')."\" title=\"".lang('up')."\" border=\"0\"></a>";
					} else {
						echo "<a href=\"movecontent.php?direction=down&amp;page_id=".$one->page_id."&amp;parent_id=".$one->parent_id."\">".
							"<img src=\"../images/cms/arrow-d.gif\" alt=\"".lang('down')."\" title=\"".lang('down')."\" border=\"0\"></a>&nbsp;".
							"<a href=\"movecontent.php?direction=up&amp;page_id=".$one->page_id."&amp;parent_id=".$one->parent_id."\">".
							"<img src=\"../images/cms/arrow-u.gif\" alt=\"".lang('up')."\" title=\"".lang('up')."\" border=\"0\"></a>";
					}
				}
				echo "</td>\n";
			}
			if ($config["query_var"] == ""){
				echo "<td align=\"center\"><a href=\"".$config["root_url"]."/index.php/".$one->page_id."\" target=\"_blank\"><img src=\"../images/cms/view.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('view')."\" title=\"".lang('view')."\"></a></td>\n";
			}else if (isset($one->page_alias) && $one->page_alias != "") {
				echo "<td align=\"center\"><a href=\"".$config["root_url"]."/".$one->page_alias.".html\" target=\"_blank\"><img src=\"../images/cms/view.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('view')."\" title=\"".lang('view')."\"></a></td>\n";
			}else{
				echo "<td align=\"center\"><a href=\"".$config["root_url"]."/index.php?page_id=".$one->page_id."\" target=\"_blank\"><img src=\"../images/cms/view.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('view')."\" title=\"".lang('view')."\"></a></td>\n";
			}
			echo "<td align=\"center\"><a href=\"editcontent.php?page_id=".$one->page_id."&amp;parent_id=".$one->parent_id."\"><img src=\"../images/cms/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('edit')."\" title=\"".lang('edit')."\"></a></td>\n";
			echo "<td align=\"center\"><a href=\"deletecontent.php?page_id=".$one->page_id."\" onclick=\"return confirm('".lang('deleteconfirm')."');\"><img src=\"../images/cms/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('delete')."\" title=\"".lang('delete')."\"></a></td>\n";
			echo "</tr>\n";

			$count++;

			($currow == "row1"?$currow="row2":$currow="row1");

		} ## foreach

		echo "</table>\n";

	} else {
		echo "<p>".lang('noentries')."</p>";
	}

	if (check_permission($userid, 'Add Content')) {
?>

<div class="button"><a href="addcontent.php"><?php echo lang("addcontent")?></a></div>

<div class="collapseTitle"><a href="#help" onClick="expandcontent('helparea')"><?php echo lang('help')?>?</a></div>
<div id="helparea" class="expand">
<?php echo lang('helplistcontent')?>
<a name="help">&nbsp;</a>
</div>
<?php
	}


include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
