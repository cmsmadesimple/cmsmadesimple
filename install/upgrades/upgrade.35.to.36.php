<?php
global $gCms;
$dbdict = NewDataDictionary($db);

echo '<p>Adding fields to module_templates table';

$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'module_templates','id I NOT NULL KEY AUTO ');
$sqlarray[0] .= " PRIMARY KEY";
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'module_templates','template_type C(100) NOTNULL default ""');
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'module_templates','default_template I1 default 0');
$dbdict->ExecuteSQLArray($sqlarray);

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 36";
$db->Execute($query);

echo '[done]</p>';

?>
