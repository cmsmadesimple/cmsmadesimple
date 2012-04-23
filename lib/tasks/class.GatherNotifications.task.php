<?php

class GatherNotificationsTask implements CmsRegularTask
{
  const   PREFNAME = '__NOTIFICATIONS__';
  private $_notifications;

  public function get_name()
  {
    return get_class();
  }


  public function get_description()
  {
    return lang_by_realm('tasks','notifications_taskdescription');
  }


  public function test($time = '')
  {
    global $CMS_LOGIN_PAGE;
    global $CMS_ADMIN_PAGE;
    if( isset($CMS_ADMIN_PAGE) && !isset($CMS_LOGIN_PAGE) ) return TRUE;
    return FALSE;
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
	$data = $module->GetNotificationOutput(3);
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
	
	if( !is_array($this->_notifications)) $this->_notifications = array();
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
