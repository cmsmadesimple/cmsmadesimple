<?php
$sql = 'SELECT permission_id FROM '.CMS_DB_PREFIX.'permissions WHERE permission_name = ?';
$tmp = (int) $db->GetOne($sql,[ 'Manage Users'] );
if( $tmp < 1 ) {
    status_msg('Create missing "Manage Users" Permission');
    $new_id = (int) $db->GenID(CMS_DB_PREFIX.'permissions_seq');
    $sql = 'INSERT INTO '.CMS_DB_PREFIX.'permissions (permission_id,permission_name,permission_text,permission_source,create_date,modified_date)
            VALUES (?,?,?,?,NOW(),NOW())';
    $db->Execute( $sql, [ $new_id, 'Manage Users', 'Manage Users', 'Core'] );
}
