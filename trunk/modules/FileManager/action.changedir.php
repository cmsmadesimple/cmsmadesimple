<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['showtemplate']) && $_GET['showtemplate'] == 'false' ) {
  echo filemanager_utils::get_cwd();
  exit;
}

if( !isset($params["newdir"]) && !isset($params['setdir']) ) $this->RedirectToAdminTab();

$path = null;
if( isset($params['newdir']) ) {
    // set a relative directory.
    $newdir = trim($params["newdir"]);
    $path = filemanager_utils::join_path(filemanager_utils::get_cwd(),$newdir);
}
else if( isset($params['setdir']) ) {
    // set an explicit directory
    $path = trim($params['setdir']);
    if( $path == '::top::' ) $path = filemanager_utils::get_default_cwd();
}

try {
    filemanager_utils::set_cwd($path);
    if( !isset($params['ajax']) ) {
        filemanager_utils::set_cwd($path);
        $this->RedirectToAdminTab();
    }
}
catch( Exception $e ) {
    audit('','FileManager','Attempt to set working directory to an invalid location: '.$path);
    if( isset($params['ajax']) ) exit('ERROR');
    $this->SetError($this->Lang('invalidchdir',$path));
    $this->RedirectToAdminTab();
}

if( isset($params['ajax']) ) echo 'OK'; exit;
$this->RedirectToAdminTab();
