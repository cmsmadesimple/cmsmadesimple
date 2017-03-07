<?php
class PruneAdminlogTask implements CmsRegularTask
{
    const  LASTEXECUTE_SITEPREF   = 'PruneAdminlog_lastexecute';
    const  LIFETIME_SITEPREF = 'adminlog_lifetime';

    public function get_name()
    {
        return get_class();
    }

    public function get_description()
    {
        return lang_by_realm('tasks','adminlog_taskdescription');
    }

    public function test($time = '') {
        $lifetime = (int) get_site_preference(self::LIFETIME_SITEPREF, (60 * 60 * 24 * 31));
        if ($lifetime == -1) return FALSE; //only manual pruning

        // do we need to do this task.
        // we only do it daily.
        if (!$time) $time = time();
        $last_execute = get_site_preference(self::LASTEXECUTE_SITEPREF, 0);
        if (($time - 24 * 60 * 60) >= $last_execute) return TRUE;
        return FALSE;
    }

    public function execute($time = '')
    {
        if( !$time ) $time = time();

        // do the task.
        $lifetime = (int)get_site_preference(self::LIFETIME_SITEPREF,(60*60*24*31));
        $db = \CmsApp::get_instance()->GetDB();
        $q="DELETE FROM ".CMS_DB_PREFIX."adminlog WHERE timestamp<?";
        $p=array(time()-$lifetime);
        $dbresult=$db->Execute($q,$p);
        return TRUE;
    }

    public function on_success($time = '')
    {
        if( !$time ) $time = time();
        set_site_preference(self::LASTEXECUTE_SITEPREF,$time);
    }

    public function on_failure($time = '')
    {
        if( !$time ) $time = time();
        // nothing here.
    }
}