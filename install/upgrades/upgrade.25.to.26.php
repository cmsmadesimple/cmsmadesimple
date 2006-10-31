<?php

echo '<p>Splitting node and content tables up... ';

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY AUTO,
	content_id I,
	parent_id I,
	item_order I,
	hierarchy C(255),
	default_node I1,
	show_in_menu I1,
	hierarchy_path X,
	alias C(255),
	collapsed I1,
	active I1,
	id_hierarchy C(255),
	create_date DATETIME,
	modified_date DATETIME
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."nodes", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute('INSERT INTO '.cms_db_prefix().'nodes (id, content_id, parent_id, item_order, hierarchy, default_node, show_in_menu, alias, collapsed, active, id_hierarchy, hierarchy_path, create_date, modified_date) SELECT content_id, content_id, parent_id, item_order, hierarchy, default_content, show_in_menu, content_alias, collapsed, active, id_hierarchy, hierarchy_path, create_date, modified_date FROM '.cms_db_prefix().'content');

$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."content", "tmp I");
$dbdict->ExecuteSQLArray($sqlarray);
$query = "UPDATE ".cms_db_prefix()."content SET tmp = content_id";
$db->Execute($query);
$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."content", "content_id"); 
$dbdict->ExecuteSQLArray($sqlarray);
//if ($config["dbms"] == 'mysql' || $config["dbms"] == 'mysqli')
//{
	$db->Execute("ALTER TABLE ".cms_db_prefix()."content ADD id INTEGER KEY NOT NULL AUTO_INCREMENT");
//}
$query = "UPDATE ".cms_db_prefix()."content SET id = tmp";
$db->Execute($query);
$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."content", "tmp"); 
$dbdict->ExecuteSQLArray($sqlarray);


$sql = $dbdict->DropColumnSQL(cms_db_prefix().'content', 'parent_id');
$dbdict->ExecuteSQLArray($sql);
$sql = $dbdict->DropColumnSQL(cms_db_prefix().'content', 'item_order');
$dbdict->ExecuteSQLArray($sql);
$sql = $dbdict->DropColumnSQL(cms_db_prefix().'content', 'hierarchy');
$dbdict->ExecuteSQLArray($sql);
$sql = $dbdict->DropColumnSQL(cms_db_prefix().'content', 'default_content');
$dbdict->ExecuteSQLArray($sql);
$sql = $dbdict->DropColumnSQL(cms_db_prefix().'content', 'show_in_menu');
$dbdict->ExecuteSQLArray($sql);
$sql = $dbdict->DropColumnSQL(cms_db_prefix().'content', 'content_alias');
$dbdict->ExecuteSQLArray($sql);
$sql = $dbdict->DropColumnSQL(cms_db_prefix().'content', 'collapsed');
$dbdict->ExecuteSQLArray($sql);
$sql = $dbdict->DropColumnSQL(cms_db_prefix().'content', 'active');
$dbdict->ExecuteSQLArray($sql);
$sql = $dbdict->DropColumnSQL(cms_db_prefix().'content', 'id_hierarchy');
$dbdict->ExecuteSQLArray($sql);
$sql = $dbdict->DropColumnSQL(cms_db_prefix().'content', 'hierarchy_path');
$dbdict->ExecuteSQLArray($sql);

echo '[done]</p>';

echo '<p>Updating schema version... ';
$query = 'UPDATE '.cms_db_prefix().'version SET version = 26';
$db->Execute($query);
echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>