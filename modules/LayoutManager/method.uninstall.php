<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
if( !isset($gCms) ) exit;
$dict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$db_prefix = cms_db_prefix();

$tmp = $dict->DropTableSQL($db_prefix.CmsLayoutTemplateType::TABLENAME);
$dict->ExecuteSQLArray($tmp);
$tmp = $dict->DropTableSQL($db_prefix.CmsLayoutTemplateCategory::TABLENAME);
$dict->ExecuteSQLArray($tmp);
$tmp = $dict->DropTableSQL($db_prefix.CmsLayoutTemplate::TABLENAME);
$dict->ExecuteSQLArray($tmp);
$tmp = $dict->DropTableSQL($db_prefix.CmsLayoutTemplate::ADDUSERSTABLE);
$dict->ExecuteSQLArray($tmp);
$tmp = $dict->DropTableSQL($db_prefix.CmsLayoutTheme::TABLENAME);
$dict->ExecuteSQLArray($tmp);
$tmp = $dict->DropTableSQL($db_prefix.CmsLayoutTheme::CSSTABLE);
$dict->ExecuteSQLArray($tmp);
$tmp = $dict->DropTableSQL($db_prefix.CmsLayoutTheme::TPLTABLE);
$dict->ExecuteSQLArray($tmp);

$this->RemovePermission('Manage Templates');
$this->RemovePermission('Add Templates');

?>