<?php

require_once("../include.php");

?>
<h3>Current Groups</h3>
<?php

	$db = new DB($config);

        $query = "SELECT group_id, group_name, active FROM ".$config->db_prefix."groups ORDER BY group_id";
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<th>Group Name</th>\n";
		echo "<th>Active</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "</tr>\n";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			echo "<tr>\n";
			echo "<td>".$row["group_name"]."</td>\n";
			echo "<td>".($row["active"] == 1?"True":"False")."</td>\n";
			echo "<td><a href=\"editgroup.php?group_id=".$row["group_id"]."\">Edit</a></td>\n";
			echo "<td><a href=\"deletegroup.php?group_id=".$row["group_id"]."\" onclick=\"return confirm('Are you sure you want to delete?');\">Delete</a></td>\n";
			echo "</tr>\n";

		}

		echo "</table>\n";

	}

        mysql_free_result($result);
	$db->close();

?>

<p><a href="addgroup.php">Add New Group</a></p>
<p><a href="index.php">Admin Menu</a></p>
