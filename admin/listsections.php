<?php

require_once("../config.php");

?>
<h3>Current Sections</h3>
<?php


	$db = new DB($config);

        $query = "SELECT section_id, section_name, active FROM ".$config->db_prefix."sections ORDER BY section_id";
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<th>Section</th>\n";
		echo "<th>Active</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "</tr>\n";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			echo "<tr>\n";
			echo "<td>".$row["section_name"]."</td>\n";
			echo "<td>".($row["active"] == 1?"True":"False")."</td>\n";
			echo "<td><a href=\"editsection.php?section_id=".$row["section_id"]."\">Edit</a></td>\n";
			echo "<td><a href=\"deletesection.php?section_id=".$row["section_id"]."\" onclick=\"return confirm('Are you sure you want to delete?');\">Delete</a></td>\n";
			echo "</tr>\n";

		}

		echo "</table>\n";

	}

        mysql_free_result($result);
        $db->query($link);

?>

<p><a href="addsection.php">Add New Section</a></p>
<p><a href="index.php">Admin Menu</a></p>
