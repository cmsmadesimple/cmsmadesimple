<?php
if (!isset($gCms)) exit;
$uid = null;
if( cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
  $uid = 1; // hardcode to first user
} else {
  $uid = get_userid();
}

$db = cmsms()->GetDb();
if( version_compare($oldversion,'1.50') < 0 ) {
  $upgrade_template = function($type,$prefix,$tplname,$currentdflt) use (&$mod,$uid) {
    if( !startswith($tplname,$prefix) ) return;
    $contents = $mod->GetTemplate($tplname);
    if( !$contents ) return;
    $prototype = substr($tplname,strlen($prefix));

    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($tpl::generate_unique_name($prototype,'MM-'));
    $tpl->set_owner($uid);
    $tpl->set_content($contents);
    $tpl->set_type($type);
    $tpl->set_type_dflt($tplname == $mod->GetPreference($currentdflt));
    $tpl->save();

    $mod->DeleteTemplate($tplname);
  };

  try {
    $mod = $this;
    $alltemplates = $this->ListTemplates();

    $menu_template_type = new CmsLayoutTemplateType();
    $menu_template_type->set_originator($this->GetName());
    $menu_template_type->set_name('navigation');
    $menu_template_type->set_dflt_flag(TRUE);
    $menu_template_type->set_lang_callback('MenuManager::page_type_lang_callback');
    $menu_template_type->set_content_callback('MenuManager::reset_page_type_defaults');
    $menu_template_type->reset_content_to_factory();
    $menu_template_type->save();

    foreach( $alltemplates as $tplname ) {
      $upgrade_template($detail_template_type,'detail',$tplname,'current_detail_template');
    }
  }
  catch( CmsException $e ) {
    debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
    audit('',$this->GetName(),'Upgrade Error: '.$e->GetMessage());
    return;
  }
}

?>
