<?php
namespace News2;
use News2;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) exit;

$field_type_list = $this->fieldTypeManager()->getList();

$fielddefs = $this->fielddefManager()->loadAll();
$tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_settings_fielddefs_tab.tpl'), null, null, $smarty );
$tpl->assign('field_type_list',$field_type_list);
$tpl->assign('fielddefs',$fielddefs);
$tpl->display();
