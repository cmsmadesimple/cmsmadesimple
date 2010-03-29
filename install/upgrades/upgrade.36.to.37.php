<?php
global $gCms;
$dbdict = NewDataDictionary($db);

echo '<p>Adding cache table';

$dbdict = NewDataDictionary($db);

$flds = "
	k		C(255) UNIQUE KEY,
	v		XL,
	expiry		T,
	create_date	T,
	modified_date	T
        ";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."cache", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->CreateIndexSQL('index_k', cms_db_prefix()."cache", 'k');
$return = $dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->CreateIndexSQL('index_expiry', cms_db_prefix()."cache", 'expiry');
$return = $dbdict->ExecuteSQLArray($sqlarray);

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 37";
$db->Execute($query);

echo '[done]</p>';

?>
