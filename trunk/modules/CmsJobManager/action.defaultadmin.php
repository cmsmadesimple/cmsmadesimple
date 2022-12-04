<?php
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

$jobs = [];
$job_objs = \CmsJobManager\JobQueue::get_all_jobs();
if( $job_objs ) {
    foreach( $job_objs as $job ) {
        $obj = new StdClass;
        $obj->name = $job->name;
        $obj->module = $job->module;
        $obj->frequency = (\CmsJobManager\utils::job_recurs($job)) ? $job->frequency : null;
        $obj->created = $job->created;
        $obj->start = $job->start;
        $obj->until = (\CmsJobManager\utils::job_recurs($job)) ? $job->until : null;
        $obj->errors = $job->errors;
        $jobs[] = $obj;
    }
}

$list = array();
$list[''] = '';
$list[\CMSMS\Async\CronJob::RECUR_NONE] = '';
$list[\CMSMS\Async\CronJob::RECUR_15M] = $this->Lang('recur_15m');
$list[\CMSMS\Async\CronJob::RECUR_30M] = $this->Lang('recur_30m');
$list[\CMSMS\Async\CronJob::RECUR_HOURLY] = $this->Lang('recur_hourly');
$list[\CMSMS\Async\CronJob::RECUR_120M] = $this->Lang('recur_120m');
$list[\CMSMS\Async\CronJob::RECUR_180M] = $this->Lang('recur_180m');
$list[\CMSMS\Async\CronJob::RECUR_DAILY] = $this->Lang('recur_daily');
$list[\CMSMS\Async\CronJob::RECUR_WEEKLY] = $this->Lang('recur_weekly');
$list[\CMSMS\Async\CronJob::RECUR_MONTHLY] = $this->Lang('recur_monthly');

$tpl = $this->create_new_template('defaultadmin.tpl');
$tpl->assign('jobs',$jobs);
$tpl->assign('async_freq',\CmsJobManager\utils::get_async_freq());
$tpl->assign('last_processing',(int) $this->GetPreference('last_processing'));
$tpl->assign('recur_list',$list);
$tpl->assign('async_freq',\CmsJobManager\utils::get_async_freq());
$tpl->display();
