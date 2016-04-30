<?php

namespace CmsJobManager;

final class utils
{
    private function __construct() {}

    public static function get_async_freq()
    {
        $config = \cms_config::get_instance();
        $freq = (int) $config['cmsjobmgr_asyncfreq'] * 60; // config entry is in minutes.
        $freq = max(30,min(600,$freq));
        return $freq;
    }

    public static function job_recurs(\CMSMS\Async\Job $job)
    {
        if( ! $job instanceof \CMSMS\Async\CronJobInterface ) return FALSE;
        if( $job->frequency == $job::RECUR_NONE ) return FALSE;
        return TRUE;
    }

    public static function calculate_next_start_time(Job $job)
    {
        $out = null;
        if( !self::job_recurs($job) ) return $out;
        switch( $job->frequency ) {
        case $job::RECUR_NONE:
            return $out;
        case $job::RECUR_15M:
            $out = $job->start + 15 * 60;
            break;
        case $job::RECUR_30M:
            $out = $job->start + 30 * 60;
            break;
        case $job::RECUR_HOURLY:
            $out = $job->start + 3600;
            break;
        case $job::RECUR_2H:
            $out = $job->start + 2 * 3600;
            break;
        case $job::RECUR_3H:
            $out = $job->start + 3 * 3600;
            break;
        case $job::RECUR_DAILY:
            $out = $job->start + 3600 * 24;
            break;
        case $job::RECUR_WEEKLY:
            $out = strtotime('+1 week',$job->start);
            break;
        case $job::RECUR_MONTHLY:
            $out = strtotime('+1 month',$job->start);
            break;
        }
        if( !$job->until || $out <= $job->until ) return $out;
    }

}