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

require_once dirname(__FILE__).'/smarty/Smarty.class.php';

class Smarty_Preview extends Smarty {

	function Smarty_Preview(&$config)
	{
		$this->Smarty();

		global $gCms;
		$this->configCMS = &$gCms->config;

		$this->template_dir = $config["root_path"].'/tmp/templates/';
		$this->compile_dir = $config["root_path"].'/tmp/templates_c/';
		$this->config_dir = $config["root_path"].'/tmp/configs/';
		$this->cache_dir = $config["root_path"].'/tmp/cache/';
		$this->plugins_dir = array($config["root_path"].'/lib/smarty/plugins/',$config["root_path"].'/plugins/');

		$this->compile_check = true;
		$this->caching = false;
		$this->assign('app_name','CMS');
		$this->debugging = false;
		$this->force_compile = true;
		$this->cache_plugins = false;

		load_plugins($this);

		$this->register_resource("preview", array(&$this, "preview_get_template",
						       "preview_get_timestamp",
						       "preview_get_secure",
						       "preview_get_trusted"));
	}

	function preview_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		global $gCms;
		$config = $gCms->config;

		$fname = $config["previews_path"] . "/" . $tpl_name;
		$handle = fopen($fname, "r");
		$data = unserialize(fread($handle, filesize($fname)));
		fclose($handle);
		unlink($fname);

		$tpl_source = $data["template"];

		#Perform the content template callback
		foreach($gCms->modules as $key=>$value)
		{
			if (isset($gCms->modules[$key]['content_template_function']) &&
				$gCms->modules[$key]['Installed'] == true &&
				$gCms->modules[$key]['Active'] == true)
			{
				call_user_func_array($gCms->modules[$key]['content_template_function'], array(&$gCms, &$tpl_source));
			}
		}

		$gCms->variables['page'] = $data['content_id'];
		$gCms->variables['page_id'] = $data['content_id'];
		$gCms->variables['content_id'] = $data['content_id'];
		$gCms->variables['page_name'] = $data['title'];
		$gCms->variables['position'] = $data['hierarchy'];

		header("Content-Type: text/html; charset=" . (isset($data['encoding']) && $data['encoding'] != ''?$data['encoding']:get_encoding()));

		$stylesheet = "";
		if (isset($data["stylesheet"]))
		{
			$stylesheet .= "<style type=\"text/css\">\n";
			$stylesheet .= "{literal}".$data["stylesheet"]."{/literal}";
			$stylesheet .= "</style>\n";
		}

		$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
		$tpl_source = ereg_replace("\{content\}", $data["content"], $tpl_source);

		$title = $data['title'];

		#Perform the content title callback
		foreach($gCms->modules as $key=>$value)
		{
			if (isset($gCms->modules[$key]['content_title_function']) &&
				$gCms->modules[$key]['Installed'] == true &&
				$gCms->modules[$key]['Active'] == true)
			{
				call_user_func_array($gCms->modules[$key]['content_title_function'], array(&$gCms, &$title));
			}
		}

		$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);

		#Do html_blobs
		$tpl_source = preg_replace_callback("|\{html_blob name=[\'\"]?(.*?)[\'\"]?\}|", "html_blob_regex_callback", $tpl_source);

		#Perform the content prerender callback
		foreach($gCms->modules as $key=>$value)
		{
			if (isset($gCms->modules[$key]['content_prerender_function']) &&
				$gCms->modules[$key]['Installed'] == true &&
				$gCms->modules[$key]['Active'] == true)
			{
				call_user_func_array($gCms->modules[$key]['content_prerender_function'], array(&$gCms, &$tpl_source));
			}
		}

		return true;
	}

	function preview_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		$tpl_timestamp = time();
		return true;
	}

	function preview_get_secure($tpl_name, &$smarty_obj)
	{
		// assume all templates are secure
		return true;
	}

	function preview_get_trusted($tpl_name, &$smarty_obj)
	{
		// not used for templates
	}
}

# vim:ts=4 sw=4 noet
?>
