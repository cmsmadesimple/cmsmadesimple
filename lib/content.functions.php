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

require_once(dirname(dirname(__FILE__)).'/smarty/Smarty.class.php');
$sorted_sections = array();

class Smarty_CMS extends Smarty {

	function Smarty_CMS(&$config)
	{
		$this->Smarty();

		$this->configCMS = &$config;

		$this->template_dir = $config->root_path.'/smarty/cms/templates/';
		$this->compile_dir = $config->root_path.'/smarty/cms/templates_c/';
		$this->config_dir = $config->root_path.'/smarty/cms/configs/';
		$this->cache_dir = $config->root_path.'/smarty/cms/cache/';
		$this->plugins_dir = $config->root_path.'/smarty/cms/plugins/';

		$this->compile_check = true;
		$this->caching = true;
		$this->assign('app_name','CMS');
		$this->debugging = false;
		$this->force_compile = true;

		$this->register_resource("db", array(&$this, "db_get_template",
						       "db_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
	}

	function db_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{

		global $cmsmodules;
		global $modulecmsobj;

		$db = $smarty_obj->configCMS->db;

		$query = "SELECT UNIX_TIMESTAMP(p.modified_date) as modified_date, p.page_content, p.page_title, p.page_type, t.template_id, t.stylesheet, t.template_content FROM ".$this->configCMS->db_prefix."pages p INNER JOIN ".$this->configCMS->db_prefix."templates t ON p.template_id = t.template_id WHERE p.page_url = '$tpl_name' AND p.active = 1";
		$result = $db->Execute($query);

		if ($result && $result->RowCount()) {
			$line = $result->FetchRow();

			$smarty_obj->assign('modified_date',$line[modified_date]);
			$stylesheet = "";
			if (isset($line[stylesheet])) {
				$stylesheet .= "<style type=\"text/css\">\n";
				$stylesheet .= "{literal}".$line["stylesheet"]."{/literal}";
				$stylesheet .= "</style>\n";
			}
			$tpl_source = $line[template_content];
			$content = $line[page_content];
			$title = $line[page_title];
			$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
			$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);

			#So no one can do anything nasty
			$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
			
			if ($line["page_type"] == "content") {
				#If it's regular content, do this...
				$tpl_source = ereg_replace("\{content\}", $content, $tpl_source);
				if ($this->configCMS->use_bb_code == true && isset($this->configCMS->bbcodeparser)) {
					$tpl_source = $this->configCMS->bbcodeparser->qparse($tpl_source);
				}
			} else {
				#If it's a module, do this instead...
				if (isset($cmsmodules[$line["page_type"]])) {
					@ob_start();
					$obj = $cmsmodules[$line["page_type"]]['Instance'];
					$obj->execute($modulecmsobj,"randstringgoeshere_",$params);
					$modoutput = @ob_get_contents();
					@ob_end_clean();
					$tpl_source = ereg_replace("\{content\}", $modoutput, $tpl_source);
				}
			}

			return true;
		}
		else {
			return false;
		}
	}

	function db_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{

		$db = $smarty_obj->configCMS->db;

		$query = "SELECT UNIX_TIMESTAMP(IF(t.modified_date>p.modified_date,t.modified_date,p.modified_date)) as create_date, p.page_type FROM ".$this->configCMS->db_prefix."pages p INNER JOIN ".$this->configCMS->db_prefix."templates t ON t.template_id = p.template_id WHERE p.page_url = '$tpl_name' AND p.active = 1";
		$result = $db->Execute($query);

		if ($result && $result->RowCount()) {
			$line = $result->FetchRow();

			#We only want to cache "static" content
			if ($line["page_type"] == "content") {

				$tpl_timestamp = $line["create_date"];
				return true;

			} else {

				$tpl_timestamp = time();
				return true;

			}
		}
		else {

			$tpl_timestamp = time();
			return false;
		}
	}

	function db_get_secure($tpl_name, &$smarty_obj)
	{
		// assume all templates are secure
		return true;
	}

	function db_get_trusted($tpl_name, &$smarty_obj)
	{
		// not used for templates
	}
}

function db_get_default_page (&$config) {

	$db = $config->db;

	$result = "";

	$query = "SELECT page_url FROM ".$config->db_prefix."pages WHERE default_page = 1";
	$dbresult = $db->Execute($query);

	if ($dbresult) {
		$line = $dbresult->FetchRow();
		$result = $line["page_url"];
	}

	#We have no default.  Just get something!!!
	if ($result == "") {
		$query = "SELECT page_url FROM ".$config->db_prefix."pages";
		$dbresult = $db->SelectLimit($query, 1);

		if ($dbresult) {
			$line = $dbresult->FetchRow();
			$result = $line["page_url"];
		}
	}

	return $result;
}

class Section {
	var $section_id;
	var $section_name;
	var $display_name;
	var $parent_id;
	var $items;
	var $level;

