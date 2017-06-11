<?php
status_msg('Performing directory changes for CMSMS 2.2.1');

$destdir = \__appbase\get_app()->get_destdir();
$plugins_from = $destdir.'/plugins';
if( !is_dir($plugins_from) ) return;
$plugins_to = $destdir.'/assets/plugins';
$files = glob($plugins_from.'/*');
if( !count($files) ) return;

// check permissions
if( !is_dir($plugins_to) || !is_writable($plugins_to) ) {
    error_msg('Note: Could not move plugins to /assets/plugins because of permissions in the destination directory');
    return;
}
foreach( $files as $filespec ) {
    if( !is_writable( $filespec ) ) {
        error_msg('Note: Could not move plugins to /assets/plugisn because because of permissions in the source directory');
        return;
    }
}

$remove = function( $in ) {
    if( is_file( $in ) ) {
        @unlink($in);
    }
    else if( is_dir( $in ) ) {
        \__appbase\utils::rrmdir($in);
    }
};

// move the files
foreach( $files as $src_name ) {
    $bn = basename($src_file);
    $dest_name = $plugins_to.'/'.$bn;
    if( ! is_file($dest_name) && !is_dir($dest_name) ) rename( $src_name, $dest_name );
    $remove( $src_name );
}

// maybe remove the directory
$files = glob($plugins_from.'/*');
$do_remove = false;
if( count($files) == 0 ) $remove = true;
if( count($files) == 1 ) {
    $bn = strtolower(basename($files[0]));
    if( $bn == 'index.html' ) $do_remove == true;
}
if( $do_remove ) \__appbase\utils::rrmdir($plugins_from);
@touch($plugins_to.'/index.html');