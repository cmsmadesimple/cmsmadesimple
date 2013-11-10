<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;

function get_all_module_info()
{
  $ops = ModuleOperations::get_instance();
  $allknownmodules = $ops->FindAllModules();
  
  $out = array();
  foreach( $allknownmodules as $module_name ) {
    $info = new CmsExtendedModuleInfo($module_name,TRUE);
    $out[] = $info;
  }
  return $out;
}

$allmoduleinfo = get_all_module_info();
for( $i = 0; $i < count($allmoduleinfo); $i++ ) {
  $rec = $allmoduleinfo[$i];
  $tmp = version_compare($rec['installed_version'],$rec['version']);
  if( $tmp < 0 ) {
    $rec['status'] = 'need_upgrade';
  } else if( $tmp > 0 ) {
    $rec['status'] = 'db_newer';
  }
}

$smarty->assign('allow_export',isset($config['modulemanager_debug'])?1:0);
$smarty->assign('module_info',$allmoduleinfo);
echo $this->ProcessTemplate('admin_installed.tpl');

?>