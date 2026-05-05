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

$modinstance = $ops->get_module_instance($mod,'',TRUE);
if( is_object($modinstance) ) {
  modmgr_utils::track_module_event($mod, 'upgrade', $modinstance->GetVersion());
}

$this->SetMessage($this->Lang('msg_module_upgraded',$mod));
$this->RedirectToAdminTab();

?>