<?php

echo "<p>Making permission field bigger...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AlterColumnSQL(cms_db_prefix()."permissions", "permission_name C(255)"); 
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Make active boolean in modules table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AlterColumnSQL(cms_db_prefix()."modules", "active I1"); 
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Fixing permission names...";

$query = "UPDATE ".cms_db_prefix()."permissions set permission_name = 'Add Global Content Blocks', permission_text = 'Add Global Content Blocks' WHERE permission_name = 'Add Html Blobs'";
$db->Execute($query);
$query = "UPDATE ".cms_db_prefix()."permissions set permission_name = 'Modify Global Content Blocks', permission_text = 'Modify Global Content Blocks' WHERE permission_name = 'Modify Html Blobs'";
$db->Execute($query);
$query = "UPDATE ".cms_db_prefix()."permissions set permission_name = 'Delete Global Content Blocks', permission_text = 'Delete Global Content Blocks' WHERE permission_name = 'Delete Html Blobs'";
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
