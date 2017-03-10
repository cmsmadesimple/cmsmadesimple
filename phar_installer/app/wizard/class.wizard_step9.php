<?php

namespace cms_autoinstaller;
use \__appbase;

class wizard_step9 extends \cms_autoinstaller\wizard_step
{
    protected function process()
    {
        // nothing here
    }


    private function do_upgrade($version_info)
    {
        $app = \__appbase\get_app();
        $destdir = $app->get_destdir();
        if( !$destdir ) throw new \Exception(\__appbase\lang('error_internal',800));

        $this->connect_to_cmsms();

        // upgrade modules
        $this->message(\__appbase\lang('msg_upgrademodules'));
        $modops = \ModuleOperations::get_instance();
        $allmodules = $modops->FindAllModules();
        foreach( $allmodules as $name ) {
            // we force all system modules to be loaded, if it's a system module
            // and needs upgrade, then it should automagically upgrade.
            // additionally, upgrade any specific modules specified by the upgrade routine.
            if( $modops->IsSystemModule($name) || $modops->IsQueuedForInstall($name) ) {
                $this->verbose(\__appbase\lang('msg_upgrade_module',$name));
                $module = $modops->get_module_instance($name,'',TRUE);
                if( !is_object($module) ) die('could not get module '.$name);
            }
        }

        // clear the cache
        \cmsms()->clear_cached_files();
        $this->message(\__appbase\lang('msg_clearedcache'));

        // todo: write history

        // set the finished message.
        $app = \__appbase\get_app();
        if( $app->has_custom_destdir() || !$app->in_phar() ) {
            $this->set_block_html('bottom_nav',\__appbase\lang('finished_custom_upgrade_msg'));
        }
        else {
            $url = $app->get_root_url();
            $admin_url = $url;
            if( !endswith($url,'/') ) $admin_url .= '/';
            $admin_url .= 'admin';
            $this->set_block_html('bottom_nav',\__appbase\lang('finished_upgrade_msg', $url, $admin_url));
        }
    }

    public function do_install()
    {
        // create tmp directories
        $app = \__appbase\get_app();
        $destdir = \__appbase\get_app()->get_destdir();
        if( !$destdir ) throw new \Exception(\__appbase\lang('error_internal',801));
        $this->message(\__appbase\lang('install_createtmpdirs'));
        @mkdir($destdir.'/tmp/cache',0777,TRUE);
        @mkdir($destdir.'/tmp/templates_c',0777,TRUE);

        $siteinfo = $this->get_wizard()->get_data('siteinfo');
        if( !$siteinfo ) throw new \Exception(\__appbase\lang('error_internal',802));

        // install modules
        $this->message(\__appbase\lang('install_modules'));
        $this->connect_to_cmsms();
        $modops = \cmsms()->GetModuleOperations();
        $allmodules = $modops->FindAllModules();
        foreach( $allmodules as $name ) {
            // we force all system modules to be loaded, if it's a system module
            // and needs upgrade, then it should automagically upgrade.
            if( $modops->IsSystemModule($name) ) {
                $this->verbose(\__appbase\lang('install_module',$name));
                $module = $modops->get_module_instance($name,'',TRUE);
            }
        }

        // write protect config.php
        @chmod("$destdir/config.php",0444);

        $adminacct = $this->get_wizard()->get_data('adminaccount');
        $root_url = $app->get_root_url();
        if( !endswith($root_url,'/') ) $root_url .= '/';
        $admin_url = $root_url.'admin';

        if( is_array($adminacct) && isset($adminacct['emailaccountinfo']) && $adminacct['emailaccountinfo'] && isset($adminacct['emailaddr']) && $adminacct['emailaddr'] ) {
            try {
                $this->message(\__appbase\lang('send_admin_email'));
                $mailer = new \cms_mailer();
                $mailer->AddAddress($adminacct['emailaddr']);
                $mailer->SetSubject(\__appbase\lang('email_accountinfo_subject'));
                $body = null;
                if( $app->in_phar() ) {
                    $body = \__appbase\lang('email_accountinfo_message',
                                            $adminacct['username'],$adminacct['password'],
                                            $destdir, $root_url);
                }
                else {
                    $body = \__appbase\lang('email_accountinfo_message_exp',
                                            $adminacct['username'],$adminacct['password'],
                                            $destdir);
                }
                $body = html_entity_decode($body, ENT_QUOTES);
                $mailer->SetBody($body);
                $mailer->Send();
            }
            catch( \Exception $e ) {
                $this->error(\__appbase\lang('error_sendingmail').': '.$e->GetMessage());
            }

        }

        // todo: set initial preferences.

        // todo: write history

        \cmsms()->clear_cached_files();
        $this->message(\__appbase\lang('msg_clearedcache'));

        // set the finished message.
        if( !$root_url || !$app->in_phar() ) {
            // find the common part of the SCRIPT_FILENAME and the destdir
            // /var/www/phar_installer/index.php
            // /var/www/foo
            $this->set_block_html('bottom_nav',\__appbase\lang('finished_custom_install_msg'));
        }
        else {
            if( endswith($root_url,'/') ) $admin_url = $root_url.'admin';
            $this->set_block_html('bottom_nav',\__appbase\lang('finished_install_msg',$root_url,$admin_url));
        }
    }

