<?php

namespace CMSMS\Database\mysqli;

class ResultSet extends \CMSMS\Database\ResultSet
{
    private $_connection;
    private $_resultId;
    private $_fields;
    private $_nrows;
    private $_pos;
    private $_sql;

    public function __construct(\mysqli $conn, $resultId, $sql = null)
    {
        $this->_connection = $conn;
        $this->_resultId = $resultId;
        $this->_pos = 0;
        $this->_nrows = 0;
        $this->_sql = $sql;
        if( is_object($resultId) ) $this->_nrows = mysqli_num_rows( $resultId );
        if( !$this->EOF() ) $this->fetch_row();
    }

    public function __destruct()
    {
        if( $this->resultId ) mysqli_free_result( $this->resultId );
    }

    public function Close()
    {
        if( $this->resultId ) mysqli_free_result( $this->resultId );
        $this->_fields = $this->resultId = null;
    }

    public function Fields( $key = null )
    {
        $key = (string) $key;
        if( empty($key) ) return $this->_fields;
        return $this->fields[$key];
    }

    public function RecordCount()
    {
        return $this->_nrows;
    }

    public function EOF()
    {
        return ($this->_nrows == 0 || $this->_pos < 0 || $this->_pos >= $this->_nrows);
    }

    protected function Move($idx)
    {
        if( $idx == $this->_pos ) return TRUE;
        if( $idx >= 0 && $idx < $this->_nrows ) {
            if( mysqli_data_seek($this->_resultId, $idx) ) {
                $this->_pos = $idx;
                $this->fetch_row();
                return TRUE;
            }
        }
        $this->_pos = $this->_nrows;
        return FALSE;
    }

    public function MoveFirst()
    {
        if( $this->_pos == 0 ) return TRUE;
        return $this->Move(0);
    }

    public function MoveNext()
    {
        return $this->Move($this->_pos+1);
    }

    protected function fetch_row()
    {
        if( !$this->EOF() ) $this->_fields = mysqli_fetch_array($this->_resultId, MYSQLI_ASSOC);
    }

} // end of class
