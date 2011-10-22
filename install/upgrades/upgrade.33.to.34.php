<?php
$gCms = cmsms();
$dbdict = NewDataDictionary($db);

echo '<p>Adding new field to content table...';
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'content','page_url C(255)');
$dbdict->ExecuteSQLArray($sqlarray);
echo '[done]</p>';

echo '<p>Adding more fields to CGB table...';
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'htmlblobs','description X,use_wysiwyg I1');
$dbdict->ExecuteSQLArray($sqlarray);
echo '[done]</p>';

echo '<p>Updating schema version... ';
$query = "UPDATE ".cms_db_prefix()."version SET version = 34";
$db->Execute($query);
echo '[done]</p>';
