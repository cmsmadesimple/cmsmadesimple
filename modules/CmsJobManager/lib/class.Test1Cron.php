<?php

use \CMSMS\Async\CronJob;

class Test1Cron extends CronJob
{
    public function __construct()
    {
        parent::__construct();
        $this->module = 'CmsJobManager';
        $this->frequency = self::RECUR_HOURLY;
        $this->until = strtotime('+1 day');
    }

    public function execute()
    {
        // simple test, creates an audit string
        //some_unknown_function(); // intentionally generate an error.
        audit('','CmsJobMgr','Cron Job Test1 Complete');
        debug_to_log('Cron Job Test1 Complete');
    }
}
