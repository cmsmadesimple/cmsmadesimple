<?php

/**
 * Module related functions
 *
 * @package CMS
 */

/**
 * Function to load all modules.
 *
 * This function loads all modules and then figures out which modules are installed
 * and active.
 *
 * @since 0.4
 */
function load_modules() {
	global $gCms;
	$db = $gCms->db;
	$cmsmodules = &$gCms->modules;

	$dir = dirname(dirname(__FILE__))."/modules";
	$ls = dir($dir);
	while (($file = $ls->read()) != "") {
		if (is_dir("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0)) {
			if (is_file("$dir/$file/cmsmodule.php")) {
				include_once("$dir/$file/cmsmodule.php");
			}
		}
	}

	#Figger out what modules are active and/or installed
	if (isset($db))
	{
		$query = "SELECT * FROM ".cms_db_prefix()."modules";
		$result = $db->Execute($query);
		while ($row = $result->FetchRow())
		{
			if (isset($row['module_name']))
			{
				$modulename = $row['module_name'];
				if (isset($modulename) && isset($cmsmodules[$modulename]))
				{
					$cmsmodules[$modulename]['Installed'] = true;
					$cmsmodules[$modulename]['Active'] = $row['active'];
				}
			}
		}
	}
}

/**
 * Registers a module with the system.  This needs to be done
 * before any module functions can be registered.
 *
 * @since 0.4
 */
function cms_mapi_register_module($name, $author, $version) {
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (!isset($cmsmodules[$name])) {
		$cmsmodules[$name] = array();
		$cmsmodules[$name]['Author'] = $author;
		$cmsmodules[$name]['Version'] = $version;
		$cmsmodules[$name]['Installed'] = false; 
		$cmsmodules[$name]['Active'] = false; 
		$cmsmodules[$name]['enable_wysiwyg'] = false;
	}
}

/**
 * Registers a module as a content module.
 *
 * Tells CMS that this module will be used as a content
 * module.  This would mean that it would show up in the
 * content type list and should (though it's not required)
 * to have an executeuser function.
 *
 * @since 0.4
 */
function cms_mapi_register_content_module($name) {
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['content_module'] = true;
	}
}

/**
 * Registers the module to be used a as a plugin.
 *
 * Tells CMS that this module should be able to be
 * used in a plugin.  It will now be able to be used in
 * templates and content as {cms_module module="name"}.
 *
 * @since 0.4
 */
function cms_mapi_register_plugin_module($name) {
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['plugin_module'] = true;
	}
}

/**
 * Registers the module's help function
 *
 * This function should echo out help html.  It should show
 * basics of what the module if for and how to use it.  This
 * will be displayed on the module list in the admin.
 *
 * @since 0.5
 */
function cms_mapi_register_help_function($name, $function) {
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['help_function'] = $function;
	}
}

/**
 * Registers the module's install function
 *
 * The registered function should setup any necessary tables,
 * sequences, etc.
 *
 * @since 0.4
 */
function cms_mapi_register_install_function($name, $function) {
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['install_function'] = $function;
	}
}

/**
 * Registers the module's uninstall function
 *
 * The registered function should clean up anything that was setup
 * in the install function.  This includes any tables, sequences, etc.
 *
 * @since 0.4
 */
function cms_mapi_register_uninstall_function($name, $function) {
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['uninstall_function'] = $function;
	}
}

/**
 * Registers the module's execute function
 *
 * The registered function is what is shown if either the
 * it is included as a content module or if has been used as a
 * plugin.
 *
 * @since 0.4
 */
function cms_mapi_register_execute_function($name, $function) {
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['execute_function'] = $function;
	}
}

/**
 * Registers the module's executeuser function
 *
 * The registered function is what is shown if the module
 * needs a page that is not really a content page.  i.e. a full
 * page form for input.
 *
 * @since 0.4
 */
function cms_mapi_register_executeuser_function($name, $function) {
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['execute_user_function'] = $function;
	}
}

/**
 * Register's the modules' executeadmin function
 *
 * This is the function that is called from the admin section.
 *
 * @since 0.4
 */
function cms_mapi_register_executeadmin_function($name, $function) {
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (isset($cmsmodules[$name])) {
		$cmsmodules[$name]['execute_admin_function'] = $function;
	}
}

/**
 * Unregisters the module and it's functions.  Not usually needed.
 *
 * @since 0.4
 */
function cms_mapi_unregister_module($name) {
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (isset($cmsmodules[$name])) {
		unset($cmsmodules[$name]);
	}
}

function cms_mapi_enable_wysiwyg($name)
{
	global $gCms;
	$cmsmodules = &$gCms->modules;
	if (isset($cmsmodules[$name]))
	{
		$cmsmodules[$name]['enable_wysiwyg'] = true;
	}
}

/**
 * Create's a new permission for use by the module.
 *
 * @since 0.4
 */
function cms_mapi_create_permission($cms, $permission_name, $permission_text) {

	$db = $cms->db;

	$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name =" . $db->qstr($permission_name); 
	$result = $db->Execute($query);

	if ($result && $result->RowCount() < 1) {

		$new_id = $db->GenID(cms_db_prefix()."permissions_seq");
		$query = "INSERT INTO ".cms_db_prefix()."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES ($new_id, ".$db->qstr($permission_name).",".$db->qstr($permission_text).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
		$db->Execute($query);
	}
}

