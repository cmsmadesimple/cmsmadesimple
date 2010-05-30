<?php

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
		audit('',lang('automatedtask_failed'),$task->get_name());
		$task->on_failure($time);
	      }
	    else
	      {
		audit('',lang('automatedtask_success'),$task->get_name());
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
    $last_check = get_site_preference('pseudocron_lastrun',time());
    if( (time() - $granularity * 60) <= $last_check )
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

?>