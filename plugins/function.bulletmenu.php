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

							
			for ($i = 0; $i <= $one_section->level; $i++) { $menu .= "."; }
			$menu .= "|".$one_section->section_name."\n";

			foreach ($one_section->items as $one_item) {
			
				for ($i = 0; $i <= $one_section->level; $i++) { $menu .= "."; }
				$menu .= ".|".$one_item->menu_text."|".$one_item->url."\n";
				
			}
		}
		
		$count++;
		$last_level = $one_section->level;
	}

		
	$text = '
	<link rel="stylesheet" href="phplayers/layersmenu-cms.css" type="text/css"></link>
	<script language="JavaScript" type="text/javascript" src="phplayers/libjs/layersmenu-browser_detection.js"></script>
	<script language="JavaScript" type="text/javascript" src="phplayers/libjs/layersmenu-library.js"></script>
	<script language="JavaScript" type="text/javascript" src="phplayers/libjs/layersmenu.js"></script>';
	
	require_once 'phplayers/lib/PHPLIB.php';
	require_once 'phplayers/lib/layersmenu-common.inc.php';
	require_once 'phplayers/lib/layersmenu.inc.php';
	
	$mid = new LayersMenu();
	
	/* TO USE RELATIVE PATHS: */
	$mid->setDirroot('./phplayers/');
	$mid->setLibjsdir('./phplayers/libjs/');
	$mid->setImgdir('./phplayers/menuimages/');
	$mid->setImgwww('phplayers/menuimages/');
	//$mid->setIcondir('./phplayers/menuicons/');
	//$mid->setIconwww('phplayers/menuicons/');
	
	$mid->setTpldir('./phplayers/templates/');
	$mid->setVerticalMenuTpl('layersmenu-vertical_menu.ihtml');
	$mid->setSubMenuTpl('layersmenu-sub_menu.ihtml');

	
	$mid->setMenuStructureString($menu);
	$mid->setIconsize(16, 16);
	$mid->parseStructureForMenu('vermenu1');
	$mid->newVerticalMenu('vermenu1');
	
	$text .= $mid->getHeader();
	$text .= $mid->getMenu('vermenu1');
	$text .= $mid->getFooter();
	
	return $text;

}

# vim:ts=4 sw=4 noet
?>
