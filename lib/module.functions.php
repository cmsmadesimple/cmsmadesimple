<?php

function load_modules() {
	global $cmsmodules;
	$dir = dirname(dirname(__FILE__))."/modules";
	$ls = dir($dir);
	while (($file = $ls->read()) != "") {
		if (is_dir("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0)) {
			if (is_file("$dir/$file/cmsmodule.php")) {
				include_once("$dir/$file/cmsmodule.php");
			}
		}
	}
}

function cms_mapi_register_module($name, $author, $version) {
	global $cmsmodules;
	if (!isset($cmsmodules[$name])) {
		$cmsmodules[$name] = array();
		$cmsmodules[$name]['Author'] = $author;
		$cmsmodules[$name]['Version'] = $version;
	}
}

function cms_mapi_register_content_module($name) {
	global $cmsmodules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['content_module'] = true;
	}
}

function cms_mapi_register_plugin_module($name) {
	global $cmsmodules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['plugin_module'] = true;
	}
}

function cms_mapi_register_install_function($name, $function) {
	global $cmsmodules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['install_function'] = $function;
	}
}

function cms_mapi_register_uninstall_function($name, $function) {
	global $cmsmodules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['uninstall_function'] = $function;
	}
}

function cms_mapi_register_execute_function($name, $function) {
	global $cmsmodules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['execute_function'] = $function;
	}
}

function cms_mapi_register_executeuser_function($name, $function) {
	global $cmsmodules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['execute_user_function'] = $function;
	}
}

function cms_mapi_register_executeadmin_function($name, $function) {
	global $cmsmodules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['execute_admin_function'] = $function;
	}
}

function cms_mapi_unregister_module($name) {
	global $cmsmodules;
	if (isset($cmsmodules[$name])) {
		unset($cmsmodules[$name]);
	}
}

function cms_mapi_create_permission($cms, $permission_name, $permission_text) {

	$db = $cms->db;

	$query = "SELECT permission_id FROM ".$cms->config->db_prefix."permissions WHERE permission_name =" . $db->qstr($permission_name); 
	$result = $db->Execute($query);

	if ($result && $result->RowCount() < 1) {

		$new_id = $db->GenID($cms->config->db_prefix."permissions_seq");
		$query = "INSERT INTO ".$cms->config->db_prefix."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES ($new_id, ".$db->qstr($permission_name).",".$db->qstr($permission_text).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
		$db->Execute($query);
	}
}

function cms_mapi_check_permission($cms, $permission_name) {

	$userid = get_userid();
	return check_permission($cms->config, $userid, $permission_name);
}

function cms_mapi_remove_permission($cms, $permission_name) {

	$db = $cms->db;

	$query = "SELECT permission_id FROM ".$cms->config->db_prefix."permissions WHERE permission_name = " . $db->qstr($permission_name); 
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {

		$row = $result->FetchRow();
		$id = $row["permission_id"];

		$query = "DELETE FROM ".$cms->config->db_prefix."group_perms WHERE permission_id = $id";
		$db->Execute($query);

		$query = "DELETE FROM ".$cms->config->db_prefix."permissions WHERE permission_id = $id";
		$db->Execute($query);
	}
}

function cms_mapi_audit($cms, $itemid, $itemname, $action) {

	$userid = get_userid();
	$username = $_SESSION["cms_admin_username"];
	audit($cms->config, $userid, $username, $itemid, $itemname, $action);

}

function cms_mapi_create_user_link($module, $id, $page_id, $params, $text, $warn_message="") {
	$val = "<a href=\"moduleinterface.php?module=$module&return_id=$page_id&id=$id";
	foreach ($params as $key=>$value) {
		$val .= "&$id$key=$value";
	}
	$val .= "\"";
	if ($warn_message !== "") {
		$val .= " onclick=\"return confirm('$warn_message');\"";
	}
	$val .= ">$text</a>";
	return $val;
}

function cms_mapi_create_admin_link($module, $id, $params, $text, $warn_message="") {

	$val = "<a href=\"moduleinterface.php?module=$module";
	foreach ($params as $key=>$value) {
		$val .= "&$id$key=$value";
	}
	$val .= "\"";
	if ($warn_message !== "") {
		$val .= " onclick=\"return confirm('$warn_message');\"";
	}
	$val .= ">$text</a>";
	return $val;

}

function cms_mapi_create_user_form_start($module, $id, $return_id, $method="post") {

	return "<form method=\"$method\" action=\"moduleinterface.php\"><input type=\"hidden\" name=\"module\" value=\"$module\" /><input type=\"hidden\" name=\"return_id\" value=\"$return_id\" /><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";


}

function cms_mapi_create_user_form_end() {

	return "</form>\n";

}

function cms_mapi_create_admin_form_start($module, $id, $method="post") {

	return "<form method=\"$method\" action=\"moduleinterface.php\"><input type=\"hidden\" name=\"module\" value=\"$module\" />\n";


}

function cms_mapi_create_admin_form_end() {

	return "</form>\n";

}

function cms_mapi_redirect_user_by_pageid($page_id) {

	redirect("index.php?page=$page_id");

}

require_once(dirname(dirname(__FILE__)).'/smarty/Smarty.class.php');

class Smarty_ModuleInterface extends Smarty {

	function Smarty_ModuleInterface(&$config)
	{
		$this->Smarty();

		$this->configCMS = &$config;

		$this->template_dir = $config->root_path.'/smarty/cms/templates/';
		$this->compile_dir = $config->root_path.'/smarty/cms/templates_c/';
		$this->config_dir = $config->root_path.'/smarty/cms/configs/';
		$this->cache_dir = $config->root_path.'/smarty/cms/cache/';
		$this->plugins_dir = $config->root_path.'/plugins/';

		$this->compile_check = true;
		$this->caching = false;
		$this->assign('app_name','CMS');
		$this->debugging = false;
		$this->force_compile = true;
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

		$this->register_resource("module", array(&$this, "module_get_template",
						       "module_get_timestamp",
						       "module_get_secure",
						       "module_get_trusted"));
	}

	function module_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{

		global $cmsmodules;
		global $modulecmsobj;

		$db = $smarty_obj->configCMS->db;

		$query = "SELECT UNIX_TIMESTAMP(p.modified_date) as modified_date, t.template_id, t.stylesheet, t.template_content FROM ".$this->configCMS->db_prefix."pages p INNER JOIN ".$this->configCMS->db_prefix."templates t ON p.template_id = t.template_id WHERE p.page_id = '$tpl_name'";
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
			
			#Run the execute_user function and replace {content} with it's output 
			if (isset($cmsmodules[$smarty_obj->module])) {
				@ob_start();
				call_user_func_array($cmsmodules[$smarty_obj->module]['execute_user_function'], array($modulecmsobj,$smarty_obj->id,$tpl_name,$smarty_obj->params));
				$modoutput = @ob_get_contents();
				@ob_end_clean();
				$tpl_source = ereg_replace("\{content\}", $modoutput, $tpl_source);
			}
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
