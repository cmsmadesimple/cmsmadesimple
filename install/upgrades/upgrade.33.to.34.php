<?php
global $gCms;
$dbdict = NewDataDictionary($db);

echo '<p>Adding new field to content table...';
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'content','page_url C(255)');
$dbdict->ExecuteSQLArray($sqlarray);
echo '[done]</p>';

echo '<p>Adding more fields to CGB table...';
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'htmlblobs','description X,use_wysiwyg I1');
$dbdict->ExecuteSQLArray($sqlarray);
echo '[done]</p>';

echo '<p>Adding &quot;Reorder Content&quot; permission';
$nid = $db->GenId()(cms_db_prefix().'permissions_seq');
$db->Execute('Insert INTO '.cms_db_prefix()."permissions (permission_id,permission_name,permission_text,create_date,modified_date)
              VALUES (?,?,?,NOW(),NOW())",array($nid,'Reorder Content','Reorder Content'));
echo '[done]</p>';

echo '<p>Updating schema version... ';
$query = "UPDATE ".cms_db_prefix()."version SET version = 34";
$db->Execute($query);
echo '[done]</p>';
