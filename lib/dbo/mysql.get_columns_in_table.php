<?php

function dbo_get_columns_in_table($table)
{
	$fields = array();
	
	$db = cms_db();
	
	$dbresult =& $db->Execute('DESC ' . $table);
	
	while ($dbresult && !$dbresult->EOF)
	{
		$fields[] = $dbresult->fields['Field'];
		$dbresult->MoveNext();
	}

	return $fields;	
}

?>