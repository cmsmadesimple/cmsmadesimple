<?php
namespace News2;
use \CmsRegularTask;
use CMSMS\HookManager;

class HookTask implements CmsRegularTask
{

    private $_hookname;

    private $_data;

    public function __construct( string $hook_name, array $data = null )
    {
        $this->_hookname = $hook_name;
        $this->_data = $data;
    }

    public function get_name() { return $this->_hookname;
    }
    public function get_description() {
    }
    public function test($time = '') { return true;
    }

    public function execute($time = '')
    {
        if( !$time ) $time = time();
        debug_to_log('HookTask - do hook '.$this->_hookname );
        HookManager::do_hook( $this->_hookname, $this->_data );
        return TRUE;
    }

    public function on_success($time = '') {
    }

    public function on_failure($time = '') {
    }
} // class
