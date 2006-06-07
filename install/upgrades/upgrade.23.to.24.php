<?php

echo "<p>Making user preferences field bigger...";

$dbdict = NewDataDictionary($db);

if ($config["dbms"] == 'postgres7')
{
	$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."userprefs", "tmp X"); 
	$dbdict->ExecuteSQLArray($sqlarray);
	$query = "UPDATE ".cms_db_prefix()."userprefs SET tmp = value";
	$db->Execute($query);
	$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."userprefs", "value"); 
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."userprefs", "value X"); 
	$dbdict->ExecuteSQLArray($sqlarray);
	$query = "UPDATE ".cms_db_prefix()."userprefs SET value = tmp";
	$db->Execute($query);
	$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."userprefs", "tmp"); 
	$dbdict->ExecuteSQLArray($sqlarray);
}
else
{
	$sqlarray = $dbdict->AlterColumnSQL(cms_db_prefix()."userprefs", "value X"); 
	$dbdict->ExecuteSQLArray($sqlarray);
}

echo '[done]</p>';

echo '<p>Setting up core events...';

Events::SetupCoreEvents();

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 24";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
