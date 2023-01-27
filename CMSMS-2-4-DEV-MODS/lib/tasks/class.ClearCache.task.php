<?php
class ClearCacheTask implements CmsRegularTask
{
    const  LASTEXECUTE_SITEPREF   = 'ClearCache_lastexecute';
    const  CACHEDFILEAGE_SITEPREF = 'auto_clear_cache_age';
    private $_age_days;

    public function get_name()
    {
        return get_class($this);
    }

    public function get_description()
    {
        return lang_by_realm('tasks','clearcache_taskdescription');
    }

    public function test($time = '')
    {
        $this->_age_days = (int)cms_siteprefs::get(self::CACHEDFILEAGE_SITEPREF,0);
        if( $this->_age_days == 0 ) return FALSE;

        // do we need to do this task.
        // we only do it daily.
        if( !$time ) $time = time();
        $last_execute = (int)cms_siteprefs::get(self::LASTEXECUTE_SITEPREF,0);
        if( ($time - 24*60*60) >= $last_execute ) {
            // set this preference here... prevents multiple requests at or about the same time from getting here.
            cms_siteprefs::set(self::LASTEXECUTE_SITEPREF,$time);
            return TRUE;
        }

        return FALSE;
    }

    public function execute($time = '')
    {
        if( !$time ) $time = time();

        // do the task.
        $gCms = CmsApp::get_instance();
        $gCms->clear_cached_files($this->_age_days);
        return TRUE;
    }

    public function on_success($time = '')
    {
        if( !$time ) $time = time();
        cms_siteprefs::set(self::LASTEXECUTE_SITEPREF,$time);
    }

    public function on_failure($time = '')
    {
        // if we failed,  we can do this again at the next request.
        if( !$time ) $time = time();
        cms_siteprefs::remove(self::LASTEXECUTE_SITEPREF);
    }
} // end of class
