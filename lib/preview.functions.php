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

class Smarty_Preview extends Smarty {

	function Smarty_Preview(&$config)
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

		$this->register_resource("preview", array(&$this, "preview_get_template",
						       "preview_get_timestamp",
						       "preview_get_secure",
						       "preview_get_trusted"));
	}

	function preview_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		$fname = $smarty_obj->configCMS->root_path . "/smarty/cms/cache/cmspreview" . $tpl_name;
		$handle = fopen($fname, "r");
		$data = unserialize(fread($handle, filesize($fname)));
		fclose($handle);
		unlink($fname);
		$tpl_source = $data["template"];
		$stylesheet = "";
		if (isset($data["stylesheet"])) {
			#$csslink = $this->configCMS->root_url."/stylesheet.php?templateid=".$data["template_id"];
			#$stylesheet .= "<link rel=\"stylesheet\" href=\"".$csslink."\" type=\"text/css\" />\n";
			$stylesheet .= "<style type=\"text/css\">\n";
			#$stylesheet .= "<!--\n";
			#$stylesheet .= "	@import \"".$csslink."\";\n";
			$stylesheet .= "{literal}".$data["stylesheet"]."{/literal}";
			#$stylesheet .= "-->\n";
			$stylesheet .= "</style>\n";
		}
		$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
		$tpl_source = ereg_replace("\{content\}", $data["content"], $tpl_source);

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
