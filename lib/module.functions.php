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

function cms_mapi_register_module($name) {
	global $cmsmodules;
	if (!isset($cmsmodules[$name])) {
		$cmsmodules[$name] = array();
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

	$new_id = $db->GenID($cms->config->db_prefix."permissions_seq");
	$query = "INSERT INTO ".$cms->config->db_prefix."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES ($new_id, ".$db->qstr($permission_name).",".$db->qstr($permission_text).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
	$db->Execute($query);
}

function cms_mapi_remove_permission($cms, $permission_name) {

	$db = $cms->db;

	$query = "DELETE FROM ".$cms->config->db_prefix."permissions WHERE permission_name = " . $db->qstr($permission_name);
	$db->Execute($query);
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

function cms_mapi_create_admin_form_start($module, $id, $method="post") {

	return "<form method=\"$method\" action=\"moduleinterface.php\"><input type=\"hidden\" name=\"module\" value=\"$module\" />\n";


}

function cms_mapi_create_admin_form_end() {

	return "</form>\n";

}

?>
