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
<div class="adminformnobkg">
<h3><?=$gettext->gettext("Current Templates")?></h3>
<?php

	$userid = get_userid();
	$add = check_permission($userid, 'Add Template');
	$edit = check_permission($userid, 'Modify Template');
	$all = check_permission($userid, 'Modify Any Content');
	$remove = check_permission($userid, 'Remove Template');

	if ($all && isset($_GET["action"]) && $_GET["action"] == "setallcontent") {
		if (isset($_GET["template_id"])) {
			$query = "UPDATE ".cms_db_prefix()."pages SET template_id = ".$_GET["template_id"];
			$result = $db->Execute($query);
			if ($result) {
				$query = "UPDATE ".cms_db_prefix()."pages SET modified_date = ".$db->DBTimeStamp(time());
				$db->Execute($query);
				echo "<p>All Pages Modified!</p>";
			} else {
				echo "<p class=\"error\">Error updating pages</p>";
			}
		}
	}

	$query = "SELECT template_id, template_name, active FROM ".cms_db_prefix()."templates ORDER BY template_id";
	$result = $db->Execute($query);

	if ($result) {

		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>".$gettext->gettext("Template")."</td>\n";
		echo "<td>".$gettext->gettext("Active")."</td>\n";
		if ($edit)
			echo "<td>&nbsp;</td>\n";
		if ($add)
			echo "<td>&nbsp;</td>\n";
		if ($remove)
			echo "<td>&nbsp;</td>\n";
		if ($all)
			echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";

		$currow = "row1";

		while($row = $result->FetchRow()) {

			echo "<tr class=\"$currow\">\n";
			echo "<td>".$row["template_name"]."</td>\n";
			echo "<td width=\"6%\" align=\"center\">".($row["active"] == 1?$gettext->gettext("True"):$gettext->gettext("False"))."</td>\n";
			if ($all)
				echo "<td width=\"12%\"><a href=\"listtemplates.php?action=setallcontent&template_id=".$row["template_id"]."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want all content to use this template?")."');\">".$gettext->gettext("Set All Content")."</a></td>\n";
			if ($add)
				echo "<td width=\"18\"><a href=\"copytemplate.php?template_id=".$row["template_id"]."\"><img src=\"../images/cms/copy.png\" alt=\"".$gettext->gettext("Copy")."\" title=\"".$gettext->gettext("Copy")."\" border=\"0\" /></a></td>\n";
			if ($edit)
				echo "<td width=\"18\"><a href=\"edittemplate.php?template_id=".$row["template_id"]."\"><img src=\"../images/cms/edit.png\" alt=\"".$gettext->gettext("Edit")."\" title=\"".$gettext->gettext("Edit")."\" border=\"0\" /></a></td>\n";
			if ($remove)
				echo "<td width=\"18\"><a href=\"deletetemplate.php?template_id=".$row["template_id"]."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to delete?")."');\"><img src=\"../images/cms/delete.png\" alt=\"".$gettext->gettext("Delete")."\" title=\"".$gettext->gettext("Delete")."\" border=\"0\" /></a></td>\n";
			echo "</tr>\n";

			($currow=="row1"?$currow="row2":$currow="row1");

		}

		echo "</table>\n";

	}

if ($add) {
?>

<div class=button><a href="addtemplate.php"><?=$gettext->gettext("Add New Template")?></a></div>
<div id="helparea" class="helparea">
<?php
echo "<p>".$gettext->gettext("This page allows you to edit, delete, and create templates.")."</p>";
echo "<p>".$gettext->gettext("To create a new template, click on the <u>Add New Template</u> button.")."<br />";
echo $gettext->gettext("If you wish to set all content pages to use the same template, click on the <u>Set All Content</u> link.")."<br />";
echo $gettext->gettext("If you wish to duplicate a template, click on the <u>Copy</u> icon and you will be prompted to name the new duplicate template.")."</p>";
?>
</div></div>
<?php
}

echo '</div>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
