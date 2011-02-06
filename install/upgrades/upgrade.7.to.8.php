<?php

echo "<p>Adding fields to pages table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."pages", "page_header X");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Adding fields to templates table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."templates", "encoding C(25)");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Clearing cache and template directories... ";

function clear_dir_7($dir){

	$path = dirname(dirname(__FILE__))."/tmp/".$dir."/";

	$handle=opendir($path);
	while ($file = readdir($handle)) {
		if ($file != "." && $file != ".." && is_file($path.$file)) {
			#echo ($path.$file);
			unlink($path.$file);
		}
	}
}

clear_dir_7("templates_c");
clear_dir_7("cache");

echo "[done]</p>";

echo "<p>Updating schema version... ";

$query = "UPDATE ".cms_db_prefix()."version SET version = 8";
$db->Execute($query);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
