<?php

require_once("config.php");

function smarty_function_bulletmenu($params, &$smarty) {

        $menu = "";
        $current_section = "";
        $firsttime = 1;

	$db = new DB($smarty->configCMS);

        $query = "SELECT p.*, s.section_name FROM ".$smarty->configCMS->db_prefix."pages p INNER JOIN ".$smarty->configCMS->db_prefix."sections s ON s.section_id = p.section_id WHERE p.show_in_menu = 1 ORDER BY s.section_id, p.menu_text";
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
