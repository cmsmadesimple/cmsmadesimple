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

require_once('Smarty.class.php');

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
		$this->force_compile = false;

		$this->register_resource("db", array(&$this, "db_get_template",
						       "db_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
	}

	function db_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		$db = new DB($this->configCMS);

		$query = "SELECT UNIX_TIMESTAMP(p.modified_date) as modified_date, p.page_content, t.template_id, t.stylesheet, t.template_content FROM ".$this->configCMS->db_prefix."pages p INNER JOIN ".$this->configCMS->db_prefix."templates t ON p.template_id = t.template_id WHERE p.page_url = '$tpl_name' AND p.active = 1";
		$result = $db->query($query);

		if (mysql_num_rows($result) > 0) {
			$line = mysql_fetch_array($result, MYSQL_ASSOC);

			$smarty_obj->assign('modified_date',$line[modified_date]);
			#$smarty_obj->assign('stylesheet',$line[stylesheet]);
			$stylesheet = "";
			if (isset($line[stylesheet])) {
				#$csslink = $this->configCMS->root_url."/stylesheet.php?templateid=".$line[template_id];
				#$stylesheet .= "<link rel=\"stylesheet\" href=\"".$csslink."\" type=\"text/css\" />\n";
				$stylesheet .= "<style type=\"text/css\">\n";
				#$stylesheet .= "<!--\n";
				#$stylesheet .= "	@import \"".$csslink."\";\n";
				$stylesheet .= "{literal}".$line["stylesheet"]."{/literal}";
				#$stylesheet .= "-->\n";
				$stylesheet .= "</style>\n";
			}
			$tpl_source = $line[template_content];
			$content = $line[page_content];
			$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
			$tpl_source = ereg_replace("\{content\}", $content, $tpl_source);

			if ($this->configCMS->use_bb_code == true) {
				$tpl_source = $this->configCMS->bbcodeparser->qparse($tpl_source);
			}

			mysql_free_result($result);
			$db->close();
			return true;
		}
		else {
			mysql_free_result($result);
			$db->close();
			return false;
		}
        }

        function db_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
        {
                $db = new DB($this->configCMS);

                $query = "SELECT UNIX_TIMESTAMP(IF(t.modified_date>p.modified_date,t.modified_date,p.modified_date)) as create_date FROM ".$this->configCMS->db_prefix."pages p INNER JOIN ".$this->configCMS->db_prefix."templates t ON t.template_id = p.template_id WHERE p.page_url = '$tpl_name' AND p.active = 1";
                $result = $db->query($query);

                if (mysql_num_rows($result) > 0) {
                        $line = mysql_fetch_array($result, MYSQL_ASSOC);

                        $tpl_timestamp = $line["create_date"];

			mysql_free_result($result);
                        $db->close();
                        return true;
                }
                else {
			mysql_free_result($result);
                        $db->close();
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

	$result = "";
	$db = new DB($config);

	$query = "SELECT page_url FROM ".$config->db_prefix."pages WHERE default_page = 1";
	$dbresult = $db->query($query);

	if (mysql_num_rows($dbresult) > 0) {
		$line = mysql_fetch_array($dbresult, MYSQL_ASSOC);
		$result = $line["page_url"];
	}

	mysql_free_result($dbresult);
	$db->close();

	return $result;
}

class Section {
	var $section_name;
	var $items;
}

class MenuItem {
	var $name;
	var $url;
}

function db_get_menu_items(&$config) {

	$sections = array();
	$current_section;

	$db = new DB($config);

	$query = "SELECT p.*, s.section_name FROM ".$config->db_prefix."pages p INNER JOIN ".$config->db_prefix."sections s ON s.section_id = p.section_id WHERE p.show_in_menu = 1 AND p.active = 1 ORDER BY s.item_order, p.item_order, p.menu_text";
	$result = $db->query($query);

	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {

		if (!isset($current_section) || $line["section_name"] != $current_section->name) {

			if (isset($current_section)) {
				array_push($sections, $current_section);
			}
			$current_section = new Section;
			$current_section->name = $line["section_name"];
			$current_section->items = array();
		}

		$menu_item = new MenuItem;
		$menu_item->name = $line["menu_text"];
		if (isset($config->query_var) && $config->query_var != "") {
			$menu_item->url = $config->root_url."/index.php?".$config->query_var."=".$line["page_url"];
		}
		else {
			$menu_item->url = $config->root_url."/index.php/".$line["page_url"];
		}
		array_push($current_section->items, $menu_item);
	}

	if (isset($current_section)) {
		array_push($sections, $current_section);
	}
	
	mysql_free_result($result);
	$db->close();

	return $sections;
}

# vim:ts=4 sw=4 noet
?>
