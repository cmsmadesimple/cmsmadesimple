<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CmsAsyncJobManager (c) 2016 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  An addon module for CMS Made Simple to allow management of asynchronous jobs
#  and cron jobs.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit the CMSMS Homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
if( !isset($gCms) ) exit;

use \CMSMS\Async\Job as Job;
use \CMSMS\Async\CronJobTrait;

final class CmsJobManager extends \CMSModule
{
    const LOCKPREF = 'lock';
    const MANAGE_JOBS = 'Manage Jobs';
    const EVT_ONFAILEDJOB = 'OnJobFailed';

    private $_current_job;
    private $_lock;

    function GetFriendlyName() { return $this->Lang('friendlyname'); }
    function GetVersion() { return '0.1'; }
    function MinimumCMSVersion() { return '2.1.99'; }
    function GetAuthor() { return 'Calguy1000'; }
    function GetAuthorEmail() { return 'calguy1000@cmsmadesimple.org'; }
    function IsPluginModule() { return TRUE; }
    function HasAdmin() { return TRUE; }
    function GetAdminDescription() { return $this->Lang('moddescription'); }
    function GetAdminSection() { return 'siteadmin'; }
    function LazyLoadFrontend() { return FALSE; }
    function LazyLoadAdmin() { return FALSE; }
    function VisibleToAdminUser() { return $this->CheckPermission(\CmsJobManager::MANAGE_JOBS); }
    public static function table_name() { return cms_db_prefix().'mod_cmsjobmgr'; }

    public function InitializeFrontend()
    {
        $this->RegisterModulePlugin();
        $this->RestrictUnknownParams();

        \CGSmartNav\smarty_plugins::init();
        $this->SetParameterType('nav',CLEAN_STRING);
        $this->SetParameterType('template',CLEAN_STRING);
    }

    public function GetEventHelp( $name )
    {
        return $this->Lang('evthelp_'.$name);
    }

    public function GetEventDescription( $name )
    {
        return $this->Lang('evtdesc_'.$name);
    }

    protected function &create_new_template($str)
    {
        $smarty = $this->GetActionTemplateObject();
        $tpl = $smarty->CreateTemplate($this->GetTemplateResource($str),null,null,$smarty);
        return $tpl;
    }

    /**
     * @ignore
     * @internal
     */
    public function &get_current_job()
    {
        return $this->_current_job;
    }

    protected function set_current_job($job = null)
    {
        if( !is_null($job) && !$job instanceof \CMSMS\Async\Job ) throw new \LogicException('Invalid data passed to '.__METHOD__);
        $this->_current_job = $job;
    }

    //////////////////////////////////////////////////////////////////////////
    // THIS STUFF SHOULD PROBABLY GO INTO A TRAIT, or atleast an interface
    //////////////////////////////////////////////////////////////////////////

    protected function clear_bad_jobs()
    {
        $now = time();
        $lastrun = (int) $this->GetPreference('last_badjob_run');
        if( $lastrun + 3600 >= $now ) return; // hardcoded

        $db = $this->GetDb();
        $sql = 'SELECT * FROM '.self::table_name().' WHERE errors >= ?';
        $list = $db->GetArray($sql,array(10));  // hardcoded
        if( is_array($list) && count($list) ) {
            $idlist = [];
            foreach( $list as $row ) {
                $obj = unserialize($row);
                $obj->set_id($row['id']);
                $idlist[] = (int) $row['id'];
                $this->SendEvent($this::EVT_ONFAILEDJOB,array('job'=>$obj));
            }
            $sql = 'DELETE FROM '.self::table_name().' WHERE id IN ('.implode(',',$idlist).')';
            $db->Execute($sql);
            audit('',$this->GetName(),'Cleared '.count($idlist).' bad jobs');
        }
        $this->SetPreference('last_badjob_run',$now);
    }