    private function do_freshen()
    {
        // create tmp directories
        $app = \__appbase\get_app();
        $destdir = \__appbase\get_app()->get_destdir();
        if( !$destdir ) throw new \Exception(\__appbase\lang('error_internal',801));
        $this->message(\__appbase\lang('install_createtmpdirs'));
        @mkdir($destdir.'/tmp/cache',0777,TRUE);
        @mkdir($destdir.'/tmp/templates_c',0777,TRUE);
        
        // write protect config.php
        @chmod("$destdir/config.php",0444);

        // clear the cache
        $this->connect_to_cmsms();
        \cmsms()->clear_cached_files();
        $this->message(\__appbase\lang('msg_clearedcache'));

        // todo: write history

        // set the finished message.
        if( $app->has_custom_destdir() ) {
            $this->set_block_html('bottom_nav',\__appbase\lang('finished_custom_freshen_msg'));
        }
        else {
            $url = $app->get_root_url();
            $admin_url = $url;
            if( !endswith($url,'/') ) $admin_url .= '/';
            $admin_url .= 'admin';
            $this->set_block_html('bottom_nav',\__appbase\lang('finished_freshen_msg', $url, $admin_url ));
        }
    }

    private function connect_to_cmsms()
    {
        // this loads the standard CMSMS stuff, except smarty cuz it's already done.
        // we do this here because both upgrade and install stuff needs it.
        global $CMS_INSTALL_PAGE, $DONT_LOAD_SMARTY, $CMS_VERSION, $CMS_PHAR_INSTALLER;
        $CMS_INSTALL_PAGE = 1;
        $CMS_PHAR_INSTALLER = 1;
        $DONT_LOAD_SMARTY = 1;
        $CMS_VERSION = $this->get_wizard()->get_data('destversion');
        $app = \__appbase\get_app();
        $destdir = $app->get_destdir();
        if( is_file("$destdir/include.php") ) {
            include_once($destdir.'/include.php');
        } else {
            include_once($destdir.'/lib/include.php');
        }
        $config = \cms_config::get_instance();

        // we do this here, because the config.php class may not set the define when in an installer.
        if( !defined('CMS_DB_PREFIX')) define('CMS_DB_PREFIX',$config['db_prefix']);
    }

    protected function display()
    {
        $app = \__appbase\get_app();
        $smarty = \__appbase\smarty();

        // display the template right off the bat.
        parent::display();
        $smarty->assign('back_url',$this->get_wizard()->prev_url());
        $smarty->display('wizard_step9.tpl');
        $destdir = $app->get_destdir();
        if( !$destdir ) throw new \Exception(\__appbase\lang('error_internal',803));


        // here, we do either the upgrade, or the install stuff.
        try {
            $action = $this->get_wizard()->get_data('action');
            $tmp = $this->get_wizard()->get_data('version_info');
            if( $action == 'upgrade' && is_array($tmp) && count($tmp) ) {
                $this->do_upgrade($tmp);
            }
            else if( $action == 'freshen' ) {
                $this->do_freshen();
            }
            else if( $action == 'install' ) {
                $this->do_install();
            }
            else {
                throw new \Exception(\__appbase\lang('error_internal',810));
            }

            // clear the session.
            $sess = \__appbase\session::get();
            $sess->clear();

            $this->finish();
        }
        catch( \Exception $e ) {
            $this->error($e->GetMessage());
        }

        $app->cleanup();
    }

} // end of class

?>