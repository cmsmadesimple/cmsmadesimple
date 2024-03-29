<?php

namespace CMSMS\Database\mysqli;

class Statement extends \CMSMS\Database\Statement
{
    private $_data;

    // meta...
    private $_bind;
    private $_bound;
    private $_types;
    private $_stmt; // the statement object.
    private $_meta; // after first execute
    private $_num_rows; // after first execute
    private $_row; // updates after each execute for queries with a resultset
    private $_pos; // updates after each execute for queries with a resultset

    public function __construct(Connection $conn,$sql = null)
    {
        // this is just for type checking.
        parent::__construct($conn,$sql);
    }

    public function __destruct()
    {
        if( $this->_stmt ) {
            $this->_stmt->free_result();
            $this->_stmt->close();
        }
    }

    protected function get_type_char($var)
    {
        $t = gettype($var);
        switch( $t ) {
        case 'double':
            return 'd';
        case 'boolean':
        case 'integer':
            return 'i';
        case 'string':
        default:
            return 's';
        }
    }

    protected function set_bound_data($data)
    {
        $this->_data = $data;
        reset($this->_data);
    }

    protected function bind_params()
    {
        if( !$this->_stmt ) $this->prepare($this->sql);

        // get the type string
        $this->_types = '';
        $keys = null;
        $args = func_get_args();
        if( count($args) == 1 && is_array($args) && is_array($args[0]) ) {
            // we expect that the data is an associtive array
            $row = $args[0];
            foreach( $row as $key => $val ) {
            	$this->_types .= $this->get_type_char($val);
            }
            $this->_bind = array_values($row);
            $keys = array_keys($row);
        } else {
            // function called with numerous parameters... get their types
            $keys = array_keys($args);
            foreach( $args as $val ) {
                $this->_types .= $this->get_type_char($val);
            }
            $this->_bind = array_values($args);
        }

        $this->_bound = array();
        $this->_bound[] =& $this->_types;
        for( $i = 0; $i < count($keys); $i++ ) {
            $this->_bound[] =& $this->_bind[$i];
        }
        call_user_func_array(array($this->_stmt,'bind_param'),$this->_bound);
    }

    protected function prepare($sql)
    {
        $conn = $this->db->get_inner_mysql();
        if( !$conn || !$this->db->IsConnected() ) throw new \LogicException('Attempt to create prepared statement when database is not connected');
        $this->_stmt = $conn->prepare( (string) $sql );
	if( !$this->_stmt ) throw new \LogicException('Could not prepare a statement: '.$conn->error);
        $this->_row = null;
        $this->_pos = 0;
    }

    public function Bind(array $data)
    {
        parent::Bind($data);
        $first = $data[0];
        $this->bind_params($first);
    }

    public function EOF()
    {
        if( $this->_meta ) return ($this->_pos >= $this->_num_rows);
        if( !$this->_data ) return TRUE;
        return (current($this->_data) === FALSE);
    }

    public function MoveFirst()
    {
        if( $this->_meta ) $this->_stmt->data_seek(0);
        if( $this->_data ) reset($this->_data);
    }

    public function MoveNext()
    {
        if( $this->_meta ) $this->_pos = $this->_pos + 1;
        if( $this->_data ) next($this->_data);
    }

    public function Fields($col = null)
    {
        $row = null;
        if( $this->_stmt ) {
            $this->_stmt->fetch();
            $row = $this->_row;
        }
        if( !$row && $this->_data ) $row = current($this->_data);
        if( !$row ) return; // nothing

        if( $col ) {
            if( isset($row[$col]) ) return $row[$col];
        } else {
            return $row;
        }
    }

    public function Execute()
    {
        if( !$this->_stmt ) $this->prepare($this->_sql);
        $args = func_get_args();
        if( count($args) == 1 && is_array($args) && is_array($args[0]) ) $args = $args[0];

        /* if we have param count, find some arguments... either via the execute method... or via bound params */
        $pc = $this->_stmt->param_count;
        $fc = $this->_stmt->field_count;
        if( $args ) {
            $this->_data = $args;
            $this->bind_params($args);
        }
        if( $pc ) {
            // we are expecting paramers
            if( !count($args) ) {
                // get the arguments via the bound data current row.
                if( !$this->_bind ) throw new \LogicException('No bound parameters, and no arguments passed');
                if( count($this->_bind) != $pc ) throw new \LogicException('Incorrect number of bound parameters.  Expecting '.$this->_stmt->field_count);
                $args = $this->Fields();
            }
        }
        if( $pc != count($args) ) throw new \LogicException('Incorrect number of arguments. Expecting '.$pc);

        if( $args ) {
            // update bound values
            $keys = array_keys($args);
            for( $i = 0; $i < count($this->_bind); $i++ ) {
                $this->_bind[$i] = $args[$keys[$i]];
            }
        }

        $res = $this->_stmt->execute();
        if( !$res ) die('ERROR: '.$this->_stmt->error."\n");

        $this->_stmt->store_result();

        $meta = $this->_stmt->result_metadata();
        if( !$this->_meta && $meta ) {
            $this->_num_rows = $this->_stmt->num_rows;
            $this->_meta = $meta;
            $this->_row = array();
            while( $field = $this->_meta->fetch_field() ) {
                $this->_row[$field->name] = null;
                $params[] =& $this->_row[$field->name];
            }
            call_user_func_array(array($this->_stmt,'bind_result'),$params);
        }
    }
}
