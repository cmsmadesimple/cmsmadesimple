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

echo '<p>Add fields to group_perms';

$dbdict = NewDataDictionary($db);

$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'group_perms','object_id I NOTNULL DEFAULT 0');
$dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'group_perms','has_access I(1) NOTNULL DEFAULT 1');
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("CREATE INDEX ".cms_db_prefix()."index_group_perms_object ON ".cms_db_prefix().
	"group_perms (group_id, permissions_id, object_id)");

echo '<p>Add fields to permissions';

$dbdict = NewDataDictionary($db);

$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'permissions','module C(100) NOTNULL DEFAULT "Core"');
$dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'permissions','extra_attr C(50) NOTNULL DEFAULT ""');
$dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'permissions','hierarchical I(1) NOTNULL DEFAULT 0');
$dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'permissions','link_table C(50) NOTNULL DEFAULT ""');
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("CREATE INDEX ".cms_db_prefix()."index_perms_module ON ".cms_db_prefix().
	"permissions ('permission_name', 'module', 'extra_attr')");

echo '<p>Add Primary Key to user_groups';

$db->Execute("ALTER TABLE ".cms_db_prefix()."user_groups ADD PRIMARY KEY ('group_id', 'user_id') ");

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 37";
$db->Execute($query);

echo '[done]</p>';

?>
