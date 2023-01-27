<?php
$lang['created'] = 'Created';
$lang['errors'] = 'Errors';
$lang['evtdesc_CmsJobManager::OnJobFailed'] = 'Sent after a job is removed from the job queue after failing too many times';
$lang['evthelp_CmsJobManager::OnJobFailed'] = '<h4>Parameters:</h4>
<ul>
  <li>"job" - A reference to the \CMSMS\Async\Job job object that has failed</li>
</ul>';
$lang['frequency'] = 'Frequency';
$lang['friendlyname'] = 'Background Job Manager';
$lang['info_background_jobs'] = 'This panel lists information about all currently known background jobs. It is normal for jobs to appear and disappear on this list frequently. If a job has a high error count OR never started it may mean that you need to investigate the reasons for that error.';
$lang['info_no_jobs'] = 'There are currently no jobs in the queue';
$lang['jobs'] = 'Jobs';
$lang['moddescription'] = 'A module for managing asynchronous processing jobs.';
$lang['module'] = 'Module';
$lang['name'] = 'Name';
$lang['processing_freq'] = 'Maximum processing frequency (seconds)';
$lang['recur_120m'] = 'Every 2 Hours';
$lang['recur_15m'] = 'Every 15 Minutes';
$lang['recur_180m'] = 'Every 3 Hours';
$lang['recur_30m'] = 'Every 30 Minutes';
$lang['recur_daily'] = 'Daily';
$lang['recur_hourly'] = 'Hourly';
$lang['recur_monthly'] = 'Monthly';
$lang['recur_weekly'] = 'Weekly';
$lang['settings'] = 'Settings';
$lang['start'] = 'Start';
$lang['until'] = 'Until';

$lang['help'] = <<<EOT
<h3>What does this do?</h3>
<p>This is a CMSMS core module that provides functionality for processing jobs asynchronously (in the background) as the website is handling requests.</p>
<p>CMSMS and third party modules can create jobs to perform tasks that do not need direct user intervention or that can take some time to process.  This module provides the processing capability for those jobs.</p>
<h3>How do I use it?</h3>
<p>This module has no interaction of its own.  It does provide a simple job report that lists jobs that the manager currently has in it's queue.  Jobs may regularly pop on to, and off of this queue so refreshing the page from time to time may give you an indication as to what is happening in the background of your site.</p>
<p>This module will only process jobs at most every minute, and at least every ten minutes.  Though the default is 3 minutes.  This infrequent processing is to ensure reasonable performance on most websites.</p>
<p>You can adjust the frequency by adding a cmsjobmgr_asyncfreq variable into the config.php file for your site containing an integer value between 0 and 10.</p>
<pre>i.e: <code>\$config["cmsjobmgr_asyncfreq"] = 5;</code>.</pre>
<p><strong>Note:</strong> It is not possible to disable asynchronous processing completely.  This is because some functioning of the CMSMS core relies on this functionality.</p>
<h3>Ensuring that background processing occurs.</h3>
<p>On some low-demand sites where incomming requests may not occur frequently, but you want to ensure that background processing occurs, we recommend that you setup a cron job to regularly (every 3 to 5 minutes) request a URL on the site.   This can be a simple content page with no content of it's own that is not shown in the menu.  i.e.:</p>
<pre>*/3 * * * * wget -O /dev/null https://www.mysite.com/</pre>
<h3>What about problem jobs.</h3>
<p>From time to time some applications may create jobs that fail, exiting with some sort of error.  CmsJobManager will remove the job after the job has failed a number of times.  At which time the originating code can re-create the job.  If you encounter a problematic job that continues to fail this is a bug that should be diagnosed, and reported in detail to the appropriate developers.</p>
EOT;

?>