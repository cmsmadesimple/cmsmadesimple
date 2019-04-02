<?php
namespace CMSMS\internal;
use CMSMS\hook_manager;

class hook_handler
{

    /**
     * @ignore
     */
    public $callable;

    /**
     * @ignore
     */
    public $priority;

    /**
     * @ignore
     */
    public function __construct($callable,$priority)
    {
        // todo: test if is callable.
        $this->priority = max(hook_manager::PRIORITY_HIGH,min(hook_manager::PRIORITY_LOW,(int)$priority));
        $this->callable = $callable;
    }
} // class
