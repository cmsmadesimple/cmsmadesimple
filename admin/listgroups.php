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

?>
<h3>Current Groups</h3>
<?php

	$userid = get_userid();
	$perm = check_permission($config, $userid, 'Modify Permissions');
	$assign = check_permission($config, $userid, 'Modify Group Assignments');
	$edit = check_permission($config, $userid, 'Modify Group');
	$remove = check_permission($config, $userid, 'Remove Group');

	$db = new DB($config);

        $query = "SELECT group_id, group_name, active FROM ".$config->db_prefix."groups ORDER BY group_id";
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<th>Group Name</th>\n";
		echo "<th>Active</th>\n";
		if ($perm)
			echo "<th>&nbsp;</th>\n";
		if ($assign)
			echo "<th>&nbsp;</th>\n";
		if ($edit)
			echo "<th>&nbsp;</th>\n";
		if ($remove)
			echo "<th>&nbsp;</th>\n";
		echo "</tr>\n";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			echo "<tr>\n";
			echo "<td>".$row["group_name"]."</td>\n";
			echo "<td>".($row["active"] == 1?"True":"False")."</td>\n";
			if ($perm)
				echo "<td><a href=\"changegroupperm.php?group_id=".$row["group_id"]."\">Permissions</a></td>\n";
			if ($assign)
				echo "<td><a href=\"changegroupassign.php?group_id=".$row["group_id"]."\">Assignments</a></td>\n";
			if ($edit)
				echo "<td><a href=\"editgroup.php?group_id=".$row["group_id"]."\">Edit</a></td>\n";
			if ($remove)
				echo "<td><a href=\"deletegroup.php?group_id=".$row["group_id"]."\" onclick=\"return confirm('Are you sure you want to delete?');\">Delete</a></td>\n";
			echo "</tr>\n";

		}

		echo "</table>\n";

	}

        mysql_free_result($result);
	$db->close();

if (check_permission($config, $userid, 'Add Group')) {
?>

<p><a href="addgroup.php">Add New Group</a></p>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
