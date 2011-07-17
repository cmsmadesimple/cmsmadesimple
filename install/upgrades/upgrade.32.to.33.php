<?php
$gCms = cmsms();

echo '<p>Adding new field to content table...';
$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'content','secure I1');
$dbdict->ExecuteSQLArray($sqlarray);
$query = 'UPDATE '.cms_db_prefix().'content SET secure = 0';
$db->Execute($query);
echo '[done]</p>';

echo '<p>Updating schema version... ';
$query = "UPDATE ".cms_db_prefix()."version SET version = 33";
$db->Execute($query);
echo '[done]</p>';
