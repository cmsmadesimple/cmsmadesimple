<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: \CMSMS\Database\Connection (c) 2015 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A class to define interaction with a database.
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
 * This file defines the abstract database connection class.
 *
 * @package CMS
 */

namespace CMSMS\Database {

    /**
     * A class defining a database connection, and mechanisms for working with a database.
     *
     * This library is largely compatible with adodb_lite with the pear,extended,transaction plugins with a few
     * notable differences:
     *
     * Differences:
     * <ul>
     *  <li>GenID will not automatically create a sequence table.
     *    <p>We encourage you to not use sequence tables and use auto-increment fields instead.</p>
     *  </li>
     * </ul>
     *
     * @package CMS
     * @author Robert Campbell
     * @copyright Copyright (c) 2015, Robert Campbell <calguy1000@cmsmadesimple.org>
     * @since 2.2
     * @property-read float $query_time_total The total query time so far in this request (in seconds)
     * @property-read int $query_count The total number of queries executed so far.
     */
    abstract class Connection
    {
        /**
         * This constant defines an error with connecting to the database.
         */
        const ERROR_CONNECT = 'CONNECT';

        /**
         * This constant defines an error with an execute statement.
         */
        const ERROR_EXECUTE = 'EXECUTE';

        /**
         * This constant defines an error with a transaction.
         */
        const ERROR_TRANSACTION = 'TRANSACTION';

        /**
         * This constant defines an error in a datadictionary command.
         */
        const ERROR_DATADICT = 'DATADICTIONARY';

        /**
         * @ignore
         */
        private $_debug;

        /**
         * @ignore
         */
        private $_debug_cb;

        /**
         * @ignore
         */
        private $_query_count = 0;

        /**
         * @ignore
         */
        private $_queries = array();

        /**
         * @ignore
         */
        private $_errorhandler;

        /**
         * The actual connectionspec object.
         *
         * @internal
         */
        protected $_connectionSpec;

        /**
         * The last SQL command executed
         *
         * @internal
         * @param string $sql
         */
        public $sql;

        /**
         * Accumulated sql query time.
         *
         * @internal
         * @param float $query_time_total
         */
        protected $query_time_total;

        /**
         * Construct a new Connection.
         *
         * @param \CMSMS\Database\ConnectionSpec $spec
         */
        public function __construct(ConnectionSpec $spec)
        {
            $this->_connectionSpec = $spec;
        }

        /**
         * @ignore
         */
        public function __get($key)
        {
            if( $key == 'query_time_total' ) return $this->query_time_total;
            if( $key == 'query_count' ) return $this->_query_count;
        }

        /**
         * @ignore
         */
        public function __isset($key)
        {
            if( $key == 'query_time_total' ) return TRUE;
            if( $key == 'query_count' ) return TRUE;
            return FALSE;
        }

        /**
         * Create a new data dictionary object.
         * Data Dictionary objects are used for manipulating tables, i.e: creating, altering and editing them.
         * @return \CMSMS\Database\DataDictionary
         */
        abstract public function &NewDataDictionary();

        /**
         * Return the database type.
         *
         * @return string
         */
        abstract public function DbType();

        /**
         * Open the database connection.
         *
         * @return bool Success or failure
         */
        abstract public function Connect();

        /**
         * Close the database connection.
         */
        abstract public function Disconnect();

        /**
         * Test if the connection object is connected to the database.
         *
         * @return bool
         */
        abstract public function IsConnected();

        /**
         * An alias for Disconnect.
         */
        final public function Close() { return $this->Disconnect(); }

        //// utilities

        /**
         * Quote a string magically using the magic quotes flag.
         * This method is now just a deprecated alias for the qstr flag
         * as we now require magic quotes to be disabled.
         *
         * @deprecated
         * @param string $str
         * @return string
         */
        public function QMagic($str)
        {
            return $this->qstr($str);
        }

        /**
         * Quote a string in a database agnostic manner.
         * Warning: This method may require two way traffic with the database depending upon the database.
         * @param string $str
         * @return string
         */
        abstract public function qstr($str);

        /**
         * output the mysql expression for a string concatenation.
         * This function accepts a variable number of string arguments.
         *
         * @param $str First string to concatenate
         * @param $str,... unlimited number of strings to concatenate.
         * @return string
         */
        abstract public function concat();

        /**
         * Output the mysql expression to test if an item is null.
         *
         * @param string $field The field to test
         * @param string $ifNull The value to use if $field is null.
         * @return string
         */
        abstract public function IfNull( $field, $ifNull );

        /**
         * Output the number of rows affected by the last query.
         *
         * @return int
         */
        abstract public function Affected_Rows();

