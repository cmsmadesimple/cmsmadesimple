<?php

namespace CMSMS\Database;

abstract class Statement
{
    private $_conn;
    private $_sql;

    public function __construct(Connection $conn,$sql = null)
    {
        $this->_conn = $conn;
        $this->_sql = $sql;
    }

    public function __get($key)
    {
        switch( $key ) {
        case 'db':
        case 'conn':
            return $this->_conn;

        case 'sql':
            return $this->_sql;
        }
    }

    public function Bind($data)
    {
        if( !is_array($data) || count($data) == 0 ) throw new \LogicException('Data passed to '.__METHOD__.' must be an associative array');
        $first = $data[0];
        if( !is_array($first) || count($first) == 0 ) throw new \LogicException('Data passed to '.__METHOD__.' must be an associative array');
        $keys = array_keys($first);
        if( is_numeric($keys[0]) && $keys[0] === 0 )  throw new \LogicException('Data passed to '.__METHOD__.' must be an associative array');

        $this->set_bound_data($data);
    }

    abstract protected function set_bound_data($data);

    abstract public function EOF();

    abstract public function MoveFirst();

    abstract public function MoveNext();

    abstract public function Fields($col = null);

    abstract public function Execute();
}
