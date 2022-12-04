<?php

if (!function_exists("cmsms")) exit;
//$db = &$this->GetDb();

// remove the database table
/*$dict = NewDataDictionary( $db );
 $sqlarray = $dict->DropTableSQL( CMS_DB_PREFIX."module_filemanager_thumbs" );
 $dict->ExecuteSQLArray($sqlarray);
 */
// remove the sequence
//$db->DropSequence( CMS_DB_PREFIX."module_skeleton_seq" );

// remove the permissions
$this->RemovePermission('Use Filemanager'); //Used in some old versions
$this->RemovePermission('Use Filemanager Advanced');
$this->RemoveEvent('OnFileUploaded');
$this->RemoveEvent('OnFileDeleted');
$this->RemovePreference();
$this->RemoveSmartyPlugin();

?>
