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

    $upgrade_template = function($type,$prefix,$tplname,$currentdflt) use (&$mod,&$_fix_name,$uid) {
        $orig_tplname = $tplname;

        if( !startswith($tplname,$prefix) ) return;
        $contents = $mod->GetTemplate($orig_tplname);
        if( !$contents ) return;
        $prototype = substr($tplname,strlen($prefix));
        $prototype = $_fix_name($prototype);

        try {
            $tpl = new CmsLayoutTemplate();
            $tpl->set_name($tpl::generate_unique_name($prototype,'MM-'));
            $tpl->set_owner($uid);
            $tpl->set_content($contents);
            $tpl->set_type($type);
            $tpl->set_type_dflt($orig_tplname == $currentdflt);
            $tpl->save();

            $mod->DeleteTemplate($orig_tplname);
        }
        catch( \CmsInvalidDataException $e ) {

        }
    };

    try {
        $mod = $this;

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
        catch( \CmsInvalidDataException $e ) {
            // ignore this error.
        }

        // convert a default file template to a database template (very temporarily... it's deleted below).
        $default_template = $this->GetPreference('default_template','simple_navigation.tpl');
        if( $default_template && endswith($default_template,'.tpl') ) {
            $tpl = $this->GetTemplateFromFile($default_template);
            $this->SetTemplate('mm-default',$tpl);
            $default_template = 'mm-default';
        }

        $alltemplates = $this->ListTemplates();
        foreach( $alltemplates as $tplname ) {
            $upgrade_template($menu_template_type,'',$tplname,$default_template);
        }
    }
    catch( CmsException $e ) {
        audit('',$this->GetName(),'Upgrade Error: '.$e->GetMessage());
        return;
    }
}

?>
