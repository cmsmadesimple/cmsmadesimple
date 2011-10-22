<?php
$gCms = cmsms();

echo '<p>Installing new FileManager module... ';

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

echo '<p>Installing new Printing module... ';

$modops =& $gCms->GetModuleOperations();
$result = $modops->InstallModule('Printing',false);
if( $result[0] == false )
{
    echo '[failed]</p>';
}
else
{
    echo '[done]</p>';
    echo '<p class="okmessage">'.$gCms->modules['Printing']['object']->InstallPostMessage().'</p>';
}

echo '<p>Updating schema version... ';
$query = 'UPDATE '.cms_db_prefix().'version SET version = 29';
$db->Execute($query);
echo '[done]</p>';  

?>
