<?php

class cmsmodule {

	function install() {
		//This function should install the database functions and do other basic init stuff for first time use.
	}

	function uninstall() {
		//This function should uninstall database tables and generally cleanup.
	}

	function execute($cms, $id) {
		//This is the entryway into the module.  All requests from CMS will come through here.
		die("cmsmodule::execute() not implemented!");
	}

	function executeadmin($cms,$id) {
		//This is the entryway for the admin section of the module.  It's optional.
	}

	function upgrade($cms, $oldversion, $newversion) {
		//This function will be run for upgrading
	}


}

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

function create_permission($cms, $permission_name, $permission_text) {

	$db = $cms->db;

	$new_id = $db->GenID($cms->config->db_prefix."permissions_seq");
	$query = "INSERT INTO ".$cms->config->db_prefix."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES ($new_id, ".$db->qstr($permission_name).",".$db->qstr($permission_text).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
	$db->Execute($query);
}

function remove_permission($cms, $permission_name) {

	$db = $cms->db;

	$query = "DELETE FROM ".$cms->config->db_prefix."permissions WHERE permission_name = " . $db->qstr($permission_name);
	$db->Execute($query);
}

function create_module_admin_link($module, $id, $params, $text, $warn_message="") {

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

function create_module_admin_start_form($module, $id, $method="post") {

	return "<form method=\"$method\" action=\"moduleinterface.php\"><input type=\"hidden\" name=\"module\" value=\"$module\" />\n";


}

function create_module_admin_end_form() {

	return "</form>\n";

}

?>
