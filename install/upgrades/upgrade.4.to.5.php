<?php
$gCms = cmsms();

echo "<p>Creating modify files permission...";

cms_mapi_create_permission($gCms, 'Modify Files', 'Modify Files');

echo "[done]</p>";

echo "<p>Adding page_alias to pages table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."pages", "page_alias C(255)");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Clearing cache and template directories... ";

function clear_dir_4($dir){

	$path = dirname(dirname(__FILE__))."/smarty/cms/".$dir."/";

	$handle=opendir($path);
	while ($file = readdir($handle)) {
		if ($file != "." && $file != ".." && is_file($path.$file)) {
			#echo ($path.$file);
			unlink($path.$file);
		}
	}
}

clear_dir_4("templates_c");
clear_dir_4("cache");

echo "[done]</p>";

echo "<p>Updating schema version... ";

$query = "UPDATE ".cms_db_prefix()."version SET version = 5";
$db->Execute($query);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
