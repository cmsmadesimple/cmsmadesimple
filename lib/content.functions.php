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
$sorted_content = array();

class Smarty_CMS extends Smarty {

	function Smarty_CMS(&$config)
	{
		$this->Smarty();

		$this->configCMS = &$config;

		$this->template_dir = $config->root_path.'/smarty/cms/templates/';
		$this->compile_dir = $config->root_path.'/smarty/cms/templates_c/';
		$this->config_dir = $config->root_path.'/smarty/cms/configs/';
		$this->cache_dir = $config->root_path.'/smarty/cms/cache/';
		$this->plugins_dir = $config->root_path.'/plugins/';

		$this->compile_check = true;
		$this->caching = true;
		$this->assign('app_name','CMS');
		$this->debugging = false;
		$this->force_compile = false;
		$this->cache_plugins = false;

		#Load all CMS plugins as non-cacheable
		$dir = dirname(dirname(__FILE__))."/plugins";
		$ls = dir($dir);
		while (($file = $ls->read()) != "") {
			if (is_file("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0)) {
				if (preg_match("/^(.*?)\.(.*?)\.php/", $file, $matches)) {
					#$filename = dirname(dirname(__FILE__)) . "/" . $this->_get_plugin_filepath($matches[1], $matches[2]);
					$filename = $this->_get_plugin_filepath($matches[1], $matches[2]);
					#echo $filename . "<br />";
					require_once $filename;
					$this->register_function($matches[2], "smarty_cms_function_" . $matches[2], $this->cache_plugins);
				}
			}
		}

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

		$query = "SELECT UNIX_TIMESTAMP(p.modified_date) as modified_date, p.page_content, p.page_title, p.page_type, t.template_id, t.stylesheet, t.template_content FROM ".$this->configCMS->db_prefix."pages p INNER JOIN ".$this->configCMS->db_prefix."templates t ON p.template_id = t.template_id WHERE p.page_id = '$tpl_name' AND p.active = 1";
		$result = $db->Execute($query);

		if ($result && $result->RowCount()) {
			$line = $result->FetchRow();

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
					call_user_func_array(&$cmsmodules[$line["page_type"]]['execute_function'], array($modulecmsobj,"cmsmodule_".++$modulecmsobj->modulenum."_",$params));
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

		$query = "SELECT t.modified_date as template_date, p.modified_date as page_date, p.page_type FROM ".$this->configCMS->db_prefix."pages p INNER JOIN ".$this->configCMS->db_prefix."templates t ON t.template_id = p.template_id WHERE p.page_id = '$tpl_name' AND p.active = 1";
		$result = $db->Execute($query);

		if ($result && $result->RowCount()) {
			$line = $result->FetchRow();

			$page_date = $db->UnixTimeStamp($line["page_date"]);
			$template_date = $db->UnixTimeStamp($line["template_date"]);

			$smarty_obj->assign('modified_date',($page_date<$template_date?$template_date:$page_date));

			#We only want to cache "static" content
			if ($line["page_type"] == "content") {

				$tpl_timestamp = ($page_date<$template_date?$template_date:$page_date);
				return true;

			} else {

				$tpl_timestamp = time();
				return true;

			}
		}
		else {

			$smarty_obj->assign('modified_date',time());
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

	$query = "SELECT page_id FROM ".$config->db_prefix."pages WHERE default_page = 1";
	$dbresult = $db->Execute($query);

	if ($dbresult) {
		$line = $dbresult->FetchRow();
		$result = $line["page_id"];
	}

	#We have no default.  Just get something!!!
	if ($result == "") {
		$query = "SELECT page_id FROM ".$config->db_prefix."pages";
		$dbresult = $db->SelectLimit($query, 1);

		if ($dbresult) {
			$line = $dbresult->FetchRow();
			$result = $line["page_id"];
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
	var $page_id;
	var $page_title;
	var $display_title;
	var $page_url;
	var $menu_text;
	var $show_in_menu;
	var $page_type;
	var $item_order;
	var $active;
	var $section_id;
	var $parent_id;
	var $level;
	var $hier;
	var $num_same_level;

	function get_child_content($parent_content, $all_content, $level, $hier) {
		global $sorted_content;
		reset($all_content);
		$count = 0;
		foreach ($all_content as $one) {
			if ($one->parent_id == $parent_content->page_id) {
				$count++;
				$prefix = "";
				for ($i=0; $i<=$level; $i++) {
					$prefix .= " -- ";
				}
				$one->hier = $hier.".".$count;
				$one->display_name = $prefix.$one->page_title;
				$one->level = $parent_content->level + 1;
				array_push($sorted_content, $one);
				$one->get_child_content($one, $all_content, $level +1, $one->hier);
			} ## if
		} ## foreach
	} ## function

	function get_num_same_level(&$config) {
		$db = $config->db;
		$query = "SELECT count(*) AS count FROM ".$config->db_prefix."pages WHERE parent_id = ".$this->parent_id;
		$result = $db->Execute($query);
		$line = $result->FetchRow();
		return $line["count"];
	}
		
} ## class

function db_get_menu_items(&$config, $style) {

	global $sorted_sections;
	global $sorted_content;
	$db = $config->db;

	$sorted_content = array();
	$content_array = array();
	
	if ($style == "content_hierarchy") {

		$query = "select p.*, u.username, t.template_name from ".$config->db_prefix."pages p LEFT OUTER JOIN ".$config->db_prefix."users u on u.user_id=p.owner LEFT OUTER JOIN ".$config->db_prefix."templates t on t.template_id=p.template_id order by parent_id, item_order";
		$result = $db->Execute($query);

		if ($result) {
			$content_array = array();
			while ($line = $result->FetchRow()) {
				$current_content = new Page;
				$current_content->page_id = $line["page_id"];
				$current_content->page_title = $line["page_title"];
				$current_content->page_url = $line["page_url"];
				$current_content->menu_text = $line["menu_text"];
				$current_content->page_type = $line["page_type"];
				$current_content->item_order = $line["item_order"];
				$current_content->active = $line["active"];
				$current_content->default_page = $line["default_page"];
				$current_content->username = $line["username"];
				$current_content->template_name = $line["template_name"];
				$current_content->parent_id = $line["parent_id"];
				$current_content->url = $line["url"];
				$current_content->hier = "1";
				$current_content->level = "1";
				$current_content->num_same_level = $current_content->get_num_same_level($config);
				# Fix URL where appropriate
				if ($current_content->page_type != "link") {
					$current_content->url = $config->root_url."/index.php?".$config->query_var."=".$current_content->page_id;	
					$current_content->page_url = "";	
				} else {
					$current_content->url = $current_content->page_url;
				}
				# Special display for separator
				if ($current_content->page_type == "separator") {
					$current_content->page_title = "--------";
				}
				array_push($content_array, $current_content);
			} ## while

			reset($content_array);
			$hier_level = 0;
			foreach ($content_array as $one_content) {
				if ($one_content->parent_id == 0) {
					$hier_level++;
					$one_content->hier = $hier_level;
					array_push($sorted_content, $one_content);
					$one_content->get_child_content($one_content, $content_array, 1, $hier_level);
				} ## if
			} ## foreach
		} ## if

		return $sorted_content;
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
	$result['separator'] = 'Separator';

	if (isset($cmsmodules)) {
		foreach ($cmsmodules as $key=>$value) {
			if (isset($value['content_module'])) {
				$result[$key] = $key;
			}
		}
	}

	return $result;

}

# vim:ts=4 sw=4 noet
?>
