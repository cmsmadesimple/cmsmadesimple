<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CMSMS\Database\compatibility (c) 2015 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
# A collection of compatibility tools for the database connectivity layer.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE

/**
 * This file contains some database connectivity tools.
 *
 * @package CMS
 */

namespace CMSMS\Database {

    /**
     * A class for providing some compatibility functionality with older module code
     *
     * @todo: move this class to a different function and rename.
     */
    final class compatibility
    {
        /**
         * @ignore
         */
        private function __construct() {}

        /**
         * Initialize the database connection according to config settings.
         *
         * @internal
         * @param cms_config $config The config object
         * @return \CMSMS\Database\Connection
         */
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

            $tmp = [];
            if( $config['set_names'] ) $tmp[] = "NAMES 'utf8'";
            if( $config['set_db_timezone'] ) {
                $dt = new \DateTime();
                $dtz = new \DateTimeZone($config['timezone']);
                $offset = timezone_offset_get($dtz,$dt);
                $symbol = ($offset < 0) ? '-' : '+';
                $hrs = abs((int)($offset / 3600));
                $mins = abs((int)($offset % 3600));
                $tmp[] = sprintf("time_zone = '%s%d:%02d'",$symbol,$hrs,$mins);
            }
            if( count($tmp) ) $spec->auto_exec = 'SET '.implode(',',$tmp);

            $obj = Connection::Initialize($spec);
            $obj->SetErrorHandler( '\\CMSMS\Database\\compatibility::on_error' );
            if( $spec->debug ) $obj->SetDebugCallback('debug_buffer');
            return $obj;
        }

        public static function on_error( Connection $conn, $errtype, $error_number, $error_msg )
        {
            debug_to_log("Database Error: $errtype($error_number) - $error_msg");
            debug_bt_to_log();
            if( !defined('CMS_DEBUG') || CMS_DEBUG == 0 ) return;
            \CmsApp::get_instance()->add_error(debug_display($error_msg, '', false, true));
        }

        /**
         * A static no-op function  that allows the autoloader to load this file
         */
        public static function noop()
        {
            // do nothing
        }
    } // end of class
} // end of namespace

namespace {
    // root namespace stuff

    /**
     * A constant to assist with date and time flags in the data dictionary.
     *
     * @name CMS_ADODB_DT
     */
    define('CMS_ADODB_DT','DT'); // backwards compatibility.

    /**
     * A method to create a new data dictionary object
     *
     * @param \CMSMS\Database\Connection $conn The existing database connection.
     * @return \CMSMS\Database\DataDictionary
     * @deprecated
     */
    function &NewDataDictionary(\CMSMS\Database\Connection $conn)
    {
        // called by module installation routines.
        return $conn->NewDataDictionary();
    }

    /**
     * A function co create a new adodb database connection.
     *
     * @param string $dbms
     * @param string $flags
     * @return \CMSMS\Database\Connection
     * @deprecated
     */
    function &ADONewConnection( $dbms, $flags )
    {
        // now that our connection object is stateless... this is just a wrapper
        // for our global db instance.... but should not be called.
        return \CmsApp::get_instance()->GetDb();
    }

    /**
     * A function forumerly used to load the adodb library.
     * This method currently has no functionality.
     *
     * @deprecated
     */
    function load_adodb()
    {
        // this should only have been called by the core
        // but now does nothing, just in case it is called.
    }

    /**
     * An old method formerly used to ensure that we were re-connected to the proper database.
     * This method currently has no functionality.
     *
     * @deprecated
     */
    function adodb_connect()
    {
        // this may be called by UDT's etc. that are talking to other databases
        // or using manual mysql methods.
    }

    /**
     * An old function for handling a database error.
     *
     * @param string $dbtype
     * @param string $function_performed
     * @param int    $error_number
     * @param string $error_message
     * @param string $host
     * @param string $database
     * @param mixed  $connection_obj
     * @deprecated
     */
    function adodb_error($dbtype,$function_performed,$error_number,$error_message,
                         $host, $database, &$connection_obj)
    {
        // does nothing.... remove me later.
    }

}
?>
