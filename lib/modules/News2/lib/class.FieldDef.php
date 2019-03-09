<?php
namespace News2;

class FieldDef
{
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_SELECT = 'select';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_URL = 'url';
    const TYPE_MULTISELECT = 'multiselect';

    private $_data = [ 'id'=>null, 'name'=>null, 'type'=>null, 'item_order'=>null, 'extra'=>null ];

    protected function __construct() {
    }

    protected function _parseOptionsText( $str )
    {
        $out = null;
        $lines = explode( "\n", $str );
        foreach( $lines as $line ) {
            $line = trim($line);
            if( !$line ) continue;
            list($key,$val) = explode('=',$line,2);
            $key = trim($key);
            $val = trim($val);
            if( !$key && !$val ) continue;
            if( !$key ) $key = $val;
            else if( !$val ) $val = $key;
            $out[$key] = $val;
        }
        return $out;
    }

    public function __get( string $key )
    {
        switch( $key ) {
            case 'id':
                return (int) $this->_data[$key];
            case 'name':
                return trim($this->_data[$key]);
            case 'label':
                if( isset($this->_data['extra'][$key]) ) $val = trim($this->_data['extra'][$key]);
                if( empty($val) ) $val = $this->name;
                return $val;
            case 'raw_label':
                if( isset($this->_data['extra'][$key]) ) return trim($this->_data['extra'][$key]);
                break;
            case 'short_type':
                $val = str_replace('\\','/',$this->type);
                return basename($val);
            case 'type':
                return trim($this->_data[$key]);
            case 'item_order':
                return (int) $this->_data[$key];
            case 'options':
                $str = $this->getExtra('optionsText');
                if( $str ) return $this->_parseOptionsText( $str );
                return;
            default:
                throw new \InvalidArgumentException("$key is not a gettable member of ".get_class($this));
        }
    }

    public function __set( string $key, $val )
    {
        // only a fiew of the proeprties are settable.
        switch( $key ) {
            case 'name':
                $val = trim($val);
                if( !$val ) throw new \InvalidArgumentException("Attempt to set invalid field name");
                $this->_data[$key] = $val;
                break;

            case 'label':
                $this->_data['extra'][$key] = trim($val);
                break;

            case 'type':
                $this->_data[$key] = $val;
                break;

            case 'item_order':
                $this->_data[$key] = max(1,(int)$val);
                break;

            default:
                throw new \InvalidArgumentException("$key is not a settable proeprty of ".get_class($this));
        }
    }

    public function getExtra( string $key )
    {
        if( isset($this->_data['extra'][$key]) ) return $this->_data['extra'][$key];
    }

    public function setExtra( string $key, $val )
    {
        $this->_data['extra'][$key] = $val;
    }

    public function extraExists( string $key ) : bool
    {
        return isset($this->_data['extra'][$key]);
    }

    /**
     * @internal
     */
    public static function from_row( array $row ) : FieldDef
    {
        $obj = new self;
        foreach( array_keys($obj->_data) as $key ) {
            if( $key == 'id' && isset($row[$key]) ) {
                $obj->_data[$key] = (int) $row[$key];
            }
            else if( $key != 'extra' && isset($row[$key]) ) {
                $obj->$key = $row[$key];
            }
            else if( $key == 'extra' && isset($row[$key]) ) {
                if( is_string($row[$key]) ) $row[$key] = json_decode($row[$key], true);
                if( is_array($row[$key]) ) $obj->_data[$key] = $row[$key];
            }
        }
        return $obj;
    }

    /**
     * @internal
     */
    public function get_extra_as_string()
    {
        if( !empty($this->_data['extra']) ) return json_encode( $this->_data['extra'] );
    }
} // class