        /**
         * Return the numeric ID of the last insert query into a table with an auto-increment field.
         * @return int
         */
        abstract public function Insert_ID();

        //// primary query functions

        /**
         * The primary function for communicating with the database.
         *
         * @internal
         * @param string $sql The SQL query
         */
        abstract public function &do_sql($sql);

        /**
         * Create a prepared statement object.
         *
         * @param string $sql The SQL query
         * @return Statement
         */
        abstract public function &Prepare($sql);

        /**
         * Execute an SQL Select and limit the output.
         *
         * @param string $sql
         * @param int $nrows  The number of rows to return
         * @param int $offset The starting offset of rows to return
         * @param array Any additional paramters required by placeholders in the $sql statement.
         * @return \CMSMS\Database\ResultSet
         */
        public function &SelectLimit( $sql, $nrows = -1, $offset = -1, $inputarr = null )
        {
            $limit = null;
            $nrows = (int) $nrows;
            $offset = (int) $offset;
            if( $nrows >= 0 || $offset >= 0 ) {
                $offset = ($offset >= 0) ? $offset . "," : '';
                $nrows = ($nrows >= 0) ? $nrows : '18446744073709551615';
                $limit = ' LIMIT ' . $offset . ' ' . $nrows;
            }

            if ($inputarr && is_array($inputarr)) {
                $sqlarr = explode('?',$sql);
                if( !is_array(reset($inputarr)) ) $inputarr = array($inputarr);
                foreach( $inputarr as $arr ) {
                    $sql = ''; $i = 0;
                    foreach( $arr as $v ) {
                        $sql .= $sqlarr[$i];
                        switch(gettype($v)){
						case 'string':
							$sql .= $this->qstr($v);
							break;
						case 'double':
							$sql .= str_replace(',', '.', $v);
							break;
						case 'boolean':
							$sql .= $v ? 1 : 0;
							break;
						default:
							if ($v === null) $sql .= 'NULL';
							else $sql .= $v;
                        }
                        $i += 1;
                    }
                    $sql .= $sqlarr[$i];
                    if ($i+1 != sizeof($sqlarr)) {
                        $false = null;
                        return $false;
                    }
                }
            }
            $sql .= $limit;

            $rs = $this->do_sql( $sql );
            return $rs;
        }

        /**
         * Execute an SQL Command
         *
         * @param string $sql The SQL statement to execute.
         * @param array $inputarr Any parameters marked as placeholders in the SQL statement.
         * @return \CMSMS\Database\ResultSet
         */
        public function &Execute($sql, $inputarr = null)
        {
            $rs = $this->SelectLimit($sql, -1, -1, $inputarr );
            return $rs;
        }

        /**
         * Execute an SQL Commmand and return all of the results as an array.
         *
         * @param string $sql The SQL statement to execute.
         * @param array $inputarr Any parameters marked as placeholders in the SQL statement.
         * @return array An associative array of matched results.
         */
        public function GetArray($sql, $inputarr = null)
        {
            $result = $this->SelectLimit( $sql, -1, -1, $inputarr );
            if( !$result ) return;
            $data = $result->GetArray();
            return $data;
        }

        /**
         * An alias for the GetArray method.
         *
         * @param string $sql The SQL statement to execute.
         * @param array $inputarr Any parameters marked as placeholders in the SQL statement.
         * @return array
         */
        public function GetAll($sql, $inputarr = null)
        {
            return $this->GetArray($sql, $inputarr);
        }

        /**
         * A method to return an associative array.
         *
         * @deprecated
         * @see Pear::getAssoc()
	 * @param string $sql The SQL statement to execute
         * @param array $inputarr Any parameters marked as placeholders in the SQL statement.
         * @param bool $force_array Force each element of the output to be an associative array.
         * @param bool $first2cols Only output the first 2 columns in an associative array.  Does not work with force_array.
         */
        public function GetAssoc( $sql, $inputarr = null, $force_array = false, $first2cols = false )
        {
            $data = null;
            $result = $this->SelectLimit($sql, -1, -1, $inputarr );
            if( $result ) $data = $result->GetAssoc($force_array,$first2cols);
            return $data;
        }

        /**
         * Execute an SQL statement that returns one column, and return all of the
         * matches as an array.
         *
         * @param string $sql The SQL statement to execute.
         * @param array $inputarr Any parameters marked as placeholders in the SQL statement.
         * @param bool $trim Optionally trim the output results.
         * @return array A single flat array of results, one entry per row matched.
         */
        public function GetCol($sql, $inputarr = null, $trim = false)
        {
            $data = null;
            $result = $this->SelectLimit($sql, -1, -1, $inputarr);
            if ($result) {
                $data = [];
                $key = null;
                while (!$result->EOF) {
                    $row = $result->Fields();
                    if( !$key ) $key = array_keys($row)[0];
                    $data[] = ($trim) ? trim($row[$key]) : $row[$key];
                    $result->MoveNext();
                }
            }
            return $data;
        }

