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

function smarty_cms_function_dhtmlmenu($params, &$smarty) {

	global $db;

	# getting menu parameters
	$showadmin = isset($params["showadmin"]) ? $params["showadmin"] : 1 ;
	
	# getting content hierarchy parameters
	$newparams = array();
	foreach($params as $key => $val) $newparams[$key] = $val;
	$newparams["show"] = "menu";

	if (isset($newparams["start_element"]))
	{
		$tmp	= $newparams["start_element"];
		$tmptab	= explode(".",$tmp);
		$parent	= 0;

		foreach($tmptab as $key)
		{
			if ("" != $key)
			{
				$query	= "SELECT page_id FROM ".cms_db_prefix()."pages WHERE item_order = '$key' AND parent_id = '$parent'";
				$result = $db->Execute($query);
				if ($result && $result->RowCount() > 0)
				{
					$line	= $result->FetchRow();
					$parent	= $line["page_id"];
				}
			}
		}
		$newparams["start_element"] = $parent;
	}

	# getting content
	$content = db_get_menu_items($newparams);

	$menu = "";

	foreach ($content as $one) {

		for ($i = 1; $i <= $one->level; $i++) { $menu .= "."; }

		if ($one->page_type == "separator") {
			$menu .= "|---\n";
		} else {
			$menu .= "|".$one->menu_text."|".$one->url."\n";
		}
	}

	if ($showadmin == 1) {
		$menu .= ".|---\n";
		$menu .= ".|Admin|admin\n";
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

function smarty_cms_help_function_dhtmlmenu() {
	?>
	<h3>What does this do?</h3>
	<p>Prints a dhtml vertical menu.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{dhtmlmenu}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em> <tt>showadmin</tt> - 1/0, whether you want to show or not the admin link.</li>
		<li><em>(optional)</em> <tt>start_element</tt> - a page ID. This parameter sets the root of the menu.</li>
		<li><em>(optional)</em> <tt>number_of_levels</tt> - an integer, the number of levels you want to show in your menu.</li>
	</ul>
	</p>
	<?php
}

function smarty_cms_about_function_dhtmlmenu() {
	?>
	<p>Author: Julien Lancien&lt;calexico@ifrance.com&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>
