<?php

echo '<p>Updating content objects...';

$dbdict = NewDataDictionary($db);

$lookup = array();

$result = $db->Execute("SELECT cp.id, prop_name, cp.content_id FROM ".cms_db_prefix()."content_props cp INNER JOIN ".cms_db_prefix()."content c ON c.id = cp.content_id WHERE c.type = 'content'");
while ($result && $row = $result->FetchRow())
{
	$content_id = $row['content_id'];
	$prop_name = $row['prop_name'];
	if (!array_key_exists($content_id, $lookup))
	{
		$lookup[$content_id] = array();
	}
	if ($row['prop_name'] == 'content_en')
	{
		$db->Execute("UPDATE ".cms_db_prefix()."content_props SET prop_name = 'default-content' WHERE id = ?", array($row['id']));
		$lookup[$content_id][] = 'default-content';
	}
	else if (!ends_with($prop_name, '-content') && !ends_with($prop_name, '-block-type'))
	{
		$db->Execute("UPDATE ".cms_db_prefix()."content_props SET prop_name = '" . strtolower($row['prop_name']) . "-content' WHERE id = ?", array($row['id']));
		$lookup[$content_id][] = strtolower($row['prop_name']) . '-content';
	}
	else
	{
		$lookup[$content_id][] = $row['prop_name'];
	}
}
$result->Close();

foreach ($lookup as $k=>$v)
{
	$db->Execute("UPDATE ".cms_db_prefix()."content SET prop_names = ? WHERE id = ?", array(implode(',', $v), $k));
}

echo '[done]</p>';

echo '<p>Updating schema version... ';
$query = 'UPDATE '.cms_db_prefix().'version SET version = 27';
$db->Execute($query);
echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>