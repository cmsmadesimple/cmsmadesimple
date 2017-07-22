<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('installed');
if( !isset($params['mod']) ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}
$module = get_parameter_value($params,'mod');

$dir = cms_join_path($config['root_path'],'modules',$module);
$result = chmod_r($dir,0777);
if( !$result ) {
  $this->SetError($this->Lang('error_chmodfailed'));
}
else {
  audit('',$this->GetName(),'Changed permissions on '.$module.' directory');
  $this->SetMessage($this->Lang('msg_module_chmod'));
}

$this->RedirectToAdminTab();
?>