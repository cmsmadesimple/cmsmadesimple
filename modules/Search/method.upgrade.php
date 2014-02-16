<?php
if (!isset($gCms)) exit;
$db = $this->GetDb();

if( version_compare($oldversion,'1.50') < 1 ) {
  $this->RegisterModulePlugin(true);
  $this->RegisterSmartyPlugin('search','function','function_plugin');

  try {
    $searchform_type = new CmsLayoutTemplateType();
    $searchform_type->set_originator($this->GetName());
    $searchform_type->set_name('searchform');
    $searchform_type->set_dflt_flag(TRUE);
    $searchform_type->set_lang_callback('Search::page_type_lang_callback');
    $searchform_type->set_content_callback('Search::reset_page_type_defaults');
    $searchform_type->reset_content_to_factory();
    $searchform_type->save();

    $template = $this->GetTemplate('displaysearch');
    if( $template ) {
      $tpl = new CmsLayoutTemplate();
      $tpl->set_name('Search Form Sample');
      $tpl->set_owner($uid);
      $tpl->set_content($template);
      $tpl->set_type($searchform_type);
      $tpl->set_type_dflt(TRUE);
      $tpl->save();
      $this->DeleteTemplate('displaysearch');
    }

    $searchresults_type = new CmsLayoutTemplateType();
    $searchresults_type->set_originator($this->GetName());
    $searchresults_type->set_name('searchresults');
    $searchresults_type->set_dflt_flag(TRUE);
    $searchresults_type->set_lang_callback('Search::page_type_lang_callback');
    $searchresults_type->set_content_callback('Search::reset_page_type_defaults');
    $searchresults_type->reset_content_to_factory();
    $searchresults_type->save();

    $template = $this->GetTemplate('displayresult');
    if( $template ) {
      $tpl = new CmsLayoutTemplate();
      $tpl->set_name('Search Results Sample');
      $tpl->set_owner($uid);
      $tpl->set_content($template);
      $tpl->set_type($searchresults_type);
      $tpl->set_type_dflt(TRUE);
      $tpl->save();
      $this->DeleteTemplate('displayresult');
    }
  }
  catch( CmsException $e ) {
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
  }
}

?>