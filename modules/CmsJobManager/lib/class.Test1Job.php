<?php

use \CMSMS\Async\Job;

class Test1Job extends Job
{
    public function __construct()
    {
        parent::__construct();
        $this->module = 'CmsJobManager';
    }

    public function execute()
    {
        // simple test, creates an audit string
        //some_unknown_function(); // intentionally generate an error.
        audit('','CmsJobMgr','Job Test1 Complete');
        debug_to_log('Job Test1 Complete');
    }
}
