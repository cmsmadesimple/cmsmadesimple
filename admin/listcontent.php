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
<h3><?=GetText::gettext("Current Pages")?></h3>
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

	$section_count;
	$query = "SELECT count(*) AS count, section_id FROM ".$config->db_prefix."pages GROUP BY section_id";
	$result = $db->query($query);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$section_count[$row[section_id]] = $row[count];
	}
	mysql_free_result($result);

	$query = "";
	if ($modifyall == true) {
		$query = "SELECT p.*, u.username, s.section_name FROM ".$config->db_prefix."pages p INNER JOIN ".$config->db_prefix."users u ON u.user_id = p.owner INNER JOIN ".$config->db_prefix."sections s ON s.section_id = p.section_id ORDER BY section_id, item_order";
	} else {
		$query = "SELECT p.*, u.username, s.section_name FROM ".$config->db_prefix."pages p INNER JOIN ".$config->db_prefix."users u ON u.user_id = p.owner INNER JOIN ".$config->db_prefix."sections s ON s.section_id = p.section_id LEFT OUTER JOIN ".$config->db_prefix."additional_users cau ON cau.page_id = p.page_id WHERE owner = ".$userid." OR cau.user_id = ".$userid." ORDER BY section_id, item_order";
	}

	$result = $db->query($query);

	if (mysql_num_rows($result) > 0) {

		echo '<table border="1" cellpadding="2" cellspacing="0" class="admintable">'."\n";
		echo "<thead class=\"tbhead\">\n";
		echo "<tr>\n";
		echo "<th>".GetText::gettext("Title")."</th>\n";
		echo "<th>".GetText::gettext("URL")."</th>\n";
		echo "<th>".GetText::gettext("Owner")."</th>\n";
		echo "<th>".GetText::gettext("Section")."</th>\n";
		if ($modifyall) {
			echo "<th>".GetText::gettext("Move")."</th>\n";
		}
		echo "<th>".GetText::gettext("Active")."</th>\n";
		echo "<th>".GetText::gettext("Default")."</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "<th>&nbsp;</th>\n";
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";

		$count = 1;

		$currow = "row1";

		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

			$totalcount = $section_count[$row[section_id]];

			echo "<tr class=\"$currow\">\n";
			echo "<td>".$row["page_title"]."</td>\n";
			echo "<td>".$row["page_url"]."</td>\n";
			echo "<td>".$row["username"]."</td>\n";
			echo "<td>".$row["section_name"]."</td>\n";
			if ($modifyall) {
				echo "<td>";
				if ($count > 1 && $totalcount > 1) {
					echo "<a href=\"movecontent.php?direction=up&page_id=".$row["page_id"]."\">".GetText::gettext("Up")."</a> ";
				}
				if ($count < $totalcount && $totalcount > 1) {
					echo "<a href=\"movecontent.php?direction=down&page_id=".$row["page_id"]."\">".GetText::gettext("Down")."</a>";
				}
				if ($totalcount == 1 && $count == 1) {
					echo "&nbsp;";
				}
				echo "</td>\n";
			}
			echo "<td>".($row["active"] == 1?GetText::gettext("True"):GetText::gettext("False"))."</td>\n";
			echo "<td>".($row["default_page"] == 1?"True":"<a href=\"listcontent.php?makedefault=".$row["page_id"]."\" onclick=\"return confirm('".GetText::gettext("Are you sure you want to set site\'s default page?")."');\">False</a>")."</td>\n";
			if ($config->query_var == "")
				echo "<td><a href=\"".$config->root_url."/index.php/".$row["page_url"]."\" target=\"_blank\">".GetText::gettext("View")."</a></td>\n";
			else
				echo "<td><a href=\"".$config->root_url."/index.php?".$config->query_var."=".$row["page_url"]."\" target=\"_blank\">".GetText::gettext("View")."</a></td>\n";
			echo "<td><a href=\"editcontent.php?page_id=".$row["page_id"]."\">".GetText::gettext("Edit")."</a></td>\n";
			echo "<td><a href=\"deletecontent.php?page_id=".$row["page_id"]."\" onclick=\"return confirm('".GetText::gettext("Are you sure you want to delete?")."');\">".GetText::gettext("Delete")."</a></td>\n";
			echo "</tr>\n";

			$count++;

			($currow == "row1"?$currow="row2":$currow="row1");

		}

		echo "</tbody>\n";
		echo "</table>\n";

	} else {
		echo "<p>".GetText::gettext("No pages")."</p>";
	}

	mysql_free_result($result);
	$db->close();

	if (check_permission($config, $userid, 'Add Content')) {
?>

<p><a href="addcontent.php"><?=GetText::gettext("Add New Content")?></a></p>

<?php
	}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
