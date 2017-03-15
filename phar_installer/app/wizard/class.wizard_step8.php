<?php

namespace cms_autoinstaller;
use \__appbase;


class wizard_step8 extends \cms_autoinstaller\wizard_step
{
    protected function process()
    {
        // nothing here
    }

    private function do_install()
    {
        global $CMS_INSTALL_PAGE, $DONT_LOAD_DB, $DONT_LOAD_SMARTY, $CMS_VERSION, $CMS_PHAR_INSTALLER;
        $CMS_INSTALL_PAGE = 1;
        $DONT_LOAD_DB = 1;
        $DONT_LOAD_SMARTY = 1;
        $CMS_PHAR_INSTALLER = 1;
        $CMS_VERSION = $this->get_wizard()->get_data('destversion');

        $dir = \__appbase\get_app()->get_appdir().'/install';

        $destdir = \__appbase\get_app()->get_destdir();
        if( !$destdir ) throw new \Exception(\__appbase\lang('error_internal',800));

        $adminaccount = $this->get_wizard()->get_data('adminaccount');
        if( !$adminaccount ) throw new \Exception(\__appbase\lang('error_internal',801));

        $destconfig = $this->get_wizard()->get_data('config');
        if( !$destconfig ) throw new \Exception(\__appbase\lang('error_internal',803));
        define('CMS_DB_PREFIX',$destconfig['dbprefix']);

        $siteinfo = $this->get_wizard()->get_data('siteinfo');
        if( !$siteinfo ) throw new \Exception(\__appbase\lang('error_internal',804));

        // setup database connection
        $db = \__appbase\new_db_connection($destconfig['dbtype']);
        $res = $db->Connect($destconfig['dbhost'],$destconfig['dbuser'],$destconfig['dbpass'],$destconfig['dbname']);
        if( !$res ) throw new \Exception(\__appbase\lang('error_dbconnect'));
        $db->Execute("SET NAMES 'utf8'");

        // setup and initialize the cmsms API's
        if( is_file("$destdir/include.php") ) {
            include_once($destdir.'/include.php');
        }
        else {
            include_once($destdir.'/lib/include.php');
        }
        \CmsApp::get_instance()->_setDb($db);

        include_once(__DIR__.'/msg_functions.php');

        try {
            // create some variables that the sub functions need.
            if( !defined('CMS_ADODB_DT') ) define('CMS_ADODB_DT','DT');
            $admin_user = null;
            $db_prefix = CMS_DB_PREFIX;

            // install the schema
            $this->message(\__appbase\lang('install_schema'));
            $fn = $dir.'/schema.php';
            if( !file_exists($fn) ) throw new \Exception(\__appbase\lang('error_internal',803));

            global $CMS_INSTALL_DROP_TABLES, $CMS_INSTALL_CREATE_TABLES;
            $CMS_INSTALL_DROP_TABLES=1;
            $CMS_INSTALL_CREATE_TABLES=1;
            include_once($fn);

            $this->verbose(\__appbase\lang('install_setsequence'));
            include_once($dir.'/createseq.php');

            if( $adminaccount['saltpw'] ) {
                $this->verbose(\__appbase\lang('install_passwordsalt'));
                $salt = substr(str_shuffle(md5($destdir).time()),0,16);
                \cms_siteprefs::set('sitemask',$salt);
            }

            // create tmp directories
            $this->verbose(\__appbase\lang('install_createtmpdirs'));
            @mkdir($destdir.'/tmp/cache',0777,TRUE);
            @mkdir($destdir.'/tmp/templates_c',0777,TRUE);

            include_once($dir.'/base.php');

            $this->message(\__appbase\lang('install_defaultcontent'));
            $fn = $dir.'/initial.php';
            if( $this->get_wizard()->get_data('samplecontent') ) $fn = $dir.'/extra.php';
            include_once($fn);

            $this->verbose(\__appbase\lang('install_setsitename'));
            \cms_siteprefs::set('sitename',$siteinfo['sitename']);

            // create new config file.
            // this step has to go here.... as config file has to exist in step9
            // so that CMSMS can connect to the database.
            $this->message(\__appbase\lang('install_createconfig'));
            $newconfig = cmsms()->GetConfig();
            $newconfig['dbms'] = trim($destconfig['dbtype']);
            $newconfig['db_hostname'] = trim($destconfig['dbhost']);
            $newconfig['db_username'] = trim($destconfig['dbuser']);
            $newconfig['db_password'] = trim($destconfig['dbpass']);
            $newconfig['db_name'] = trim($destconfig['dbname']);
            $newconfig['db_prefix'] = trim($destconfig['dbprefix']);
            $newconfig['timezone'] = trim($destconfig['timezone']);
            if( $destconfig['query_var'] ) $newconfig['query_var'] = trim($destconfig['query_var']);
            if( isset($destconfig['dbport']) ) {
                $num = (int)$destconfig['dbport'];
                if( $num > 0 ) $newconfig['db_port'] = $num;
            }
            $newconfig->save();

            // update all hierarchy positioss
            $this->message(\__appbase\lang('install_updatehierarchy'));
            $contentops = cmsms()->GetContentOperations();
            $contentops->SetAllHierarchyPositions();

            // todo: install default preferences
            set_site_preference('global_umask','022');

        }
        catch( \Exception $e ) {
            $this->error($e->GetMessage());
        }
    }

