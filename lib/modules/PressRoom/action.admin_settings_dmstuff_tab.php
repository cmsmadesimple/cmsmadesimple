<?php
namespace PressRoom;
use PressRoom;
use CmsLayoutTemplateType;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) exit;

if( isset($_POST['create']) ) {
    try {
        TemplateTypeAssistant::create_dm_types();
        $this->RedirectToAdminTab('categories',null,'admin_settings');
    }
    catch( CmsException $e ) {
        // log it
        echo $this->ShowErrors($e->GetMessage());
        audit('',$this->GetName(),'Problem creating template types: '.$e->GetMessage());
    }
}

$tmp = CmsLayoutTemplateType::load_all_by_originator('PressRoom');
$have_dmsetup = is_array($tmp) && count($tmp);

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_dmstuff_tab.tpl'), null, null, $smarty );
$tpl->assign('have_dmsetup',$have_dmsetup);
$tpl->display();
