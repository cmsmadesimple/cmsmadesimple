<?php
namespace News2;
use News2;

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

$this->RemovePermission(News2::MANAGE_PERM);
$this->RemovePermission(News2::OWN_PERM);
$this->RemovePermission(News2::DELOWN_PERM);
$this->RemovePermission(News2::APPROVE_PERM);

TemplateTypeAssistant::remove_dm_templates();
