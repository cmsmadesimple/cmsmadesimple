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

/**
 * Handles content related functions
 *
 * @package CMS
 */
require_once(dirname(dirname(__FILE__)).'/smarty/Smarty.class.php');
$sorted_sections = array();
$sorted_content = array();

/**
 * Extends the Smarty class for content.
 *
 * Extends the Smarty class for checking timestamps and rendering
 * content to the browser.
 *
 * @since 0.1
 */
class Smarty_CMS extends Smarty {

	function Smarty_CMS(&$config)
	{
		$this->Smarty();

		$this->template_dir = $config["root_path"].'/smarty/cms/templates/';
		$this->compile_dir = $config["root_path"].'/smarty/cms/templates_c/';
		$this->config_dir = $config["root_path"].'/smarty/cms/configs/';
		$this->cache_dir = $config["root_path"].'/smarty/cms/cache/';
		$this->plugins_dir = $config["root_path"].'/plugins/';

		$this->compile_check = true;
		$this->caching = true;
		$this->assign('app_name','CMS');
		$this->debugging = false;
		$this->force_compile = false;
		$this->cache_plugins = false;

		if ($config["debug"] == true)
		{
			#$this->caching = false;
			#$this->force_compile = true;
		}

		if (get_site_preference('enablesitedownmessage') == "1")
		{
			$this->caching = false;
			$this->force_compile = true;
		}

		load_plugins($this);

		$this->register_resource("db", array(&$this, "db_get_template",
						       "db_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
	}

	function db_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{

		global $gCms;

		$cmsmodules = $gCms->modules;
		$db = $gCms->db;
		$config = $gCms->config;

		if (get_site_preference('enablesitedownmessage') == "1")
		{
			$tpl_source = get_site_preference('sitedownmessage');
			return true;
		} else {
			$query = "SELECT p.page_id, p.page_content, p.page_title, p.page_type, p.head_tags, t.template_id, t.stylesheet, t.template_content FROM ".cms_db_prefix()."pages p INNER JOIN ".cms_db_prefix()."templates t ON p.template_id = t.template_id WHERE (p.page_id = ".$db->qstr($tpl_name)." OR p.page_alias=".$db->qstr($tpl_name).") AND p.active = 1";
			$result = $db->Execute($query);

			if (!$result || !$result->RowCount())
			{
				if (get_site_preference('custom404template') > 0 && get_site_preference('enablecustom404') == "1")
				{
					$this->caching = false;
					$this->force_compile = true;
					$query = "SELECT t.template_id, t.stylesheet, t.template_content FROM ".cms_db_prefix()."templates t WHERE t.template_id = ".get_site_preference('custom404template');
					$result = $db->Execute($query);
				}
			}

			if ($result && $result->RowCount())
			{
				$line = $result->FetchRow();

				#This way the id is right, even if an alias is given
				$gCms->variables['page'] = $line['page_id'];

				if (isset($line['stylesheet']))
				{
					$stylesheet .= "<style type=\"text/css\">\n";
					$stylesheet .= "{literal}".$line["stylesheet"]."{/literal}";
					$stylesheet .= "</style>\n";
				}

				# the new css stuff
				$tempstylesheet = "";

				$cssquery = "SELECT css_text FROM ".cms_db_prefix()."css, ".cms_db_prefix()."css_assoc
					WHERE	css_id		= assoc_css_id
					AND		assoc_type	= 'template'
					AND		assoc_to_id = '".$line[template_id]."'";
				$cssresult = $db->Execute($cssquery);

				$stylesheet .= "<style type=\"text/css\">\n";
				while ($cssline = $cssresult->FetchRow())
				{
					$tempstylesheet .= "\n".$cssline[css_text]."\n";
				}
				$stylesheet .= "{literal}".$tempstylesheet."{/literal}";
				$stylesheet .= "</style>\n";
				
				$tpl_source = $line['template_content'];
				$content = $line['page_content'];
				$title = $line['page_title'];
				$head_tags = $line['head_tags'];
				$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
				$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);
				if (isset($head_tags) && $head_tags != "")
				{
					$tpl_source = ereg_replace("<\/head>", $head_tags."</head>", $tpl_source);
				}

				#So no one can do anything nasty
				if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
				{
					$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
				}
				
				if ($line["page_type"] == "content")
				{
					#If it's regular content, do this...
					$tpl_source = ereg_replace("\{content\}", $content, $tpl_source);
					if ($config["use_bb_code"] == true && isset($gCms->bbcodeparser))
					{
						$tpl_source = $gCms->bbcodeparser->qparse($tpl_source);
					}
				}
				else
				{
					#If it's a module, do this instead...
					if (isset($cmsmodules[$line["page_type"]]['plugin_module'])
						&& $cmsmodules[$line["page_type"]]['Installed'] == true
						&& $cmsmodules[$line["page_type"]]['Active'] == true)
					{
						@ob_start();
						call_user_func_array($cmsmodules[$line["page_type"]]['execute_function'], array($gCms,"cmsmodule_".++$gCms->variables["modulenum"]."_",$params));
						$modoutput = @ob_get_contents();
						@ob_end_clean();
						$tpl_source = ereg_replace("\{content\}", $modoutput, $tpl_source);
					}
					else //Prolly a 404 message
					{
						$tpl_source = ereg_replace("\{title\}", 'Page Not Found!', $tpl_source);
						$tpl_source = ereg_replace("\{content\}", get_site_preference('custom404'), $tpl_source);
					}
				}


				return true;
			}
			else
			{
				if (get_site_preference('enablecustom404') == "1")
				{
					$tpl_source = get_site_preference('custom404');
					return true;	
				}
				else
				{
					return false;
				}
			}
		}
	}

	function db_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{

		global $gCms;
		$db = $gCms->db;
		$config = $gCms->config;

		if (get_site_preference('enablesitedownmessage') == "1")
		{
			$tpl_timestamp = time();
			return true;
		}
		else
		{
			$query = "SELECT p.page_id, t.modified_date as template_date, p.modified_date as page_date, p.page_type FROM ".cms_db_prefix()."pages p INNER JOIN ".cms_db_prefix()."templates t ON t.template_id = p.template_id WHERE (p.page_id = ".$db->qstr($tpl_name)." OR p.page_alias=".$db->qstr($tpl_name).") AND p.active = 1";
			$result = $db->Execute($query);

			if ($result && $result->RowCount()) {
				$line = $result->FetchRow();

				#This way the id is right, even if an alias is given
				$gCms->variables['page'] = $line['page_id'];

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
				return true;
			}
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

/**
 * Loads all plugins into the system
 *
 * @since 0.5
 */
function load_plugins(&$smarty)
{
	global $gCms;
	$plugins = &$gCms->cmsplugins;
	$userplugins = &$gCms->userplugins;
	$db = &$gCms->db;

	$dir = dirname(dirname(__FILE__))."/plugins";
	$ls = dir($dir);
	while (($file = $ls->read()) != "") {
		if (is_file("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0)) {
			if (preg_match("/^(.*?)\.(.*?)\.php/", $file, $matches)) {
				#$filename = dirname(dirname(__FILE__)) . "/" . $this->_get_plugin_filepath($matches[1], $matches[2]);
				$filename = $smarty->_get_plugin_filepath($matches[1], $matches[2]);
				#echo $filename . "<br />";
				include_once $filename;
				$smarty->register_function($matches[2], "smarty_cms_function_" . $matches[2], $smarty->cache_plugins);
				array_push($plugins, $matches[2]);
			}
		}
	}

	$query = "SELECT * FROM ".cms_db_prefix()."userplugins";
	$result = $db->Execute($query);
	if ($result && $result->RowCount() > 0)
	{
		while ($row = $result->FetchRow())
		{
			if (!in_array($row['userplugin_name'], $plugins))
			{
				array_push($plugins, $row['userplugin_name']);
				$userplugins[$row['userplugin_name']] = $row['userplugin_id'];
				$functionname = "cms_tmp_".$row['userplugin_name']."_userplugin_function";
				//Only register valid code
				if (!(@eval('function '.$functionname.'($params, &$smarty) {'.$row['code'].'}') === FALSE))
				{
					$smarty->register_function($row['userplugin_name'], $functionname, $smarty->cache_plugins);
				}
			}
		}
	}
	sort($plugins);
}

/**
 * Returns the id of the current selected default page
 *
 * @since 0.1
 */
function db_get_default_page () {

	global $gCms;
	
	$db = $gCms->db;
	$config = $gCms->config;

	$result = "";

	$query = "SELECT page_id FROM ".cms_db_prefix()."pages WHERE default_page = 1";
	$dbresult = $db->Execute($query);

	if ($dbresult) {
		$line = $dbresult->FetchRow();
		$result = $line["page_id"];
	}

	#We have no default.  Just get something!!!
	if ($result == "") {
		$query = "SELECT page_id FROM ".cms_db_prefix()."pages";
		$dbresult = $db->SelectLimit($query, 1);

		if ($dbresult) {
			$line = $dbresult->FetchRow();
			$result = $line["page_id"];
		}
	}

	return $result;
}

class MenuItem {
	var $name;
	var $url;
}

class Page {
	var $page_id;
	var $page_title;
	var $page_alias;
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
	var $childs;
		
} ## class

function db_get_menu_items($params = array()) {

	global $sorted_content;

	global $gCms;
	$db = $gCms->db;
	$config = $gCms->config;

	$sorted_content = array();
	$content_array = array();
	
	$query = "select p.*, u.username, t.template_name from ".cms_db_prefix()."pages p LEFT OUTER JOIN ".cms_db_prefix()."users u on u.user_id=p.owner LEFT OUTER JOIN ".cms_db_prefix()."templates t on t.template_id=p.template_id order by p.parent_id, p.item_order";
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {
		$content_array = array();
		while ($line = $result->FetchRow()) {
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
			if (isset($line["url"])) {
				$current_content->url = $line["url"];
			}
			
			# Fix URL where appropriate
			if ($current_content->page_type != "link") {
				if (isset($current_content->page_alias) && $current_content->page_alias != "")
				{
					if ($config["assume_mod_rewrite"])
					{
						$current_content->url = $config["root_url"]."/".$current_content->page_alias.".shtml";
					}
					else
					{
						$current_content->url = $config["root_url"]."/index.php?".$config["query_var"]."=".$current_content->page_alias;
					}
				}
					else
					{
						if ($config["assume_mod_rewrite"])
						{
							$current_content->url = $config["root_url"]."/".$current_content->page_id.".shtml";
						}
						else
						{
							$current_content->url = $config["root_url"]."/index.php?".$config["query_var"]."=".$current_content->page_id;
						}
					}
					$current_content->page_url = "";	
				} else {
					$current_content->url = $current_content->page_url;
				}
				# Special display for separator
				if ($current_content->page_type == "separator") {
					$current_content->page_title = "--------";
				}

				# Now that all treatment have been done to $current_content, we push it in the array
				$content_array[$line["page_id"]] = $current_content;
				$parents[$line["page_id"]] = $line["parent_id"];
			} ## while
		
			construct_tree_from_list($content_array, $parents, $params);
		
			# to change : this should be a parameter of the function
			$start_element = 0;

			$new_array			= array();

			$show				= isset($params['show'])				? $params['show']				: "all" ;
			$start_element		= isset($params['start_element'])		? $params['start_element']		: 0 ;
			$number_of_levels	= isset($params['number_of_levels'])	? $params['number_of_levels']	: 10 ;

			$first_level_childs	= array_keys($parents,$start_element);

			foreach($first_level_childs as $element)
			{
				if (($show == "all") or ($show == "menu" && $content_array[$element]->active && $content_array[$element]->show_in_menu && $number_of_levels > 0))
				{
					array_push($new_array, $content_array[$element]);
				}
			}
			
			flatten_tree_to_list($new_array, $sorted_content);

		} ## if

		return $sorted_content;

} ## function

/**
 * Construct a tree array from a flat array. Returns nothing.
 *
 * This function will take an array of elements, an array of equivalence between elements
 * and parents. It then constructs an array which contains :
 * - element1
 * - element1->child = array contening the childs of element1
 * - etc...
 *
 * It is recursive in the sense it recalls itself.
 * It does not matter what kind of elements it is.
 *
 * @param array		content_array		this is the array you want to construct the tree from.
 * @param array		parents				this is the array containing the associations : $parents[identifier] = parent_identifier of element
 * @param array		params				this is the array containing all the parameters
 *
 * @since 0.5
 */
function construct_tree_from_list(&$content_array, &$parents, $params) {

	# we get all the parameters and define default options
	$start_element		= isset($params['start_element'])		? $params['start_element']			: 0 ;
	$number_of_levels	= isset($params['number_of_levels'])	? $params['number_of_levels']		: 10 ;
	$hierarchy_level	= isset($params['hierarchy_level'])		? $params['hierarchy_level']		: 0 ;
	$total_hierarchy	= isset($params['total_hierarchy'])		? $params['total_hierarchy']		: "" ;
	$show				= isset($params['show'])				? $params['show']					: "all" ;

	if ($number_of_levels > 0)
	{
		# the current element
		$current = &$content_array[$start_element];

		# this array contains the child of our current element
		$childs			= array_keys($parents, $start_element);
		$num_of_childs	= count($childs);

		if ($num_of_childs > 0)
		{
			$current->childs = array();
			$count = 1;

			foreach($childs as $key)
			{
				$newchild					= &$content_array[$key];
				$newchild->num_same_level	= $num_of_childs;
				$newchild->level			= $hierarchy_level + 1;
				$newchild->hier				= $total_hierarchy."$count.";

				if (($show == "menu" && $newchild->active && $newchild->show_in_menu) or ($show == "all")) {
				
					$current->childs[$count - 1] = &$newchild;
					# array_push($current->childs, &$newchild);
					
					$newparams = array(
						"start_element"		=>	$key,
						"number_of_levels"	=>	$number_of_levels - 1,
						"hierarchy_level"	=>	$hierarchy_level + 1,
						"total_hierarchy"	=>	$newchild->hier,
						"show"				=>	$show
					);
					construct_tree_from_list($content_array, $parents, $newparams);

					$count++;
				}
			} #foreach
		} #if numchilds
	} # if number_of_levels
} # function


/**
 * Constructs a flat array from a tree array. Returns nothing
 *
 * @since 0.5
 */
function flatten_tree_to_list($content_array, &$flatarray) {

	if (is_array($content_array) && count($content_array) > 0)
	{
		foreach($content_array as $element)
		{
			array_push($flatarray, $element);
			flatten_tree_to_list($element->childs, $flatarray);
		}
	}
}

/**
 * Returns a list of all currently registered content types
 *
 * @since 0.3
 */
function get_page_types() {

	global $gCms;

	$db = $gCms->db;
	$modules = $gCms->modules;
	$config = $gCms->config;

	$result['content'] = 'Content';
	$result['link'] = 'Link';
	$result['separator'] = 'Separator';

	$installedmodules = array();

	$query = "SELECT * FROM ".cms_db_prefix()."modules";
	$dbresult = $db->Execute($query);

	if ($dbresult && $dbresult->RowCount() > 0) {

		while ($row = $dbresult->FetchRow()) {
			$installedmodules[$row['module_name']] = 1;
		}

		foreach ($modules as $key=>$value) {
			if (isset($modules[$key]['content_module']) && isset($installedmodules[$key])) {
				$result[$key] = $key;
			}
		}

	}

	return $result;

}

# vim:ts=4 sw=4 noet
?>
