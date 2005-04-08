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
#
#$Id$

/**
 * Extend smarty for moduleinterface.php
 *
 * @package CMS
 */

require_once(dirname(dirname(__FILE__)).'/lib/smarty/Smarty.class.php');

class Smarty_ModuleInterface extends Smarty {

	function Smarty_ModuleInterface(&$config)
	{
		$this->Smarty();

		global $gCms;
		$config = &$gCms->config;

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

		#Load all CMS plugins as non-cacheable
		$dir = dirname(dirname(__FILE__))."/plugins";
		$ls = dir($dir);
		while (($file = $ls->read()) != "") {
			if (is_file("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0)) {
				if (preg_match("/^(.*?)\.(.*?)\.php/", $file, $matches)) {
					$filename = $this->_get_plugin_filepath($matches[1], $matches[2]);
					if (strpos($filename, 'function') !== false)
					{
						require_once $filename;
						$this->register_function($matches[2], "smarty_cms_function_" . $matches[2], $this->cache_plugins);
					}
					else if (strpos($filename, 'compiler') !== false)
					{
						require_once $filename;
						$this->register_compiler_function($matches[2], "smarty_cms_compiler_" . $matches[2], $this->cache_plugins);
					}
					else if (strpos($filename, 'prefilter') !== false)
					{
						require_once $filename;
						$this->register_prefilter($matches[2], "smarty_cms_prefilter_" . $matches[2]);
					}
					else if (strpos($filename, 'modifier') !== false)
					{
						require_once $filename;
						$this->register_modifier($matches[2], "smarty_cms_modifier_" . $matches[2]);
					}
				}
			}
		}

		$this->register_resource("module", array(&$this, "module_get_template",
						       "module_get_timestamp",
						       "module_get_secure",
						       "module_get_trusted"));
	}

	function module_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{

		global $gCms;
		$config = $gCms->config;
		$db = $gCms->db;
		$cmsmodules = $gCms->modules;

		$query = "SELECT t.template_id, t.stylesheet, t.template_content, p.hierarchy, p.content_id, t.encoding FROM ".cms_db_prefix()."content p INNER JOIN ".cms_db_prefix()."templates t ON p.template_id = t.template_id WHERE p.content_id = ?";
		$result = $db->Execute($query, array($tpl_name));

		if ($result && $result->RowCount())
		{
			$gCms->variables['content_id'] = $tpl_name;
			$gCms->variables['page'] = $tpl_name;
			$gCms->variables['page_id'] = $tpl_name;

			$gCms->variables['page_name'] = $tpl_name;

			$line = $result->FetchRow();

			#Set the title
			$title = $line['title'];

			#Perform the content title callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->ContentTitle($title);
				}
			}

			#Setup the stylesheet inclusion
			$template_id = $line['template_id'];
			$stylesheet = '<link rel="stylesheet" type="text/css" href="stylesheet.php?templateid='.$template_id.'" />';

			if ($smarty_obj->showtemplate == true)
			{
				$tpl_source = $line['template_content'];

				#Perform the content template callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->ContentTemplate($tpl_source);
					}
				}

				#$content = $line['page_content'];

				$gCms->variables['position'] = $line['hierarchy'];

				$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
				$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);

				#So no one can do anything nasty
				if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true)) {
					$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
				}
			}
			
			#Run the execute_user function and replace {content} with it's output 
			if (isset($gCms->modules[$smarty_obj->module]))
			{
				@ob_start();
				#call_user_func_array($gCms->modules[$smarty_obj->module]['execute_admin_function'], array($gCms,"module_".$module."_"));
				$id = $smarty_obj->id;
				$params = @ModuleOperations::GetModuleParameters($id);
				echo $gCms->modules[$smarty_obj->module]['object']->DoActionBase((isset($_REQUEST[$id.'action'])?$_REQUEST[$id.'action']:'default'), $id, $params, $tpl_name);
				$modoutput = @ob_get_contents();
				@ob_end_clean();
				if ($smarty_obj->showtemplate == true)
				{
					#Perform the content data callback
					foreach($gCms->modules as $key=>$value)
					{
						if ($gCms->modules[$key]['installed'] == true &&
							$gCms->modules[$key]['active'] == true)
						{
							$gCms->modules[$key]['object']->ContentData($modoutput);
						}
					}

					$tpl_source = ereg_replace("\{content\}", $modoutput, $tpl_source);

					#In case any lingering tags are coming in from the content
					$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
					$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);
				}
				else
				{
					$tpl_source = $modoutput;
				}
			}

			#Do html_blobs (they're recursive now... but only 15 deep...  deal!)
			$safetycount = 0;
			$regexstr = "|\{html_blob name=[\'\"]?(.*?)[\'\"]?\}|";
			while (1 == 1)
			{
				$result = preg_replace_callback($regexstr, "html_blob_regex_callback", $tpl_source);
				if ($result != '' && $result != $tpl_source)
				{
					$tpl_source = $result;
				}
				else
				{
					break;
				}

				$safetycount++;

				if ($safetycount > 15)
				{
					# Remove the last one so it doesn't get sent to smarty
					$tpl_source = preg_replace($regexstr, '', $tpl_source);
					break;
				}
			}

			#Perform the content prerender callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->ContentPreRender($tpl_source);
				}
			}
			
			header("Content-Type: ".$gCms->variables['content-type']."; charset=" . (isset($line['encoding']) && $line['encoding'] != ''?$line['encoding']:get_encoding()));

			return true;
		}
		else {
			return false;
		}
	}

	function module_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		$tpl_timestamp = time();
		return true;
	}

	function module_get_secure($tpl_name, &$smarty_obj)
	{
		// assume all templates are secure
		return true;
	}

	function module_get_trusted($tpl_name, &$smarty_obj)
	{
		// not used for templates
	}
}

# vim:ts=4 sw=4 noet
?>
