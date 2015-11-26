<?php

namespace CMSMS\Database {

    abstract class Connection
    {
        const ERROR_CONNECT = 'CONNECT';
        const ERROR_EXECUTE = 'EXECUTE';
        const ERROR_TRANSACT = 'TRANSACTION';
        const ERROR_DATADICT = 'DATADICTIONARY';

        private $_debug;
        private $_debug_cb;
        private $_query_count = 0;
        private $_queries = array();
        private $_errorhandler;

        protected $_connectionSpec;
        protected $sql;
        protected $query_time_total;

        public function __construct(ConnectionSpec $spec)
        {
            $this->_connectionSpec = $spec;
        }

        public function __get($key)
        {
            if( $key == 'query_time_total' ) return $this->query_time_total;
            if( $key == 'query_count' ) return $this->_query_count;
        }

        public function __isset($key)
        {
            if( $key == 'query_time_total' ) return TRUE;
            if( $key == 'query_count' ) return TRUE;
            return FALSE;
        }

        // data dictionary stuff
        abstract public function &NewDataDictionary();

        // connection and disconnection
        abstract public function DbType();
        abstract public function Connect();
        abstract public function Disconnect();
        abstract public function IsConnected();
        final public function Close() { return $this->Disconnect(); }

        //// utilities

        abstract public function qstr($str);
        abstract public function concat();
        abstract public function IfNull( $field, $ifNull );

        abstract public function Affected_Rows();
        abstract public function Insert_ID();

        //// primary query functions

        // returns resultset
        abstract public function do_sql($sql);

        public function &SelectLimit( $sql, $nrows = -1, $offset = -1, $inputarr = null )
        {
            $limit = null;
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
                    if ($i+1 != sizeof($sqlarr)) return $false;
                }
            }
            $sql .= $limit;

            $rs = $this->do_sql( $sql );
            return $rs;
        }

        public function Execute($sql, $inputarr = null)
        {
            $rs = $this->SelectLimit($sql, -1, -1, $inputarr );
            return $rs;
        }

        public function GetArray($sql, $inputarr = null)
        {
            $result = $this->SelectLimit( $sql, -1, -1, $inputarr );
            $data = $result->GetArray();
            return $data;
        }

        public function GetAll($sql, $inputarr = null)
        {
            return $this->GetArray($sql, $inputarr);
        }

        public function GetCol($sql, $inputarr = null, $trim = false)
        {
            $data = false;
            $result = $this->SelectLimit($sql, -1, -1, $inputarr);
            if ($result) {
                $data = array();
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

        public function GetRow($sql, $inputarr = null)
        {
            $rs = $this->SelectLimit( $sql, 1, -1, $inputarr );
            return $rs->Fields();
        }

        public function GetOne($sql, $inputarr = null)
        {
            $rs =  $this->SelectLimit( $sql, 1, -1, $inputarr );
            $res = $rs->Fields();
            if( !$res ) return;
            $key = array_keys($res)[0];
            return $res[$key];
        }

        //// transactions

        abstract public function BeginTrans();
        abstract public function StartTrans();
        abstract public function CompleteTrans();
        abstract public function CommitTrans($ok = true);
        abstract public function RollbackTrans();
        abstract public function FailTrans();
        abstract public function HasFailedTrans();

        //// sequence table stuff

        abstract public function GenID($seqname);

        // these methods should be in the DataDictionary stuff.
        abstract public function CreateSequence($seqname,$startID=1);
        abstract public function DropSequence($seqname);

        //// time and date stuff

        public function DBTimeStamp($timestamp)
        {
            if (empty($timestamp) && $timestamp !== 0) return 'null';

            # strlen(14) allows YYYYMMDDHHMMSS format
            if (is_string($timestamp) && is_numeric($timestamp) && strlen($timestamp)<14) {
                // todo: test me.
                $timestamp = strtotime($timestamp);
            }
            return date("'Y-m-d H:i:s'",$timestamp);
        }

        public function UnixTimeStamp($str)
        {
            return strtotime($str);
        }

        // returns null, or a quote encoded string suitable for use in
        public function DBDate($date)
        {
            if (empty($date) && $date !== 0) return 'null';

            if (is_string($date) && !is_numeric($date)) {
                if ($date === 'null' || strncmp($date, "'", 1) === 0) return $date;
                $date = $this->UnixDate($date);
            }
            return strftime('%x',$date);
        }

        public function UnixDate()
        {
            return strtotime('today midnight');
        }

        // alias for unixtimestamp
        public function Time() { return $this->UnixTimeStamp(); }

        // alias for unixdate
        public function Date() { return $this->UnixDate(); }

        //// error and debug message handling

        abstract public function ErrorMsg();
        abstract public function ErrorNo();

        public function SetErrorHandler($fn = null)
        {
            $this->_errorhandler = null;
            if( $fn && is_callable($fn) ) $this->_errorhandler = $fn;
        }

        public function SetDebugMode($flag = true,$debug_handler = null)
        {
            $this->_debug = (bool) $flag;
            if( $debug_handler && is_callable($this->_debug_handler) ) $this->_debug_cb = $debug_handler;
        }

        public function SetDebugCallback(callable $debug_handler = null)
        {
            $this->_debug_cb = $debug_handler;
        }

        protected function add_debug_query($sql)
        {
            $this->_query_count++;
            debug_buffer('query: '.$sql);
            if( $this->_debug && $this->_debug_cb ) {
                $this->_queries[] = trim($sql);
                call_user_func($this->_debug_cb,$sql);
            }
        }

        public function OnError($errtype, $error_number, $error_message )
        {
            if( $this->_errorhandler && is_callable($this->_errorhandler) ) {
                call_user_func($fn, $this, $errtype, $error_number, $error_msg);
                return;
            }

            switch( $errtype ) {
            case self::ERROR_CONNECT:
                throw new DatabaseConnectionException($error_message,$error_number,$this->sql,$this->_connnectionSpec);

            case self::ERROR_EXECUTE:
                throw new DatabaseException($error_message,$error_number,$this->sql,$this->_connectionSpec);
            }
        }

        //// initialization

        public static function &Initialize(ConnectionSpec $spec)
        {
            if( !$spec->valid() ) throw new ConnectionSpecException();
            $connection_class = '\\CMSMS\\Database\\'.$spec->type.'\\Connection';
            if( !class_exists($connection_class) ) throw new \LogicException('Could not find a database abstraction layer named '.$spec->type);

            $obj = new $connection_class($spec);
            if( !($obj instanceof Connection ) ) throw new \LogicException("$connection_class is not derived from the primary database class.");
            if( $spec->debug ) $obj->SetDebugMode();
            $obj->Connect();
            return $obj;
        }

    } // end of class

    class DatabaseException extends \LogicException
    {
        protected $_connection;
        protected $_sql;

        public function __construct($msg,$number,$sql,ConnectionSpec $connection)
        {
            parent::__construct($msg,$number);
            $this->_connection = $connection;
            $this->_sql = $sql;
        }

        public function getSQL() { return $this->_sql; }
        public function getConnectionSpec() { return $this->_connection; }
    }

    class DatabaseConnectionException extends \CmsException {}

} // end of Namespace