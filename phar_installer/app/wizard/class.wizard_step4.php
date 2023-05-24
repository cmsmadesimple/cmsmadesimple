<?php

namespace cms_autoinstaller;

use __appbase\utils;
use cms_autoinstaller\wizard_step;
use CMSMS\Database\Connection;
use CMSMS\Database\ConnectionSpec;
use CMSMS\Database\DatabaseException;
use Exception;
use function __appbase\get_app;
use function __appbase\lang;
use function __appbase\smarty;

class wizard_step4 extends wizard_step
{
    private $_config;
    private $_dbms_options;

    public function __construct()
    {
        parent::__construct();

        $tz = date_default_timezone_get();
        if( !$tz ) @date_default_timezone_set('UTC');
        $this->_config = array('dbtype'=>'','dbhost'=>'localhost','dbname'=>'','dbuser'=>'',
                               'dbpass'=>'','dbprefix'=>'cms_','dbport'=>'',
                               'samplecontent'=>TRUE,
                               'query_var'=>'','timezone'=>$tz);

        // get saved date
        $tmp = $this->get_wizard()->get_data('config');
        if( $tmp ) $this->_config = array_merge($this->_config,$tmp);

        $databases = array('mysqli'=>'MySQL (4.1+)');
        $this->_dbms_options = array();
        foreach ($databases as $db => $lbl) {
            if( extension_loaded($db) ) $this->_dbms_options[$db] = $lbl;
        }
        if( !count($this->_dbms_options) ) throw new Exception(lang('error_nodatabases'));

        $action = $this->get_wizard()->get_data('action');
        if( $action == 'freshen' || $action == 'upgrade' ) {
            // read config data from config.php for freshen action.
            $app = get_app();
            $destdir = $app->get_destdir();
            $config_file = $destdir.'/config.php';
            include_once($config_file);
            $this->_config['dbtype'] = $config['dbms'];
            $this->_config['dbhost'] = $config['db_hostname'];
            $this->_config['dbuser'] = $config['db_username'];
            $this->_config['dbpass'] = $config['db_password'];
            $this->_config['dbname'] = $config['db_name'];
            $this->_config['dbprefix'] = $config['db_prefix'];
            if( isset($config['db_port']) ) $this->_config['dbport'] = $config['db_port'];
            if( isset($config['query_var']) ) $this->_config['query_var'] = $config['query_var'];
            if( isset($config['timezone']) ) $this->_config['timezone'] = $config['timezone'];
        }
    }

    private function validate($config)
    {
        $action = $this->get_wizard()->get_data('action');
        if( !isset($config['dbtype']) || !$config['dbtype'] ) throw new Exception(lang('error_nodbtype'));
        if( !isset($config['dbhost']) || !$config['dbhost'] ) throw new Exception(lang('error_nodbhost'));
        if( !isset($config['dbname']) || !$config['dbname'] ) throw new Exception(lang('error_nodbname'));
        if( !isset($config['dbuser']) || !$config['dbuser'] ) throw new Exception(lang('error_nodbuser'));
        if( !isset($config['dbpass']) || !$config['dbpass'] ) throw new Exception(lang('error_nodbpass'));
        if( $action == 'install' && ( !isset($config['dbprefix']) || !$config['dbprefix'] ) ) throw new Exception(lang('error_nodbprefix'));
        if( !isset($config['timezone']) || !$config['timezone'] ) throw new Exception(lang('error_notimezone'));

        $re = '/^[a-zA-Z0-9_\.]*$/';
        if( isset($config['query_var']) && $config['query_var'] && !preg_match($re,$config['query_var']) ) {
            throw new Exception(lang('error_invalidqueryvar'));
        }

        $all_timezones = timezone_identifiers_list();
        if( !in_array($config['timezone'],$all_timezones) ) throw new Exception(lang('error_invalidtimezone'));

        if( $config['dbpass'] ) {
            if( strpos($config['dbpass'],"'") !== FALSE || strpos($config['dbpass'],'\\') !== FALSE ) {
                throw new Exception(lang('error_invaliddbpassword'));
            }
        }

        // try a test connection
        $spec = new ConnectionSpec;
        $spec->type = $config['dbtype'];
        $spec->host = $config['dbhost'];
        $spec->username = $config['dbuser'];
        $spec->password = $config['dbpass'];
        $spec->dbname = $config['dbname'];
        $spec->port = isset($config['dbport']) ? $config['dbport'] : null;
        $spec->prefix = $config['dbprefix'];
        $db = Connection::initialize($spec);
        $db->Execute("SET NAMES 'utf8'");

        // see if we can create and drop a table.
        try {
            $db->Execute('CREATE TABLE '.$config['dbprefix'].'_dummyinstall (i int)');
        }
        catch( Exception $e ) {
            throw new Exception(lang('error_createtable'));
        }

        try {
            $db->Execute('DROP TABLE '.$config['dbprefix'].'_dummyinstall');
        }
        catch( Exception $e ) {
            throw new Exception(lang('error_droptable'));
        }

        // see if a smattering of core tables exist
        if( $action == 'install' ) {
            try {
                $res = $db->GetOne('SELECT content_id FROM '.$config['dbprefix'].'content');
                if( $res > 0 ) throw new Exception(lang('error_cmstablesexist'));
            }
            catch( DatabaseException $e ) {
                // if this fails it's not a problem
            }

            try {
                $db->GetOne('SELECT module_name FROM '.$config['dbprefix'].'modules');
                if( $res > 0 ) throw new Exception(lang('error_cmstablesexist'));
            }
            catch( DatabaseException $e ) {
                // if this fails it's not a problem.
            }
        }
    }

