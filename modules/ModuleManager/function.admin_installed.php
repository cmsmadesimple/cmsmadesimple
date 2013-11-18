<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;

$allmoduleinfo = ModuleManagerModuleInfo::get_all_module_info();
$smarty->assign('allow_export',isset($config['modulemanager_debug'])?1:0);
$smarty->assign('module_info',$allmoduleinfo);
echo $this->ProcessTemplate('admin_installed.tpl');

?>