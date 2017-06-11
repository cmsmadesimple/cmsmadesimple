<?php
status_msg('Performing directory changes for CMSMS 2.2.1');

$create_private_dir = function($relative_dir) {
    $app = \__appbase\get_app();
    $destdir = $app->get_destdir();
    $relative_dir = trim($relative_dir);
    if( !$relative_dir ) return;

    $dir = $destdir.'/'.$relative_dir;
    if( !is_dir($dir) ) {
        @mkdir($dir,0777,true);
    }
    @touch($dir.'/index.html');
};

$move_directory_files = function($srcdir,$destdir) {
    $srcdir = trim($srcdir);
    $destdir = trim($destdir);
    if( !is_dir($srcdir) ) return;

    $files = glob($srcdir.'/*');
    if( !count($files) ) return;

    foreach( $files as $src ) {
        $bn = basename($src);
        $dest = $destdir.'/'.$bn;
        rename($src,$dest);
    }
    @touch($dir.'/index.html');
};

$destdir = \__appbase\get_app()->get_destdir();
$plugins_from = $destdir.'/plugins';
$plugins_to = $destdir.'/assets/plugins';
$files = glob($plugins_from.'/*php');
if( !count($files) ) return;

// check permissions
if( !is_dir($plugins_to) || !is_writable($plugins_to) ) {
    error_msg('Note: Could not move plugins to /assets/plugins because of permissions in the destination directory');
    return;
}
foreach( $fileas as $filespec ) {
    if( !is_writable( $filespec ) ) {      
        error_msg('Note: Could not move plugins to /assets/plugisn because because of permissions in the source directory');
        return;
    }
j

// move the files
foreach( $files as $src_file ) {
    $bn = basename($src_file);
    $dest_file = $plugins_to.'/'.$bn;
    if( ! is_file($bn) ) rename( $src_file, $dest_file );
    @unlink( $src_file );
}

// maybe remove the directory
$files = glob($plugins_from.'/*');
$remove = false;
if( count($files) == 0 ) $remove = true;
if( count($files) == 1 ) {
    $bn = strtolower(basename($files[0]));
    if( $bn == 'index.html' ) $remove == true;
}
if( $remove ) recursive_remove($plugins_from);
