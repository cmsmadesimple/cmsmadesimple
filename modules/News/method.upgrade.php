<?php
if (!isset($gCms)) exit;
$db = $this->GetDb();

$uid = null;
if( cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
  $uid = 1; // hardcode to first user
} else {
  $uid = get_userid();
}

if( version_compare($oldversion,'2.50') < 0 ) {
  // create template types.
  $upgrade_template = function($type,$prefix,$tplname,$currentdflt,$prefix2) use (&$mod,$uid) {
      if( !startswith($tplname,$prefix) ) return;
      $contents = $mod->GetTemplate($tplname);
      if( !$contents ) return;
      $prototype = substr($tplname,strlen($prefix));

      $tpl = new CmsLayoutTemplate();
      $tpl->set_name($tpl::generate_unique_name($prototype,$prefix2));
      $tpl->set_owner($uid);
      $tpl->set_content($contents);
      $tpl->set_type($type);
      $tpl->set_type_dflt($prototype == $mod->GetPreference($currentdflt));

      $tpl->save();

      $mod->DeleteTemplate($tplname);
  };

  try {
      $dict = NewDataDictionary($db);
      $sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_news','searchable I1');
      $dict->ExecuteSQLArray($sqlarray);

      $sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_news_categories','item_order I');
      $dict->ExecuteSQLArray($sqlarray);

      $query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY parent_id";
      $categories = $db->GetArray($query);
      if( is_array($categories) && count($categories) ) {
          $prev_parent = null;
          $item_order = 0;
          foreach( $categories as $row ) {
              $parent = $row['parent_id'];
              if( $parent != $prev_parent ) $item_order = 0;
              $item_order++;
              $db->Execute($uquery,array($item_order,$row['news_category_id']));
          }
      }

      $mod = $this;
      $alltemplates = $this->ListTemplates();

    $summary_template_type = new CmsLayoutTemplateType();
    $summary_template_type->set_originator($this->GetName());
    $summary_template_type->set_name('summary');
    $summary_template_type->set_dflt_flag(TRUE);
    $summary_template_type->set_lang_callback('News::page_type_lang_callback');
    $summary_template_type->set_content_callback('News::reset_page_type_defaults');
    $summary_template_type->reset_content_to_factory();
    $summary_template_type->save();
    foreach( $alltemplates as $tplname ) {
      $upgrade_template($summary_template_type,'summary',$tplname,'current_summary_template','News-Summary-');
    }

    $detail_template_type = new CmsLayoutTemplateType();
    $detail_template_type->set_originator($this->GetName());
    $detail_template_type->set_name('detail');
    $detail_template_type->set_dflt_flag(TRUE);
    $detail_template_type->set_lang_callback('News::page_type_lang_callback');
    $detail_template_type->set_content_callback('News::reset_page_type_defaults');
    $detail_template_type->reset_content_to_factory();
    $detail_template_type->save();
    foreach( $alltemplates as $tplname ) {
      $upgrade_template($detail_template_type,'detail',$tplname,'current_detail_template','News-Detail-');
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
    foreach( $alltemplates as $tplname ) {
      $upgrade_template($form_template_type,'form',$tplname,'current_form_template','News-Form-');
    }

    $browsecat_template_type = new CmsLayoutTemplateType();
    $browsecat_template_type->set_originator($this->GetName());
    $browsecat_template_type->set_name('browsecat');
    $browsecat_template_type->set_dflt_flag(TRUE);
    $browsecat_template_type->set_lang_callback('News::page_type_lang_callback');
    $browsecat_template_type->set_content_callback('News::reset_page_type_defaults');
    $browsecat_template_type->reset_content_to_factory();
    $browsecat_template_type->save();
    foreach( $alltemplates as $tplname ) {
      $upgrade_template($browsecat_template_type,'browsecat',$tplname,'current_browsecat_template','News-Browsecat-');
    }
  }
  catch( CmsException $e ) {
    audit('',$this->GetName(),'Upgrade Error: '.$e->GetMessage());
    return;
  }

  $this->RegisterModulePlugin(TRUE);
  $this->RegisterSmartyPlugin('news','function','function_plugin');
  $this->CreateStaticRoutes();
}

?>