        /**
         * Exeute an SQL statement that returns one row of results, and return that row
         * as an associative array.
         *
         * @param string $sql The SQL statement to execute.
         * @param array $inputarr Any parameters marked as placeholders in the SQL statement.
         * @return array An associative array representing a single resultset row.
         */
        public function GetRow($sql, $inputarr = null)
        {
            $nrows = 1;
            if( stripos( $sql, 'LIMIT' ) !== FALSE ) $nrows = -1;
            $rs = $this->SelectLimit( $sql, $nrows, -1, $inputarr );
            if( !$rs ) return FALSE;
            return $rs->Fields();
        }

        /**
         * Execute an SQL statement and return a single value.
         *
         * @param string $sql The SQL statement to execute.
         * @param array $inputarr Any parameters marked as placeholders in the SQL statement.
         * @return mixed
         */
        public function GetOne($sql, $inputarr = null)
        {
            $res = $this->Getrow( $sql, $inputarr );
            if( !$res ) return FALSE;
            $key = array_keys($res)[0];
            return $res[$key];
        }

        //// transactions

        /**
         * Begin a transaction
         */
        abstract public function BeginTrans();

        /**
         * Begin a smart transaction
         */
        abstract public function StartTrans();

        /**
         * Complete a smart transaction.
         * This method will either do a rollback or a commit depending upon if errors have been detected.
         *
         * @param bool $autoComplete If no errors have been detected attempt to auto commit the transaction.
         */
        abstract public function CompleteTrans($autoComplete = true);

        /**
         * Commit a simple transaction.
         *
         * @param bool $ok Indicates wether there is success or not.
         */
        abstract public function CommitTrans($ok = true);

        /**
         * Roll back a simple transaction.
         */
        abstract public function RollbackTrans();

        /**
         * Mark a transaction as failed.
         */
        abstract public function FailTrans();

        /**
         * Test if a transaction has failed.
         *
         * @return bool
         */
        abstract public function HasFailedTrans();

        //// sequence table stuff

        /**
         * For use with sequence tables, this method will generate a new ID value.
         *
         * This function will not automatically create the sequence table if not specified.
         *
         * @param string $seqname The name of the sequence table.
         * @return int
         * @deprecated
         */
        abstract public function GenID($seqname);

        // these methods should be in the DataDictionary stuff.

        /**
         * Create a new sequence table.
         *
         * @param string $seqname the name of the sequence table.
         * @param int $startID
         * @return bool
         * @deprecated
         */
        abstract public function CreateSequence($seqname,$startID=0);

        /**
         * Drop a sequence table
         * @param string $seqname The name of the sequence table.
         * @return bool
         */
        abstract public function DropSequence($seqname);

        //// time and date stuff

        /**
         * A utility method to convert a unix timestamp into a database specific string suitable
         * for use in queries.
         *
         * @param int $timestamp
         * @return string
         */
        public function DBTimeStamp($timestamp)
        {
            if (empty($timestamp) && $timestamp !== 0) return 'null';

            // strlen(14) allows YYYYMMDDHHMMSS format
            if( is_string($timestamp) ) {
                if( strlen($timestamp) === 14 || preg_match('/[0-9-\s:]*/',$timestamp) ) {
                    $tmp = strtotime($timestamp);
                    if( $tmp < 1 ) return 'null';
                    $timestamp = $tmp;
                } else if( is_numeric($timestamp) ) {
                    $timestamp = (int) $timestamp;
                }
            }
            if( $timestamp > 0 ) return date("'Y-m-d H:i:s'",$timestamp);
        }

        /**
         * A convenience method for converting a database specific string representing a date and time
         * into a unix timestamp.
         *
         * @param string $str
         * @return int
         */
        public function UnixTimeStamp($str)
        {
            return strtotime($str);
        }

        /**
         * Convert a date into something that is suitable for writing to a database.
         *
         * @param mixed $date Either a string date, or an integer timestamp
         * @return string
         */
        public function DBDate($date)
        {
            if (empty($date) && $date !== 0) return 'null';

            if (is_string($date) && !is_numeric($date)) {
                if ($date === 'null' || strncmp($date, "'", 1) === 0) return $date;
                $date = $this->UnixDate($date);
            }
            return strftime('%x',$date);
        }

        /**
         * Generate a unix timestamp representing the current date at midnight.
         *
         * @deprecated
         * @return int
         */
        public function UnixDate()
        {
            return strtotime('today midnight');
        }

