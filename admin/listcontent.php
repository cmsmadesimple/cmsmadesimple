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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login($config);

include_once("header.php");

if (isset($_GET["message"])) {
    echo "<p class=\"error\">".$_GET["message"]."</p>";
}

?>
<h3><?=$gettext->gettext("Current Pages")?></h3>
<?php

	$userid = get_userid();

	$modifyall = check_permission($config, $userid, 'Modify Any Content');

	if ($modifyall) {
		if (isset($_GET["makedefault"])) {
			$query = "UPDATE ".$config->db_prefix."pages SET default_page = 0";
			$result = $dbnew->Execute($query);

			$query = "UPDATE ".$config->db_prefix."pages SET default_page = 1 WHERE page_id = ".$_GET["makedefault"];
			$result = $dbnew->Execute($query);
		}
	}

	$section_count;
	$query = "SELECT count(*) AS count, section_id FROM ".$config->db_prefix."pages GROUP BY section_id";
	$result = $dbnew->Execute($query);
	while($row = $result->FetchRow()) {
		$section_count[$row[section_id]] = $row[count];
	}

	$query = "";
	if ($modifyall == true) {
		$query = "SELECT p.*, u.username, s.section_name, t.template_name FROM ".$config->db_prefix."pages p LEFT OUTER JOIN ".$config->db_prefix."users u ON u.user_id = p.owner INNER JOIN ".$config->db_prefix."sections s ON s.section_id = p.section_id LEFT OUTER JOIN ".$config->db_prefix."templates t ON t.template_id = p.template_id ORDER BY section_id, item_order";
	} else {
		$query = "SELECT p.*, u.username, s.section_name, t.template_name FROM ".$config->db_prefix."pages p LEFT OUTER JOIN ".$config->db_prefix."users u ON u.user_id = p.owner INNER JOIN ".$config->db_prefix."sections s ON s.section_id = p.section_id LEFT OUTER JOIN ".$config->db_prefix."additional_users cau ON cau.page_id = p.page_id LEFT OUTER JOIN ".$config->db_prefix."templates t ON t.template_id = p.template_id WHERE owner = ".$userid." OR cau.user_id = ".$userid." ORDER BY section_id, item_order";
	}

	$result = $dbnew->Execute($query);

	if ($result) {

		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>".$gettext->gettext("Title")."</td>\n";
		echo "<td>".$gettext->gettext("Type")."</td>\n";
		echo "<td>".$gettext->gettext("URL")."</td>\n";
		echo "<td>".$gettext->gettext("Owner")."</td>\n";
		echo "<td>".$gettext->gettext("Section")."</td>\n";
		echo "<td>".$gettext->gettext("Template")."</td>\n";
		echo "<td>".$gettext->gettext("Active")."</td>\n";
		echo "<td>".$gettext->gettext("Default")."</td>\n";
		if ($modifyall) {
			echo "<td align=\"center\">".$gettext->gettext("Move")."</td>\n";
		}
		echo "<td>&nbsp;</td>\n";
		echo "<td>&nbsp;</td>\n";
		echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";

		$count = 1;
		$oldsectionid = -1;

		$currow = "row1";

		while($row = $result->FetchRow()) {

			$totalcount = $section_count[$row[section_id]];
			if ($oldsectionid != $row["section_id"]) {
				$count = 1;
				$oldsectionid = $row["section_id"];
			}

			$types = get_page_types($config);

			echo "<tr class=\"$currow\">\n";
			echo "<td>".$row["page_title"]."</td>\n";
			echo "<td>".$types[$row["page_type"]]."</td>\n";
			echo "<td>".$row["page_url"]."</td>\n";
			echo "<td>".$row["username"]."</td>\n";
			echo "<td>".$row["section_name"]."</td>\n";
			echo "<td>".$row["template_name"]."</td>\n";
			echo "<td>".($row["active"] == 1?$gettext->gettext("True"):$gettext->gettext("False"))."</td>\n";
			echo "<td>".($row["default_page"] == 1?"True":"<a href=\"listcontent.php?makedefault=".$row["page_id"]."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to set site\'s default page?")."');\">False</a>")."</td>\n";
			if ($modifyall) {
				echo "<td align=\"center\">";
				if ($count > 1 && $totalcount > 1) {
					echo "<a href=\"movecontent.php?direction=up&page_id=".$row["page_id"]."\"><img src=\"../images/arrow-u.png\" alt=\"".$gettext->gettext("Up")."\" border=\"0\" /></a> ";
				}
				if ($count < $totalcount && $totalcount > 1) {
					echo "<a href=\"movecontent.php?direction=down&page_id=".$row["page_id"]."\"><img src=\"../images/arrow-d.png\" alt=\"".$gettext->gettext("Down")."\" border=\"0\" /></a>";
				}
				if ($totalcount == 1 && $count == 1) {
					echo "&nbsp;";
				}
				echo "</td>\n";
			}
			if ($config->query_var == "")
				echo "<td><a href=\"".$config->root_url."/index.php/".$row["page_url"]."\" target=\"_blank\">".$gettext->gettext("View")."</a></td>\n";
			else
				echo "<td><a href=\"".$config->root_url."/index.php?".$config->query_var."=".$row["page_url"]."\" target=\"_blank\">".$gettext->gettext("View")."</a></td>\n";
			echo "<td><a href=\"editcontent.php?page_id=".$row["page_id"]."\">".$gettext->gettext("Edit")."</a></td>\n";
			echo "<td><a href=\"deletecontent.php?page_id=".$row["page_id"]."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to delete?")."');\">".$gettext->gettext("Delete")."</a></td>\n";
			echo "</tr>\n";

			$count++;

			($currow == "row1"?$currow="row2":$currow="row1");

		}

		echo "</table>\n";

	} else {
		echo "<p>".$gettext->gettext("No pages")."</p>";
	}

	if (check_permission($config, $userid, 'Add Content')) {
?>

<div class="button"><a href="addcontent.php"><?=$gettext->gettext("Add New
Content")?></a></div>

<?php
	}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
