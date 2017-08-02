<?php
// destination directory is in $destdir

// in this version we make sure that /assets/modules exists
// and move /modules/MenuManager to /assets/modules/MenuManager
// before the manifest is executed.
// note: that if modules/MenuManager exists as a DELETED file in the manifest
//     then this will cause errors to be reported in the manifest stage, that can be ignored.
//     use the option "--dnd modules/MenuManager" when creating the manifest to prevent this.

$assets_modules = "$destdir/assets/modules";
$src_folder = "$destdir/modules/MenuManager";
$dest_folder = "$assets_modules/MenuManager";
if( is_dir( $src_folder) && !is_dir($dest_folder) ) {
    verbose_msg("Moving Menumanger to $assets_modules");
    if( !is_dir($assets_modules) ) {
        $res = mkdir( $assets_modules, 0777, true );
        if( !$res ) throw new \RuntimeException('Problem creating directory at '.$assets_modules);
    }
    rename( $src_folder, $dest_folder );
}