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
require_once("../lib/classes/class.htmlblob.inc.php");

check_login();

include_once("header.php");

if (isset($_GET["message"])) {
	echo '<div class="pagemcontainer"><p class="pagemessage">'.$_GET["message"].'</p>';
}

?>
<div class="pagecontainer">
	<div class="pageoverflow">

<?php
	$userid	= get_userid();

	$htmlbloblist = HtmlBlobOperations::LoadHtmlBlobs();

	$page = 1;
	if (isset($_GET['page']))$page = $_GET['page'];
	$limit = 20;
	echo "<p class=\"pageshowrows\">".pagination($page, count($htmlbloblist), $limit)."</p>";
	echo '<p class="pageheader">'.lang('htmlblobs').'</p></div>';

	if ($htmlbloblist && count($htmlbloblist) > 0) {
		echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
		echo '<thead>';
		echo "<tr>\n";
		echo "<th>".lang('name')."</th>\n";
		echo "<th class=\"pageicon\">&nbsp;</th>\n";
		echo "<th class=\"pageicon\">&nbsp;</th>\n";
		echo "</tr>\n";
		echo '</thead>';
		echo '<tbody>';

		$currow = "row1";
		// construct true/false button images
        $image_true = $themeObject->DisplayImage('true.gif', lang('true'));
        $image_false = $themeObject->DisplayImage('false.gif', lang('false'));

		$counter = 0;
		foreach ($htmlbloblist as $onehtmlblob){
			if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit) {
				echo "<tr class=\"$currow\" onmouseover=\"this.className='".$currow.'hover'."';\" onmouseout=\"this.className='".$currow."';\">\n";
				echo "<td><a href=\"edithtmlblob.php?htmlblob_id=".$onehtmlblob->id."\">".$onehtmlblob->name."</a></td>\n";
				echo "<td><a href=\"edithtmlblob.php?htmlblob_id=".$onehtmlblob->id."\">";
                echo $themeObject->DisplayImage('edit.gif', lang('edit'));
                echo "</a></td>\n";
				echo "<td><a href=\"deletehtmlblob.php?htmlblob_id=".$onehtmlblob->id."\" onclick=\"return confirm('".lang('deleteconfirm')."');\">";
                echo $themeObject->DisplayImage('delete.gif', lang('delete'));
                echo "</a></td>\n";
				echo "</tr>\n";

				($currow=="row1"?$currow="row2":$currow="row1");
			}
			$counter++;
		}

		echo '</tbody>';
		echo "</table>\n";
	}

#if ($add) {
?>
	<div class="pageoptions">
		<p class="pageoptions">
			<a href="addhtmlblob.php">
				<?php 
					echo $themeObject->DisplayImage('newobject.gif', lang('addhtmlblob')).'</a>'; 
					echo ' <a class="pageoptions" href="addhtmlblob.php">'.lang("addhtmlblob");
				?>
			</a>
		</p>		
	</div>
</div>
<p class="pageback"><a class="pageback" href="toplayout.php">&#171; <?php echo lang('back')?></a></p>

<?php
#}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
