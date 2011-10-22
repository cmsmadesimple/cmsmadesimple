<?php
$gCms = cmsms();

echo '<p>Adding new permission...';

cms_mapi_create_permission($gCms, 'Manage All Content', 'Manage All Content');
echo '[done]</p>';

echo '<p>Removing stale permission...';
cms_mapi_remove_permission('Modify Page Structure');
echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 32";
$db->Execute($query);

echo '[done]</p>';

?>
