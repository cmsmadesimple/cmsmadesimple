<?php
$gCms = cmsms();

echo '<p>Adding module_smarty_plugins table...';
$flds = "
         sig C(80) KEY NOT NULL,
         name C(80) NUT NULL,
         module C(160) NOT NULL,
         type C(40) NOT NULL,
         callback C(255) NOT NULL,
         available I,
         cachable I1
";


$dbdict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."module_smarty_plugins", $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);
$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
echo '[done]</p>';

echo '<p>Updating schema version... ';
$query = "UPDATE ".cms_db_prefix()."version SET version = 36";
$db->Execute($query);
echo '[done]</p>';

?>