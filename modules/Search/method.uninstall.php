<?php
if (!isset($gCms)) exit;

$db =& $this->GetDb();
$dict = NewDataDictionary($db);

$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_search_words');
$dict->ExecuteSQLArray($sqlarray);

$this->DeleteTemplate();
$this->RemovePreference();
	
$this->RemoveEvent('SearchInitiated');
$this->RemoveEvent('SearchCompleted');
$this->RemoveEvent('SearchItemAdded');
$this->RemoveEvent('SearchItemDeleted');
$this->RemoveEvent('SearchAllItemsDeleted');
$this->RemoveEvent('SearchReIndex');

$this->RemoveEventHandler( 'Core', 'ContentEditPost');
$this->RemoveEventHandler( 'Core', 'ContentDeletePost');
$this->RemoveEventHandler( 'Core', 'AddTemplatePost');
$this->RemoveEventHandler( 'Core', 'EditTemplatePost');
$this->RemoveEventHandler( 'Core', 'DeleteTemplatePost');
$this->RemoveEventHandler( 'Core', 'AddGlobalContentPost');
$this->RemoveEventHandler( 'Core', 'EditGlobalContentPost');
$this->RemoveEventHandler( 'Core', 'DeleteGlobalContentPost');
$this->RemoveEventHandler( 'Core', 'ModuleUninstalled');

?>
