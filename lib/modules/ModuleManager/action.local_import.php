<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('installed');

$key = $id.'upload';
if( !isset($_FILES[$key]) ) {
    $this->SetError($this->Lang('error_nofileuploaded'));
    $this->RedirectToAdminTab();
}
$file = $_FILES[$key];
if( $file['error'] || !$file['size'] || !isset($file['tmp_name']) || $file['tmp_name'] == '' ) {
    $this->SetError($this->Lang('error_fileupload'));
    $this->RedirectToAdminTab();
}
if( $file['type'] != 'text/xml' ) {
    $this->SetError($this->Lang('error_notxmlfile'));
    $this->RedirectToAdminTab();
}

try {
    \CMSMS\HookManager::do_hook('ModuleManager::BeforeModuleImport', [ 'file'=>$file['name']] );
    $this->get_operations()->expand_xml_package( $file['tmp_name'], true, false );
    \CMSMS\HookManager::do_hook('ModuleManager::AfterModuleImport', [ 'file'=>$file['name']] );

    audit('',$this->GetName(),'Imported Module '.$file['name']);
    $this->Setmessage($this->Lang('msg_module_imported'));
}
catch( Exception $e ) {
    $this->SetError($e->GetMessage());
}

$this->RedirectToAdminTab();

#
# EOF
#
