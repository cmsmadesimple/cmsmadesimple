<?php

echo '<p>Installing new FileManager... ';

$modops =& $gCms->GetModuleOperations();
$result = $modops->InstallModule('FileManager',false);
if( $result[0] == false )
{
    echo '[failed]</p>';
}
else
{
    echo '[done]</p>';
    echo '<p class="okmessage">'.$gCms->modules['FileManager']['object']->InstallPostMessage().'</p>';
}

echo '<p>Updating schema version... ';
$query = 'UPDATE '.cms_db_prefix().'version SET version = 29';
$db->Execute($query);
echo '[done]</p>';  

?>