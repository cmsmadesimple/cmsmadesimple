<?php

namespace cms_autoinstaller;

use __appbase\utils;
use cms_autoinstaller\utils as utils2;
use cms_autoinstaller\wizard_step;
use Exception;
use function __appbase\get_app;
use function __appbase\lang;
use function __appbase\smarty;

class wizard_step2 extends wizard_step
{
    private function get_cmsms_info($dir)
    {
        if( !$dir ) return [];
        if( !is_dir($dir.'/modules') ) return [];
        if( !is_file($dir.'/version.php') && !is_file("$dir/lib/version.php") ) return [];
        if( !is_file($dir.'/include.php') && !is_file("$dir/lib/include.php") ) return [];
        if( !is_file($dir.'/config.php') ) return [];
        if( !is_file($dir.'/moduleinterface.php') ) return [];

        $info = array();
        if( is_file("$dir/version.php") ) {
            include($dir.'/version.php');
            $info['mtime'] = filemtime($dir.'/version.php');
        } else {
            include("$dir/lib/version.php");
            $info['mtime'] = filemtime($dir.'/lib/version.php');
        }
        $info['version'] = $CMS_VERSION;
        $info['version_name'] = $CMS_VERSION_NAME;
        $info['schema_version'] = $CMS_SCHEMA_VERSION;
        $info['config_file'] = $dir.'/config.php';

        $app = get_app();
        $app_config = $app->get_config();
        if( !isset($app_config['min_upgrade_version']) ) throw new Exception(lang('error_missingconfigvar','min_upgrade_version'));
        if( version_compare($info['version'],$app_config['min_upgrade_version']) < 0 ) $info['error_status'] = 'too_old';
        if( version_compare($info['version'],$app->get_dest_version()) == 0 ) $info['error_status'] = 'same_ver';
        if( version_compare($info['version'],$app->get_dest_version()) > 0 ) $info['error_status'] = 'too_new';

        $fn = $dir.'/config.php';
        include_once($fn);
        $info['config'] = $config;
        if( isset($config['admin_dir']) ) {
            if( $config['admin_dir'] != 'admin' ) throw new Exception(lang('error_admindirrenamed'));
        }
        return $info;
    }

    protected function process()
    {
        if( isset($_REQUEST['install']) ) {
            $this->get_wizard()->set_data('action','install');
        }
        else if( isset($_REQUEST['upgrade']) ) {
            $this->get_wizard()->set_data('action','upgrade');
        }
        else if( isset($_REQUEST['freshen']) ) {
            $this->get_wizard()->set_data('action','freshen');
        }
        else {
            throw new Exception(lang('error_internal',200));
        }
        utils::redirect($this->get_wizard()->next_url());
    }

    protected function display()
    {
        // search for installs of CMSMS.
        parent::display();
        $app = get_app();
        $config = $app->get_config();

        $rpwd = get_app()->get_destdir();
        $info = $this->get_cmsms_info($rpwd);
        $wizard = $this->get_wizard();
        $smarty = smarty();
        $smarty->assign('pwd',$rpwd);
        $smarty->assign('nofiles',$config['nofiles']);

        if( $info ) {
            // its an upgrade
            $wizard->set_data('version_info',$info);
            $smarty->assign('cmsms_info',$info);
            if( !isset($info['error_status']) || $info['error_status'] != 'same_ver' ) {
                $versions = utils2::get_upgrade_versions();
                $out = array();
                foreach( $versions as $version ) {
                    if( version_compare($version,$info['version']) < 1 ) continue;
                    $readme = utils2::get_upgrade_readme($version);
                    $changelog = utils2::get_upgrade_changelog($version);
                    if( $readme || $changelog ) $out[$version] = array('readme'=>$readme,'changelog'=>$changelog);
                }
                $smarty->assign('upgrade_info',$out);
            }
        }
        else {
            // looks like a new install
            // double check for the phar stuff.
            if( is_dir($rpwd.'/app') && is_file($rpwd.'/index.php') && is_dir($rpwd.'/lib') && is_file($rpwd.'/app/class.cms_install.php') ) {
                // should never happen except if you're working on this project.
                throw new Exception(lang('error_invalid_directory'));
            }

            $is_dir_empty = function($dir,$phar_url) {
                if( !$dir ) return FALSE;
                if( !is_dir($dir) ) return FALSE;
                $files = glob($dir.'/*');
                if( !count($files) ) return TRUE;
                if( count($files) > 3 ) return FALSE;
                // trivial check for index.html
                foreach( $files as $file ) {
                    $bn = strtolower(basename($file));
                    if( fnmatch('index.htm*',$bn) ) continue; // this is okay
                    if( fnmatch('readme*.txt',$bn) ) continue; // this is okay
                    if( $phar_url ) {
                        $phar_bn = basename( $phar_url );
                        if( fnmatch( $phar_bn, $bn ) ) continue; // this is okay
                    }
                    // found a not-okay file.
                    return FALSE;
                }
                return TRUE;
            };
            $list_files = function($dir,$n = 5) {
                $n = max(1,min(100,$n));
                if( !$dir ) return;
                if( !is_dir($dir) ) return;
                $files = glob($dir.'/*');
                $files = array_slice($files,0,$n);
                foreach( $files as &$file ) {
                    $file = basename($file);
                }
                return $files;
            };
            $empty_dir = $is_dir_empty($rpwd,$app->get_phar());
            $existing_files = $list_files($rpwd);
            $smarty->assign('install_empty_dir',$empty_dir);
            $smarty->assign('existing_files',$existing_files);
            $wizard->clear_data('version_info');
        }

        $smarty->assign('retry_url',$_SERVER['REQUEST_URI']);
        $smarty->display('wizard_step2.tpl');
        $this->finish();
    }

} // end of class

?>