/**
 * Checks a permission against the currently logged in user.
 *
 * @since 0.4
 */
function cms_mapi_check_permission($cms, $permission_name) {

	$userid = get_userid();
	return check_permission($userid, $permission_name);
}

/** 
 * Removes a permission from the system.  If recreated, the
 * permission would have to be set to all groups again.
 *
 * @since 0.4
 */
function cms_mapi_remove_permission($cms, $permission_name) {

	$db = $cms->db;

	$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name = " . $db->qstr($permission_name); 
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {

		$row = $result->FetchRow();
		$id = $row["permission_id"];

		$query = "DELETE FROM ".cms_db_prefix()."group_perms WHERE permission_id = $id";
		$db->Execute($query);

		$query = "DELETE FROM ".cms_db_prefix()."permissions WHERE permission_id = $id";
		$db->Execute($query);
	}
}

/**
 * Put an event into the audit (admin) log.  This should be
 * done on most admin events for consistency.
 *
 * @since 0.4
 */
function cms_mapi_audit($cms, $itemid, $itemname, $action) {

	$userid = get_userid();
	$username = $_SESSION["cms_admin_username"];
	audit($userid, $username, $itemid, $itemname, $action);

}

/**
 * Creates a link to call an executeuser function.
 *
 * @since 0.4
 */
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

/**
 * Creates a link to call an executeadmin function.
 *
 * @since 0.4
 */
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

/**
 * Creates a new user form.  This should be used in the executeuser function to
 * start any forms.  This way, necessary variables are set without any work by
 * the module writer.
 *
 * @since 0.4
 */
function cms_mapi_create_user_form_start($module, $id, $return_id, $method="post") {

	return "<form name=\"".$id."_moduleform\" method=\"$method\" action=\"moduleinterface.php\"><input type=\"hidden\" name=\"module\" value=\"$module\" /><input type=\"hidden\" name=\"return_id\" value=\"$return_id\" /><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";


}

/**
 * Closes a user form.  Doesn't do much now, but could be user for more
 * in the future.
 *
 * @since 0.4
 */
function cms_mapi_create_user_form_end() {

	return "</form>\n";

}

/**
 * Creates a new admin form.  This should be used in the executemadin function to
 * start any forms.  This way, necessary variables are set without any work by
 * the module writer.
 *
 * @since 0.4
 */
function cms_mapi_create_admin_form_start($module, $id, $method="post") {

	return "<form name=\"".$id."moduleform\" method=\"$method\" action=\"moduleinterface.php\"><input type=\"hidden\" name=\"module\" value=\"$module\" />\n";


}

/**
 * Closes an admin form.  Doesn't do much now, but could be user for more
 * in the future.
 *
 * @since 0.4
 */
function cms_mapi_create_admin_form_end() {

	return "</form>\n";

}

/**
 * Used to redirect back to a content page from an executeuser function
 *
 * @since 0.4
 */
function cms_mapi_redirect_user_by_pageid($page_id) {

	redirect("index.php?page=$page_id");

}

/**
 * Used to create a link back to a content page from an executeuser function
 *
 * @since 0.5
 */
function cms_mapi_create_content_link_by_page_id($page_id, $link_text) {
	global $gCms;
	$config = &$gCms->config;
	return "<a href=\"".$config["root_url"]."/index.php?page=$page_id\">$link_text</a>\n";
}

require_once(dirname(dirname(__FILE__)).'/smarty/Smarty.class.php');

class Smarty_ModuleInterface extends Smarty {

	function Smarty_ModuleInterface(&$config)
	{
		$this->Smarty();

		global $gCms;
		$config = &$gCms->config;

		$this->template_dir = $config["root_path"].'/smarty/cms/templates/';
		$this->compile_dir = $config["root_path"].'/smarty/cms/templates_c/';
		$this->config_dir = $config["root_path"].'/smarty/cms/configs/';
		$this->cache_dir = $config["root_path"].'/smarty/cms/cache/';
		$this->plugins_dir = $config["root_path"].'/plugins/';

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

		global $gCms;
		$config = $gCms->config;
		$db = $gCms->db;
		$cmsmodules = $gCms->modules;

		$query = "SELECT UNIX_TIMESTAMP(p.modified_date) as modified_date, t.template_id, t.stylesheet, t.template_content FROM ".cms_db_prefix()."pages p INNER JOIN ".cms_db_prefix()."templates t ON p.template_id = t.template_id WHERE p.page_id = '$tpl_name'";
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
			if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true)) {
				$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
			}
			
			#Run the execute_user function and replace {content} with it's output 
			if (isset($cmsmodules[$smarty_obj->module])
				&& isset($cmsmodules[$smarty_obj->module]['execute_user_function'])
				&& $cmsmodules[$smarty_obj->module]['Installed'] == true
				&& $cmsmodules[$smarty_obj->module]['Active'] == true)
			{
				@ob_start();
				call_user_func_array($cmsmodules[$smarty_obj->module]['execute_user_function'], array($gCms,$smarty_obj->id,$tpl_name,$smarty_obj->params));
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
