<?php
if (!isset($gCms)) exit;

$db = $this->GetDb();
$this->DeleteTemplate('displaysummary');
$this->DeleteTemplate('displaydetail');

$dict = NewDataDictionary( $db );

$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_news" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_news_categories" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_news_fielddefs" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_news_fieldvals" );
$dict->ExecuteSQLArray($sqlarray);

$db->DropSequence( cms_db_prefix()."module_news_seq" );
$db->DropSequence( cms_db_prefix()."module_news_categories_seq" );

$this->RemovePermission('Modify News');
$this->RemovePermission('Approve News');
$this->RemovePermission('Delete News');

// Remove all preferences for this module
$this->RemovePreference();

// And all Templates
$this->DeleteTemplate();

#Setup events
$this->RemoveEvent('NewsArticleAdded');
$this->RemoveEvent('NewsArticleEdited');
$this->RemoveEvent('NewsArticleDeleted');
$this->RemoveEvent('NewsCategoryAdded');
$this->RemoveEvent('NewsCategoryEdited');
$this->RemoveEvent('NewsCategoryDeleted');

$this->RemoveSmartyPlugin();

cms_route_manager::del_static('',$this->GetName());

?>