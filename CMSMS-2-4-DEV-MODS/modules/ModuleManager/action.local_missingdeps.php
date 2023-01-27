<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('installed');
if( !isset($params['mod']) ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}
$module = get_parameter_value($params,'mod');

$info = ModuleManagerModuleInfo::get_module_info($module);
$smarty->assign('back_url',$this->create_url($id,'defaultadmin',$returnid));
$smarty->assign('info',$info);

echo $this->ProcessTemplate('local_missingdeps.tpl');
#
# EOF
#
?>