<?php
namespace CMSMS\internal;
use JsonSerializable;

final class MactInfo implements JsonSerializable
{
    const CNTNT01 = 'cntnt01';

    private $_module;

    private $_action;

    private $_id;

    private $_inline;

    private $_params = [];

    public function __get( string $key )
    {
        switch( $key ) {
            case 'module':
                return $this->_module;

            case 'action':
                return $this->_action;

            case 'id':
                return $this->_id;

            case 'inline':
                if( $this->_id == self::CNTNT01 || !$this->_inline ) return 0;
                return 1;

            case 'params':
                return $this->_params;

            default:
                throw new \LogicException("$key is not a gettable property of ".__CLASS__);
        }
    }

    /**
     * @internal
     */
    public static function from_array( array $in )
    {
        $obj = new self;
        foreach( $in as $key => $val ) {
            switch( $key ) {
                case 'module':
                    $obj->_module = trim($val);
                    break;
                case 'id':
                    $obj->_id = trim($val);
                    break;
                case 'action':
                    $obj->_action = trim($val);
                    break;
                case 'inline':
                    $obj->_inline = is_null($val) ? false : cms_to_bool($val);
                    break;
                case 'params':
                    if( is_array($val) && !empty($val) ) $obj->_params = $val;
                    break;
            }
        }
        return $obj;
    }

    public function __set( string $key, $value )
    {
        throw new \LogicException("$key is not a settable property of ".__CLASS__);
    }

    public function jsonSerialize()
    {
        $out = [];
        $out['module'] = $this->module;
        $out['action'] = $this->action;
        $out['id'] = $this->id;
        $out['inline'] = $this->inline;
        if( !empty($this->_params) ) $out['params'] = $this->_params;
        return $out;
    }
} // class
