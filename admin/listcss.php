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
<h3><?=$gettext->gettext("Current CSS")?></h3>
<?php

	$userid = get_userid();

	$modify = check_permission($userid, 'Modify CSS');
	$addcss = check_permission($userid, 'Add CSS');
	$delcss = check_permission($userid, 'Remove CSS');

	$query = "SELECT * FROM ".cms_db_prefix()."css ORDER BY css_name";
	$result = $db->Execute($query);

	if ($result) {

		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>".$gettext->gettext("Title")."</td>\n";
		echo "<td>&nbsp;</td>\n";
		echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";

		$count = 1;

		$currow = "row1";

		while ($one = $result->FetchRow()) {

			echo "<tr class=\"$currow\">\n";
			echo "<td>".$one["css_name"]."</td>\n";

			# edit
			if ($modify) {
				echo "<td width=\"18\"><a href=\"editcss.php?css_id=".$one["css_id"]."\"><img src=\"../images/cms/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".gettext("Edit")."\"></a></td>\n";
			}
			else {
				echo "<td>&nbsp;</td>";
			}

			# delete
			if ($delcss) {
				echo "<td width=\"18\"><a href=\"deletecss.php?css_id=".$one["css_id"]."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to delete?")."');\"><img src=\"../images/cms/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".gettext("Delete")."\"></a></td>\n";
			}
			else {
				echo "<td>&nbsp;</td>";
			}

			echo "</tr>\n";

			$count++;

			($currow == "row1"?$currow="row2":$currow="row1");

		} ## foreach

		echo "</table>\n";

	} else {
		echo "<p>".$gettext->gettext("No CSS")."</p>";
	}

	if ($addcss) {
?>

<div class="button"><a href="addcss.php"><?=$gettext->gettext("Add New CSS")?></a></div>

<?php
	}


include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
