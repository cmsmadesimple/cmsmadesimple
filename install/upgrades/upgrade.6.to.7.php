<?php
$gCms = cmsms();

echo "<p>Adding fields to users table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."users", "admin_access I1");
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."users", "first_name C(50)");
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."users", "last_name C(50)");
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."users", "email C(255)");
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("UPDATE ".cms_db_prefix()."users SET admin_access = 1");

echo "[done]</p>";

echo "<p>Adding hierarchy position to pages table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."pages", "hierarchy_position C(255)");
$dbdict->ExecuteSQLArray($sqlarray);

#set_all_pages_hierarchy_position();

echo "[done]</p>";

echo "<p>Creating htmlblobs table...";

$dbdict = NewDataDictionary($db);
$flds = "
	htmlblob_id I,
	htmlblob_name C(255),
	html X,
	owner I,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."htmlblobs", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence($config["db_prefix"]."htmlblobs_seq");

echo "[done]</p>";

echo "<p>Creating additional_htmlblob_users table...";

$dbdict = NewDataDictionary($db);
$flds = "
	additional_htmlblob_users_id I KEY,
	user_id I,
	htmlblob_id I
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."additional_htmlblob_users", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating htmlblob permissions...";

cms_mapi_create_permission($gCms, 'Add Html Blobs', 'Add Html Blobs');
cms_mapi_create_permission($gCms, 'Modify Html Blobs', 'Modify Html Blobs');
cms_mapi_create_permission($gCms, 'Remove Html Blobs', 'Remove Html Blobs');

echo "[done]</p>";

echo "<p>Clearing cache and template directories... ";

function clear_dir_6($dir){

	$path = dirname(dirname(__FILE__))."/tmp/".$dir."/";

	$handle=opendir($path);
	while ($file = readdir($handle)) {
		if ($file != "." && $file != ".." && is_file($path.$file)) {
			#echo ($path.$file);
			unlink($path.$file);
		}
	}
}

clear_dir_6("templates_c");
clear_dir_6("cache");

echo "[done]</p>";

echo "<p>Updating schema version... ";

$query = "UPDATE ".cms_db_prefix()."version SET version = 7";
$db->Execute($query);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
