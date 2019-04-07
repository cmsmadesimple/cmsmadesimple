<?php
namespace PressRoom;
use \CmsRegularTask;

class HookTask implements CmsRegularTask
{

    private $_hookname;

    private $_data;

    public function __construct( string $hook_name, array $data = null )
    {
        $this->_hookname = $hook_name;
        $this->_data = $data;
    }

    public function get_name()
    {
        return $this->_hookname;
    }

    public function get_description()
    {
        // nothing here
    }

    public function test($time = '')
    {
        return true;
    }

    public function execute($time = '')
    {
        if( !$time ) $time = time();
        cmsms()->get_hook_manager()->emit( $this->_hookname, $this->_data );
        return TRUE;
    }

    public function on_success($time = '') {
    }

    public function on_failure($time = '') {
    }
} // class
