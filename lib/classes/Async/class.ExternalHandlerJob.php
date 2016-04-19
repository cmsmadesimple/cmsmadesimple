<?php

namespace \CMSMS\Async;

class ExternalHandlerJob extends Job
{
    const HANDLER_UDT   = '_UDT_';
    private $_data = ['function'=>null,'is_udt'=>FALSE];

    public function __get($key)
    {
        switch( $key ) {
        case 'function':
            return trim($this->_data[$key]);

        case 'is_udt':
            return (bool) $this->_data[$key];

        default:
            return parent::__get($key);
        }
    }

    public function __set($key,$val)
    {
        switch( $key ) {
        case 'function':
            $this->_data[$key] = trim($val);
            break;

        case 'is_udt':
            $this->_data[$key] = cms_to_bool($val);
            break;

        default:
            return parent::__set($key,$val);
        }
    }

    public function execute()
    {
        if( $this->is_udt ) {
            // execute the UDT, pass in this
            $tmp = $this->function;
            UserTagOperations::get_instance()->$tmp();
        }
        else {
            // call the function, pass in this.
            $module_name = $this->module;
            if( $module_name ) {
                $mod_obj = \CmsApp::get_instance()->GetModule($module_name);
                if( !is_object($mod_obj) ) throw new \RuntimeException('Job requires '.$module_name.' but the module could not be loaded');
            }
            call_user_func($this->function);
        }
    }
}
