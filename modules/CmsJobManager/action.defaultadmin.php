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

#
# EOF
#