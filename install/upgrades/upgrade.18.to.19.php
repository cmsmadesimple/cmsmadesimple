<?php

echo "<p>Adding hierarchy_path field to content table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."content", "hierarchy_path X"); 
$dbdict->ExecuteSQLArray($sqlarray);

echo '[done]</p>';

echo '<p>Updating hierarchy positions...';

ContentManager::SetAllHierarchyPositions();

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 19";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
