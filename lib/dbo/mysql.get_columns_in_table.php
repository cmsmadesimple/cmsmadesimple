<?php

function dbo_get_columns_in_table(&$objref)
{
	$fields = array();
	
	global $gCms;
	$db =& $gCms->GetDb();
	
	$dbresult =& $db->Execute('DESC ' . $objref->get_table());
	
	while ($dbresult && !$dbresult->EOF)
	{
		$fields[] = $dbresult->fields['Field'];
		$dbresult->MoveNext();
	}

	return $fields;	
}

?>