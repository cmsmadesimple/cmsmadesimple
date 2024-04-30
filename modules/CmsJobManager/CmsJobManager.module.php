<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CmsJobManager (c) 2016 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A core module for CMS Made Simple to allow management of asynchronous jobs
#  and cron jobs.
#
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
#
#-------------------------------------------------------------------------
#END_LICENSE
if( !isset($gCms) ) exit;

use \CMSMS\Async\Job as Job;
use \CMSMS\Async\CronJobTrait;

final class CmsJobManager extends \CMSModule
{
    const LOCKPREF = 'lock';
    const ASYNCFREQ_PREF = 'asyncfreq';
    const MANAGE_JOBS = 'Manage Jobs';
    const EVT_ONFAILEDJOB = 'CmsJobManager::OnJobFailed';

    private $_current_job;
    private $_lock;
    public static function table_name() { return cms_db_prefix().'mod_cmsjobmgr'; }

    function GetFriendlyName() { return $this->Lang('friendlyname'); }
    function GetVersion() { return '1.0.0'; }
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
    public function GetHelp() { return $this->Lang('help'); }
    public function HandlesEvents() { return TRUE; }

    public function InitializeFrontend()
    {
        $this->RegisterModulePlugin();
        $this->RestrictUnknownParams();
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

    protected function is_locked()
    {
        $this->_lock = (int) $this->GetPreference(self::LOCKPREF);
        return ($this->_lock > 0);
    }

    protected function lock_expired()
    {
        $this->_lock = (int) $this->GetPreference(self::LOCKPREF);
        if( $this->_lock && $this->_lock < time() - \CmsJobManager\utils::get_async_freq() ) return TRUE;
        return FALSE;
    }

    protected function lock()
    {
        $this->_lock = time();
        $this->SetPreference(self::LOCKPREF,$this->_lock);
    }

    protected function unlock()
    {
        $this->_lock = null;
        $this->RemovePreference(self::LOCKPREF);
    }

    protected function check_for_jobs_or_tasks()
    {
        // this is cheaper.
        $out = \CmsJobManager\JobQueue::get_jobs(1);
        if( $out ) return TRUE;

        // gotta check for tasks, which is more expensive
        $now = time();
        $lastcheck = (int) $this->GetPreference('tasks_lastcheck');
        if( $lastcheck < $now - 900 ) {
            $this->SetPreference('tasks_lastcheck',$now);
            $res = $this->create_jobs_from_eligible_tasks();
            if( $res ) return TRUE;
        }
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
      if( !is_object($one) ) continue; # for some reason the module exists but cannot be loaded
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


    //////////////////////////////////////////////////////////////////////////
    // THIS STUFF SHOULD PROBABLY GO INTO A TRAIT, or at least an interface
    //////////////////////////////////////////////////////////////////////////

    public function load_job_by_id( $job_id )
    {
        $job_id = (int) $job_id;
        if( $job_id < 1 ) throw new \LogicException('Invalid job_id passed to '.__METHOD__);

        $db = $this->GetDb();
        $sql = 'SELECT * FROM '.self::table_name().' WHERE id = ?';
        $row = $db->GetRow( $sql, [ $job_id] );
        if( !is_array($row) || !count($row) ) return;

        $obj = unserialize($row['data']);
        $obj->set_id( $row['id'] );
        return $obj;
    }

    public function save_job(Job &$job)
    {
        $recurs = $until = null;
        if( \CmsJobManager\utils::job_recurs($job) ) {
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

    public function delete_jobs_by_module($module_name)
    {
        $module_name = trim($module_name);
        if( !$module_name ) throw new \LogicException('Invalid module name passed to '.__METHOD__);

        $db = $this->GetDb();
        $sql = 'DELETE FROM '.self::table_name().' WHERE module = ?';
        $db->Execute($sql,array($module_name));
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
        if( $last_trigger >= $now - \CmsJobManager\utils::get_async_freq() ) return; // do nothing

        $jobs = $this->check_for_jobs_or_tasks();
        if( is_array($jobs) && !count($jobs) ) return; // nothing to do.

        // this could go into a function...
        $url_str = html_entity_decode($this->create_url('__','process',$_returnid));
        $url_ob = new \cms_url($url_str);
        if( !$url_ob->get_host() ) {
            // todo: audit something.
            return;
        }

            // gotta determine a scheme
        $url_ob->set_queryvar('cms_cron',1);
        $url_ob->set_queryvar('showtemplate','false');
        $prefix_scheme = null;
        if( !$url_ob->get_scheme() ) {
            $url_ob->set_scheme('http');
            if( CmsApp::get_instance()->is_https_request() ) $url_ob->set_scheme('https');
        }
        if( !$url_ob->get_port() ) {
            $url_ob->set_port(80);
            if( strtolower($url_ob->get_scheme()) == 'https' ) $url_ob->set_port(443);
        }
        if( strtolower($url_ob->get_scheme()) == 'https' ) $prefix_scheme = 'ssl://';

        $endpoint = $url_ob->get_path();
        $query = urldecode($url_ob->get_query());
        if( $query ) $endpoint .= '?'.$query;
        $post_string = $query;
        $out = "GET ".$endpoint." HTTP/1.1\r\n";
        $out .= 'Host: '.$url_ob->get_host()."\r\n";
        $out .= "Connection: Close\r\n\r\n";  // two lines

        $this->SetPreference('last_async_trigger',$now+1);

        try {
            $fp = @fsockopen($prefix_scheme.$url_ob->get_host(),$url_ob->get_port(),$errno,$errstr,1);
            if( !$fp ) throw new \RuntimeException('Could not connect to the async processing action');
            fwrite($fp,$out);
            $code = null;
            $data = fgets($fp);
            $code = (int) substr($data,9,3);
            fclose($fp);
            if( $code != 200 ) {
                audit('',$this->GetName(),'Received '.$code.' response when trying to trigger async processing');
            }
        }
        catch( \Exception $e ) {
            debug_to_log('exception '.$e->GetMessage());
            // do nothing
        }
    }

} // class
