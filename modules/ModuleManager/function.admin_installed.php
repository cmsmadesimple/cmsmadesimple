<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;

$allmoduleinfo = ModuleManagerModuleInfo::get_all_module_info();
$smarty->assign('allow_export',isset($config['developer_mode'])?1:0);
$smarty->assign('module_info',$allmoduleinfo);
$smarty->assign('allow_modman_uninstall',$this->GetPreference('allowuninstall',0));
echo $this->ProcessTemplate('admin_installed.tpl');

?>