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
<h3>Current Users</h3>
<?php

	$userid = get_userid();
	$edit = check_permission($config, $userid, 'Modify User');
	$remove = check_permission($config, $userid, 'Remove User');

	$db = new DB($config);

        $query = "SELECT user_id, username, active FROM ".$config->db_prefix."users ORDER BY user_id";
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<th>Username</th>\n";
		echo "<th>Active</th>\n";
		if ($edit)
			echo "<th>&nbsp;</th>\n";
		if ($remove)
			echo "<th>&nbsp;</th>\n";
		echo "</tr>\n";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			echo "<tr>\n";
			echo "<td>".$row["username"]."</td>\n";
			echo "<td>".($row["active"] == 1?"True":"False")."</td>\n";
			if ($edit)
				echo "<td><a href=\"edituser.php?user_id=".$row["user_id"]."\">Edit</a></td>\n";
			if ($remove)
				echo "<td><a href=\"deleteuser.php?user_id=".$row["user_id"]."\" onclick=\"return confirm('Are you sure you want to delete?');\">Delete</a></td>\n";
			echo "</tr>\n";

		}

		echo "</table>\n";

	}

        mysql_free_result($result);
	$db->close();

if (check_permission($config, $userid, 'Add User')) {
?>

<p><a href="adduser.php">Add New User</a></p>

<?php
}

include_once("footer.php");

?>
