<?php

require_once("../include.php");

check_login($config);

include_once("header.php");

?>
<h3>Current Sections</h3>
<?php

	$userid = get_userid();
	$edit = check_permission($config, $userid, 'Modify Section');
	$remove = check_permission($config, $userid, 'Remove Section');

	$db = new DB($config);

        $query = "SELECT section_id, section_name, active FROM ".$config->db_prefix."sections ORDER BY section_id";
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<th>Section</th>\n";
		echo "<th>Active</th>\n";
		if ($edit)
			echo "<th>&nbsp;</th>\n";
		if ($remove)
			echo "<th>&nbsp;</th>\n";
		echo "</tr>\n";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			echo "<tr>\n";
			echo "<td>".$row["section_name"]."</td>\n";
			echo "<td>".($row["active"] == 1?"True":"False")."</td>\n";
			if ($edit)
				echo "<td><a href=\"editsection.php?section_id=".$row["section_id"]."\">Edit</a></td>\n";
			if ($remove)
				echo "<td><a href=\"deletesection.php?section_id=".$row["section_id"]."\" onclick=\"return confirm('Are you sure you want to delete?');\">Delete</a></td>\n";
			echo "</tr>\n";

		}

		echo "</table>\n";

	}

        mysql_free_result($result);
        $db->query($link);

if (check_permission($config, $userid, 'Add Section')) {
?>

<p><a href="addsection.php">Add New Section</a></p>

<?php
}

include_once("footer.php");

?>
