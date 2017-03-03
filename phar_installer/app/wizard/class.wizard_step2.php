<?php

namespace cms_autoinstaller;
use \__appbase;

class wizard_step2 extends \cms_autoinstaller\wizard_step
{
    private function get_cmsms_info($dir)
    {
        if( !$dir ) return;
        if( !is_dir($dir.'/modules') ) return;
        if( !is_file($dir.'/version.php') && !is_file("$dir/lib/version.php") ) return;
        if( !is_file($dir.'/include.php') && !is_file("$dir/lib/include.php") ) return;
        if( !is_file($dir.'/config.php') ) return;
        if( !is_file($dir.'/moduleinterface.php') ) return;

        $info = array();
        if( is_file("$dir/version.php") ) {
            include_once($dir.'/version.php');
            $info['mtime'] = filemtime($dir.'/version.php');
        } else {
            include_once("$dir/lib/version.php");
            $info['mtime'] = filemtime($dir.'/lib/version.php');
        }
        $info['version'] = $CMS_VERSION;
        $info['version_name'] = $CMS_VERSION_NAME;
        $info['schema_version'] = $CMS_SCHEMA_VERSION;
        $info['config_file'] = $dir.'/config.php';

        $app = \__appbase\get_app();
        $app_config = $app->get_config();
        if( !isset($app_config['min_upgrade_version']) ) throw new \Exception(\__appbase\lang('error_missingconfigvar','min_upgrade_version'));
        if( version_compare($info['version'],$app_config['min_upgrade_version']) < 0 ) $info['error_status'] = 'too_old';
        if( version_compare($info['version'],$app->get_dest_version()) == 0 ) $info['error_status'] = 'same_ver';
        if( version_compare($info['version'],$app->get_dest_version()) > 0 ) $info['error_status'] = 'too_new';

        $fn = $dir.'/config.php';
        include_once($fn);
        $info['config'] = $config;
        if( isset($config['admin_dir']) ) {
            if( $config['admin_dir'] != 'admin' ) throw new \Exception(\__appbase\lang('error_admindirrenamed'));
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
            throw new \Exception(\__appbase\lang('error_internal',200));
        }
        \__appbase\utils::redirect($this->get_wizard()->next_url());
    }

    protected function display()
    {
        // search for installs of CMSMS.
        parent::display();
        $app = \__appbase\get_app();
        $config = $app->get_config();

        $rpwd = \__appbase\get_app()->get_destdir();
        $info = $this->get_cmsms_info($rpwd);
        $wizard = $this->get_wizard();
        $smarty = \__appbase\smarty();
        $smarty->assign('pwd',$rpwd);
        $smarty->assign('nofiles',$config['nofiles']);

        if( $info ) {
            // its an upgrade
            $wizard->set_data('version_info',$info);
            $smarty->assign('cmsms_info',$info);
            if( !isset($info['error_status']) || $info['error_status'] != 'same_ver' ) {
                $versions = utils::get_upgrade_versions();
                $out = array();
                foreach( $versions as $version ) {
                    if( version_compare($version,$info['version']) < 1 ) continue;
                    $readme = utils::get_upgrade_readme($version);
                    $changelog = utils::get_upgrade_changelog($version);
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
                throw new \Exception(\__appbase\lang('error_invalid_directory'));
            }

            $is_dir_empty = function($dir) {
                if( !$dir ) return FALSE;
                if( !is_dir($dir) ) return FALSE;
                $files = glob($dir.'/*');
                if( !count($files) ) return TRUE;
                if( count($files) > 1 ) return FALSE;
                // trivial check for index.html
                $bn = strtolower(basename($files[0]));
                if( fnmatch('index.htm*',$bn) ) return TRUE;
                return FALSE;
            };
            $smarty->assign('install_empty_dir',$is_dir_empty($rpwd));
            $wizard->clear_data('version_info');
        }

        $smarty->assign('retry_url',$_SERVER['REQUEST_URI']);
        $smarty->display('wizard_step2.tpl');
        $this->finish();
    }

} // end of class

?>
