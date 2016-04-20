<?php

namespace CMSMS\Async;

// a class to allow asynchronous processing of a CmsRegularTask
// assume it's already been tested... and can just execute.
class RegularTask extends Job
{
    private $_task;

    public function __construct(\CmsRegularTask $task)
    {
        parent::__construct();
        $this->_task = $task;
        $this->name = $task->get_name();
    }

    public function __get($key)
    {
        switch( $key ) {
        case 'task':
            return $this->_task;
        default:
            return parent::__get($key);
        }
    }

    public function __set($key,$val)
    {
        switch( $key ) {
        case 'task':
            if( !$val instanceof \CmsRegularTask ) throw new \LogicException('Invalid value for '.$key.' in a '.__CLASS__);
            $this->_task = $val;
            break;

        default:
            return parent::__set($key,$val);
        }
    }

    public function execute()
    {
        // no testing, just execute the damned thing
        if( !$this->_task ) throw new \LogicException(__CLASS__.' job is being executed, but has no task associated');
        $task = $this->_task;
        $now = time();
        $res = $task->execute($now);
        if( $res ) {
            $task->on_success($now);
        } else {
            $task->on_failure($now);
        }
    }
}