<?php  
$gCms = cmsms();

echo '<p>Adding TemplatePreFetch event...';
Events::CreateEvent('Core','TemplatePreFetch');
echo '[done]</p>';

echo '<p>Updating schema version... ';
$query = "UPDATE ".cms_db_prefix()."version SET version = 37";
$db->Execute($query);
echo '[done]</p>';

?>
