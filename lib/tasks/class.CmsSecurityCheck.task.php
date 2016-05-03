<?php
class CmsSecurityCheckTask implements CmsRegularTask
{
    const  LASTEXECUTE_SITEPREF   = __CLASS__;

    public function get_name()
    {
        return __CLASS__;
    }

    public function get_description()
    {
        return __CLASS__;
    }

    public function test($time = '')
    {
        // do we need to do this task.
        // we only do it daily.
        if( !$time ) $time = time();
        $last_execute = (int) \cms_siteprefs::get(self::LASTEXECUTE_SITEPREF);
        debug_to_log($last_execute,__METHOD__);
        if( $last_execute > ($time - 24*60*60) ) return FALSE;
        return TRUE;
    }

    public function execute($time = '')
    {
        if( !$time ) $time = time();

        // check if config is writable
        if( is_writable(CONFIG_FILE_LOCATION) ) {
            $alert = new \CMSMS\AdminAlerts\SimpleAlert('Modify Site Preferences');
            $alert->name = __CLASS__.'config';
            $alert->msg = lang('config_writable');
            $alert->priority = $alert::PRIORITY_HIGH;
            $alert->title = lang('security_issue');
            $alert->save();
        }

        // check if install file exists
        $pattern = cms_join_path(CMS_ROOT_PATH,'cmsms-*-install.php');
        $files = glob($pattern);
        if( is_array($files) && count($files) > 0 ) {
            $fn = basename($files[0]);
            $alert = new \CMSMS\AdminAlerts\SimpleAlert('Modify Site Preferences');
            $alert->name = __CLASS__.'install';
            $alert->msg = lang('installfileexists',$fn);
            $alert->priority = $alert::PRIORITY_HIGH;
            $alert->title = lang('security_issue');
            $alert->save();
        }

        // check if mail is configured
        // not really a security issue... but meh, it saves another class.
        if(  !cms_siteprefs::get('mail_is_set',0) ) {
            $alert = new \CMSMS\AdminAlerts\SimpleAlert('Modify Site Preferences');
            $alert->name = __CLASS__.'mail';
            $alert->msg = lang('info_mail_notset');
            $alert->priority = $alert::PRIORITY_HIGH;
            $alert->title = lang('config_issue');
            $alert->save();
        }
        return TRUE;
    }

    public function on_success($time = '')
    {
        if( !$time ) $time = time();
        \cms_siteprefs::set(self::LASTEXECUTE_SITEPREF,$time);
    }

    public function on_failure($time = '')
    {
        // nothing here.
    }
}
