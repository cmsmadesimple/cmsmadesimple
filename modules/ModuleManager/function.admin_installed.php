<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;

function get_all_module_info()
{
  $ops = ModuleOperations::get_instance();
  $allknownmodules = $ops->FindAllModules();
  
  $out = array();
  foreach( $allknownmodules as $module_name ) {
    $info = new ModuleManagerModuleInfo($module_name,TRUE);
    $out[] = $info;
  }
  return $out;
}

$allmoduleinfo = get_all_module_info();
$smarty->assign('allow_export',isset($config['modulemanager_debug'])?1:0);
$smarty->assign('module_info',$allmoduleinfo);
echo $this->ProcessTemplate('admin_installed.tpl');

?>