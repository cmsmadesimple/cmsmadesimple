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
$result = recursive_delete( $dir );

if( !$result ) {
  $this->SetError($this->Lang('error_moduleremovefailed'));
}
else {
  audit('',$this->GetName(),'Module '.$module.' removed');
  $this->SetMessage($this->Lang('msg_module_removed'));
}

$this->RedirectToAdminTab();

?>