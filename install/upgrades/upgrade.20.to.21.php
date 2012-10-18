<?php

echo "<p>Adding Cross Reference tables...";

$dbdict = NewDataDictionary($db);
$flds = '
	child_type C(100),
	child_id I,
	parent_type C(100),
	parent_id I,
	create_date DT,
	modified_date DT
';
		
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."crossref", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("CREATE INDEX ".cms_db_prefix()."index_child_type_and_id ON ".cms_db_prefix().
	"crossref (child_type, child_id)");

$db->Execute("CREATE INDEX ".cms_db_prefix()."index_parent_type_and_id ON ".cms_db_prefix().
	"crossref (parent_type, parent_id)");

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 21";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
