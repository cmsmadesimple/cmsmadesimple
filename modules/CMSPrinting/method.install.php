<?php
if (!isset($gCms)) exit;

$this->ResetOverrideStyle();
$this->RegisterModulePlugin(TRUE);
$this->RegisterSmartyPlugin('print','function','function_plugin');

try {
  $linktpl_type = new CmsLayoutTemplateType();
  $linktpl_type->set_originator($this->GetName());
  $linktpl_type->set_name('link');
  $linktpl_type->set_dflt_flag(TRUE);
  $linktpl_type->set_lang_callback('CMSPrinting::page_type_lang_callback');
  $linktpl_type->set_content_callback('CMSPrinting::reset_page_type_defaults');
  $linktpl_type->reset_content_to_factory();
  $linktpl_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'linktemplate.tpl';
  $template = @file_get_contents($fn);
  $tpl = new CmsLayoutTemplate();
  $tpl->set_name('CMSPrinting Sample Link Template');
  $uid = get_userid(FALSE);
  $tpl->set_owner($uid);
  $tpl->set_content($template);
  $tpl->set_type($linktpl_type);
  $tpl->set_type_dflt(TRUE);
  $tpl->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  $printtpl_type = new CmsLayoutTemplateType();
  $printtpl_type->set_originator($this->GetName());
  $printtpl_type->set_name('print');
  $printtpl_type->set_dflt_flag(TRUE);
  $printtpl_type->set_lang_callback('CMSPrinting::page_type_lang_callback');
  $printtpl_type->set_content_callback('CMSPrinting::reset_page_type_defaults');
  $printtpl_type->reset_content_to_factory();
  $printtpl_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'printtemplate.tpl';
  $template = @file_get_contents($fn);
  $tpl = new CmsLayoutTemplate();
  $tpl->set_name('CMSPrinting Sample Print Template');
  $tpl->set_owner($uid);
  $tpl->set_content($template);
  $tpl->set_type($printtpl_type);
  $tpl->set_type_dflt(TRUE);
  $tpl->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

?>
