<?php
if (!isset($gCms)) exit;
$db =$this->GetDb();

$uid = null;
if( cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
  $uid = 1; // hardcode to first user
} else {
  $uid = get_userid();
}

if( version_compare($oldversion,'2.50') < 0 ) {
  // create template types.
  $upgrade_template = function($type,$prefix,$tplname,$currentdflt) use (&$mod,$uid) {
    if( !startswith($tplname,$prefix) ) return;
    $contents = $mod->GetTemplate($tplname);
    if( !$contents ) return;
    $prototype = substr($tplname,strlen($prefix));
    
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($tpl::generate_unique_name($prototype,'News-'));
    $tpl->set_owner($uid);
    $tpl->set_content($contents);
    $tpl->set_type($type);
    $tpl->set_type_dflt($tplname == $mod->GetPreference($currentdflt));

    echo "DEBUG: creating template ".$tpl->get_name()." from $tplname for News<br/>\n";
    $tpl->save();

    $mod->DeleteTemplate($tplname);
  };

  try {
    $mod = $this;
    $alltemplates = $this->ListTemplates();
    print_r($alltemplates); echo '<br/>';
    
    $summary_template_type = new CmsLayoutTemplateType();
    $summary_template_type->set_originator($this->GetName());
    $summary_template_type->set_name('summary');
    $summary_template_type->set_dflt_flag(TRUE);
    $summary_template_type->set_lang_callback('News::page_type_lang_callback');
    $summary_template_type->set_content_callback('News::reset_page_type_defaults');
    $summary_template_type->reset_content_to_factory();
    $summary_template_type->save();
    echo "DEBUG: create summary template type<br/>\n";
    foreach( $alltemplates as $tplname ) {
      $upgrade_template($summary_template_type,'summary',$tplname,'current_summary_template');
    }

    $detail_template_type = new CmsLayoutTemplateType();
    $detail_template_type->set_originator($this->GetName());
    $detail_template_type->set_name('detail');
    $detail_template_type->set_dflt_flag(TRUE);
    $detail_template_type->set_lang_callback('News::page_type_lang_callback');
    $detail_template_type->set_content_callback('News::reset_page_type_defaults');
    $detail_template_type->reset_content_to_factory();
    $detail_template_type->save();
    echo "DEBUG: create detail template type<br/>\n";
    foreach( $alltemplates as $tplname ) {
      $upgrade_template($detail_template_type,'detail',$tplname,'current_detail_template');
    }

    // Setup form template
    $form_template_type = new CmsLayoutTemplateType();
    $form_template_type->set_originator($this->GetName());
    $form_template_type->set_name('form');
    $form_template_type->set_dflt_flag(TRUE);
    $form_template_type->set_lang_callback('News::page_type_lang_callback');
    $form_template_type->set_content_callback('News::reset_page_type_defaults');
    $form_template_type->reset_content_to_factory();
    $form_template_type->save();
    echo "DEBUG: create form template type<br/>\n";
    foreach( $alltemplates as $tplname ) {
      $upgrade_template($form_template_type,'form',$tplname,'current_form_template');
    }

    $browsecat_template_type = new CmsLayoutTemplateType();
    $browsecat_template_type->set_originator($this->GetName());
    $browsecat_template_type->set_name('browsecat');
    $browsecat_template_type->set_dflt_flag(TRUE);
    $browsecat_template_type->set_lang_callback('News::page_type_lang_callback');
    $browsecat_template_type->set_content_callback('News::reset_page_type_defaults');
    $browsecat_template_type->reset_content_to_factory();
    $browsecat_template_type->save();
    echo "DEBUG: create browsecat template type<br/>\n";
    foreach( $alltemplates as $tplname ) {
      $upgrade_template($browsecat_template_type,'browsecat',$tplname,'current_browsecat_template');
    }
  }
  catch( CmsException $e ) {
    debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
    audit('',$this->GetName(),'Upgrade Error: '.$e->GetMessage());
    return;
  }

  $this->RegisterModulePlugin(TRUE);
  $this->RegisterSmartyPlugin('news','function','function_plugin');
  $this->CreateStaticRoutes();
}

?>