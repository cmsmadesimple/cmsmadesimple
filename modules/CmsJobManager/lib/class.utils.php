<?php

namespace CmsJobManager;

final class utils
{
    private function __construct() {}

    public static function get_async_freq()
    {
        $config = \cms_config::get_instance();
        $minutes = (int) $config['cmsjobmgr_asyncfreq'];
	$minutes = max(3,$minutes);
        $minutes = min(60,$minutes);
        $freq = (int) $minutes * 60; // config entry is in minutes.
        return $freq;
    }

    public static function job_recurs(\CMSMS\Async\Job $job)
    {
        if( ! $job instanceof \CMSMS\Async\CronJobInterface ) return FALSE;
        if( $job->frequency == $job::RECUR_NONE ) return FALSE;
        return TRUE;
    }

    public static function calculate_next_start_time(\CMSMS\Async\CronJob $job)
    {
        $out = null;
        $now = time();
        if( !self::job_recurs($job) ) return $out;
        switch( $job->frequency ) {
        case $job::RECUR_NONE:
            return $out;
        case $job::RECUR_15M:
            $out = $now + 15 * 60;
            break;
        case $job::RECUR_30M:
            $out = $now + 30 * 60;
            break;
        case $job::RECUR_HOURLY:
            $out = $now + 3600;
            break;
        case $job::RECUR_2H:
            $out = $now + 2 * 3600;
            break;
        case $job::RECUR_3H:
            $out = $now + 3 * 3600;
            break;
        case $job::RECUR_DAILY:
            $out = $now + 3600 * 24;
            break;
        case $job::RECUR_WEEKLY:
            $out = strtotime('+1 week',$now);
            break;
        case $job::RECUR_MONTHLY:
            $out = strtotime('+1 month',$now);
            break;
        }
        debug_to_log("adjusted to {$out} -- {$now} // {$job->until}");
        if( !$job->until || $out <= $job->until ) return $out;
    }

}
