<?php
if (!isset($gCms)) exit;

$uid = null;
if( cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
  $uid = 1; // hardcode to first user
} else {
  $uid = get_userid();
}

try {
  $menu_template_type = new CmsLayoutTemplateType();
  $menu_template_type->set_originator($this->GetName());
  $menu_template_type->set_name('navigation');
  $menu_template_type->set_dflt_flag(TRUE);
  $menu_template_type->set_lang_callback('MenuManager::page_type_lang_callback');
  $menu_template_type->set_content_callback('MenuManager::reset_page_type_defaults');
  $menu_template_type->reset_content_to_factory();
  $menu_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  $fn = cms_join_path(dirname(__FILE__),'templates','simple_navigation.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name(CmsLayoutTemplate::generate_unique_name('Simple Navigation Menu'));
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($menu_template_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

// register plugins
$this->RegisterModulePlugin(true);
$this->RegisterSmartyPlugin('menu','function','function_plugin');
$this->RegisterSmartyPlugin('cms_breadcrumbs','function','smarty_cms_breadcrumbs');

?>