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
