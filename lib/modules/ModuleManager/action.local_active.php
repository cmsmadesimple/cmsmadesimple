<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('installed');
if( !isset($params['mod']) ) {
    $this->SetError($this->Lang('error_missingparam'));
    $this->RedirectToAdminTab();
}
$state = 0;
if( isset($params['state']) ) $state = (int)$params['state'];
$module = trim(get_parameter_value($params,'mod'));
$ops = ModuleOperations::get_instance();

$res = $ops->ActivateModule( $module, $state );
if( !$res ) {
    $this->SetError($this->Lang('error_active_failed'));
    $this->RedirectToAdminTab();
}

if( $state ) {
  $this->SetMessage($this->Lang('msg_module_activated',$module));
}
else {
  $this->SetMessage($this->Lang('msg_module_deactivated',$module));
}
$this->RedirectToAdminTab();
