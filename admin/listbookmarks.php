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
$CMS_TOP_MENU='bookmarks';
$CMS_ADMIN_TITLE='bookmarks';

require_once("../include.php");

check_login();

include_once("header.php");

?>
<h3><?php echo lang('bookmarks')?></h3>

<?php

	$userid = get_userid();

	$marklist = BookmarkOperations::LoadBookmarks($userid);

	$page = 1;
	if (isset($_GET['page']))$page = $_GET['page'];
	$limit = 20;
	if (count($marklist) > $limit)
	{
		echo "<div align=\"right\" class=\"clearbox\">".pagination($page, count($marklist), $limit)."</div>";
	}

	if (count($marklist) > 0) {

		echo "<table cellspacing=\"0\" class=\"AdminTable\" style=\"width: 500px;\">\n";
		echo '<thead>';
		echo "<tr>\n";
		echo "<th width=\"60%\">".lang('name')."</th>\n";
		echo "<th align=\"center\">".lang('url')."</th>\n";
		echo "<th width=\"7%\" align=\"center\">&nbsp;</th>\n";
		echo "<th width=\"7%\" align=\"center\">&nbsp;</th>\n";
		echo "</tr>\n";
		echo '</thead>';
		echo '<tbody>';

		$currow = "row1";

		// construct true/false button images
        $image_true = $themeObject->DisplayImage('true.gif', lang('true'));
        $image_false = $themeObject->DisplayImage('false.gif', lang('false'));

		$counter=0;
		foreach ($marklist as $onemark){
			if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit) {
				echo "<tr class=\"$currow\">\n";
				echo "<td><a href=\"editbookmark.php?bookmark_id=".$onemark->bookmark_id."\">".$onemark->title."</a></td>\n";
				echo "<td>".$onemark->url."</td>\n";
				echo "<td width=\"16\"><a href=\"editbookmark.php?bookmark_id=".$onemark->bookmark_id."\">";
                echo $themeObject->DisplayImage('edit.gif', lang('edit'));
                echo "</a></td>\n";
				echo "<td width=\"16\"><a href=\"deletebookmark.php?bookmark_id=".$onemark->bookmark_id."\" onclick=\"return confirm('".lang('deleteconfirm')."');\">";
                echo $themeObject->DisplayImage('delete.gif', lang('delete'));
                echo "</a></td>\n";
				echo "</tr>\n";
				($currow == "row1"?$currow="row2":$currow="row1");
			}
			$counter++;
		}

		echo '</tbody>';
		echo "</table>\n";

	}
?>
<div class="button"><a href="addbookmark.php"><?php echo lang('addbookmark') ?></a></div><br />

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
