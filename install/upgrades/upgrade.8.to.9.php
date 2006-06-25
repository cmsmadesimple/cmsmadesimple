<?php

echo "<p>Adding content table...";

$dbdict = NewDataDictionary($db);
$flds = "
	content_id I,
	content_name C(255),
	type C(25),
	owner_id I,
	parent_id I,
	template_id I,
	item_order I,
	hierarchy C(255),
	default_content I1,
	menu_text C(255),
	content_alias C(255),
	show_in_menu I1,
	markup C(25),
	active I1,
	cachable I1,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."content", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence(cms_db_prefix()."content_seq");

echo "[done]</p>";

echo "<p>Adding content_props table...";

$dbdict = NewDataDictionary($db);
$flds = "
	content_prop_id I,
	content_id I,
	type C(25),
	prop_name C(255),
	param1 C(255),
	param2 C(255),
	param3 C(255),
	content X,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."content_props", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence(cms_db_prefix()."content_props_seq");

echo "[done]</p>";

echo "<p>Converting existing content...";

$query = "SELECT * from ".cms_db_prefix()."pages ORDER BY hierarchy_position";
$result = $db->Execute($query);

if ($result && $result->RecordCount() > 0)
{
	$idmap = array();

	while ($row = $result->FetchRow())
	{
		$newid = $db->GenID(cms_db_prefix()."content_seq");
		$idmap[$row['page_id']] = $newid;

		$contentquery = "INSERT INTO ".cms_db_prefix()."content (content_id, content_name, type, owner_id, parent_id, item_order, create_date, modified_date, active, default_content, template_id, content_alias, menu_text, show_in_menu, markup) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$contentresult = $db->Execute($contentquery, array(
			$newid,
			$row['page_title'],
			$row['page_type'],
			$row['owner'],
			$row['parent_id'],
			$row['item_order'],
			$row['create_date'],
			trim($db->DBTimeStamp(time()), "'"),
			$row['active'],
			$row['default_page'],
			$row['template_id'],
			$row['page_alias'],
			$row['menu_text'],
			$row['show_in_menu'],
			'html'
		));

		$type = $row['page_type'];

		switch ($type)
		{
			case 'content':
				$newcontent = @ContentManager::LoadContentFromId($newid);
				if ($newcontent !== FALSE)
				{
					$oldcontent = $row['page_content'];
					
					#Fix for dhtmlmenu
					$oldcontent = str_replace('{dhtmlmenu', "{cms_module module='phplayers'", $oldcontent);

					$newcontent->mCachable = true;

					$newcontent->SetPropertyValue('content_en', $oldcontent);
					$newcontent->SetPropertyValue('head_tags', $row['head_tags']);
					$newcontent->Save();
				}
				break;
			case 'link':
				$newcontent = @ContentManager::LoadContentFromId($newid);
				if ($newcontent !== FALSE)
				{
					$newcontent->SetPropertyValue('url', $row['page_url']);
					$newcontent->Save();
				}
				break;
			default:
				break;
		}

	}

	#Fix parent ids
	$query = "UPDATE ".cms_db_prefix()."content SET parent_id = -1 WHERE parent_id = 0";
	$result = $db->Execute($query);

	$query = "SELECT content_id, parent_id from ".cms_db_prefix()."content";
	$result = $db->Execute($query);

	if ($result && $result->RecordCount() > 0)
	{
		while ($row = $result->FetchRow())
		{
			if (isset($row['parent_id']) && $row['parent_id'] > -1)
			{
				$newquery = "UPDATE ".cms_db_prefix()."content SET parent_id = ? WHERE content_id = ?";
				$db->Execute($newquery, array(
					$idmap[$row['parent_id']],
					$row['content_id']
				));
			}
		}
	}

	$query = "UPDATE ".cms_db_prefix()."content SET parent_id = -1 WHERE parent_id IS NULL";
	$result = $db->Execute($query);

	$query = "UPDATE ".cms_db_prefix()."content SET content_alias = '' WHERE content_alias IS NULL";
	$result = $db->Execute($query);
}

echo '[done]</p>';

@ob_flush();

echo '<p>Adding module_deps table...';

$dbdict = NewDataDictionary($db);
$flds = "
	parent_module C(25),
	child_module C(25),
	minimum_version C(25),
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."module_deps", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo '[done]</p>';

echo '<p>Updating hierarchy positions...';

@ContentManager::SetAllHierarchyPositions();

echo '[done]</p>';

echo '<p>Changing dhtmlmenu to phplayers module on templates...';

$alltemplates = TemplateOperations::LoadTemplates();
foreach ($alltemplates as $onetemplate)
{
	#Fix for dhtmlmenu
	$onetemplate->content = str_replace('{dhtmlmenu', "{cms_module module='phplayers'", $onetemplate->content); 
	$onetemplate->Save();
}

echo '[done]</p>';

echo '<p>"Installing" phplayers module (if necessary)... ';

$query = "SELECT * from ".cms_db_prefix()."modules WHERE module_name = 'PHPLayers'";
$result = $db->Execute($query);

if ($result && $result->RecordCount() < 1)
{
	$query = "INSERT INTO ".cms_db_prefix()."modules (module_name, status, version, active) VALUES ('PHPLayers', 'Installed', '1.0', 1)";
	$result = $db->Execute($query);
}

echo '[done]</p>';

echo '<p>Updating additional_users to new content ids...';

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."additional_users", "content_id I");
$dbdict->ExecuteSQLArray($sqlarray);

$query = "SELECT * from ".cms_db_prefix()."additional_users";
$result = $db->Execute($query);

if ($result && $result->RecordCount() > 0)
{
	while ($row = $result->FetchRow())
	{
		$query2 = "UPDATE ".cms_db_prefix()."additional_users SET content_id = ? WHERE additional_users_id = ?";
		$db->Execute($query2, array($idmap[$row['page_id']], $row['additional_users_id']));
	}
}

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 9";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
