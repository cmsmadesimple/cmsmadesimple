<?php
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

$jobs = [];
$job_objs = \CmsJobManager\JobQueue::get_jobs();
if( $job_objs ) {
    foreach( $job_objs as $job ) {
        $obj = new StdClass;
        $obj->name = $job->name;
        $obj->frequency = (\CmsJobManager\utils::job_recurs($job)) ? $job->frequency : null;
        $obj->created = $job->created;
        $obj->start = $job->start;
        $obj->errors = $job->errors;
        $jobs[] = $obj;
    }
}

//$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_jobs_tab.tpl'),null,null,$smarty);
$tpl = $this->create_new_template('defaultadmin.tpl');
$tpl->assign('jobs',$jobs);
$tpl->assign('async_freq',\CmsJobManager\utils::get_async_freq());
$tpl->assign('last_processing',(int) $this->GetPreference('last_processing'));
$tpl->display();
