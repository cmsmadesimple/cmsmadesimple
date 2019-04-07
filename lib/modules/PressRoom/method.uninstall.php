<?php
namespace PressRoom;
use PressRoom;

$gCms = cmsms();
$db = $gCms->GetDb();
$dict = NewDataDictionary($db);
$categories_table = $this->categoriesManager()->table_name();
$fielddefs_table = $this->fielddefManager()->table_name();
$news_table = $this->articleManager()->news_table();
$fieldvals_table = $this->articleManager()->fieldvals_table();

$tables = [ $fieldvals_table, $news_table, $fielddefs_table, $categories_table ];
foreach( $tables as $table ) {
    $sqlarr = $dict->DropTableSQL( $table );
    $dict->ExecuteSQLArray( $sqlarr );
}

$this->RemovePermission(PressRoom::MANAGE_PERM);
$this->RemovePermission(PressRoom::OWN_PERM);
$this->RemovePermission(PressRoom::DELOWN_PERM);
$this->RemovePermission(PressRoom::APPROVE_PERM);

TemplateTypeAssistant::remove_dm_templates();
