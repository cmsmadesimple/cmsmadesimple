<?php

require_once("../config.php");

?>
<h3>Current Templates</h3>
<?php

	$db = new DB($config);

        $query = "SELECT template_id, template_name, active FROM templates ORDER BY template_id";
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<th>Template</th>\n";
		echo "<th>Active</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "</tr>\n";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			echo "<tr>\n";
			echo "<td>".$row["template_name"]."</td>\n";
			echo "<td>".($row["active"] == 1?"True":"False")."</td>\n";
			echo "<td><a href=\"edittemplate.php?template_id=".$row["template_id"]."\">Edit</a></td>\n";
			echo "<td><a href=\"deletetemplate.php?section_id=".$row["template_id"]."\" onclick=\"return confirm('Are you sure you want to delete?');\">Delete</a></td>\n";
			echo "</tr>\n";

		}

		echo "</table>\n";

	}

        mysql_free_result($result);
	$db->close();

?>

<p><a href="addtemplate.php">Add New Template</a></p>
<p><a href="index.php">Admin Menu</a></p>
