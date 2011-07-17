<?php
$gCms = cmsms();

echo '<p>Adding new permission...';

cms_mapi_create_permission($gCms, 'View Tag Help', 'View Tag Help');
echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 31";
$db->Execute($query);

echo '[done]</p>';

?>
