<?php
// destination directory is in $destdir
// NOTE: this script is executed BEFORE manifest operations have taken place.

// in this version we make sure that /assets/modules exists
// and move /modules/MenuManager to /assets/modules/MenuManager
// before the manifest is executed.
// note: that if modules/MenuManager exists as a DELETED file in the manifest
//     then this will cause errors to be reported in the manifest stage, that can be ignored.
//     use the option "--dnd modules/MenuManager" when creating the manifest to prevent this.

$dest_dir = "$destdir/assets/modules";
$src_dir = "$destdir/modules";
$cmssystemmodules =  [ 'AdminLog', 'AdminSearch', 'DesignManager', 'CMSContentManager', 'FileManager', 'ModuleManager', 'Search',
		       'News2', 'MicroTiny',
                       'Navigator', 'CmsJobManager', 'FilePicker', 'CoreAdminLogin' ];

// move any directory that a:  Contains a xxxxx.module.php file,   and b: is not in the above list from /modules to /assets/modules

if( is_dir($dest_dir) ) return;
$res = mkdir($dest_dir, 0777, true);
if( !$res ) throw new \RuntimeException('Problem creating directory at '.$assets_modules);

status_msg( "Moving non core modules to assets/modules" );
$dh = opendir( $src_dir );
while( ($file = readdir($dh)) !== false ) {
    if( $file == '.' || $file == '..' ) continue;
    $full_path = "$src_dir/$file";
    if( !is_dir( $full_path ) ) continue;
    if( !is_file( "$full_path/$file.module.php" ) ) continue;
    if( in_array( $file, $cmssystemmodules ) ) continue;

    verbose_msg("Moving module $file to $dest_dir");
    $dest_folder = "$dest_dir/$file";
    rename( $full_path, $dest_folder );
}
