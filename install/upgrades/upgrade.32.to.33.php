<?php
global $gCms;

echo '<p>Adding SSL field to content table';

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'content',
				  'secure I1');
$dbdict->ExecuteSQLArray($sqlarray);

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 33";
$db->Execute($query);

echo '[done]</p>';

?>
