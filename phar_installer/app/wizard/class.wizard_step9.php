<?php
namespace cms_autoinstaller;
use \CmsLayoutTemplateType;
use \CmsLayoutTemplate;
use \Exception;
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
        if( !$destdir ) throw new \Exception(\__appbase\lang('error_internal',900));

        $this->connect_to_cmsms();

        // upgrade modules
        $this->message(\__appbase\lang('msg_upgrademodules'));
        $modops = \ModuleOperations::get_instance();
        $allmodules = $modops->FindAllModules();
        foreach( $allmodules as $name ) {
            // we force all system modules to be loaded, if it's a system module
            // and needs upgrade, then it should automagically upgrade.
            if( $modops->IsSystemModule($name) ) {
                $this->verbose(\__appbase\lang('msg_upgrade_module',$name));
                $module = $modops->get_module_instance($name,'',TRUE);
                if( !is_object($module) ) {
                    $this->error("FATAL ERROR: could not load module {$name} for upgrade");
                }
            }
        }

        // clear the cache
        \cmsms()->clear_cached_files();
        $this->message(\__appbase\lang('msg_clearedcache'));

        // write protect config.php
        @chmod("$destdir/config.php",0444);

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
        if( !$destdir ) throw new \Exception(\__appbase\lang('error_internal',901));
        $this->message(\__appbase\lang('install_createtmpdirs'));
        @mkdir($destdir.'/tmp/cache',0777,TRUE);
        @mkdir($destdir.'/tmp/templates_c',0777,TRUE);

        $siteinfo = $this->get_wizard()->get_data('siteinfo');
        if( !$siteinfo ) throw new \Exception(\__appbase\lang('error_internal',902));

        $destconfig = $this->get_wizard()->get_data('config');
        if( !$destconfig ) throw new \Exception(\__appbase\lang('error_internal',904));

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

        $dmmod = $modops->get_module_instance('DesignManager');
        $cmmod = $modops->get_module_instance('CMSContentManager');
        if( !$dmmod || !$cmmod ) throw new \Exception( \__appbase\lang('error_internal',905) );
        if( !class_exists( '\dm_design_reader' ) ) throw new \Exception( \__appbase\lang('error_internal',906) );

        // make sure we have an uploads/images directory
        $dir = "$destdir/uploads/images";
        if( !is_dir($dir) ) {
            @mkdir($dir,0777,TRUE);
            @touch( $dir.'/index.html' );
        }

        $dir = \__appbase\get_app()->get_appdir().'/install';
        if( ! $destconfig['samplecontent'] ) {
            $this->message(\__appbase\lang('install_defaultcontent'));
            $fn = $dir.'/initial.php';
            include_once($fn);
        } else {
            // install a theme.
            try {
                $dsn = \CmsLayoutCollection::load('simplex');
                $dsn->delete( TRUE );
            }
            catch( \CmsDataNotFoundException $e ) {
                // not an error.
            }

            $this->message(\__appbase\lang('install_theme'));
            $theme_file = 'simplex_theme.zip';
            $theme_name = 'Simplex';

            // copy the filename into the temporary directory for unzipping.
            $src_filename = $dir.DIRECTORY_SEPARATOR.$theme_file;
            $tmp_filename = $app->get_my_tmpdir().DIRECTORY_SEPARATOR.$theme_file;
            $cksum = md5_file($src_filename);
            copy($src_filename,$tmp_filename);
            $cksum2 = md5_file($tmp_filename);
            if( $cksum != $cksum2 ) throw new \Exception( \__appbase\lang('error_internal', 907 ));
            $theme_dest_dir = $destdir.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'themes';
            if( !is_dir($theme_dest_dir)) mkdir($theme_dest_dir,0777,true);

            // unzip the archive
            $zip = new \ZipArchive;
            if( $zip->open($tmp_filename) === TRUE ) {
                $zip->extractTo( $theme_dest_dir );
                $zip->close();
            }
            $theme_dest_dir = $destdir.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme_name;

            // get the exported templates
            $theme_json_file = $theme_dest_dir.DIRECTORY_SEPARATOR.'theme.json';
            if( !is_file($theme_json_file) ) throw new \CmsDataNotFoundException('could not find theme.json in '.$theme_dest_dir);
            $theme_json = json_decode(file_get_contents($theme_json_file));
            if( !$theme_json || !isset($theme_json->page_templates) || empty($theme_json->page_templates) ) {
                throw new \CmsDataNotFoundException('invalid theme.json file at '.$theme_json_file);
            }
            if( count($theme_json->page_templates) < 2 || !isset($theme_json->page_templates[0]->template) ) {
                throw new \CmsDataNotFoundException('invalid theme.json file at '.$theme_json_file);
            }
            $dflt_template_rsrc = "cms_theme:$theme_name;".$theme_json->page_templates[0]->template;  // simplex-sub
            $home_template_rsrc = "cms_theme:$theme_name;".$theme_json->page_templates[1]->template;  // simplex-home

            // copy assets/simplex/images to uploads/simplex/images because
            // some of the initial content properties references images in there.
            // TODO: modify the simplex theme to not use additional content blocks, particularly for images
            // so that we don't have to do this cruft
            $this->verbose(\__appbase\lang('msg_copy_theme_images'));
            $fromdir = $theme_dest_dir.'/images';
            $todir = "$destdir/uploads/$theme_name/images";
            if( is_dir( $fromdir ) && is_readable( $fromdir ) && !is_dir( $todir ) ) {
                // copy all files in this directory (not recursively)
                $res = @mkdir( $todir, 0777, true );
                if( $res ) {
                    @touch( $todir.'/index.html' );
                    $files = glob( $fromdir.'/*.*' );
                    for( $i = 0, $n = count($files); $i < $n; $i++ ) {
                        $src = $files[$i];
                        $bn = basename( $src );
                        $dest = $todir.'/'.$bn;
                        @copy( $src, $dest );
                    }
                }
            }

            // import the default content.
            $this->message(\__appbase\lang('install_defaultcontent'));
            $filename = $dir.'/initial_content.xml';
            $contentops = \ContentOperations::get_instance();
            $reader = new \__appbase\content_reader( $filename, $contentops);
            $reader->set_template_callback( function( \ContentBase $obj ) use($dflt_template_rsrc, $home_template_rsrc) {
                    // callback to get the template resource
                    // given a content object.
                    if( $obj->Name() == 'Home' ) return $home_template_rsrc;
                    return $dflt_template_rsrc;
                });
            $reader->import();
        }

        // update all hierarchy positioss
        $this->message(\__appbase\lang('install_updatehierarchy'));
        $contentops = cmsms()->GetContentOperations();
        $contentops->SetAllHierarchyPositions();

        // write protect config.php
        @chmod("$destdir/config.php",0444);

        $adminacct = $this->get_wizard()->get_data('adminaccount');
        $root_url = $app->get_root_url();
        if( !endswith($root_url,'/') ) $root_url .= '/';
        $admin_url = $root_url.'admin';

        if( is_array($adminacct) && isset($adminacct['emailaccountinfo']) && $adminacct['emailaccountinfo']
            && isset($adminacct['emailaddr']) && $adminacct['emailaddr'] ) {
            try {
                $this->message(\__appbase\lang('send_admin_email'));
                $mailer = new \cms_mailer(true,false);
                $mailer->SetFrom('root@localhost.localdomain');
                $mailer->SetFromName('CMSMS Installer');
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
        if( !$destdir ) throw new \Exception(\__appbase\lang('error_internal',901));
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
        if( is_file("$destdir/lib/include.php") ) {
            include_once($destdir.'/lib/include.php');
        }
        else {
            // do not need to test /include.php as if it still exists, it is bad... and
            // and it should have been deleted by now.
            throw new \RuntimeException("Could not find $destdir/lib/include.php");
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
        if( !$destdir ) throw new \Exception(\__appbase\lang('error_internal',903));


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
                throw new \Exception(\__appbase\lang('error_internal',910));
            }

            // clear the session.
            // $sess = \__appbase\session::get();
            // $sess->clear();

            $this->finish();
        }
        catch( \Exception $e ) {
            $this->error($e->GetMessage());
        }

        $app->cleanup(); // for debug, remove me.
    }

} // end of class
