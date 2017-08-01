<?php
if (!isset($gCms)) exit;
$db = $this->GetDb();
$dict = NewDataDictionary($db);

$sqlarr = $dict->DropTableSQL( \AdminLog\storage::table_name() );
$dict->ExecuteSQLArray( $sqlarr );

$this->RemovePermission('View Admin Log');
$this->RemovePermission('Clear Admin Log');
