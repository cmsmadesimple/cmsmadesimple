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

check_login($config);

include_once("header.php");

if (isset($_GET["message"])) {
	echo "<p class=\"error\">".$_GET["message"]."</p>";
}

?>
<h3><?=$gettext->gettext("Current Sections")?></h3>
<?php

	$userid = get_userid();
	$edit = check_permission($config, $userid, 'Modify Section');
	$remove = check_permission($config, $userid, 'Remove Section');

	$sections = db_get_menu_items($config, "subs");
	if (count($sections) > 0) {

		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>".$gettext->gettext("Section")."</td>\n";
		echo "<td width=\"10%\">".$gettext->gettext("Active")."</td>\n";
		if ($edit) {
			echo "<td width=\"10%\">&nbsp;</td>\n";
			echo "<td width=\"10%\">&nbsp;</td>\n";
		}
		if ($remove)
			echo "<td width=\"10%\">&nbsp;</td>\n";
		echo "</tr>\n";
		
		$currow = "row1";

		$count = 1;

		foreach ($sections as $one_section) {
			echo "<tr class=\"$currow\">\n";
			echo "<td>".$one_section->display_name."</td>\n";
			echo "<td width=\"10%\">".($one_section->active == 1?$gettext->gettext("True"):$gettext->gettext("False"))."</td>\n";
			if ($edit) {
				echo "<td width=\"10%\">";
				if ($count > 1 && count($sections) > 1) {
					echo "<a href=\"movesection.php?direction=up&section_id=".$one_section->section_id."\"><img src=\"../images/arrow-u.png\" alt=\"".$gettext->gettext("Up")."\" border=\"0\" /></a> ";
				}
				if ($count < count($sections) && count($sections) > 1) {
					echo "<a href=\"movesection.php?direction=down&section_id=".$one_section->section_id."\"><img src=\"../images/arrow-d.png\" alt=\"".$gettext->gettext("Down")."\" border=\"0\" /></a> ";
				}
				if ($count == 1) {
					echo "&nbsp;";
				}
				echo "</td>\n";
				echo "<td width=\"10%\"><a href=\"editsection.php?section_id=".$one_section->section_id."&parent_id=".$one_section->parent_id."\">".$gettext->gettext("Edit")."</a></td>\n";
			}
			if ($remove)
				echo "<td width=\"10%\"><a href=\"deletesection.php?section_id=".$one_section->section_id."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to delete?")."');\">".$gettext->gettext("Delete")."</a></td>\n";
			echo "</tr>\n";

			($currow=="row1"?$currow="row2":$currow="row1");

			$count++;

		}

		echo "</table>\n";

	}

if (check_permission($config, $userid, 'Add Section')) {
?>

<div class=button><a href="addsection.php"><?=$gettext->gettext("Add New
Section")?></div></p>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
