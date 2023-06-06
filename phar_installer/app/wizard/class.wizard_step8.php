<?php

namespace cms_autoinstaller;

use cms_autoinstaller\wizard_step;
use cms_config;
use cms_siteprefs;
use CmsApp;
use CMSMS\Database\compatibility;
use CMSMS\Database\Connection;
use CMSMS\Database\ConnectionSpec;
use Exception;
use RuntimeException;
use const CMS_DB_PREFIX;
use function __appbase\get_app;
use function __appbase\lang;
use function __appbase\smarty;
use function cmsms;
use function set_site_preference;

class wizard_step8 extends wizard_step
{
    protected function process()
    {
        // nothing here
    }

    private function &db_connect($destconfig)
    {
        $spec = new ConnectionSpec;
        if( isset($destconfig['dbms']) ) {
            $spec->type = $destconfig['dbms'];
            $spec->host = $destconfig['db_hostname'];
            $spec->username = $destconfig['db_username'];
            $spec->password = $destconfig['db_password'];
            $spec->dbname = $destconfig['db_name'];
            $spec->prefix = $destconfig['db_prefix'];
        }
        else {
            $spec->type = $destconfig['dbtype'];
            $spec->host = $destconfig['dbhost'];
            $spec->username = $destconfig['dbuser'];
            $spec->password = $destconfig['dbpass'];
            $spec->dbname = $destconfig['dbname'];
            $spec->port = isset($destconfig['dbport']) ? $destconfig['dbport'] : null;
            $spec->prefix = $destconfig['dbprefix'];
        }
        if( !defined('CMS_DB_PREFIX')) define('CMS_DB_PREFIX',$spec->prefix);
        $db = Connection::initialize($spec);
        $db->SetErrorHandler(function() { /* do nohing */ });
        $db->Execute("SET NAMES 'utf8'");
        compatibility::noop();
        CmsApp::get_instance()->_setDb($db);
        return $db;
    }

    private function connect_to_cmsms($destdir)
    {
        if( is_file("$destdir/lib/include.php") ) {
            global $CMS_INSTALL_PAGE, $DONT_LOAD_DB, $DONT_LOAD_SMARTY, $CMS_VERSION, $CMS_PHAR_INSTALLER;
            $CMS_INSTALL_PAGE = 1;
            $DONT_LOAD_DB = 1;
            $DONT_LOAD_SMARTY = 1;
            $CMS_PHAR_INSTALLER = 1; //TODO unused anywhere TODO if extended installer ?
            $CMS_VERSION = get_app()->get_dest_version();
            // setup and initialize the cmsms API's
            // note DONT_LOAD_DB and DONT_LOAD_SMARTY are used.
            require_once "$destdir/lib/include.php";
            if( !defined('CMS_DB_PREFIX') ) {
                // $config does not define this when installer is running.
                $config = cms_config::get_instance();
                define('CMS_DB_PREFIX',$config['db_prefix']);
            }
        }
        else {
            throw new RuntimeException('Could not find include.php file in destination');
        }
    }

    private function do_install()
    {
        $dir = get_app()->get_appdir().'/install';

        $destdir = get_app()->get_destdir();
        if( !$destdir ) throw new Exception(lang('error_internal',700));

        $adminaccount = $this->get_wizard()->get_data('adminaccount');
        if( !$adminaccount ) throw new Exception(lang('error_internal',701));

        $destconfig = $this->get_wizard()->get_data('config');
        if( !$destconfig ) throw new Exception(lang('error_internal',703));

        $siteinfo = $this->get_wizard()->get_data('siteinfo');
        if( !$siteinfo ) throw new Exception(lang('error_internal',704));

        $this->connect_to_cmsms($destdir);

        // connect to the database
        $db = $this->db_connect($destconfig);

        include_once(__DIR__.'/msg_functions.php');

        try {
            // create some variables that the sub functions need.
            if( !defined('CMS_ADODB_DT') ) define('CMS_ADODB_DT','DT');

            // install the schema
            $this->message(lang('install_schema'));
            $fn = $dir.'/schema.php';
            if( !file_exists($fn) ) throw new Exception(lang('error_internal',705));

            global $CMS_INSTALL_DROP_TABLES, $CMS_INSTALL_CREATE_TABLES;
            $CMS_INSTALL_DROP_TABLES=1;
            $CMS_INSTALL_CREATE_TABLES=1;
            include_once($fn);

            $this->verbose(lang('install_setsequence'));
            include_once($dir.'/createseq.php');

            if( $adminaccount['saltpw'] ) {
                $this->verbose(lang('install_passwordsalt'));
                $salt = substr(str_shuffle(md5($destdir).time()),0,16);
                cms_siteprefs::set('sitemask',$salt);
            }

            // create tmp directories
            $this->verbose(lang('install_createtmpdirs'));
            @mkdir($destdir.'/tmp/cache',0777,TRUE);
            @mkdir($destdir.'/tmp/templates_c',0777,TRUE);

            include_once($dir.'/base.php');

            $this->message(lang('install_defaultcontent'));
            $fn = $dir.'/initial.php';
            if( $destconfig['samplecontent'] ) $fn = $dir.'/extra.php';
            include_once($fn);

            $this->verbose(lang('install_setsitename'));
            cms_siteprefs::set('sitename',$siteinfo['sitename']);

            $this->write_config();

            // update all hierarchy positioss
            $this->message(lang('install_updatehierarchy'));
            $contentops = cmsms()->GetContentOperations();
            $contentops->SetAllHierarchyPositions();

            // todo: install default preferences
            set_site_preference('global_umask','022');
        }
        catch( Exception $e ) {
            $this->error($e->GetMessage());
        }
    }

