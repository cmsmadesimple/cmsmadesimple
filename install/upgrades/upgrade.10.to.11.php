<?php

echo "<p>Adding fields to css table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."css", "media_type C(255)");
$dbdict->ExecuteSQLArray($sqlarray);

$query = "UPDATE ".cms_db_prefix()."css SET media_type = '' WHERE media_type IS NULL";
$db->Execute($query);

echo "[done]</p>";

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 11";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
