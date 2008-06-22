<?php

echo '<p>Creating "Modify Page Structure" permission...';

$perm_id = $db->GenID(cms_db_prefix() . 'permissions_seq');
$table  = cms_db_prefix() . 'permissions';
$name   = 'Modify Page Structure';
$timestamp = $db->DBTimeStamp(time());
if ($timestamp[0] != "'")
{
    $timestamp = "'$timestamp'";
}
$query ="
    INSERT INTO 
        $table
    (permission_id, permission_name, permission_text, create_date, modified_date) 
        VALUES
    ('$perm_id', '$name', '$name', $timestamp, $timestamp)
";
$result = $db->Execute($query);
if ($result) 
{
    $groupperm_id = $db->GenID(cms_db_prefix() . 'group_perms_seq');
    $table  = cms_db_prefix() . 'group_perms'; 
    $query = "
        INSERT INTO 
            $table 
        (group_perm_id, group_id, permission_id, create_date, modified_date) 
            VALUES 
        ('$groupperm_id', 1, $perm_id, $timestamp, $timestamp)
    ";
    $result = $db->Execute($query);
}
echo '[done]</p>';

echo '<p>Updating schema version... ';
$query = 'UPDATE '.cms_db_prefix().'version SET version = 25';
$db->Execute($query);
echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
