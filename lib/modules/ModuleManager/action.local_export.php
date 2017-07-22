<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('installed');
if( !isset($params['mod']) ) {
    $this->SetError($this->Lang('error_missingparam'));
    $this->RedirectToAdminTab();
}
$module = get_parameter_value($params,'mod');

$ops = ModuleOperations::get_instance();
$modinstance = $ops->get_module_instance($module,'',TRUE);
if( !is_object($modinstance) ) {
    $this->SetError($this->Lang('error_getmodule',$module));
    $this->RedirectToAdminTab();
}

$old_display_errors = ini_set('display_errors',0);
$orig_lang = CmsNlsOperations::get_current_language();
CmsNlsOperations::set_language('en_US');
$files = 0;
$message = '';

\CMSMS\HookManager::do_hook('ModuleManager::BeforeModuleExport', [ 'module_name' => $module, 'version' => $modinstance->GetVersion() ] );
$xmltext = $ops->CreateXMLPackage($modinstance,$message,$files);
CmsNlsOperations::set_language($orig_lang);
if( $old_display_errors !== FALSE ) ini_set('display_errors',$old_display_errors);

if( !$files ) {
    $this->SetMessage('error_moduleexport');
}
else {
    $xmlname = $modinstance->GetName().'-'.$modinstance->GetVersion().'.xml';
    audit('',$this->GetName(),'Exported '.$modinstance->GetName().' to '.$xmlname);

    // send the file.
    $handlers = ob_list_handlers();
    for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }
    header('Content-Description: File Transfer');
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment; filename='.$xmlname);
    echo $xmltext;
    exit();
}
#
# EOF
#
?>