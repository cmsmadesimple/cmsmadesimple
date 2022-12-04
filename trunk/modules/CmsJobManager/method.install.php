<?php
if( !isset($gCms) ) exit;

$this->CreatePermission(\CmsJobManager::MANAGE_JOBS,\CmsJobManager::MANAGE_JOBS);
$this->CreateEvent(\CmsJobManager::EVT_ONFAILEDJOB);
$this->AddEventHandler('Core','ModuleUninstalled',FALSE);

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$dict = NewDataDictionary($db);

$flds = "id I KEY AUTO NOTNULL,
         name C(255) NOTNULL,
         created I NOTNULL,
         module C(255) NOTNULL,
         errors I NOTNULL DEFAULT 0,
         start I NOTNULL,
         recurs C(255),
         until I,
         data X2
         ";
$sqlarray = $dict->CreateTableSQL( CmsJobManager::table_name(), $flds, $taboptarray );
$dict->ExecuteSQLArray($sqlarray);
