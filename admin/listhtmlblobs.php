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
	echo "<p class=\"error\">".$_GET["message"]."</p>";
}

?>
<h3>HTML Blobs</h3>
<?php

	$userid	= get_userid();

	$htmlbloblist = HtmlBlobOperations::LoadHtmlBlobs();

	$page = 1;
	if (isset($_GET['page']))$page = $_GET['page'];
	$limit = 20;
	echo "<div align=\"right\" class=\"clearbox\">".pagination($page, count($htmlbloblist), $limit)."</div>";

	echo "<table cellspacing=\"0\" class=\"admintable\">"."\n";
	echo "<tr>\n";
	echo "<td>".lang('name')."</td>\n";
	echo "<td>&nbsp;</td>\n";
	echo "<td width=\"16\">&nbsp;</td>\n";
	echo "</tr>\n";

	if ($htmlbloblist && count($htmlbloblist) > 0) {

		$currow = "row1";
		// construct true/false button images
		$image_true ="<img src=\"../images/cms/true.gif\" alt=\"".lang('true')."\" title=\"".lang('true')."\" border=\"0\" />";
		$image_false ="<img src=\"../images/cms/false.gif\" alt=\"".lang('false')."\" title=\"".lang('false')."\" border=\"0\" />";

		$counter = 0;
		foreach ($htmlbloblist as $onehtmlblob){
			if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit) {
				echo "<tr class=\"$currow\">\n";
				echo "<td><a href=\"edithtmlblob.php?htmlblob_id=".$onehtmlblob->id."\">".$onehtmlblob->name."</a></td>\n";
				echo "<td width=\"16\"><a href=\"edithtmlblob.php?htmlblob_id=".$onehtmlblob->id."\"><img src=\"../images/cms/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('edit')."\" title=\"".lang('edit')."\" /></a></td>\n";
				echo "<td width=\"16\"><a href=\"deletehtmlblob.php?htmlblob_id=".$onehtmlblob->id."\" onclick=\"return confirm('".lang('deleteconfirm')."');\"><img src=\"../images/cms/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('delete')."\" title=\"".lang('delete')."\" /></a></td>\n";
				echo "</tr>\n";

				($currow=="row1"?$currow="row2":$currow="row1");
			}
			$counter++;
		}
	}

	echo "</table>\n";

#if ($add) {
?>

<div class="button"><a href="addhtmlblob.php"><?php echo lang('addhtmlblob')?></a></div><br />

<!--
<div class="collapseTitle"><a href="#help" onClick="expandcontent('helparea')" style="cursor:hand; cursor:pointer"><?php echo lang('help') ?>?</a></div>
<div id="helparea" class="expand">
<?php echo lang('helplisttemplate')?>
<a name="help">&nbsp;</a>
</div>
-->

<?php
#}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