    private function do_upgrade($version_info)
    {
        global $CMS_INSTALL_PAGE, $DONT_LOAD_DB, $DONT_LOAD_SMARTY, $CMS_VERSION, $CMS_PHAR_INSTALLER;
        $CMS_INSTALL_PAGE = 1;
        $CMS_PHAR_INSTALLER = 1;
        $DONT_LOAD_DB = 1;
        $DONT_LOAD_SMARTY = 1;
        $CMS_VERSION = $this->get_wizard()->get_data('destversion');

        // get the list of all available versions that this upgrader knows about
        $app = \__appbase\get_app();
        $dir =  $app->get_appdir().'/upgrade';
        if( !is_dir($dir) ) throw new \Exception(\__appbase\lang('error_internal',810));
        $destdir = $app->get_destdir();
        if( !$destdir ) throw new \Exception(\__appbase\lang('error_internal',811));

        $dh = opendir($dir);
        $versions = array();
        if( !$dh ) throw new \Exception(\__appbase\lang('error_internal',812));
        while( ($file = readdir($dh)) !== false ) {
            if( $file == '.' || $file == '..' ) continue;
            if( is_dir($dir.'/'.$file) || !file_exists("$dir/$file/MANIFEST.DAT") ) $versions[] = $file;
        }
        closedir($dh);
        if( count($versions) ) usort($versions,'version_compare');

        // setup database connection
        $cfg = $version_info['config'];
        $db = \__appbase\new_db_connection($cfg['dbms']);
        $res = $db->Connect($cfg['db_hostname'],$cfg['db_username'],$cfg['db_password'],$cfg['db_name']);
        if( !$res ) throw new \Exception(\__appbase\lang('error_dbconnect'));
        $db->Execute("SET NAMES 'utf8'");
        if( !defined('CMS_DB_PREFIX')) define('CMS_DB_PREFIX',$cfg['db_prefix']);

        // setup and initialize the cmsms API's
        if( is_file("$destdir/include.php") ) {
            include_once($destdir.'/include.php');
        } else {
            include_once($destdir.'/lib/include.php');
        }
        \CmsApp::get_instance()->_setDb($db);
        include_once(__DIR__.'/msg_functions.php');

        try {
            // ready to do the upgrading now (in a loop)
            // only perform upgrades for the versions known by the installer that are greater than what is instaled.
            $current_version = $version_info['version'];
            foreach( $versions as $ver ) {
                $fn = "$dir/$ver/upgrade.php";
                if( version_compare($current_version,$ver) < 0 && file_exists($fn) ) {
                    @include_once($fn);
                }
            }
        }
        catch( \Exception $e ) {
            $this->error($e->GetMessage());
        }
    }

    private function do_freshen()
    {
        // nothing here
    }

    protected function display()
    {
        parent::display();
        \__appbase\smarty()->assign('next_url',$this->get_wizard()->next_url());
        echo \__appbase\smarty()->display('wizard_step8.tpl');

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
                throw new \Exception(\__appbase\lang('error_internal',805));
            }
        }
        catch( \Exception $e ) {
            $this->error($e->GetMessage());
        }

        $this->finish();
    }
} // end of class

?>
