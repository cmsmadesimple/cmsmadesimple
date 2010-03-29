<?php

echo '<p>Adding stylesheet association ordering capability... ';

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().'css_assoc',
				  'assoc_order I');
$dbdict->ExecuteSQLArray($sqlarray);

// Now update the values
$query = 'SELECT assoc_to_id, assoc_css_id FROM '.cms_db_prefix().'css_assoc 
          ORDER BY assoc_to_id, assoc_css_id';
$allrows = $db->GetArray($query);
$assoc_to_id = -1;
foreach( $allrows as $row )
  {
    if( $assoc_to_id != $row['assoc_to_id'] )
      {
	$ord = 1;
	$assoc_to_id = $row['assoc_to_id'];
      }

    $q2 = 'UPDATE '.cms_db_prefix().'css_assoc SET assoc_order = ?
            WHERE assoc_to_id = ? AND assoc_css_id = ?';
    $db->Execute( $q2, array($ord,$row['assoc_to_id'],$row['assoc_css_id']) );
    $ord++;
  }
echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 30";
$db->Execute($query);

echo '[done]</p>';

?>