    private function do_upgrade($version_info)
    {
        global $CMS_INSTALL_PAGE, $DONT_LOAD_DB, $DONT_LOAD_SMARTY, $CMS_VERSION, $CMS_PHAR_INSTALLER;
        $CMS_INSTALL_PAGE = 1;
        $CMS_PHAR_INSTALLER = 1; // TODO never used elsewhere
        $DONT_LOAD_DB = 1;
        $DONT_LOAD_SMARTY = 1;
        $app = get_app();
        $CMS_VERSION = $app->get_dest_version();

        // get the list of all available versions that this upgrader knows about
        $dir =  $app->get_appdir().'/upgrade';
        if( !is_dir($dir) ) throw new Exception(lang('error_internal',710));
        $destdir = $app->get_destdir();
        if( !$destdir ) throw new Exception(lang('error_internal',711));

        $dh = opendir($dir);
        $versions = array();
        if( !$dh ) throw new Exception(lang('error_internal',712));
        while( ($file = readdir($dh)) !== false ) {
            if( $file == '.' || $file == '..' ) continue;
            if( is_dir($dir.'/'.$file) && (is_file("$dir/$file/MANIFEST.DAT") || is_file("$dir/$file/MANIFEST.DAT.gz")) ) $versions[] = $file;
        }
        closedir($dh);
        if( count($versions) ) usort($versions,'version_compare');

        $destconfig = $this->get_wizard()->get_data('config');
        if( !$destconfig ) throw new Exception(lang('error_internal',703));

        // setup and initialize the cmsms API's
        if( is_file("$destdir/lib/include.php") ) {
            include_once("$destdir/lib/include.php");
        }
        else if( is_file( "$destdir/include.php")) {
            include_once( "$destdir/lib/include.php" );
        }
        else {
            throw new RuntimeException('Could not find include.php file in destination');
        }

        // setup database connection
        $db = $this->db_connect($destconfig);

        include_once(__DIR__.'/msg_functions.php');

        try {
            // ready to do the upgrading now (in a loop)
            // only perform upgrades for the versions known by the installer that are greater than what is instaled.
            $current_version = $version_info['version'];
            foreach( $versions as $ver ) {
                $fn = "$dir/$ver/upgrade.php";
                if( version_compare($current_version,$ver) < 0 && is_file($fn) ) {
                    include_once($fn);
                }
            }

            $this->write_config();

            $this->message(lang('done'));
        }
        catch( Exception $e ) {
            $this->error($e->GetMessage());
        }
    }

    private function do_freshen()
    {
        try {
            $this->write_config();
        }
        catch( Exception $e ) {
            $this->error($e->GetMessage());
        }
    }

    private function write_config()
    {
        $destconfig = $this->get_wizard()->get_data('config');
        if( !$destconfig ) throw new Exception(lang('error_internal',703));

        $destdir = get_app()->get_destdir();
        if( !$destdir ) throw new Exception(lang('error_internal',700));

        // create new config file.
        // this step has to go here.... as config file has to exist in step9
        // so that CMSMS can connect to the database.
        $fn = $destdir."/config.php";
        if( is_file($fn) ) {
            $this->verbose(lang('install_backupconfig'));
            $destfn = $destdir.'/bak.config.php';
            if( !copy($fn,$destfn) ) throw new Exception(lang('error_backupconfig'));
        }

        $this->connect_to_cmsms($destdir);

        $this->message(lang('install_createconfig'));
        $newconfig = cms_config::get_instance();
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
    }

    protected function display()
    {
        parent::display();
        $wiz = $this->get_wizard();
        $smarty = smarty();
        $smarty->assign('next_url',$wiz->next_url())
         ->display('wizard_step8.tpl');

        // here, we do the action-specific stuff.
        try {
            $action = $wiz->get_data('action');
            switch( $action ) {
             case 'upgrade':
                 $tmp = $wiz->get_data('version_info'); //valid only for upgrades
                 if( is_array($tmp) && count($tmp) ) {
                     $this->do_upgrade($tmp);
                     break;
                 }
                 else {
                     throw new Exception(lang('error_internal',908));
                 }
                 //no break here
             case 'freshen':
                 $this->do_freshen();
                 break;
             case 'install':
                 $this->do_install();
                 break;
             default:
                 throw new Exception(lang('error_internal',910));
            }
        }
        catch( Exception $e ) {
            $this->error($e->GetMessage());
        }

        $this->finish();
    }
} // end of class

?>
