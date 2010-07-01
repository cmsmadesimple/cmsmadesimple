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

class CmsRegularTaskHandler
{
  private static $_tasks;

  private static function get_tasks()
  {
    if( !is_object(self::$_tasks) )
      {
	self::$_tasks = new ArrayObject();
      }

    // 1.  Get task objects from files.
    global $gCms;
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
    global $gCms;
    $opts = $gCms->GetModuleOperations();
    $modules = $opts->get_modules_with_capability('tasks');
    foreach( $modules as $one )
      {
	$tasks = $one->current()->get_tasks();
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
		audit('',lang_by_realm('automatedtask_failed','tasks'),$task->get_name());
		$task->on_failure($time);
	      }
	    else
	      {
		audit('',lang_by_realm('automatedtask_success','tasks'),$task->get_name());
		$task->on_success($time);
	      }
	  }
      }
  }


  private static function cleanup()
  {
    // we don't need the tasks in memory any more.
    self::$_tasks = '';
  }


  public static function handle_tasks()
  {
    $granularity = (int)get_site_preference('pseudocron_granularity',60);
    $last_check = get_site_preference('pseudocron_lastrun',0);
    if( (time() - $granularity * 60) >= $last_check )
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