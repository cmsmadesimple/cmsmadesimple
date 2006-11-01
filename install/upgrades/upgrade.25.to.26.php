<?php

echo '<p>Modifying content and content props tables...';

$dbdict = NewDataDictionary($db);

//Add id field to props
$db->Execute("ALTER TABLE ".cms_db_prefix()."content_props ADD id INTEGER KEY NOT NULL AUTO_INCREMENT");

//Add tmp field for old content_id
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."content_props", "tmp I");
$dbdict->ExecuteSQLArray($sqlarray);

//Set all content_ids in the tmp field
$query = "UPDATE ".cms_db_prefix()."content_props SET tmp = content_id";
$db->Execute($query);

//Add tmp field for content
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."content", "tmp I");
$dbdict->ExecuteSQLArray($sqlarray);

//Add tmp field for content
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."content", "tmp2 I");
$dbdict->ExecuteSQLArray($sqlarray);

//Set all content_ids in the tmp field
$query = "UPDATE ".cms_db_prefix()."content SET tmp = content_id";
$db->Execute($query);

//Set all content_ids in the tmp field
$query = "UPDATE ".cms_db_prefix()."content SET tmp2 = parent_id";
$db->Execute($query);

//Drop the content_id column (to remove the primary key)
$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."content", "content_id");
$dbdict->ExecuteSQLArray($sqlarray);

//Add the new id with the auto increment
//if ($config["dbms"] == 'mysql' || $config["dbms"] == 'mysqli')
//{
    $db->Execute("ALTER TABLE ".cms_db_prefix()."content ADD id INTEGER KEY NOT NULL AUTO_INCREMENT");
//}

//Update all the ids to the tmp value
//$query = "UPDATE ".cms_db_prefix()."content SET id = tmp";
//$db->Execute($query);

//Run through all the new ids... modify the content props based on the old id to new id
$dbresult = $db->Execute('SELECT id, tmp FROM '.cms_db_prefix().'content');
if ($dbresult && $dbresult->RecordCount() > 0)
{
	while ($row = $dbresult->FetchRow())
	{
		$db->Execute("UPDATE ".cms_db_prefix()."content_props SET content_id = ? WHERE tmp = ?", array($row['id'], $row['tmp']));
		$db->Execute("UPDATE ".cms_db_prefix()."content SET parent_id = ? WHERE tmp2 = ?", array($row['id'], $row['tmp']));
	}
}

//Drop the tmp column
$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."content", "tmp");
$dbdict->ExecuteSQLArray($sqlarray);

//Drop the tmp column
$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."content", "tmp2");
$dbdict->ExecuteSQLArray($sqlarray);

//Drop the tmp column
$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."content_props", "tmp");
$dbdict->ExecuteSQLArray($sqlarray);

global $gCms;
$ops =& $gCms->GetContentOperations();
$ops->SetAllHierarchyPositions();

echo '[done]</p>';

echo '<p>Updating schema version... ';
$query = 'UPDATE '.cms_db_prefix().'version SET version = 26';
$db->Execute($query);
echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>