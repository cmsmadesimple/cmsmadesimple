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

function smarty_cms_function_bulletmenu($params, &$smarty) {

	# getting menu parameters
	$showadmin = isset($params["showadmin"]) ? $params["showadmin"] : 1 ;
	
	# getting content hierarchy parameters
	$newparams = array();
	foreach($params as $key => $val) $newparams[$key] = $val;
	$newparams["show"] = "menu";

	# getting content
	$content = db_get_menu_items($newparams);

	# defining variables
	$menu = "";
	$last_level = 0;

	foreach ($content as $one) {

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

	for ($i = 0; $i < $last_level; $i++) $menu .= "</ul>";

	if ($showadmin == 1) {
		$menu .= "<ul><li><a href='admin'>Admin</a></li></ul>\n";
	}

	return $menu;

}

function smarty_cms_help_function_bulletmenu() {
	?>
	<h3>What does this do?</h3>
	<p>Prints a bullet menu.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{bulletmenu}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em> <tt>showadmin</tt> - 1/0, whether you want to show or not the admin link.</li>
		<li><em>(optional)</em> <tt>start_element</tt> - a page ID. This parameter sets the root of the menu.</li>
		<li><em>(optional)</em> <tt>number_of_levels</tt> - an integer, the number of levels you want to show in your menu.</li>
	</ul>
	</p>

	<?
}

function smarty_cms_about_function_bulletmenu() {
	?>
	<p>Author: Julien Lancien&lt;calexico@ifrance.com&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?
}

# vim:ts=4 sw=4 noet
?>
