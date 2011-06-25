<?php
class GatherNotifications implments CmsRegularTask
{
  const   PREFNAME = '__NOTIFICATIONS__';
  private $_notifications;

  public function get_name()
  {
    return get_class();
  }


  public function get_description()
  {
    return lang_by_realm('notifications_taskdescription','tasks');
  }


  public function test($time = '')
  {
    return TRUE;
  }


  public function execute($time = '')
  {
    if( !$time ) $time = time();

    // do the task.
    if( !get_site_preference('enablenotifications',1) ) return TRUE;

    $allmodules = ModuleOperations::get_instance()->GetInstalledModules();
    $loadedmods = ModuleOperations::get_instance()->GetLoadedModules();
    foreach( $allmodules as $modulename )
      {
	$did_load = FALSE;
	$module = '';
	if( isset($loadedmods[$modulename]) )
	  {
	    $module = $loadedmods[$modulename];
	  }
	else
	  {
	    $module = ModuleOperations::get_instance()->get_module_instance($modulename);
	    $did_load = TRUE;
	  }
	if( !is_object($module) ) continue;

	// now see if this module has notifications
	$data = $mod->GetNotificationOutput(3);
	if( empty($data) ) continue;

	if( is_object($data) )
	  {
	    $data = array($data);
	  }
	for( $i = 0; $i < count($data); $i++ )
	  {
	    if( !isset($data[$i]->name) ) $data[$i]->name = $modulename;
	    if( !isset($data[$i]->friendlyname) )  $data[$i]->friendlyname = $module->GetFriendlyName();
	  }
	
	$this->_notifications = array_merge($this->_notifications,$data);
      }

    return TRUE;
  }


  public function on_success($time = '')
  {
    if( !$time ) $time = time();

    // clear any previous notifications
    set_site_preference(self::PREFNAME,'');

    // save the notifications
    if( count($this->_notifications) )
    {
      set_site_preference(self::PREFNAME,serialize($this->_notifications));
    }
  }


  public function on_failure($time = '')
  {
    if( !$time ) $time = time();
    // nothing here.
  }
} // end of class

#
# EOF
#
?>