    protected function process()
    {
        $tmp = array_keys($this->_dbms_options);
        $this->_config['dbtype'] = $tmp[0];
        $this->_config['dbhost'] = trim(utils::clean_string($_POST['dbhost']));
        $this->_config['dbname'] = trim(utils::clean_string($_POST['dbname']));
        $this->_config['dbuser'] = trim(utils::clean_string($_POST['dbuser']));
        $this->_config['dbpass'] = $_POST['dbpass'];
        $this->_config['timezone'] = trim(utils::clean_string($_POST['timezone']));
        if( isset($_POST['dbtype']) ) $this->_config['dbtype'] = trim(utils::clean_string($_POST['dbtype']));
        if( isset($_POST['dbport']) ) $this->_config['dbport'] = trim(utils::clean_string($_POST['dbport']));
        if( isset($_POST['dbprefix']) ) $this->_config['dbprefix'] = trim(utils::clean_string($_POST['dbprefix']));
        if( isset($_POST['query_var']) ) $this->_config['query_var'] = trim(utils::clean_string($_POST['query_var']));
        if( isset($_POST['samplecontent']) ) $this->_config['samplecontent'] = (int)$_POST['samplecontent'];
        $this->get_wizard()->set_data('config',$this->_config);

        try {
            $app = get_app();
            $config = $app->get_config();
            $this->validate($this->_config);
            $url = $this->get_wizard()->next_url();
            $action = $this->get_wizard()->get_data('action');
            if( $action == 'freshen' ) $url = $this->get_wizard()->step_url(6);
            if( $action == 'upgrade' ) {
                if( $config['nofiles'] ) {
                    $url = $this->get_wizard()->step_url(8);
                } else {
                    $url = $this->get_wizard()->step_url(7);
                }
            }
            utils::redirect($url);
        }
        catch( Exception $e ) {
            $smarty = smarty();
            $smarty->assign('error',$e->GetMessage());
        }
    }

    protected function display()
    {
        parent::display();
        $tmp = timezone_identifiers_list();
        if( !is_array($tmp) ) throw new Exception(lang('error_tzlist'));
        $tmp2 = array_combine(array_values($tmp),array_values($tmp));
        $smarty = smarty();
        $smarty->assign('timezones',array_merge(array(''=>lang('none')),$tmp2))
          ->assign('dbtypes',$this->_dbms_options)
          ->assign('action',$this->get_wizard()->get_data('action'))
          ->assign('verbose',$this->get_wizard()->get_data('verbose',0))
          ->assign('config',$this->_config)
          ->assign('yesno',array('0'=>lang('no'),'1'=>lang('yes')))
          ->display('wizard_step4.tpl');
        $this->finish();
    }

} // end of class
