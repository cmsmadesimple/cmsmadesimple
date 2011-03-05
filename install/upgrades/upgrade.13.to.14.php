<?php

echo "<p>Adding fields to content table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AlterColumnSQL(cms_db_prefix()."templates", "template_name C(160)"); 
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Setting all content to collapsed...";

$query = "UPDATE ".cms_db_prefix()."content SET collapsed = 1";
$db->Execute($query);

echo "[done]</p>";

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 14";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
