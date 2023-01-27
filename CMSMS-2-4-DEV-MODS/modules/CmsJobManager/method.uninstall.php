<?php
if( !isset($gCms) ) exit;

$dict = NewDataDictionary( $db );
$sqlarray = $dict->DropTableSQL( CmsJobManager::table_name() );
$dict->ExecuteSQLArray($sqlarray);

$this->RemovePermission(\CmsJobManager::MANAGE_JOBS);
$this->RemoveEvent(\CmsJobManager::EVT_ONFAILEDJOB);
$this->RemoveEventHandler('Core','ModuleUninstalled');