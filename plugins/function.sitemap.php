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

function smarty_cms_function_sitemap($params, &$smarty) {
	# set defaults
	global $gCms; 
	$thispage = $gCms->variables['page'];

	$standard = '<a href="{url}">{menu_text} - {page_title}</a>';
	$rootitem = '<h2><a href="{url}">{menu_text} - {page_title}</a></h2>';
	$sectionheader = '<h3>{menu_text}</h3>';
	
	$sitemap = "";
	$sitemap .= "\n\n<!-- sitemap start-->\n<ul>\n";

	# get menu data
	$newparams["show"] = "menu";
	$content = db_get_menu_items($newparams);	
	$level = array(0);
	
	foreach ($content as $menuitem) {
		
		# have we changed into another level?
		if ($menuitem->parent_id!=end($level)) {
			if (in_array($menuitem->parent_id, $level)) {
				do {
					$sitemap .= "</ul>\n</li>\n";
				} while ($menuitem->parent_id != array_pop($level) && $menuitem->parent_id >0);
			} else {
				array_push($level, $menuitem->parent_id);
			}
		} 
	
	
		# set class
		if ($menuitem->page_type=='sectionheader') $class=" class=\"sectionheader\""; 
		elseif ($menuitem->page_type=='link') $class=" class=\"link\"";
		else $class="";

		$sitemap .= "<li$class>";
		
		if ($menuitem->parent_id==0)
			$a = $rootitem;
		elseif ($menuitem->page_type=="sectionheader")
			$a = $sectionheader;
		else
			$a = $standard;

		// replace {url}, {menu_text}, {page_title}
		$search = array('{url}', '{menu_text}', '{page_title}');
		$replace = array($menuitem->url, $menuitem->menu_text, $menuitem->page_title);
		
		$sitemap .= str_replace($search, $replace, $a);

		# any children?
		if ($menuitem->childs!="") {
			$sitemap .= "\n<ul>\n";
		} else {
			$sitemap .= "</li>\n";
		}
	}
	
	# descend to ground safely - close off all the tags
	while (array_pop($level)) $sitemap .= "</ul>\n</li>\n";

	#close off the menu
	$sitemap .= "</ul>\n<!-- sitemap end -->\n\n";
	
	return $sitemap;
}

function smarty_cms_help_function_sitemap() {
	?>
	<h3>What does this do?</h3>
	<p>Prints a sitemap.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{sitemap}</code></p>
	<h3>What parameters does it take?</h3>
	<p>
		Sitemap doesn't take any parameters.	
	</p>

	<?php
}

function smarty_cms_about_function_sitemap() {
	?>
	<p>Author: Marcus Deglos &lt;<a href="mailto:md@zioncore.com">md@zioncore.com</a>&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>
