<?php
namespace PressRoom;
use PressRoom;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission(PressRoom::MANAGE_PERM) ) exit;

$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_categories_tab.tpl'), null, null, $smarty );
$cats = $this->categoriesManager()->loadAll();
$tpl->assign('categories', $cats );
$tpl->display();
