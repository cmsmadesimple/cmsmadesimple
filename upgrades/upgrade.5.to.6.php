<?php

echo "<p>Creating codeblocks table...";

$dbdict = NewDataDictionary($db);
$flds = "
	codeblock_id I,
	codeblock_name C(255),
	codeblock_data X,
	active I1
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($config["db_prefix"]."codeblocks", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence($config["db_prefix"]."codeblocks_seq");

echo "[done]</p>";

echo "<p>Adding head_tags to pages table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."pages", "head_tags X");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating modify code blocks permission...";

cms_mapi_create_permission($gCms, 'Modify Code Blocks', 'Modify Code Blocks');

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

echo "<p>Updating schema version... ";

$query = "UPDATE ".cms_db_prefix()."version SET version = 6";
$db->Execute($query);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
