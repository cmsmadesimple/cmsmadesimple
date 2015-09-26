<?php

namespace CMSMS\Database {

    final class compatibility
    {
        private function __construct() {}

        public static function init(\cms_config $config)
        {
            $spec = new ConnectionSpec;
            $spec->type = $config['dbms'];
            $spec->host = $config['db_hostname'];
            $spec->username = $config['db_username'];
            $spec->password = $config['db_password'];
            $spec->dbname = $config['db_name'];
            $spec->port = $config['db_port'];
            $spec->debug = CMS_DEBUG;
            $obj = Connection::Initialize($spec);
            if( $spec->debug ) $obj->SetDebugCallback('debug_buffer');
            return $obj;
        }

    } // end of class
} // end of namespace

namespace {
    // root namespace stuff

    define('CMS_ADODB_DT','DT'); // backwards compatibility.

    function NewDataDictionary(\CMSMS\Database\Connection $conn)
    {
        // called by module installation routines.
        return $conn->NewDataDictionary();
    }

    function &ADONewConnection( $dbms, $flags )
    {
        // now that our connection object is stateless... this is just a wraper
        // for our global db instance.... but should not be called.
        return \CmsApp::get_instance()->GetDb();
    }

    function load_adodb()
    {
        // this should only have been called by the core
        // but now does nothing, just in case it is called.
    }

    function &adodb_connect()
    {
        // this may be called by UDT's etc. that are talking to other databases
        // or using manual mysql methods.
    }

    function adodb_error($dbtype,$function_performed,$error_number,$error_message,
                         $host, $database, &$connection_obj)
    {
        // does nothing.... remove me later.
    }

}
?>