<?php

require_once("../include.php");

check_login($config);

include_once("header.php");

?>
<h3>Current Templates</h3>
<?php

	$userid = get_userid();
	$edit = check_permission($config, $userid, 'Modify Template');
	$remove = check_permission($config, $userid, 'Remove Template');

	$db = new DB($config);

        $query = "SELECT template_id, template_name, active FROM ".$config->db_prefix."templates ORDER BY template_id";
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<th>Template</th>\n";
		echo "<th>Active</th>\n";
		if ($edit)
			echo "<th>&nbsp;</th>\n";
		if ($remove)
			echo "<th>&nbsp;</th>\n";
		echo "</tr>\n";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			echo "<tr>\n";
			echo "<td>".$row["template_name"]."</td>\n";
			echo "<td>".($row["active"] == 1?"True":"False")."</td>\n";
			if ($edit)
				echo "<td><a href=\"edittemplate.php?template_id=".$row["template_id"]."\">Edit</a></td>\n";
			if ($remove)
				echo "<td><a href=\"deletetemplate.php?template_id=".$row["template_id"]."\" onclick=\"return confirm('Are you sure you want to delete?');\">Delete</a></td>\n";
			echo "</tr>\n";

		}

		echo "</table>\n";

	}

        mysql_free_result($result);
	$db->close();

if (check_permission($config, $userid, 'Add Template')) {
?>

<p><a href="addtemplate.php">Add New Template</a></p>

<?php
}

include_once("footer.php");

?>
