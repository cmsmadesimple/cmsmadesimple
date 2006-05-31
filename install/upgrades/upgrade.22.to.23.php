<?php

echo "<p>Adding fields to modules table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."modules", "admin_only I1"); 
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute('UPDATE '.cms_db_prefix().'modules SET admin_only = 0');

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 23";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>