<?php
namespace PressRoom;

class Category
{

    private $_data = [
        'id'=>null, 'name'=>null, 'alias'=>null, 'parent_id'=>-1, 'item_order'=>null,
        'hierarchy'=>null, 'long_name'=>null, 'image_url'=>null
        ];

    protected function __construct()
    {
        // nothing here
    }

    public function __get( $key )
    {
        switch( $key ) {
            case 'id':
                return (int) $this->_data[$key];
            case 'name':
            case 'alias':
            case 'image_url':
                return trim($this->_data[$key]);
            case 'parent_id':
            case 'item_order':
                return (int) $this->_data[$key];
            case 'hierarchy':
            case 'long_name':
                return trim($this->_data[$key]);
            case 'depth':
                $tmp = explode('.', $this->hierarchy);
                return count($tmp) - 1;
            default:
                throw new \InvalidArgumentException("$key is not a gettable property of ".get_class($this));
        }
    }

    public function __set( $key, $val )
    {
        switch( $key ) {
            case 'name':
                $this->_data[$key] = trim($val);
                break;
            case 'alias':
            case 'image_url':
                $this->_data[$key] = trim($val);
                break;
            case 'parent_id':
                $this->_data[$key] = (int) $val;
                break;
            case 'item_order':
                // don't know about this.
                $this->_data[$key] = (int) $val;
                break;
            default:
                throw new \InvalidArgumentException("$key is not a settable property of ".get_class($this));
        }
    }

    public static function from_row( array $row ) : Category
    {
        $obj = new self;
        foreach( $row as $key => $val ) {
            switch( $key ) {
                case 'id':
                case 'parent_id':
                case 'item_order':
                    $obj->_data[$key] = (int) $val;
                    break;
                case 'name':
                case 'alias':
                case 'image_url':
                case 'hierarchy':
                case 'long_name':
                    $obj->_data[$key] = trim($val);
                    break;
            }
        }
        return $obj;
    }

    public function isDecendedFromName( string $long_name )
    {
        return startswith( $this->long_name, $long_name);
    }

    public function isDescendedFrom( Category $b )
    {
        // test if category a is a child of category b
        if( $this->parent_id === $b->id ) return true;
        return $this->isDecendedFromName( $b->long_name );
    }
} // class