        /**
         * An alias for the UnixTimestamp method.
         *
         * @return int
         */
        public function Time() { return $this->UnixTimeStamp(); }

        /**
         * An Alias for the UnixDate method.
         *
         * @return int
         */
        public function Date() { return $this->UnixDate(); }

        //// error and debug message handling

        /**
         * Return a string describing the latest error (if any)
         *
         * @return string
         */
        abstract public function ErrorMsg();

        /**
         * Return the latest error number (if any)
         *
         * @return int
         */
        abstract public function ErrorNo();

        /**
         * Set an error handler function
         *
         * @param callable $fn
         */
        public function SetErrorHandler($fn = null)
        {
            $this->_errorhandler = null;
            if( $fn && is_callable($fn) ) $this->_errorhandler = $fn;
        }

        /**
         * Toggle debug mode.
         *
         * @param bool $flag Enable or Disable debug mode.
         * @param callable $debug_handler
         */
        public function SetDebugMode($flag = true,$debug_handler = null)
        {
            $this->_debug = (bool) $flag;
            if( $debug_handler && is_callable($this->_debug_handler) ) $this->_debug_cb = $debug_handler;
        }

        /**
         * Set the debug callback.
         *
         * @param callable $debug_handler
         */
        public function SetDebugCallback(callable $debug_handler = null)
        {
            $this->_debug_cb = $debug_handler;
        }

        /**
         * Add a query to the debug log
         *
         * @internal
         * @param string $sql the SQL statement
         */
        protected function add_debug_query($sql)
        {
            $this->_query_count++;
            if( $this->_debug && $this->_debug_cb ) call_user_func($this->_debug_cb,$sql);
        }

        /**
         * A callback that is called when a database error occurs.
         * This method will by default call the error handler if it has been set.
         * If no error handler is set, an exception will be thrown.
         *
         * @internal
         * @param string $errtype The type of error
         * @param int $error_number The error number
         * @param string $error_message The error message
         */
        public function OnError($errtype, $error_number, $error_message )
        {
            if( $this->_errorhandler && is_callable($this->_errorhandler) ) {
                call_user_func($this->_errorhandler, $this, $errtype, $error_number, $error_message);
                return;
            }

            switch( $errtype ) {
            case self::ERROR_CONNECT:
                throw new DatabaseConnectionException($error_message,$error_number);

            case self::ERROR_EXECUTE:
                throw new DatabaseException($error_message,$error_number,$this->sql,$this->_connectionSpec);
            }
        }

        //// initialization

        /**
         * Create a new database connection object.
         * This is the preferred wa to open a new database connection.
         *
         * @param \CMSMS\Database\Connectionspec $spec An object describing the database to connect to.
         * @return \CMSMS\Database\Connection
	 * @todo  Move this into a factory class
         */
        public static function &Initialize(ConnectionSpec $spec)
        {
            if( !$spec->valid() ) throw new ConnectionSpecException('Invalid or incorrect configuration information');
            $connection_class = '\\CMSMS\\Database\\'.$spec->type.'\\Connection';
            if( !class_exists($connection_class) ) throw new \LogicException('Could not find a database abstraction layer named '.$spec->type);

            $obj = new $connection_class($spec);
            if( !($obj instanceof Connection ) ) throw new \LogicException("$connection_class is not derived from the primary database class.");
            if( $spec->debug ) $obj->SetDebugMode();
            $obj->Connect();

            if( $spec->auto_exec ) $obj->Execute($spec->auto_exec);
            return $obj;
        }

    } // end of class

    /**
     * A special type of exception related to database queries.
     */
    class DatabaseException extends \LogicException
    {
        /**
         * @internal
         */
        protected $_connection;

        /**
         * @internal
         */
        protected $_sql;

        /**
         * Constructor
         *
         * @param string $msg The message string
         * @param int $number The error number
         * @param string $sql The related SQL statement, if any
         * @param \CMSMS\Database\ConnectionSpec The connection specification
         */
        public function __construct($msg,$number,$sql,ConnectionSpec $connection)
        {
            parent::__construct($msg,$number);
            $this->_connection = $connection;
            $this->_sql = $sql;
        }

        /**
         * Get the SQL statement related to this exception.
         * @return string
         */
        public function getSQL() { return $this->_sql; }

        /**
         * Get the Connectionspec that was used when generating the error.
         *
         * @return \CMSMS\Database\ConnectionSpec
         */
        public function getConnectionSpec() { return $this->_connection; }
    }

    /**
     * A special exception indicating a problem connecting to the database.
     */
    class DatabaseConnectionException extends \Exception {}

} // end of Namespace
