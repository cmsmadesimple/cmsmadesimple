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

require_once("../include.php");

check_login($config);

include_once("header.php");

if (isset($_GET["message"])) {
	echo "<p class=\"error\">".$_GET["message"]."</p>";
}

?>
<h3><?=GetText::gettext("Current Sections")?></h3>
<?php

	$userid = get_userid();
	$edit = check_permission($config, $userid, 'Modify Section');
	$remove = check_permission($config, $userid, 'Remove Section');

	$db = new DB($config);

	$query = "SELECT section_id, section_name, active FROM ".$config->db_prefix."sections ORDER BY item_order";
	$result = $db->query($query);

	$totalcount = $db->rowcount($result);

	if ($totalcount > 0) {

		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>".GetText::gettext("Section")."</td>\n";
		echo "<td>".GetText::gettext("Active")."</td>\n";
		if ($edit) {
			echo "<td>&nbsp;</td>\n";
			echo "<td>&nbsp;</td>\n";
		}
		if ($remove)
			echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";
		
		$currow = "row1";

		$count = 1;

		while($row = $db->getresulthash($result)) {

			echo "<tr class=\"$currow\">\n";
			echo "<td>".$row["section_name"]."</td>\n";
			echo "<td>".($row["active"] == 1?GetText::gettext("True"):GetText::gettext("False"))."</td>\n";
			if ($edit) {
				echo "<td>";
				if ($count > 1 && $totalcount > 1) {
					echo "<a href=\"movesection.php?direction=up&section_id=".$row["section_id"]."\">".GetText::gettext("Up")."</a> ";
				}
				if ($count < $totalcount && $totalcount > 1) {
					echo "<a href=\"movesection.php?direction=down&section_id=".$row["section_id"]."\">".GetText::gettext("Down")."</a>";
				}
				if ($count == 1) {
					echo "&nbsp;";
				}
				echo "</td>\n";
				echo "<td><a href=\"editsection.php?section_id=".$row["section_id"]."\">".GetText::gettext("Edit")."</a></td>\n";
			}
			if ($remove)
				echo "<td><a href=\"deletesection.php?section_id=".$row["section_id"]."\" onclick=\"return confirm('".GetText::gettext("Are you sure you want to delete?")."');\">".GetText::gettext("Delete")."</a></td>\n";
			echo "</tr>\n";

			($currow=="row1"?$currow="row2":$currow="row1");

			$count++;

		}

		echo "</table>\n";

	}

	$db->freeresult($result);
	$db->query($link);

if (check_permission($config, $userid, 'Add Section')) {
?>

<div class=button><a href="addsection.php"><?=GetText::gettext("Add New
Section")?></div></p>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
