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
	active I1,
	create_date T,
	modified_date T
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
	create_date T,
	modified_date T
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."content_props", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence(cms_db_prefix()."content_props_seq");

echo "[done]</p>";

echo "<p>Converting existing content...";

$query = "SELECT * from ".cms_db_prefix()."pages ORDER BY hierarchy_position";
$result = $db->Execute($query);

if ($result && $result->RowCount() > 0)
{
	$idmap = array();

	while ($row = $result->FetchRow())
	{
		$newid = $db->GenID(cms_db_prefix()."content_seq");
		$this->mId = $newid;

		$idmap[$row['page_id']] = $newid;

		$contentquery = "INSERT INTO ".cms_db_prefix()."content (content_id, content_name, type, owner_id, parent_id, item_order, create_date, modified_date, active, default_content, template_id, content_alias, menu_text, show_in_menu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$contentresult = $db->Execute($contentquery, array(
			$newid,
			$row['page_title'],
			$row['page_type'],
			$row['owner'],
			$row['parent_id'],
			$row['item_order'],
			$row['create_date'],
			$db->DBTimeStamp(time()),
			$row['active'],
			$row['default_page'],
			$row['template_id'],
			$row['page_alias'],
			$row['menu_text'],
			$row['show_in_menu']
		));

		$type = $row['page_type'];

		switch ($type)
		{
			case 'content':
				$newcontent = @ContentManager::LoadContentFromId($newid);
				if ($newcontent !== FALSE)
				{
					$newcontent->SetPropertyValue('content_en', $row['page_content']);
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
	$query = "SELECT content_id, parent_id from ".cms_db_prefix()."content";
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0)
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

	$query = "UPDATE ".cms_db_prefix()."content SET parent_id = -1 wHERE parent_id IS NULL";
	$result = $db->Execute($query);

	$query = "UPDATE ".cms_db_prefix()."content SET content_alias = '' wHERE content_alias IS NULL";
	$result = $db->Execute($query);
}

echo "[done]</p>";

@ob_flush();

echo "<p>Updating hierarchy positions...";

@ContentManager::SetAllHierarchyPositions();

echo "[done]</p>";

echo "<p>Updating schema version... ";

$query = "UPDATE ".cms_db_prefix()."version SET version = 9";
$db->Execute($query);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
