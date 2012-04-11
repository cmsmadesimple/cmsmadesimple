<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * @package CMS
 */


/**
 * A task for managing autometed tasks.  
 * Task classes are found in the lib/tasks directory.
 *
 * @package CMS
 * @author  Robert Campbell
 * @since   1.8
 * @license GPL
 */
class CmsRegularTaskHandler
{
	private function __construct() {}

	/**
	 * A list of tasks that have been found
	 */
	private static $_tasks;

	/**
	 * Private method to find available tasks.  This method will look in the lib/tasks directory
	 * for tack files, and also ask modules for tasks that they support.  Modules exporting tasks
	 * should create and return a task object.
	 *
	 * @return void
	 */
	private static function get_tasks()
	{
		if( !is_object(self::$_tasks) )
		{
			self::$_tasks = new ArrayObject();
		}
		
		// 1.  Get task objects from files.
		$gCms = cmsms();
		$dir = $gCms->config['root_path'].'/lib/tasks';

		$tmp = new DirectoryIterator($dir);
		$iterator = new RegexIterator($tmp,'/class\..+task\.php$/');
		foreach( $iterator as $match )
		{
			$tmp = explode('.',basename($match->current()));
			if( is_array($tmp) && count($tmp) == 4 )
			{
				$classname = $tmp[1].'Task';
				require_once($dir.'/'.$match->current());
				$obj = new $classname;
				if( $obj instanceof CmsRegularTask )
				{
					self::$_tasks->append($obj);
				}
			}
		}


		// 2.  Get task objects from modules.
		$opts = $gCms->GetModuleOperations();
		$modules = $opts->get_modules_with_capability('tasks');
		if (!$modules) return;
		foreach( $modules as $one )
		{
			if( !is_object($one) ) $one = cms_utils::get_module($one);
			if( !method_exists($one,'get_tasks') ) continue;
			
			$tasks = $one->get_tasks();
			if( $tasks )
			{
				if( !is_array($tasks) )
				{
					$tmp = array($tasks);
					$tasks = $tmp;
				}

				foreach( $tasks as $onetask )
				{
					if( is_object($onetask) && $onetask instanceof CmsRegularTask )
					{
						self::$_tasks->append($onetask);
					}
				}
			}
		}
	}


	/**
	 * A method eto execute the found tasks (if they are eligible)
	 *
	 * First each task is tested to see if it is valid to be executed
	 * at the specified time, if it is, then it is executed.
	 * An audit log is created for each succeeded or failed task
	 *
	 * @param integer The time that should be considered for all tests, if not specified the current time is used.
	 * @return void.
	 */
  private static function execute($time = '')
  {
    if( !self::$_tasks ) return;
    if( empty($time) ) $time = time();

    $list = new ArrayIterator(self::$_tasks);
    foreach( $list as $task )
      {
		if( $task->test($time) )
		  {
			$res = $task->execute($time);
			if( !$res )
			  {
			// test failed.
				  audit('','Automated Task Failed',$task->get_name());
				  $task->on_failure($time);
			  }
			else
			  {
				  audit('','Automated Task Succeeded',$task->get_name());
				  $task->on_success($time);
			  }
		  }
      }
  }


  /**
   * Cleanup in memory tasks
   */
  private static function cleanup()
  {
    // we don't need the tasks in memory any more.
    self::$_tasks = '';
  }


  /**
   * The primary interface to the pseudo-cron methodoligy.
   *
   * This method checks to see if the last time that tasks were handled was
   * outside of its granularity window.  If it is, then the tasks are queried
   * and executed.
   *
   */
  public static function handle_tasks()
  {
	  global $CMS_STYLESHEET;
	  global $CMS_INSTALL_PAGE;
      global $CMS_LOGIN_PAGE;

	  if( (isset($CMS_STYLESHEET) && $CMS_STYLESHEET == 1) || isset($CMS_INSTALL_PAGE) || isset($CMS_LOGIN_PAGE) )
	  {
		  return;
	  }

	  $granularity = (int)get_site_preference('pseudocron_granularity',60);
	  $last_check = get_site_preference('pseudocron_lastrun',0);
	  if( ((time() - $granularity * 60) >= $last_check) )
		  {
			  // 1.  Get Task objects.
			  self::get_tasks();
	
			  // 2.  Evaluate Tasks
			  self::execute();
	
			  // 3.  Cleanup to minimize memory usage
			  self::cleanup();

			  // 4.  Say we've done a check
			  set_site_preference('pseudocron_lastrun',time());
		  }
  }
}

# vim:ts=4 sw=4 noet
?>
