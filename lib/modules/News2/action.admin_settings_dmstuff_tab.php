<?php
namespace News2;
use News2;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) exit;

$have_dmsetup = false;
$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_dmstuff_tab.tpl'), null, null, $smarty );
$tpl->assign('have_dmsetup',$have_dmsetup);
$tpl->display();
