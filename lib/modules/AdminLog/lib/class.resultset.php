<?php
namespace AdminLog;

class resultset extends \CmsDbQueryBase
{

    private $_db;

    public function __construct( \CMSMS\Database\Connection $db, filter $filter )
    {
        $this->_db = $db;
        $this->_args = $filter;
        $this->_offset = $filter->offset;
        $this->_limit = $filter->limit;
    }

    public function execute()
    {
        if( $this->_rs ) return;
        $filter = $this->_args;

        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.storage::table_name();
        $where = $parms = [];
        $severity = $filter->severity;
        if( !is_null($severity) && $severity > -1 ) {
            $where[] = 'severity >= ?';
            $parms[] = $severity;
        }
        if( ($val = $filter->username) ) {
            $where[] = 'username = ?';
            $parms[] = $val;
        }
        if( ($val = $filter->msg) ) {
            $where[] = 'msg LIKE ?';
            $parms[] = '%'.$val.'%';
        }
        if( ($val = $filter->subject) ) {
            $where[] = 'subject LIKE ?';
            $parms[] = '%'.$val.'%';
        }
        if( count($where) ) {
            $sql .= ' WHERE '.implode(' AND ',$where);
        }
        $sql .= ' ORDER BY timestamp DESC';

        $this->_rs = $this->_db->SelectLimit( $sql, $this->_limit, $this->_offset, $parms );
        $this->_totalmatchingrows = $this->_db->GetOne( 'SELECT FOUND_ROWS()' );
    }

    public function &GetObject()
    {
        $row = $this->fields;
        return $row;
    }

    public function GetMatches()
    {
        $this->execute();
        if( !$this->_rs ) \LogicException('Invalid query generated');

        $out = [];
        while( !$this->EOF() ) {
            $out[] = $this->GetObject();
            $this->MoveNext();
        }
        return $out;
    }
}
