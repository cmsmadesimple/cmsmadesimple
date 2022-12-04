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

$query = "UPDATE ".CMS_DB_PREFIX."modules SET active = ? WHERE module_name = ?";
$dbr = $db->Execute($query, array($state,$module));
if( !$dbr ) {
  $this->SetError($this->Lang('error_active_failed'));
  $this->RedirectToAdminTab();
}

cmsms()->clear_cached_files();
if( $state ) {
  $this->SetMessage($this->Lang('msg_module_activated',$module));
  audit('',$this->GetName(),'Activated module '.$module);
}
else {
  $this->SetMessage($this->Lang('msg_module_deactivated',$module));
  audit('',$this->GetName(),'Dectivated module '.$module);
}
$this->RedirectToAdminTab();

?>