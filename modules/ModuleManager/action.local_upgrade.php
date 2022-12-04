<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('installed');

$mod = get_parameter_value($params,'mod');
if( !$mod ) {
  $this->SetError($this->Lang('error_missingparams'));
  $this->RedirectToAdminTab();
}

$ops = ModuleOperations::get_instance();
$result = $ops->UpgradeModule($mod);
if( !is_array($result) || !isset($result[0]) ) $result = array(FALSE,$this->Lang('error_moduleupgradefailed'));

if( $result[0] == FALSE ) {
  $this->SetError($result[1]);
  $this->RedirectToAdminTab();
}

$this->SetMessage($this->Lang('msg_module_upgraded',$mod));
$this->RedirectToAdminTab();

?>