<?php
namespace CMSMS\internal;
use JsonSerializable;

// immutable
class hook_mapping implements JsonSerializable
{
    const TYPE_MODULE = 'module';
    const TYPE_CALLABLE = 'callable';
    const TYPE_SIMPLE = 'simple_plugin';

    private $_hook;
    private $_handlers;

    protected function __construct() {}

    public function __get( string $key )
    {
        switch( $key ) {
        case 'hook':
            return $this->_hook;

        case 'handlers':
            return $this->_handlers;

        default:
            throw new \InvalidArgumentException("$key is not a gettable property of ".__CLASS__);
        }
    }

    public function __set( string $key, $value )
    {
        throw new \InvalidArgumentException("$key is not a settable property of ".__CLASS__);
    }

    public function has_handlers()
    {
        if( is_null($this->_handlers) ) return false;
        return count($this->_handlers) > 0;
    }

    public static function from_array( array $in ) : hook_mapping
    {
        $obj = new self;
        foreach( $in as $key => $val ) {
            switch( $key ) {
            case 'hook':
                $val = trim($val);
                if( empty($val) ) throw new \InvalidArgumentException('Invalid data passed to '.__METHOD__);
                $obj->_hook = $val;
                break;

            case 'handlers':
                if( empty($val) || !is_array($val) ) throw new \InvalidArgumentException('Invalid data passed to '.__METHOD__);
                foreach( $val as $handler ) {
                    if( !is_array($handler) || !isset($handler['type']) || !isset($handler['name']) ) {
                        throw new \InvalidArgumentException('Invalid data passed to '.__METHOD__.'(2)');
                    }
                    if( !in_array($handler['type'], [ self::TYPE_MODULE, self::TYPE_CALLABLE, self::TYPE_SIMPLE ] ) ) {
                        throw new \InvalidArgumentException('Invalid data passed to '.__METHOD__.'(3)');
                    }
                    $obj->_handlers[$handler['name']] = $handler;
                }
            }
        }
        return $obj;
    }

    public function add_handler( string $type, string $handler ) : hook_mapping
    {
        $type = trim($type);
        switch( $type ) {
        case self::TYPE_MODULE:
        case self::TYPE_SIMPLE:
        case self::TYPE_CALLABLE:
            break;
        default:
            throw new \InvalidArgumentException('Invalid handler type passed to '.__METHOD__);
            break;
        }
        $handler = trim($handler);
        if( empty($handler) ) throw new \InvalidArgumentException('Invalid handler name passed to '.__METHOD__);

        $handlers = $this->_handlers;
        $handlers[] = $handler;
        $obj = clone $this;
        $obj->_handlers = array_unique($handlers);
        return $obj;
    }

    public function remove_handler( string $type, string $handler ) : hook_mapping
    {
        $type = trim($type);
        switch( $type ) {
        case self::TYPE_MODULE:
        case self::TYPE_SIMPLE:
        case self::TYPE_CALLABLE:
            break;
        default:
            throw new \InvalidArgumentException('Invalid handler type passed to '.__METHOD__);
            break;
        }
        $handler = trim($handler);
        if( empty($handler) ) throw new \InvalidArgumentException('Invalid handler name passed to '.__METHOD__);

        $handlers = null;
        foreach( $this->_handlers as $one ) {
            if( $one['type'] == $type && $one['name'] == $name ) continue;
            $handlers[] = $one;
        }
        $obj = clone $this;
        $obj->_handlers = $handlers;
        return $obj;
    }

    public function JsonSerialize()
    {
        return [ 'hook'=>$this->_hook, 'handlers'=>$this->_handlers ];
    }

} // class