<?php
if( !isset($gCms) ) exit;
if( !isset($_REQUEST['cms_cron']) ) exit();

while(ob_get_level()) @ob_end_clean();
ignore_user_abort();
header('Connection: close');
header('X-CMSMS: Processing');
echo ' '; // single character
flush();

if( !function_exists('_cmsjobmgr_errorhandler') ) {
    // on cleanup, put this cruft into a utils class..
    function _cmsjobmgr_process_errors()
    {
        $fn = md5(__FILE__).'.err';
        $fn = TMP_CACHE_LOCATION.'/'.$fn;
        if( !is_file($fn) ) return;

        $data = file_get_contents($fn);
        @unlink($fn);
        if( !$data ) return;

        $tmp = explode("\n",$data);
        if( !is_array($tmp) || !count($tmp) ) return;

        $job_ids = [];
        foreach( $tmp as $one ) {
            $one = (int) $one;
            if( $one < 1 ) continue;
            if( !in_array($one,$job_ids) ) $job_ids[] = $one;
        }

        // have jobs to increase error count on.
        $db = \cms_utils::get_db();
        $sql = 'UPDATE '.CmsJobManager::table_name().' SET errors = errors + 1 WHERE id IN ('.implode(',',$job_ids).')';
        $db->Execute($sql);
        debug_to_log('Increased error count on '.count($job_ids).' jobs ');
    }

    function _cmsjobmgr_put_error($job_id)
    {
        $fn = md5(__FILE__).'.err';
        $fn = TMP_CACHE_LOCATION.'/'.$fn;
        $fh = fopen($fn,'a');
        fwrite($fh,$job_id."\n");
        fclose($fh);
    }

    function _cmsjobmgr_joberrorhandler($job,$errmsg,$errfile,$errline)
    {
        // no access to the database here.
        debug_to_log('Fatal error occurred processing async jobs at: '.$errfile.':'.$errline);
        debug_to_log('Msg: '.$errmsg);

        if( !is_object($job) ) return;
        _cmsjobmgr_put_error($job->id);
    }

    function _cmsjobmgr_errorhandler()
    {
        $err = error_get_last();
        if( is_null($err) ) return;
        if( $err['type'] != E_ERROR ) return;
        $mod = \ModuleOperations::get_instance()->get_module_instance('CmsJobManager');
        $job = $mod->get_current_job();
        if( !$job ) return;

        _cmsjobmgr_joberrorhandler($job,$err['message'],$err['file'],$err['line']);
    }
}

register_shutdown_function('_cmsjobmgr_errorhandler');

try {
    $now = time();
    $last_run = (int) $this->GetPreference('last_processing');
    if( $last_run >= $now - \CmsJobManager\utils::get_async_freq() ) return;

    _cmsjobmgr_process_errors();
    \CmsJobManager\JobQueue::clear_bad_jobs();

    $jobs = \CmsJobManager\JobQueue::get_jobs();
    if( !is_array($jobs) || !count($jobs) ) return; // nothing to do.

    if( $this->is_locked() ) {
        if( $this->lock_expired() ) {
            debug_to_log($this->GetName().': Removing an expired lock (probably an error occurred)');
            audit('',$this->GetName(),'Removing an expired lock. An error probably occurred with a previous job.');
            $this->unlock();
        } else {
            debug_to_log($this->GetName().': Processing still locked (probably because of an error)... wait for a bit');
            audit('',$this->GetName(),'Processing is already occurring.');
            exit;
        }
    }

    $time_limit = (int) $config['cmsjobmanager_timelimit'];
    if( !$time_limit ) $time_limit = (int) ini_get('max_execution_time');
    $time_limit = max(30,min(1800,$time_limit)); // no stupid time limit values
    set_time_limit($time_limit);
    $started_at = $now;

    $this->lock(); // get a new lock.
    foreach( $jobs as $job ) {
        // make sure we are not out of time.
        if( $now - $time_limit >= $started_at ) break;
        try {
            $this->set_current_job($job);
            $job->execute();
            if( \CmsJobManager\utils::job_recurs($job) ) {
                $job->start = \CmsJobManager\utils::calculate_next_start_time($job);
                if( $job->start ) {
                    $this->errors = 0;
                    $this->save_job($job);
                } else {
                    $this->delete_job($job);
                }
            } else {
                $this->delete_job($job);
            }
            $this->set_current_job(null);
            if( $config['developer_mode'] ) audit('','CmsJobManager','Processed job '.$job->name);
        }
        catch( \Exception $e ) {
            $job = $this->get_current_job();
            audit('','CmsJobManager','An error occurred while processing: '.$job->name);
            _cmsjobmgr_joberrorhandler($job,$e->GetMessage(),$e->GetFile(),$e->GetLine());
        }
    }
    $this->unlock();
    $this->GetPreference('last_processing',$now);
}
catch( \Exception $e ) {
    // some other error occurred, not processing jobs.
    debug_to_log('--Major async processing exception--');
    debug_to_log('exception '.$e->GetMessage());
    debug_to_log($e->GetTraceAsString());
}

exit;
