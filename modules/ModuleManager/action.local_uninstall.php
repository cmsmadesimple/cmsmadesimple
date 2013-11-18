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
$modinstance = $ops->get_module_instance($mod,'',TRUE);
if( !is_object($modinstance) ) {
  // uh-oh
  $this->SetError($this->Lang('error_getmodule',$mod));
  $this->RedirectToAdminTab();
}

$postmsg = $modinstance->UninstallPostMessage();
if( $postmsg == '' ) $postmsg = $this->Lang('msg_module_uninstalled',$mod);
$result = $ops->UninstallModule($mod);
if( !is_array($result) || !isset($result[0]) ) $result = array(FALSE,$this->Lang('error_moduleuninstallfailed'));

if( $result[0] == FALSE ) {
  $this->SetError($result[1]);
  $this->RedirecToAdminTab();
}

$this->SetMessage($postmsg);
$this->RedirectToAdminTab();

?>