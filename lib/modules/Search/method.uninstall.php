<?php
if (!isset($gCms)) exit;

$db =& $this->GetDb();
$dict = NewDataDictionary($db);

$sqlarray = $dict->DropTableSQL(CMS_DB_PREFIX.'module_search_index');
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL(CMS_DB_PREFIX.'module_search_items');
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL(CMS_DB_PREFIX.'module_search_words');
$dict->ExecuteSQLArray($sqlarray);

$db->DropSequence( CMS_DB_PREFIX."module_search_items_seq" );

$this->DeleteTemplate();
$this->RemovePreference();
$this->RemoveSmartyPlugin();

// remove templates
// and template types.
try {
  $types = CmsLayoutTemplateType::load_all_by_originator($this->GetName());
  if( is_array($types) && count($types) ) {
    foreach( $types as $type ) {
      $templates = $type->get_template_list();
      if( is_array($templates) && count($templates) ) {
	foreach( $templates as $template ) {
	  $template->delete();
	}
      }
      $type->delete();
    }
  }
}
catch( Exception $e ) {
  // log it
  audit('',$this->GetName(),'Uninstall Error: '.$e->GetMessage());
}

?>