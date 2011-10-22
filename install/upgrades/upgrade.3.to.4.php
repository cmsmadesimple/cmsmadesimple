<?php

echo "<p>Creating sequences...";


function upgrade_create_sequence_table($db, $tablename, $idcol) {

	$num = $db->GetOne("SELECT MAX($idcol) FROM $tablename");
	$db->DropSequence($tablename."_seq");
	$db->CreateSequence($tablename."_seq", $num + 1);
	$dict = NewDataDictionary($db);
	$sqlarray = $dict->AlterColumnSQL($tablename, "$idcol I"); 
	$dict->ExecuteSQLArray($sqlarray);

}

upgrade_create_sequence_table($db, $config["db_prefix"]."additional_users", "additional_users_id");
upgrade_create_sequence_table($db, $config["db_prefix"]."group_perms", "group_perm_id");
upgrade_create_sequence_table($db, $config["db_prefix"]."groups", "group_id");
upgrade_create_sequence_table($db, $config["db_prefix"]."pages", "page_id");
upgrade_create_sequence_table($db, $config["db_prefix"]."permissions", "permission_id");
upgrade_create_sequence_table($db, $config["db_prefix"]."templates", "template_id");
upgrade_create_sequence_table($db, $config["db_prefix"]."users", "user_id");

echo "[done]</p>";

echo "<p>Creating modules table...";

$dbdict = NewDataDictionary($db);
$flds = "
	module_name C(160),
	status C(255),
	version C(255),
	active L
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($config["db_prefix"]."modules", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Adding parent_id to pages table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL($config["db_prefix"]."pages", "parent_id I NOTNULL DEFAULT 0");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Removing section_id from pages table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->DropColumnSQL($config["db_prefix"]."pages", "section_id");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Removing sections table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->DropTableSQL($config["db_prefix"]."sections");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Adding module admin permission... ";

$new_id = $db->GenID($config["db_prefix"]."permissions_seq");
$query = "INSERT INTO ".$config["db_prefix"]."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES ($new_id,'Modify Modules','Modify Modules',".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
$db->Execute($query);

echo "[done]</p>";

echo "<p>Clearing cache and template directories... ";

function clear_dir_3($dir){

	$path = dirname(dirname(__FILE__))."/smarty/cms/".$dir."/";

	$handle=opendir($path);
	while ($file = readdir($handle)) {
		if ($file != "." && $file != ".." && is_file($path.$file)) {
			#echo ($path.$file);
			unlink($path.$file);
		}
	}
}

clear_dir_3("templates_c");
clear_dir_3("cache");

echo "[done]</p>";

echo "<p>Updating schema version... ";

$query = "UPDATE ".$config["db_prefix"]."version SET version = 4";
$db->Execute($query);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
