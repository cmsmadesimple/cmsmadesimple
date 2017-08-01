<?php
namespace AdminLog;

// filter value object.
class filter
{
    private $_data = [ 'severity'=>-1, 'username'=>null, 'subject'=>null, 'msg'=>null, 'limit'=>100, 'offset'=>0 ];

    public function __get( $key ) {
        switch( $key ) {
        case 'subject':
        case 'username':
        case 'msg':
            return trim($this->_data[$key]);

        case 'severity':
        case 'limit':
        case 'offset':
            return (int) $this->_data[$key];

        default:
            throw new \LogicException("$key is not a gettable member of ".__CLASS__);
        }
    }

    public function __set( $key, $val )
    {
        switch( $key ) {
        case 'subject':
        case 'username':
        case 'msg':
            $this->_data[$key] = trim($val);
            break;
        case 'severity':
            // allow null or negative value to indicate any severity
            if( is_null($val) || (int) $val < 0 ) {
                $this->_data[$key] = null;
            }
            else {
                $this->_data[$key] = max(0,min(3,(int)$val));
            }
            break;
        case 'limit':
            $this->_data[$key] = max(1,(int)$val);
            break;
        case 'offset':
            $this->_data[$key] = max(0,(int)$val);
            break;
        default:
            throw new \LogicException("$key is not a settable member of ".__CLASS__);
        }
    }
}