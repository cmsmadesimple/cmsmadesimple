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

require_once("config.php");

function smarty_function_bulletmenu($params, &$smarty) {

        $menu = "";
        $current_section = "";
        $firsttime = 1;

	$db = new DB($smarty->configCMS);

        $query = "SELECT p.*, s.section_name FROM ".$smarty->configCMS->db_prefix."pages p INNER JOIN ".$smarty->configCMS->db_prefix."sections s ON s.section_id = p.section_id WHERE p.show_in_menu = 1 ORDER BY s.section_id, p.item_order, p.menu_text";
        $result = $db->query($query);

        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {

                if ($line["section_name"] != $current_section) {

                        if ($firsttime == 0) {
                                $menu .= "</ul>";
                        }

                        $menu .= "<p class=\"sectionname\">".$line["section_name"]."</p>";
                        $menu .= "<ul>";
                        $current_section = $line["section_name"];

                        $firsttime = 0;

                }

		if (isset($smarty->configCMS->query_var) && $smarty->configCMS->query_var != "") {
			$menu .= "<li><a href=\"".$smarty->configCMS->root_url."/?".$smarty->configCMS->query_var."=".$line["page_url"]."\">".$line["menu_text"]."</a></li>";
		}
		else {
			$menu .= "<li><a href=\"".$smarty->configCMS->root_url."/index.php".$line["page_url"]."\">".$line["menu_text"]."</a></li>";
		}
        }
        $menu .= "</ul>";

        mysql_free_result($result);
        $db->close();

        return $menu;

}

?>
