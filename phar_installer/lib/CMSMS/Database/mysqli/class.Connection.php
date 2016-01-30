<?php

namespace CMSMS\Database\mysqli;

class Connection extends \CMSMS\Database\Connection
{
    private $_mysql;
    private $_in_transaction;
    private $_in_smart_transaction;
    private $_transaction_failed;

    public function DbType() { return 'mysqli'; }

    public function Connect()
    {
        if( !class_exists('\mysqli') ) throw new \LogicException("Configuration error... mysqli functions are not available");

        $this->_mysql = new \mysqli( $this->_connectionSpec->host, $this->_connectionSpec->username,
                                     $this->_connectionSpec->password,
                                     $this->_connectionSpec->dbname,
                                     (int) $this->_connectionSpec->port );
        if( $this->_mysql->connect_error ) {
            $this->_mysql = null;
            $this->OnError(self::ERROR_CONNECT,mysqli_connect_errno(),mysqli_connect_error());
            return FALSE;
        }
        return TRUE;
    }

    public function &NewDataDictionary()
    {
        $obj = new DataDictionary($this);
        return $obj;
    }

    public function Disconnect()
    {
        if( $this->_mysql ) {
            $this->_mysql->Close();
            $this->_mysql = null;
        }
    }

    public function &get_inner_mysql()
    {
        return $this->_mysql;
    }

    public function IsConnected()
    {
        return is_object($this->_mysql);
    }

    public function ErrorMsg()
    {
        if( $this->_mysql ) return $this->_mysql->error;
        return mysqli_connect_error();
    }

    public function ErrorNo()
    {
        if( $this->_mysql ) return $this->_mysql->errno;
        return mysqli_connect_errno();
    }

    public function Affected_Rows()
    {
        return $this->_mysql->affected_rows;
    }

    public function Insert_ID()
    {
        return $this->_mysql->insert_id;
    }

    public function qstr($str)
    {
        // note... this could be a two way tcp/ip or socket communication
        return "'".$this->_mysql->escape_string($str)."'";
    }

    public function Concat()
    {
		$arr = func_get_args();
		$list = implode(', ', $arr);

		if (strlen($list) > 0) return "CONCAT($list)";
    }

    public function IfNull( $field, $ifNull )
    {
        return " IFNULL($field, $ifNull)";
    }

    public function do_sql($sql)
    {
        $this->sql = $sql;
        $time_start = array_sum(explode(' ',microtime()));
        $resultid = $this->_mysql->query( $sql );
        $time_total = (array_sum(explode(' ', microtime())) - $time_start);
        $this->query_time_total += $time_total;
        if( !$resultid ) {
            $this->FailTrans();
            $this->OnError(self::ERROR_EXECUTE,$this->_mysql->errno, $this->_mysql->error);
            return;
        }
        $this->add_debug_query($sql);
        $resultset = new ResultSet( $this->_mysql, $resultid, $sql );
        return $resultset;
    }

    public function &Prepare($sql)
    {
        $stmt = new Statement($this,$sql);
        return $stmt;
    }

    public function BeginTrans()
    {
        if( $this->_in_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'Transactions cannot be nested');
            return FALSE;
        }
        $this->_in_transaction = TRUE;
        $this->_transaction_failed = FALSE;
        $this->Execute('SET AUTOCOMMIT=0; BEGIN');
        return TRUE;
    }

    public function StartTrans()
    {
        if( $this->_in_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'Transactions cannot be nested');
            return FALSE;
        }
        $this->_in_smart_transaction = TRUE;
        $this->BeginTrans();
    }

    public function RollbackTrans()
    {
        if( !$this->_in_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'BeginTrans has not been called');
            return FALSE;
        } else if( $this->_in_smart_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'Smart and simple transactions cannot be mixed.');
            return FALSE;
        }

        $this->Execute('ROLLBACK; SET AUTOCOMMIT=1');
        $this->_in_transaction = FALSE;
        return TRUE;
    }

	function CommitTrans($ok=true)
	{
		if (!$ok) return $this->RollbackTrans();

        if( !$this->_in_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'BeginTrans has not been called');
            return FALSE;
        } else if( $this->_in_smart_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'Smart and simple transactions cannot be mixed.');
            return FALSE;
        }

		$this->Execute('COMMIT; SET AUTOCOMMIT=1');
        $this->_in_transaction = FALSE;
		return TRUE;
	}

    public function CompleteTrans()
    {
        if( !$this->_in_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'BeginTrans has not been called');
            return FALSE;
        } else if( !$this->_in_smart_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'StartTrans has not been called');
            return FALSE;
        }

        $this->_in_smart_transaction = FALSE;
        if( $this->HasFailedTrans() ) {
            return $this->RollbackTrans();
        }
        else {
            return $this->CommitTrans();
        }
    }

    public function FailTrans()
    {
        if( $this->_in_transaction ) $this->_tranaction_failed = TRUE;
    }

    function HasFailedTrans()
    {
        if( $this->_in_transaction ) return $this->_transaction_failed;
        return FALSE;
    }

    public function GenID($seqname)
    {
        $getnext = sprintf('UPDATE %s SET id=LAST_INSERT_id(id+1);',$seqname);
        $this->Execute($getnext);
        return $this->_mysql->insert_id;
    }

    public function CreateSequence($seqname,$startID=1)
    {
        $out = array();
        $out[] = sprintf('CREATE TABLE %s (id int not null) ENGINE MyISAM',$seqname);
        $out[] = sprintf('INSERT INTO %s values (%s)',$seqname,$startID);
        $dict = $this->NewDataDictionary();
        $dict->ExecuteSQLArray($out);
        return TRUE;
    }

    public function DropSequence($seqname)
    {
        return $this->Execute(sprintf('DROP TABLE %s',$seqname));
    }
} //