    public function save_job(Job &$job)
    {
        $recurs = $until = null;
        if( $this->job_recurs($job) ) {
            $recurs = $job->frequency;
            $until = $job->until;
        }
        $db = $this->GetDb();
        if( !$job->id ) {
            $sql = 'INSERT INTO '.self::table_name().' (name,created,module,errors,start,recurs,until,data) VALUES (?,?,?,?,?,?,?,?)';
            $dbr = $db->Execute($sql,array($job->name,$job->created,$job->module,$job->errors,$job->start,$recurs,$until,serialize($job)));
            $new_id = $db->Insert_ID();
            $job->set_id($new_id);
            return $new_id;
        } else {
            // note... we do not at any time play with the module, the data, or recus/until stuff for existing jobs.
            $sql = 'UPDATE '.self::table_name().' SET start = ? WHERE id = ?';
            $db->Execute($sql,array($job->start,$job->id));
            return $job->id;
        }
    }

    public function delete_job(Job &$job)
    {
        if( !$job->id ) throw new \LogicException('Cannot delete a job that has no id');
        $db = $this->GetDb();
        $sql = 'DELETE FROM '.self::table_name().' WHERE id = ?';
        $db->Execute($sql,array($job->id));
    }

    public function is_processing()
    {
        return $this->_processing;
    }

    public function set_processing($flag)
    {
        $this->_processing = (bool) $flag;
    }

    protected function lock()
    {
        $this->_lock = time();
        $this->SetPreference(self::LOCKPREF,$this->_lock);
    }

    protected function unlock()
    {
        $this->_unlock = null;
        $this->RemovePreference(self::LOCKPREF);
    }

    protected function lock_expired()
    {
        $this->_lock = (int) $this->GetPreference(self::LOCKPREF);
        if( $this->_lock < time() - 180 ) return TRUE; // hardcoded, locks expire in 3 minutes
        return FALSE;
    }

    protected function check_for_jobs_or_tasks()
    {
        // this is cheaper.
        $out = $this->get_jobs(1);
        if( count($out) ) return TRUE;

        // gotta check for tasks, which is more expensive
        $now = time();
        $lastcheck = (int) $this->GetPreference('tasks_lastcheck');
        //if( $lastcheck >= $now - 900 ) return FALSE; // hardcoded, only check tasks every 15 minutes.
        $this->SetPreference('tasks_lastcheck',$now);
        $tasks = $this->create_jobs_from_eligible_tasks();
        if( count($tasks) ) return TRUE;
        return FALSE;
    }

    protected function create_jobs_from_eligible_tasks()
    {
        // this creates jobs out of CmsRegularTask objects that we find,and that need to be executed.
        $now = time();
        $res = false;

		// 1.  Get task objects from files.
		$dir = CMS_ROOT_PATH.'/lib/tasks';

        // fairly expensive as we have to iterate a directory and load files and create objects.
		$tmp = new DirectoryIterator($dir);
		$iterator = new RegexIterator($tmp,'/class\..+task\.php$/');
		foreach( $iterator as $match ) {
			$tmp = explode('.',basename($match->current()));
			if( is_array($tmp) && count($tmp) == 4 ) {
				$classname = $tmp[1].'Task';
				require_once($dir.'/'.$match->current());
				$obj = new $classname;
				if( !$obj instanceof CmsRegularTask ) continue;
                if( !$obj->test($now) ) continue;
                $job = new \CMSMS\Async\RegularTask($obj);
                $job->save();
                $res = true;
			}
		}

		// 2.  Get task objects from modules.
		$opts = ModuleOperations::get_instance();
		$modules = $opts->get_modules_with_capability('tasks');
		if (!$modules) return;
		foreach( $modules as $one ) {
			if( !is_object($one) ) $one = \cms_utils::get_module($one);
			if( !method_exists($one,'get_tasks') ) continue;

			$tasks = $one->get_tasks();
			if( !$tasks ) continue;
            if( !is_array($tasks) ) $tasks = array($tasks);

            foreach( $tasks as $onetask ) {
                if( ! is_object($onetask) ) continue;
                if( ! $onetask instanceof CmsRegularTask ) continue;
                if( ! $onetask->test() ) continue;
                $job = new \CMSMS\Async\RegularTask($onetask);
                $job->module = $one->GetName();
                $job->save();
                $res = true;
            }
		}

        return $res;
    }

