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

require("../include.php");

check_login($config);

include_once("header.php");

?>
<h3>Current Pages</h3>
<?php
	$userid = get_userid();

	$modifyall = check_permission($config, $userid, 'Modify Any Content');

	$db = new DB($config);

	if ($modifyall) {
		if (isset($_GET["makedefault"])) {
			$query = "UPDATE ".$config->db_prefix."pages SET default_page = 0";
			$result = $db->query($query);

			$query = "UPDATE ".$config->db_prefix."pages SET default_page = 1 WHERE page_id = ".$_GET["makedefault"];
			$result = $db->query($query);
		}
	}

	$query = "";
	if ($modifyall == true) {
		$query = "SELECT p.*, u.username, s.section_name FROM ".$config->db_prefix."pages p INNER JOIN ".$config->db_prefix."users u ON u.user_id = p.owner INNER JOIN ".$config->db_prefix."sections s ON s.section_id = p.section_id ORDER BY page_id";
	} else {
		$query = "SELECT p.*, u.username, s.section_name FROM ".$config->db_prefix."pages p INNER JOIN ".$config->db_prefix."users u ON u.user_id = p.owner INNER JOIN ".$config->db_prefix."sections s ON s.section_id = p.section_id WHERE owner = ".$userid." ORDER BY page_id";
	}
        $result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<th>Title</th>\n";
		echo "<th>URL</th>\n";
		echo "<th>Owner</th>\n";
		echo "<th>Section</th>\n";
		echo "<th>Active</th>\n";
		echo "<th>Default</th>\n";
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
			echo "<td>".($row["default_page"] == 1?"True":"<a href=\"listcontent.php?makedefault=".$row["page_id"]."\" onclick=\"return confirm('Are you sure you want to set site\'s default page?');\">False</a>")."</td>\n";
			if ($config->query_var == "")
				echo "<td><a href=\"".$doc_root."/index.php/".$row["page_url"]."\" target=\"_blank\">View</a></td>\n";
			else
				echo "<td><a href=\"".$doc_root."/?".$config->query_var."=".$row["page_url"]."\" target=\"_blank\">View</a></td>\n";
			echo "<td><a href=\"editcontent.php?page_id=".$row["page_id"]."\">Edit</a></td>\n";
			echo "<td><a href=\"deletecontent.php?page_id=".$row["page_id"]."\" onclick=\"return confirm('Are you sure you want to delete?');\">Delete</a></td>\n";
			echo "</tr>\n";

		}

		echo "</table>\n";

	} else {
		echo "<p>No pages</p>";
	}

        mysql_free_result($result);
	$db->close();

if (check_permission($config, $userid, 'Add Content')) {
?>

<p><a href="addcontent.php">Add New Content</a></p>

<?php
}

include_once("footer.php");

?>
