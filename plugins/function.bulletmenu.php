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

function smarty_cms_function_bulletmenu($params, &$smarty) {

	$menu = "";
	$levelstop = $params["levelstop"] ? $params["levelstop"] : 2 ;
	$showadmin = $params["showadmin"] ? $params["showadmin"] : "true";
	

	$content = db_get_menu_items($smarty->configCMS, "content_hierarchy");

	$last_level = 0;

	foreach ($content as $one) {

		if ($one->active && $one->level <= $levelstop) {

			if ($one->level < $last_level) {
				for ($i = $one->level; $i < $last_level; $i++)	$menu .= "</ul>\n";
			}
			if ($one->level > $last_level) {
				for ($i = $one->level; $i > $last_level; $i--) $menu .= "<ul>\n";
			}
			if ($one->page_type == "separator") {
				$menu .= "<hr class=\"separator\"/>";
			} else {
				$menu .= "<li><a href=\"".$one->url."\">".$one->menu_text."</a></li>\n";
			}
			$last_level = $one->level;
		}
	}

	for ($i = 0; $i < $last_level; $i++) $menu .= "</ul>";

	if ($showadmin == "true") {
		$menu .= "<br/>\n"."<ul><li><a href='admin'>Admin</a></li></ul>\n";
	}

	return $menu;

}

# vim:ts=4 sw=4 noet
?>