	function get_child_sections($section_id, $sections, $level) {

		global $sorted_sections;
		reset($sections);
		$child_sections = array();
		foreach ($sections as $one) {
			if ($section_id == $one->parent_id) {
				$prefix = "";
				for ($i=0; $i<=$level; $i++) { $prefix .= " -- "; }

				$one->display_name = $prefix.$one->section_name;
				$one->level = $level;
				array_push($sorted_sections, $one);
				$children = $one->get_child_sections($one->section_id, $sections, $level +1);
			} ## if
		} ## foreach

	} ## function
} ## class

class MenuItem {
	var $name;
	var $url;
}

class Page {
	var $title;
	var $url;
	var $menu_text;
	var $show_in_menu;
	var $page_type;
	var $item_order;
	var $active;
	var $section_id;
}

function db_get_menu_items(&$config, $style) {

	global $sorted_sections;
	$db = $config->db;

	$sections = array();
	$current_section;

## 	echo "style: ($style)";
	if ($style === "subs") { ## shows all sections and subsections, no pages
		$query = "SELECT p.menu_text, p.page_title, p.page_url, s.section_name, s.parent_id, s.section_id, s.active FROM ".$config->db_prefix."sections s LEFT OUTER JOIN ".$config->db_prefix."pages p ON s.section_id=p.section_id order by s.parent_id, s.item_order";
		$result = $db->Execute($query);

		$pages = array();
		while ($line = $result->FetchRow()) {

			if (!isset($current_section) || $line["section_id"] != $current_section->section_id) {

				if (isset($current_section)) {
					array_push($sections, $current_section);
				} ## if

				$current_section = new Section;
				$current_section->section_id = $line["section_id"];
				$current_section->section_name = $line["section_name"];
				$current_section->display_name = $line["section_name"];
				$current_section->parent_id = $line["parent_id"];
				$current_section->level = 0;
				$current_section->active = $line["active"];
				$current_section->items = array();
			} ## if

			if ($line["page_title"] != "") {
				$menu_item = new MenuItem;
				$menu_item->menu_text = $line["menu_text"];
				if ($line['page_type'] == "link") {
					$menu_item->url = $line["page_url"];
				} else {
					if (isset($config->query_var) && $config->query_var != "") {
						$menu_item->url = $config->root_url."/index.php?".$config->query_var."=".$line["page_url"];
					}
					else if ($line['page_type'] == "link") {
						$menu_item->url = $line["page_url"];
					}
					array_push($current_section->items, $menu_item);
				}
			}

		} ## while

		if (isset($current_section)) {
			array_push($sections, $current_section);
		} ## if


		reset($sections);
		$children = array();
		foreach($sections as $one_section) {
			if ($one_section->parent_id == 0) {
				array_push($sorted_sections, $one_section);
				$children = $one_section->get_child_sections($one_section->section_id, $sections, 1);
			} ## if
		} ## foreach
		
		return $sorted_sections;

	} elseif ($style === "basic") {
		$query = "SELECT p.*, s.section_name, s.parent_id FROM ".$config->db_prefix."pages p INNER JOIN ".$config->db_prefix."sections s ON s.section_id = p.section_id WHERE p.show_in_menu = 1 AND p.active = 1 ORDER BY s.item_order, p.item_order, p.menu_text";
		$result = $db->Execute($query);

		while ($line = $result->FetchRow()) {

			if (!isset($current_section) || $line["section_name"] != $current_section->section_name) {

				if (isset($current_section)) {
					array_push($sections, $current_section);
				}
				$current_section = new Section;
				$current_section->section_name = $line["section_name"];
				$current_section->parent_id = $line["parent_id"];
				$current_section->items = array();
			}

			$menu_item = new MenuItem;
			$menu_item->menu_text = $line["menu_text"];
			if ($line['page_type'] == "link") {
				$menu_item->url = $line["page_url"];
			}
			else {
				if (isset($config->query_var) && $config->query_var != "") {
					$menu_item->url = $config->root_url."/index.php?".$config->query_var."=".$line["page_url"];
				}
				else if ($line['page_type'] == "link") {
					$menu_item->url = $line["page_url"];
				}
				array_push($current_section->items, $menu_item);
			}

		} ## while

	} ## if

	if (isset($current_section)) {
		array_push($sections, $current_section);
	}
	return $sections;
			
} ## function

function get_page_types(&$config) {

	global $cmsmodules;

	$result['content'] = 'Content';
	$result['link'] = 'Link';

	if (isset($cmsmodules)) {
		foreach ($cmsmodules as $key=>$value) {
			$result[$key] = $key;
		}
	}

	return $result;

}

# vim:ts=4 sw=4 noet
?>
