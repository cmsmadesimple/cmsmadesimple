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
<h3><?=$gettext->gettext("Current Users")?></h3>
<?php

	$userid = get_userid();
	$edit = check_permission($userid, 'Modify User');
	$remove = check_permission($userid, 'Remove User');

	$query = "SELECT user_id, username, active FROM ".cms_db_prefix()."users ORDER BY user_id";
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {

		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>".$gettext->gettext("Username")."</td>\n";
		echo "<td>".$gettext->gettext("Active")."</td>\n";
		echo "<td>&nbsp;</td>\n";
		if ($remove)
			echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";

		$currow = "row1";

		while($row = $result->FetchRow()) {

			echo "<tr class=\"$currow\">\n";
			echo "<td>".$row["username"]."</td>\n";
			echo "<td width=\"10%\">".($row["active"] == 1?$gettext->gettext("True"):$gettext->gettext("False"))."</td>\n";
			if ($edit || $userid == $row["user_id"])
				echo "<td width=\"18\"><a href=\"edituser.php?user_id=".$row["user_id"]."\"><img src=\"../images/edit.png\" alt=\"".$gettext->gettext("Edit")."\" border=\"0\" /></a></td>\n";
			else
				echo "<td width=\"18\">&nbsp;</td>\n";
			if ($remove)
				echo "<td width=\"18\"><a href=\"deleteuser.php?user_id=".$row["user_id"]."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to delete?")."');\"><img src=\"../images/delete.png\" alt=\"".$gettext->gettext("Delete")."\" border=\"0\" /></a></td>\n";
			echo "</tr>\n";

			($currow=="row1"?$currow="row2":$currow="row1");

		}

		echo "</table>\n";

	}

if (check_permission($userid, 'Add User')) {
?>

<div class=button><a href="adduser.php"><?=$gettext->gettext("Add New User")?></div></p>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