    protected function get_jobs($check_only = FALSE)
    {
        $db = $this->GetDb();
        $now = time();
        $limit = 100; // hardcoded.... should never be more than 100 jobs in the queue for a site.
        if( $check_only ) $limit = 1;

        if( !$limit ) $limit = 100;
        $limit = max(1,(int)$limit);
        $sql = 'SELECT * FROM '.self::table_name().' WHERE start < UNIX_TIMESTAMP() AND created < UNIX_TIMESTAMP() ORDER BY errors ASC,created ASC LIMIT ?';
        $list = $db->GetArray($sql,array($limit));
        if( !is_array($list) || count($list) == 0 ) return;
        if( $check_only ) return TRUE;

        $out = [];
        foreach( $list as $row ) {
            $obj = unserialize($row['data']);
            $obj->set_id($row['id']);
            $out[] = $obj;
        }

        return $out;
    }

    protected function job_recurs(Job $job)
    {
        if( ! $job instanceof \CMSMS\Async\CronJobInterface ) return FALSE;
        if( $job->frequency == $job::RECUR_NONE ) return FALSE;
        return TRUE;
    }

    protected function calculate_next_start_time(Job $job)
    {
        $out = null;
        if( !$this->job_recurs($job) ) return $out;
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

    public function trigger_async_processing()
    {
        // quick check to make sure this method only does something once per request
        // and store a returnid in there for safety.
        static $_returnid = -1;
        if( $_returnid !== -1 ) return; // only once per request thanks.
        $_returnid = \ContentOperations::get_instance()->GetDefaultContent();

        // if this function was called because we are actually processing a cron request... stop
        if( isset($_REQUEST['cms_cron']) ) return;

        // if we triggered the thing less than N minutes ago... do nothing
        $now = time();
        $last_trigger = (int) $this->GetPreference('last_async_trigger');
        // if( $last_trigger >= $now - 180 ) return; // debug

        $jobs = $this->check_for_jobs_or_tasks();
        if( !count($jobs) ) return; // nothing to do.

        // this could go into a function...
        $url_str = html_entity_decode($this->create_url('__','process',$_returnid));
        $url_ob = new \cms_url($url_str);
        $url_ob->set_queryvar('cms_cron',1);
        $scheme = $url_ob->get_scheme();
        if( !$scheme ) {
            $url_ob->set_scheme('http');
            if( CmsApp::get_instance()->is_https_request() ) $url_ob->set_scheme('https');
        }
        $port = $url_ob->get_port();
        if( !$port) {
            $url_ob->set_port(80);
            if( strtolower($scheme) == 'https' ) $url_ob->set_port(443);
        }

        $endpoint = $url_ob->get_path();
        $query = $url_ob->get_query();
        if( $query ) $endpoint .= '?'.$query;
        $post_string = $url_ob->get_query();
        $out = "GET: ".$endpoint." HTTP/1.1\r\n";
        $out .= 'Host: '.$url_ob->get_host()."\r\n";
        $out .= "Connection: Close\r\n\r\n";  // two lines

        $this->SetPreference('last_async_trigger',$now+1);

        try {
            $fp = fsockopen($url_ob->get_host(),$url_ob->get_port(),$errno,$errstr,3);
            if( !$fp ) throw new \RuntimeException('Could n ot connect to the async processing action');
            fwrite($fp,$out);
            fclose($fp);
        }
        catch( \Exception $e ) {
            // do nothing
        }
    }

} // class CGSmartNav
