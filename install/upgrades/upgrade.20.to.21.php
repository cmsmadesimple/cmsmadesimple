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

echo '<p>Setting up existing cross references...';

//Do the templates first
$query = 'SELECT template_id, template_content FROM '.cms_db_prefix().'templates';
$result = &$db->Execute($query);

while ($result && !$result->EOF)
{
	do_cross_reference($result->fields['template_id'], 'template', $result->fields['template_content']);
	$result->MoveNext();
}

//Gather up all content
$query = 'SELECT content_id, content FROM '.cms_db_prefix().'content_props ORDER BY content_id';
$result = &$db->Execute($query);

$contentary = array();
while ($result && !$result->EOF)
{
	if (isset($contentary[$result->fields['content_id']]))
		$contentary[$result->fields['content_id']] .= $result->fields['content'];
	else
		$contentary[$result->fields['content_id']] = $result->fields['content'];
	$result->MoveNext();
}

//Now run it all through the cross reference routine to populate
if (count($contentary) > 0)
{
	foreach ($contentary as $key=>$val)
	{
		do_cross_reference($key, 'content', $val);
	}
}

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 21";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
