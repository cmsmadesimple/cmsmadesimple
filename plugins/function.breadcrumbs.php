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

function smarty_cms_function_breadcrumbs($params, &$smarty) {

	global $db;
	global $gCms; 
	
	$thispage = $gCms->variables['page'];

	# track - identify all ancestors of current page
	$parent = $thispage;
	$breadcrumbs = array();
	
	# gather data
	while ($parent != 0) {
		$query	= "SELECT * FROM ".cms_db_prefix()."pages WHERE page_id = '$parent'";
		$result = $db->Execute($query);
		if ($result && $result->RowCount() > 0) {
			$line	= $result->FetchRow();
			$parent	= $line["parent_id"];

			$current_content = new Page;
			$current_content->page_id		= $line["page_id"];
			$current_content->page_title	= $line["page_title"];
			$current_content->page_alias	= $line["page_alias"];
			$current_content->page_url		= $line["page_url"];
			$current_content->menu_text		= $line["menu_text"];
			$current_content->page_type		= $line["page_type"];
			$current_content->item_order	= $line["item_order"];
			$current_content->active		= $line["active"];
			$current_content->show_in_menu	= $line["show_in_menu"];
			$current_content->default_page	= $line["default_page"];
			$current_content->username		= $line["username"];
			$current_content->template_name = $line["template_name"];
			$current_content->parent_id		= $line["parent_id"];
			if (isset($line["url"])) $current_content->url = $line["url"];
			array_push($breadcrumbs, $current_content);
		}
	}

	$trail = "";
	# process $breadcrumbs
	while ($a = array_pop($breadcrumbs)) {
		if ($a->page_id != $thispage && $a->page_type != 'seperator') {
			if (getURL($thispage)!="") $trail .= "<a href=\"".getURL($a)."\">".$a->page_title."</a> &gt;&gt;\n";
			else $trail .= $a->page_title." &gt;&gt; \n";
		} else {
			$trail .= "<strong>".$a->page_title."</strong>\n";
		}
	}

	return $trail;

}

function smarty_cms_help_function_breadcrumbs() {
	?>
	<h3>What does this do?</h3>
	<p>Prints a breadcrumb trail .</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{breadcrumbs}</code></p>
	<h3>What parameters does it take?</h3>
	<p>
		No parameters at the moment, a future release may allow you to change what goes between each page.
	</p>


	<?php
}

function smarty_cms_about_function_breadcrumbs() {
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
