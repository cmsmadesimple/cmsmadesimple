<?php

echo "<p>Creating sequences...";

function upgrade_create_sequence_table($db, $tablename, $idcol) {

	$num = $db->GetOne("SELECT MAX($idcol) FROM $tablename");
	$db->DropSequence($tablename."_seq");
	$db->CreateSequence($tablename."_seq", $num + 1);
	$dict = NewDataDictionary($db);
	$sqlarray = $dict->AlterColumnSQL($tablename, "$idcol I"); 
	$dict->ExecuteSQLArray($sqlarray);

}

upgrade_create_sequence_table($dbnew, $config->db_prefix."additional_users", "additional_users_id");
upgrade_create_sequence_table($dbnew, $config->db_prefix."group_perms", "group_perm_id");
upgrade_create_sequence_table($dbnew, $config->db_prefix."groups", "group_group_id");
upgrade_create_sequence_table($dbnew, $config->db_prefix."pages", "page_id");
upgrade_create_sequence_table($dbnew, $config->db_prefix."permissions", "permission_id");
upgrade_create_sequence_table($dbnew, $config->db_prefix."sections", "section_id");
upgrade_create_sequence_table($dbnew, $config->db_prefix."templates", "template_id");
upgrade_create_sequence_table($dbnew, $config->db_prefix."users", "user_id");

echo "[done]</p>";

echo "<p>Creating modules table...";

$db = NewDataDictionary($dbnew);
$flds = "
	module_name C(255),
	status C(255),
	version C(255),
	active L
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $db->CreateTableSQL($config->db_prefix."modules", $flds, $taboptarray);
$db->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

#permission_id | permission_name          | permission_text              | create_date         | modified_date 

echo "<p>Adding module admin permission... ";

$new_id = $dbnew->GenID($config->db_prefix."permissions_seq");
$query = "INSERT INTO ".$config->db_prefix."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES ($new_id,'Modify Modules','Modify Modules',".$dbnew->DBTimeStamp(time()).",".$dbnew->DBTimeStamp(time()).")";
$dbnew->Execute($query);

echo "[done]</p>";

echo "<p>Updating schema version... ";

$query = "UPDATE ".$config->db_prefix."version SET version = 4";
$dbnew->Execute($query);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
