<?php

echo "<p>Adding module_templates table...";

$dbdict = NewDataDictionary($db);
$flds = "
	module_name C(255),
	template_name C(255),
	content X,
	create_date T,
	modified_date T
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."module_templates", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Adding fields to content table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."content", "last_modified_by I");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Adding fields to template table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."templates", "default_template I1");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Setting a default template...";

$onetemplateid = $db->GetOne("SELECT template_id FROM ".cms_db_prefix()."templates");
$db->Execute("UPDATE ".cms_db_prefix()."templates SET default_template = 0");
$db->Execute("UPDATE ".cms_db_prefix()."templates SET default_template = 1 WHERE template_id = ".$onetemplateid);

echo "[done]</p>";

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 10";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
