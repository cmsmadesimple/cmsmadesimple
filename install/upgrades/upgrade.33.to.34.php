<?php
global $gCms;

echo '<p>Adding lft and rgt fields to content table';

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'content',
				  'lft I1');
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'content',
				  'rgt I1');
$dbdict->ExecuteSQLArray($sqlarray);
echo '[done]</p>';

echo '<p>Adding id field to content_props table';
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'content_props','id I NOT NULL KEY AUTO');
$dbdict->ExecuteSQLArray($sqlarray);

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 34";
$db->Execute($query);

echo '[done]</p>';

?>