<?php
$gCms = cmsms();

echo "<p>Creating userplugins table...";

$dbdict = NewDataDictionary($db);
$flds = "
	userplugin_id I,
	userplugin_name C(255),
	code X,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($config["db_prefix"]."userplugins", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence($config["db_prefix"]."userplugins_seq");

echo "[done]</p>";

echo "<p>Creating css table...";

$dbdict = NewDataDictionary($db);
$flds = "
	css_id I,
	css_name C(255),
	css_text X,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($config["db_prefix"]."css", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence($config["db_prefix"]."css_seq");

echo "[done]</p>";

echo "<p>Creating css_assoc table...";

$dbdict = NewDataDictionary($db);
$flds = "
	assoc_to_id I,
	assoc_css_id I,
	assoc_type C(80),
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($config["db_prefix"]."css_assoc", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating siteprefs table...";

$dbdict = NewDataDictionary($db);
$flds = "
	sitepref_name C(255),
	sitepref_value text,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($config["db_prefix"]."siteprefs", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("INSERT INTO ".$config["db_prefix"]."siteprefs (sitepref_name, sitepref_value) VALUES (".$db->qstr('enablecustom404').", ".$db->qstr('0').")");
$db->Execute("INSERT INTO ".$config["db_prefix"]."siteprefs (sitepref_name, sitepref_value) VALUES (".$db->qstr('custom404').", ".$db->qstr('<p>Page could not be found.</p>').")");
$db->Execute("INSERT INTO ".$config["db_prefix"]."siteprefs (sitepref_name, sitepref_value) VALUES (".$db->qstr('custom404template').", ".$db->qstr('-1').")");
$db->Execute("INSERT INTO ".$config["db_prefix"]."siteprefs (sitepref_name, sitepref_value) VALUES (".$db->qstr('enablesitedownmessage').", ".$db->qstr('0').")");
$db->Execute("INSERT INTO ".$config["db_prefix"]."siteprefs (sitepref_name, sitepref_value) VALUES (".$db->qstr('sitedownmessage').", ".$db->qstr('<p>Site is currently down for maintenance.</p>').")");
$db->Execute("INSERT INTO ".$config["db_prefix"]."siteprefs (sitepref_name, sitepref_value) VALUES (".$db->qstr('sitedownmessagetemplate').", ".$db->qstr('-1').")");
$db->Execute("INSERT INTO ".$config["db_prefix"]."siteprefs (sitepref_name, sitepref_value) VALUES (".$db->qstr('useadvancedcss').", ".$db->qstr('1').")");

echo "[done]</p>";

echo "<p>Creating modify CSS permission...";

cms_mapi_create_permission($gCms, 'Modify Site Preferences', 'Modify Site Preferences');

echo "[done]</p>";

echo "<p>Creating modify CSS permission...";

cms_mapi_create_permission($gCms, 'Modify CSS', 'Modify CSS');
cms_mapi_create_permission($gCms, 'Add CSS', 'Add CSS');
cms_mapi_create_permission($gCms, 'Remove CSS', 'Remove CSS');
cms_mapi_create_permission($gCms, 'Add CSS association', 'Add CSS association');
cms_mapi_create_permission($gCms, 'Edit CSS association', 'Edit CSS association');
cms_mapi_create_permission($gCms, 'Remove CSS association', 'Remove CSS association');

echo "[done]</p>";

echo "<p>Adding head_tags to pages table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."pages", "head_tags X");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating modify code blocks permission...";

cms_mapi_create_permission($gCms, 'Modify Code Blocks', 'Modify Code Blocks');

echo "[done]</p>";

echo "<p>Creating clear admin log permission...";

cms_mapi_create_permission($gCms, 'Clear Admin Log', 'Clear Admin Log');

echo "[done]</p>";

echo "<p>Clearing cache and template directories... ";

function clear_dir_5($dir){

	$path = dirname(dirname(__FILE__))."/smarty/cms/".$dir."/";

	$handle=opendir($path);
	while ($file = readdir($handle)) {
		if ($file != "." && $file != ".." && is_file($path.$file)) {
			#echo ($path.$file);
			unlink($path.$file);
		}
	}
}

clear_dir_5("templates_c");
clear_dir_5("cache");

echo "[done]</p>";

#echo "<p>Deleting stylesheet column... ";
#$dbdict = NewDataDictionary($db);
#$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."templates", "stylesheet");
#$dbdict->ExecuteSQLArray($sqlarray);
#echo "[done]</p>";

echo "<p>Updating schema version... ";

$query = "UPDATE ".cms_db_prefix()."version SET version = 6";
$db->Execute($query);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
