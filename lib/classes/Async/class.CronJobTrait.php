<?php

namespace CMSMS\Async;

trait CronJobTrait
{
    // the start member in this class overrides that in the base class.
    private $_data = [ 'start'=>null, 'frequency' => self::RECUR_NONE, 'until'=>null  ];

    public function __construct()
    {
        parent::__construct();
        $this->_data['start'] = time();
    }

    public function __get($key)
    {
        switch( $key ) {
        case 'frequency':
            return trim($this->_data[$key]);

        case 'start':
        case 'until':
            return (int) $this->_data[$key];

        default:
            return parent::__get($key);
        }
    }

    public function __set($key,$val)
    {
        switch( $key ) {
        case 'frequency':
            switch( $val ) {
            case self::RECUR_NONE:
            case self::RECUR_HOURLY:
            case self::RECUR_DAILY:
            case self::RECUR_WEEKLY:
            case self::RECUR_MONTHLY:
                $this->_data[$key] = $val;
                break;
            default:
                throw new \LogicException("$val is an invalid value for $key");
            }
            break;

        case 'start':
            $val = (int) $val;
            if( $val < time() - 60 ) throw new \LogicException('Cannot adjust a start value to the past');
            // fall through.
        case 'until':
            $this->_data[$key] = (int) $val;
            break;

        default:
            return parent::__set($key,$val);
        }
    }
}