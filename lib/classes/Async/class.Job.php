<?php

namespace CMSMS\Async;

// get matching jobs in queue that can run (not )
// see if it can run... (not too early, not too late)
// load the job
// process the job.
// delete the job.
// go to next job.
// field: id, created, handler, module, recurs, recurs_until, start_time
abstract class Job
{
    const MODULE_NAME = 'CmsJobManager';
    private $_id;
    private $_created;
    private $_module;
    private $_start;
    private $_errors;

    public function __construct()
    {
        $this->_created = $this->_start = time();
    }

    public function __get($key)
    {
        $tkey = '_'.$key;
        switch( $key ) {
        case 'id':
        case 'created':
        case 'start':
        case 'errors':
            return (int) $this->$tkey;

        case 'module':
            return trim($this->$tkey);

        default:
            throw new \LogicException("$key is not a gettable member of ".get_class($this));
        }
    }

    public function __set($key,$val)
    {
        $tkey = '_'.$key;
        switch( $key ) {
        case 'module':
            $this->$tkey = trim($val);
            break;

        case 'errors':
            $this->$tkey = (int) $val;
            break;

        default:
            throw new \LogicException("$key is not a settable member of ".get_class($this));
        }
    }

    /**
     * @ignore
     * @internal
     */
    final public function set_id($id)
    {
        $id = (int) $id;
        if( $id < 1 ) throw new \LogicException('Invalid id passed to '.__METHOD__);
        if( $this->_id ) throw new \LogicException('Cannot overwrite an id in a job that has one');
        $this->_id = $id;
    }

    public function delete()
    {
        // get the asyncmanager module
        $module = ModuleOperations::get_instance()->get_module_instance(self::MODULE_NAME);
        if( !$module ) throw new \LogicException('Cannot delete a job... the CmsJobMgr module is not available');
        $module->delete_job($this);
    }

    public function save()
    {
        // get the AsyncManager module
        // call it's save method with this.
        $module = \ModuleOperations::get_instance()->get_module_instance(self::MODULE_NAME);
        if( !$module ) throw new \LogicException('Cannot save a job... the CmsJobMgr module is not available');
        $this->_id = (int) $module->save_job($this);
    }

    abstract public function execute();
}