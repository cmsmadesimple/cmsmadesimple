<?php

echo "<p>Adding fields to content table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."content", "id_hierarchy C(255)"); 
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."content", "prop_names X"); 
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo '<p>Updating hierarchy positions...';

ContentManager::SetAllHierarchyPositions();

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 15";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
