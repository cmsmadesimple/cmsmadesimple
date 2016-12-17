<?php
if (!isset($gCms)) exit;
$db = $this->GetDb();

if( version_compare($oldversion,'2.50') < 0 ) {
    $uid = null;
    if( cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
        $uid = 1; // hardcode to first user
    } else {
        $uid = get_userid();
    }

    $_fix_name = function($str) {
        if( CmsAdminUtils::is_valid_itemname($str) ) return $str;
        $orig = $str;
        $str = trim($str);
        if( !CmsAdminUtils::is_valid_itemname($str[0]) ) $str[0] = '_';
        for( $i = 1; $i < strlen($str); $i++ ) {
            if( !CmsAdminUtils::is_valid_itemname($str[$i]) ) $str[$i] = '_';
        }
        for( $i = 0; $i < 5; $i++ ) {
            $in = $str;
            $str = str_replace('__','_',$str);
            if( $in == $str ) break;
        }
        if( $str == '_' ) throw new \Exception('Invalid name '.$orig.' and cannot be corrected');
        return $str;
    };

    // create template types.
    $upgrade_template = function($type,$prefix,$tplname,$currentdflt,$prefix2) use (&$mod,&$_fix_name,$uid) {
        if( !startswith($tplname,$prefix) ) return;
        $contents = $mod->GetTemplate($tplname);
        if( !$contents ) return;
        $prototype = substr($tplname,strlen($prefix));
        $prototype = $_fix_name($prototype);

        try {
            $tpl = new CmsLayoutTemplate();
            $tpl->set_name($tpl::generate_unique_name($prototype,$prefix2));
            $tpl->set_owner($uid);
            $tpl->set_content($contents);
            $tpl->set_type($type);
            $tpl->set_type_dflt($prototype == $mod->GetPreference($currentdflt));
            $tpl->save();

            $mod->DeleteTemplate($tplname);
        }
        catch( \CmsInvalidDataException $e ) {
        }

  };

  try {
      $dict = NewDataDictionary($db);
      $sqlarray = $dict->AddColumnSQL(CMS_DB_PREFIX.'module_news','searchable I1');
      $dict->ExecuteSQLArray($sqlarray);

      $sqlarray = $dict->AddColumnSQL(CMS_DB_PREFIX.'module_news_categories','item_order I');
      $dict->ExecuteSQLArray($sqlarray);

      $query = "SELECT * FROM ".CMS_DB_PREFIX."module_news_categories ORDER BY parent_id";
      $categories = $db->GetArray($query);

      $uquery = 'UPDATE '.CMS_DB_PREFIX.'module_news_categories SET item_order = ? WHERE news_category_id = ?';
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

      try {
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
      }
      catch( \CmsInvalidDataException $e ) {
          // ignore this error.
      }

      try {
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
      }
      catch( \CmsInvalidDataException $e ) {
          // ignore this error.
      }

      try {
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
      }
      catch( \CmsInvalidDataException $e ) {
          // ignore this error.
      }

      try {
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
      catch( \CmsInvalidDataException $e ) {
          // ignore this error.
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

if( version_compare($oldversion,'2.50.8') < 0 ) {
    try {
        $types = CmsLayoutTemplateType::load_all_by_originator($this->GetName());
        if( is_array($types) && count($types) ) {
            foreach( $types as $type_obj ) {
                $type_obj->set_help_callback('News::template_help_callback');
                $type_obj->save();
            }
        }
    }
    catch( Exception $e ) {
        // log it
        audit('',$this->GetName(),'Uninstall Error: '.$e->GetMessage());
        return FALSE;
    }
}

?>
