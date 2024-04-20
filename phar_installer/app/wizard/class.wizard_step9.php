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
        if( !$destdir ) throw new \RuntimeException(\__appbase\lang('error_internal', 900));

        $this->connect_to_cmsms();

        // upgrade modules
        $this->message(\__appbase\lang('msg_upgrademodules'));
        $modops = \ModuleOperations::get_instance();
        $allmodules = $modops->FindAllModules();
        
        foreach($allmodules as $name)
        {
            // we force all system modules to be loaded, if it's a system module
            // and needs upgrade, then it should automagically upgrade.
            // additionally, upgrade any specific modules specified by the upgrade routine.
            // even if the module is optional we still need to load it... if not queued for install
            // it should be handled by the force load routine.
            
            if($modops->IsSystemModule($name) || $modops->IsQueuedForInstall($name))
            {
                self::verbose(\__appbase\lang('msg_upgrade_module', $name));
                $module = $modops->get_module_instance($name, '', TRUE);
                if(!\is_object($module))
                {
                    $this->error("FATAL ERROR: could not load module {$name} for upgrade");
                }
            }
        }

        // clear the cache
        \cmsms()->clear_cached_files();
        $this->message(\__appbase\lang('msg_clearedcache'));

        // write protect config.php
        @\chmod("$destdir/config.php", 0444);

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
    
    /**
     * @throws \Exception
     */
    public function do_install()
    {
        // create tmp directories
        $app = \__appbase\get_app();
        $destdir = \__appbase\get_app()->get_destdir();
        if( !$destdir ) throw new \RuntimeException(\__appbase\lang('error_internal', 901));
        $this->message(\__appbase\lang('install_createtmpdirs'));
        
        if(!@\mkdir($concurrentDirectory = $destdir . '/tmp/cache', 0777, TRUE) && !\is_dir($concurrentDirectory))
        {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        
        if(!@\mkdir($concurrentDirectory = $destdir . '/tmp/templates_c', 0777, TRUE) && !\is_dir($concurrentDirectory))
        {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $siteinfo = $this->get_wizard()->get_data('siteinfo');
        if( !$siteinfo ) throw new \RuntimeException(\__appbase\lang('error_internal', 902));

        // install modules
        $this->message(\__appbase\lang('install_modules'));
        $this->connect_to_cmsms();
        $modops = \cmsms()->GetModuleOperations();
        $allmodules = $modops->FindAllModules();
        
        # we check if we have the correct version  of modops just in case
        if(! \method_exists($modops, 'IsOptionalSystemModule') )
        {
            throw new \RuntimeException(\__appbase\lang('error_internal', 903));
        }
        
        foreach($allmodules as $name)
        {
            // we force all system modules to be loaded, if it's a system module
            // and needs upgrade, then it should automagically upgrade.
            if($modops->IsSystemModule($name) && !$modops->IsOptionalSystemModule($name))
            {
                # system and mandatory modules should always be loaded
                self::verbose(\__appbase\lang('install_module', $name));
                $module = $modops->get_module_instance($name, '', TRUE);
            }
            else if ($modops->IsOptionalSystemModule($name)
                     && NULL !== $this->get_wizard()->get_data('optional_modules')
                     && \in_array($name, $this->get_wizard()->get_data('optional_modules')))
            {
                # now we handle optional modules.
                ## TODO add a language string here. self::verbose(\__appbase\lang('install_optional_module', $name));
                self::verbose(\__appbase\lang('install_module', $name));
                $module = $modops->get_module_instance($name, '', TRUE);
            }
            
            if(!\is_object($module))
            {
                $this->error("FATAL ERROR: could not load module {$name} for install");
            }
        }

        // write protect config.php
        @\chmod("$destdir/config.php", 0444);

        $adminacct = $this->get_wizard()->get_data('adminaccount');
        $root_url = $app->get_root_url();
        if( !endswith($root_url,'/') ) $root_url .= '/';
        $admin_url = $root_url.'admin';

        if(\is_array($adminacct) && isset($adminacct['emailaccountinfo']) && $adminacct['emailaccountinfo'] && isset($adminacct['emailaddr']) && $adminacct['emailaddr'] ) {
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
                $body = \html_entity_decode($body, \ENT_QUOTES);
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
        if( !$destdir ) throw new \RuntimeException(\__appbase\lang('error_internal', 901));
        $this->message(\__appbase\lang('install_createtmpdirs'));
        
        if(!\mkdir($concurrentDirectory = $destdir . '/tmp/cache', 0777, TRUE) && !\is_dir($concurrentDirectory))
        {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        
        if(!mkdir($concurrentDirectory = $destdir . '/tmp/templates_c', 0777, TRUE) && !is_dir($concurrentDirectory))
        {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        // write protect config.php
        @\chmod("$destdir/config.php", 0444);

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
        if( \is_file("$destdir/lib/include.php") ) {
            include_once($destdir.'/lib/include.php');
        }
        else {
            // do not need to test /include.php as if it still exists, it is bad... and 
            // and it should have been deleted by now.
            throw new \RuntimeException("Could not find $destdir/lib/include.php");
        }
        $config = \cms_config::get_instance();

        // we do this here, because the config.php class may not set the define when in an installer.
        if( !\defined('CMS_DB_PREFIX')) \define('CMS_DB_PREFIX', $config['db_prefix']);
    }
    
    /**
     * @throws \SmartyException
     */
    protected function display()
    {
        $app = \__appbase\get_app();
        $smarty = \__appbase\smarty();

        // display the template right off the bat.
        parent::display();
        $smarty->assign('back_url',$this->get_wizard()->prev_url());
        $smarty->display('wizard_step9.tpl');
        $destdir = $app->get_destdir();
        if( !$destdir ) throw new \RuntimeException(\__appbase\lang('error_internal', 903));


        // here, we do either the upgrade, or the install stuff.
        try {
            $action = $this->get_wizard()->get_data('action');
            $tmp = $this->get_wizard()->get_data('version_info');
            if('upgrade' == $action && \is_array($tmp) && \count($tmp) ) {
                $this->do_upgrade($tmp);
            }
            else if('freshen' == $action) {
                $this->do_freshen();
            }
            else if('install' == $action) {
                $this->do_install();
            }
            else {
                throw new \RuntimeException(\__appbase\lang('error_internal', 910));
            }

            // clear the session.
            $sess = \__appbase\session::get();
            $sess::clear();

            $this->finish();
        }
        catch( \Exception $e ) {
            $this->error($e->GetMessage());
        }

        $app->cleanup();
    }

} // end of class

?>
