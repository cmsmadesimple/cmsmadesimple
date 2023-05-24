<?php

namespace cms_autoinstaller;

use Exception;
use function __appbase\get_app;
use function __appbase\lang;

final class utils
{
    private function __construct() {}

    // get the list of versions we can upgrade from.
    public static function get_upgrade_versions()
    {
        $app = get_app();
        $app_config = $app->get_config();
        $min_upgrade_version = $app_config['min_upgrade_version'];
        if( !$min_upgrade_version ) throw new Exception(lang('error_invalidconfig'));

        $dir = $app->get_appdir().'/upgrade';
        if( !is_dir($dir) ) throw new Exception(lang('error_internal','u100'));

        $dh = opendir($dir);
        $versions = array();
        if( !$dh ) throw new Exception(lang('error_internal',712));
        while( ($file = readdir($dh)) !== false ) {
            if( $file == '.' || $file == '..' ) continue;
            if( is_dir($dir.'/'.$file) &&
                (is_file("$dir/$file/MANIFEST.DAT.gz") || is_file("$dir/$file/MANIFEST.DAT") || is_file("$dir/$file/upgrade.php")) ) {
                if( version_compare($min_upgrade_version, $file) <= 0 ) $versions[] = $file;
            }
        }
        closedir($dh);
        if( count($versions) ) {
            usort($versions,'version_compare');
            return $versions;
        }
    }

    public static function get_upgrade_changelog($version)
    {
        // it is not an error to not have a changelog file
        $app = get_app();
        $dir = $app->get_appdir()."/upgrade/$version";
        if( !is_dir($dir) ) throw new Exception(lang('error_internal','u100'));
        $files = array('CHANGELOG.txt','CHANGELOG.TXT','changelog.txt');
        foreach( $files as $fn ) {
            if( is_file("$dir/$fn") ) {
                // convert text into some sort of html
                $tmp = @file_get_contents("$dir/$fn");
                $tmp = nl2br(wordwrap(htmlspecialchars($tmp),80));
                return $tmp;
            }
        }
    }

    public static function get_upgrade_readme($version)
    {
        // it is not an error to not have a readme file
        $app = get_app();
        $dir = $app->get_appdir()."/upgrade/$version";
        if( !is_dir($dir) ) throw new Exception(lang('error_internal','u100'));
        $files = array('README.HTML.INC','readme.html.inc','README.HTML','readme.html');
        foreach( $files as $fn ) {
            if( is_file("$dir/$fn") ) return @file_get_contents("$dir/$fn");
        }
        if( is_file("$dir/readme.txt") ) {
            // convert text into some sort of html.
            $tmp = @file_get_contents("$dir/readme.txt");
            $tmp = nl2br(wordwrap(htmlspecialchars($tmp),80));
            return $tmp;
        }
    }
} // end of class

?>
