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

	$sections = db_get_menu_items($smarty->configCMS, "subs");

	$count=0;
	$last_level = -1;
	foreach ($sections as $one_section) {

		if ($one_section->active) {
			if (($one_section->level < $last_level || $one_section->level == 0) && $count > 0) {
				for ($i=$one_section->level; $i<=$last_level; $i++) {
					$menu .= "</ul>\n";
				}
			}

			if ($one_section->level >= $last_level || $one_section->level == 0) {
				if ($one_section->level != 0) { $menu .= "<li>"; }
				$menu .= "<p class=\"sectionname\">".$one_section->section_name."</p>\n";
				if ($one_section->level != 0) { $menu .= "</li>"; }
				if ($one_section->level > $last_level || $one_section->level == 0) {$menu .= "<ul>\n"; }
			}

			foreach ($one_section->items as $one_item) {
				$menu .= "<li><a href=\"".$one_item->url."\">".$one_item->menu_text."</a></li>\n";
			}
		}
		$count++;
		$last_level = $one_section->level;
	}

    for ($i=$one_section->level; $i<=$last_level +1; $i++) {
       $menu .= "</ul>\n";
    }
	
	return $menu;

}

# vim:ts=4 sw=4 noet
?>
