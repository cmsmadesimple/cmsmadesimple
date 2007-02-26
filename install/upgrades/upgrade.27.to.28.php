<?php

$perm_table = cms_db_prefix() . 'permissions';
$perm_seq_table = cms_db_prefix() . 'permissions_seq';


// Check if the "Modify Page Structure" permission exists...
$query = "
    SELECT 1 FROM $perm_table WHERE permission_name = 'Modify Page Structure'
";
$result = $db->Execute($query);
if ($result->RecordCount() == 0) 
{
	echo '<p>Creating "Modify Page Structure" permission...</p>';
	
	$perm_id = $db->GenID($perm_seq_table);
	$name   = 'Modify Page Structure';
	
	$timestamp = $db->DBTimeStamp(time());
	if ($timestamp[0] != "'")
	{
		$timestamp = "'$timestamp'";
	}
	
	$query = "
		INSERT INTO 
			$perm_table
		(permission_id, permission_name, permission_text, create_date, modified_date) 
			VALUES
		('$perm_id', '$name', '$name', $timestamp, $timestamp)
	";
	$result = $db->Execute($query);
	if ($result) 
	{
		$groupperm_id = $db->GenID($config['db_prefix'] . 'group_perms_seq');
		$table  = $config['db_prefix'] . 'group_perms'; 
		$query = "
			INSERT INTO 
				$table
			(group_perm_id, group_id, permission_id, create_date, modified_date) 
				VALUES 
			('$groupperm_id', 1, $perm_id, $timestamp, $timestamp)
		";
		$result = $db->Execute($query);
	}
}
echo '<p>Updating schema version... ';

$query = 'UPDATE '.cms_db_prefix().'version SET version = 28';
$db->Execute($query);
echo '[done]</p>';  

?>