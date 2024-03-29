<?php

namespace CMSMS\Database\mysqli;

class Connection extends \CMSMS\Database\Connection
{
    private $_mysql;
    private $_in_transaction = 0;
    private $_in_smart_transaction = 0;
    private $_transaction_status = TRUE;
    private $_transaction_failed = FALSE;

    public function DbType() { return 'mysqli'; }

    public function Connect()
    {
        if( !class_exists('\mysqli') ) throw new \LogicException("Configuration error... mysqli functions are not available");

        mysqli_report(MYSQLI_REPORT_STRICT);
        try {
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
        catch( \Exception $e ) {
            $this->_mysql = null;
            $this->OnError(self::ERROR_CONNECT,mysqli_connect_errno(),mysqli_connect_error());
            return FALSE;
        }
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

    public function get_inner_mysql()
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
        $res =  $this->_mysql->insert_id;
        return $res;
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

    protected function do_multisql($sql)
    {
        // no error checking for this stuff
        // and no return data
        $_t = $this->_mysql->multi_query($sql);
        if( $_t ) {
            do {
                $res = $this->_mysql->store_result();
            } while( $this->_mysql->more_results() && $this->_mysql->next_result() );
        }
    }
  
  /**
   * @throws \CMSMS\Database\DatabaseConnectionException
   */
  public function do_sql($sql)
    {
        // execute all queries, but only need the resultset from the last one.
        $resultset = null;
        $this->sql = $sql;
        $time_start = microtime(TRUE);
        $resultid = $this->_mysql->query( $sql );
        $time_total = microtime(TRUE) - $time_start;
        $this->query_time_total += $time_total;
        if( !$resultid ) {
            $this->FailTrans();
            $this->OnError(self::ERROR_EXECUTE,$this->_mysql->errno, $this->_mysql->error);
            return $resultset;
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
        if( $this->_in_smart_transaction ) return TRUE; // allow nesting in this case.
        $this->_in_transaction++;
        $this->_transaction_failed = FALSE;
        $this->Execute('BEGIN');
        return TRUE;
    }

    public function StartTrans()
    {
        if( $this->_in_smart_transaction ) {
            $this->_in_smart_transaction++;
            return;
        }

        if( $this->_in_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'Bad Transaction: StartTrans called within BeginTrans');
            return FALSE;
        }
        $this->_transaction_status = TRUE;
        $this->_in_smart_transaction++;
        $this->BeginTrans();
    }

    public function RollbackTrans()
    {
        if( !$this->_in_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'BeginTrans has not been called');
            return FALSE;
        }

        $this->_in_transaction--;
        $this->Execute('ROLLBACK');
        return TRUE;
    }

	function CommitTrans($ok=true)
	{
		if (!$ok) return $this->RollbackTrans();

        if( !$this->_in_transaction ) {
            $this->OnError( self::ERROR_TRANSACTION, -1, 'BeginTrans has not been called');
            return FALSE;
        }

        $this->_in_transaction--;
		$this->Execute('COMMIT');
		return TRUE;
	}

    public function CompleteTrans($autoComplete = true)
    {
        if( $this->_in_smart_transaction > 0 ) {
            $this->_in_smart_transaction--;
            return TRUE;
        }

        if( $this->_transaction_status && $autoComplete ) {
            if( !$this->CommitTrans() ) {
                $this->_transaction_status = FALSE;
            }
        } else {
            $this->RollbackTrans();
        }
        $this->_in_smart_transaction = 0;
        return $this->_transaction_status;
    }

    public function FailTrans()
    {
        $this->_transaction_status = FALSE;
    }

    function HasFailedTrans()
    {
        if( $this->_in_smart_transaction > 0 ) return $this->_transaction_status == FALSE;
        return FALSE;
    }

    public function GenID($seqname)
    {
        $sql = sprintf('UPDATE %s SET id=id+1;',$seqname);
        $this->Execute($sql);
        $sql = sprintf('SELECT id FROM %s',$seqname);
        return (int) $this->GetOne($sql);
    }

    public function CreateSequence($seqname,$startID=0)
    {
        $out = array();
        $startID = (int) $startID;
        $out[] = sprintf('CREATE TABLE %s (id int not null) ENGINE MyISAM',$seqname);
        $out[] = sprintf('INSERT INTO %s (id) values (%s)',$seqname,$startID);
        $dict = $this->NewDataDictionary();
        $dict->ExecuteSQLArray($out);
        return TRUE;
    }

    public function DropSequence($seqname)
    {
        return $this->Execute(sprintf('DROP TABLE %s',$seqname));
    }
} // end of class
