<?php

	require("../config.php");

?>
<h3>Current Pages</h3>
<?php
	$db = new DB($config);

        $query = "SELECT p.*, u.username, s.section_name FROM pages p INNER JOIN users u ON u.user_id = p.owner INNER JOIN sections s ON s.section_id = p.section_id ORDER BY page_id";
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<th>Title</th>\n";
		echo "<th>URL</th>\n";
		echo "<th>Owner</th>\n";
		echo "<th>Section</th>\n";
		echo "<th>Active</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "</tr>\n";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			echo "<tr>\n";
			echo "<td>".$row["page_title"]."</td>\n";
			echo "<td>".$row["page_url"]."</td>\n";
			echo "<td>".$row["username"]."</td>\n";
			echo "<td>".$row["section_name"]."</td>\n";
			echo "<td>".($row["active"] == 1?"True":"False")."</td>\n";
			echo "<td><a href=\"".$doc_root."/index.php".$row["page_url"]."\" target=\"_blank\">View</a></td>\n";
			echo "<td><a href=\"editcontent.php?page_id=".$row["page_id"]."\">Edit</a></td>\n";
			echo "<td><a href=\"deletecontent.php?page_id=".$row["page_id"]."\" onclick=\"return confirm('Are you sure you want to delete?');\">Delete</a></td>\n";
			echo "</tr>\n";

		}

		echo "</table>\n";

	}

        mysql_free_result($result);
	$db->close();

?>

<p><a href="addcontent.php">Add New Content</a></p>
<p><a href="index.php">Admin Menu</a></p>
