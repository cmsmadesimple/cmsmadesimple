<?php
namespace News2;
use News2;
use CmsRegularTask;

class CreateDraftAlertTask implements CmsRegularTask
{

    private $mod;

    public function __construct( News2 $mod )
    {
        parent::__construct();
        $this->mod = $mod;
        $this->module = $mod->GetName();
    }

    public function __wakeup()
    {
        if( $this->module ) $this->mod = \cms_utils::get_module( $this->module );
    }

    public function get_name()
    {
        return basename(get_class($this));
    }

    public function get_description()
    {
        return $this->get_name();
    }

    public function test($time = '')
    {
        if( !$time ) $time = time();
        $mod = \cms_utils::get_module(__NAMESPACE__);
        $lastrun = (int) $mod->GetPreference('task1_lastrun');
        if( $lastrun >= ($time - 900) ) return FALSE; // hardcoded to 15 minutes
        return TRUE;
    }

    public function on_success($time = '')
    {
        IF( !$time ) $time = time();
        $mod = \cms_utils::get_module(__NAMESPACE__);
        $mod->SetPreference('task1_lastrun',$time);
    }

    public function on_failure($time = '') {
    }

    public function execute($time = '')
    {
        $db = \CmsApp::get_instance()->GetDb();
        if( !$time ) $time = time();

        $opts = [ 'status'=> Article::STATUS_DRAFT ];
        $filter = $artm->createFilter( $opts );
        $count = $artm->loadByFilter( $filter, true );
        if( !$count ) return TRUE;

        $alert = new DraftMessageAlert($count);
        $alert->save();
        return TRUE;
    }
} // class
