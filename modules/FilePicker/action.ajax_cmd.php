<?php
use \FilePicker\TemporaryProfileStorage;
use \FilePicker\PathAssistant;

if( !isset($gCms) ) exit;
try {
    if( strtolower($_SERVER['REQUEST_METHOD']) != 'post' ) {
        throw new \RuntimeException('Invalid request method');
    }

    $sig = cleanValue(get_parameter_value($_POST,'sig'));
    $cmd = cleanValue(get_parameter_value($_POST,'cmd'));
    $val = cleanValue(get_parameter_value($_POST,'val'));
    $cwd = cleanValue(get_parameter_value($_POST,'cwd'));

    // get the profile.
    $profile = null;
    if( $sig ) $profile = TemporaryProfileStorage::get($sig);
    if( !$profile ) $profile = $this->get_default_profile();

    // check the cwd make sure it is okay
    $topdir = $profile->top;
    if( !$topdir ) $topdir = $config['uploads_path'];
    $assistant = new PathAssistant($config,$topdir);

    $fullpath = $assistant->to_absolute($cwd);
    if( ! $assistant->is_relative($fullpath) ) throw new \RuntimeException('Invalid cwd '.$cwd);

    switch( $cmd ) {
    case 'mkdir':
        if( startswith($val,'.') || startswith($val,'_') ) throw new \RuntimeException($this->Lang('error_ajax_invalidfilename'));
        if( !is_writable($fullpath) ) throw new \RuntimeException($this->Lang('error_ajax_writepermission'));
        $destpath = $fullpath.'/'.$val;
        if( is_dir($destpath) || is_file($destpath) ) throw new \RuntimeException($this->Lang('error_ajax_fileexists'));
        if( !@mkdir($destpath) ) throw new \RuntimeException($this->Lang('error_ajax_mkdir ',$cwd.'/'.$val));
        break;

    case 'del':
        if( startswith($val,'.') || startswith($val,'_') ) throw new \RuntimeException($this->Lang('error_ajax_invalidfilename'));
        //if( !is_writable($fullpath) ) throw new \RuntimeException($this->Lang('error_ajax_writepermission'));
        $destpath = $fullpath.'/'.$val;
        if( !is_writable($destpath) ) throw new \RuntimeException($this->Lang('error_ajax_writepermission').' '.$destpath);
        if( is_dir($destpath) ) {
            // check if the directory is empty
            if( count(scandir($destpath)) > 2 ) throw new \RuntimeException($this->Lang('error_ajax_dirnotempty'));
            @rmdir($destpath);
        } else {
            @unlink($destpath);
        }
        break;

    default:
        throw new \RuntimeException('Invalid cmd '.$cmd);
    }
}
catch( \Exception $e ) {
    // throw a 500 error
    \cge_utils::log_exception($e);
    header("HTTP/1.1 500 ".$e->GetMessage());
}
exit();