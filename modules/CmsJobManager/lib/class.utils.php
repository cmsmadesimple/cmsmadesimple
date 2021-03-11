<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

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

} // end of class

#
# EOF
#