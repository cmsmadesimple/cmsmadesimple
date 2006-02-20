<?php

echo "<p>Making permission field bigger...";

if ($config["dbms"] == 'postgres7')
{
	$dbdict = NewDataDictionary($db);
	$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."permissions", "tmp C(255)"); 
	$dbdict->ExecuteSQLArray($sqlarray);
	$query = "UPDATE ".cms_db_prefix()."permissions SET tmp = permission_name";
	$db->Execute($query);
	$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."permissions", "permission_name"); 
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."permissions", "permission_name C(255)"); 
	$dbdict->ExecuteSQLArray($sqlarray);
	$query = "UPDATE ".cms_db_prefix()."permissions SET permission_name = tmp";
	$db->Execute($query);
	$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."permissions", "tmp"); 
	$dbdict->ExecuteSQLArray($sqlarray);
}
else
{
	$sqlarray = $dbdict->AlterColumnSQL(cms_db_prefix()."permissions", "permission_name C(255)"); 
	$dbdict->ExecuteSQLArray($sqlarray);
}

echo "[done]</p>";

echo "<p>Make active boolean in modules table...";

$dbdict = NewDataDictionary($db);
if ($config["dbms"] == 'postgres7')
{
	$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."modules", "tmp I1"); 
	$dbdict->ExecuteSQLArray($sqlarray);
	$query = "UPDATE ".cms_db_prefix()."modules SET tmp = 1 WHERE active = true";
	$db->Execute($query);
	$query = "UPDATE ".cms_db_prefix()."modules SET tmp = 0 WHERE active = false";
	$db->Execute($query);
	$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."modules", "active"); 
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."modules", "active I1"); 
	$dbdict->ExecuteSQLArray($sqlarray);
	$query = "UPDATE ".cms_db_prefix()."modules SET active = tmp";
	$db->Execute($query);
	$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."modules", "tmp"); 
	$dbdict->ExecuteSQLArray($sqlarray);
}
else
{
	$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."modules", "active I1"); 
	$dbdict->ExecuteSQLArray($sqlarray);
}

echo "[done]</p>";

echo "<p>Fixing permission names...";

$query = "UPDATE ".cms_db_prefix()."permissions set permission_name = 'Add Global Content Blocks', permission_text = 'Add Global Content Blocks' WHERE permission_name = 'Add Html Blobs'";
$db->Execute($query);
$query = "UPDATE ".cms_db_prefix()."permissions set permission_name = 'Modify Global Content Blocks', permission_text = 'Modify Global Content Blocks' WHERE permission_name = 'Modify Html Blobs'";
$db->Execute($query);
$query = "UPDATE ".cms_db_prefix()."permissions set permission_name = 'Remove Global Content Blocks', permission_text = 'Remove Global Content Blocks' WHERE permission_name = 'Remove Html Blobs'";
$db->Execute($query);
$query = "UPDATE ".cms_db_prefix()."permissions set permission_name = 'Modify User-defined Tags', permission_text = 'Modify User-defined Tags' WHERE permission_name = 'Modify Code Blocks'";
$db->Execute($query);

echo "[done]</p>";

echo '<p>Updating hierarchy positions...';

ContentManager::SetAllHierarchyPositions();

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 18";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
