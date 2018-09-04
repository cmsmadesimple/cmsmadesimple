<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('installed');

try {
    if( !isset($params['mod']) ) throw new \LogicException( $this->Lang('error_missingparam') );
    $module = get_parameter_value($params,'mod');

    $dirs = $dir = null;
    $dirs[] = cms_join_path( CMS_ROOT_PATH, 'lib', 'modules', $module );
    $dirs[] = cms_join_path( CMS_ASSETS_PATH, 'modules', $module );
    foreach( $dirs as $dir ) {
        if( is_dir( $dir ) ) break;
    }
    if( !$dir ) throw new \RuntimeException( $this->Lang('error_moduleremovefailed') );
    if( !is_directory_writable( $dir ) ) throw new \RuntimeException( $this->Lang('error_moduleremovefailed') );
    $result = recursive_delete( $dir );
    if( !$result ) throw new \RuntimeException( $this->Lang('error_moduleremovefailed') );
    $gCms->clear_cached_files();
    audit('',$this->GetName(),'Module '.$module.' removed');
    $this->SetMessage($this->Lang('msg_module_removed'));
}
catch( \CmsException $e ) {
    $this->SetError( $e->GetMessage() );
}
$this->RedirectToAdminTab();
