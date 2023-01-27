<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# \CMSMS\Async\JobManager (c) 2016 by Robert Campbell (calguy1000@cmsmadesimple.org)
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
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

/**
 * This file defines the job manager for asyncrhonous jobs.
 *
 * @package CMS
 */
namespace CMSMS\Async;

/**
 * A singleton class defining a manager for asyncrhonous jobs.
 *
 * In reality, this is a simple proxy for methods in the CmsJobManager module.
 *
 * @package CMS
 * @author Robert Campbell
 * @copyright Copyright (c) 2017, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 2.2
 */
final class JobManager
{
    /**
     * @ignore
     */
    const MANAGER_MODULE = 'CmsJobManager';

    /**
     * @ignore
     */
    private $_mod;

    /**
     * @ignore
     */
    private static $_instance;

    /**
     * @ignore
     */
    protected function __construct() {}

    /**
     * Get the sole permitted instance of this object
     *
     * @return \CMSMS\JobManager
     */
    public static function get_instance()
    {
        if( !self::$_instance ) self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * Get the module that handles job requests.
     *
     * @internal
     * @return CmsModule
     */
    protected function get_mod()
    {
        if( !$this->_mod ) $this->_mod = \CmsApp::get_instance()->GetModuleInstance(self::MANAGER_MODULE);
        return $this->_mod;
    }

    /**
     * Trigger asynchronous processing.
     *
     * @internal
     */
    public function trigger_async_processing()
    {
        $mod = $this->get_mod();
        if( $mod ) return $mod->trigger_async_processing();
    }

    /**
     * Given an integer job id, load the job.
     *
     * @param int $job_id
     * @return Job
     */
    public function load_job( $job_id )
    {
        $mod = $this->get_mod();
        if( $mod ) return $mod->load_job_by_id( $job_id );
    }

    /**
     * Save a job to the queue.
     *
     * @param Job $job
     * @return int The id of the job.
     */
    public function save_job( Job &$job )
    {
        $mod = $this->get_mod();
        if( $mod ) return $mod->save_job($job);
    }

    /**
     * Remove a job from the queue
     *
     * Note: After calling this method, the job object itself is invalid and cannot be saved.
     *
     * @param Job $job
     */
    public function delete_job( Job &$job )
    {
        $mod = $this->get_mod();
        if( $mod ) return $mod->delete_job($job);
    }

    /**
     * Remove all of the jbos originating from a specific module
     *
     * @param string $module_name
     */
    public function delete_jobs_by_module( $module_name )
    {
        $mod = $this->get_mod();
        if( $mod ) return $mod->delete_job($module_name);
    }
} // end of class