<?php

require_once("../include.php");

check_login($config);

?>
<h3>Current Users</h3>
<?php

	$db = new DB($config);

        $query = "SELECT user_id, username, active FROM ".$config->db_prefix."users ORDER BY user_id";
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<th>Username</th>\n";
		echo "<th>Active</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "</tr>\n";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			echo "<tr>\n";
			echo "<td>".$row["username"]."</td>\n";
			echo "<td>".($row["active"] == 1?"True":"False")."</td>\n";
			echo "<td><a href=\"edituser.php?user_id=".$row["user_id"]."\">Edit</a></td>\n";
			echo "<td><a href=\"deleteuser.php?user_id=".$row["user_id"]."\" onclick=\"return confirm('Are you sure you want to delete?');\">Delete</a></td>\n";
			echo "</tr>\n";

		}

		echo "</table>\n";

	}

        mysql_free_result($result);
	$db->close();

?>

<p><a href="adduser.php">Add New User</a></p>
<p><a href="index.php">Admin Menu</a></p>
