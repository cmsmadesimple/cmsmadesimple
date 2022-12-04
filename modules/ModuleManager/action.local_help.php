<?php
if( !isset($gCms) ) exit;
//if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('installed');
if( !isset($params['mod']) ) {
    $this->SetError($this->Lang('error_missingparam'));
    $this->RedirectToAdminTab();
}
$module = get_parameter_value($params,'mod');
$lang = get_parameter_value($params,'lang');

// get the module instance... force it to load if necessary.
$ops = ModuleOperations::get_instance();
$modinstance = $ops->get_module_instance($module,'',TRUE);
if( !is_object($modinstance) ) {
    $this->SetError($this->Lang('error_getmodule',$module));
    $this->RedirectToAdminTab();
}
$theme = cms_utils::get_theme_object();
$theme->SetTitle('module_help');

$our_lang = CmsNlsOperations::get_current_language();
$smarty->assign('our_lang',$our_lang);

if( $our_lang != 'en_US' ) {
    if( $lang != '' ) {
        $smarty->assign('mylang_text',$this->Lang('display_in_mylanguage'));
        $smarty->assign('mylang_url',$this->create_url($id,'local_help',$returnid,array('mod'=>$module)));
        CmsNlsOperations::set_language('en_US');
    }
    else {
        $yourlang_url = $this->create_url($id,'local_help',$returnid,array('mod'=>$module,'lang'=>'en_US'));
        $smarty->assign('our_lang',$our_lang);
        $smarty->assign('englang_url',$yourlang_url);
        $smarty->assign('englang_text',$this->Lang('display_in_english'));
    }
}

$smarty->assign('module_name',$modinstance->GetName());
$smarty->assign('friendly_name',$modinstance->GetFriendlyName());

$smarty->assign('help_page',$modinstance->GetHelpPage());
if( $our_lang != 'en_US' && $lang != '' ) {
    CmsNlsOperations::set_language($our_lang);
}

echo $this->ProcessTemplate('local_help.tpl');
