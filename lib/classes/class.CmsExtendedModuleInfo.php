<?php

class CmsExtendedModuleInfo extends CmsModuleInfo
{
  private static $_ekeys = array('installed','status','active','system_module','installed_version','admin_only',
				 'active','allow_fe_lazyload','allow_admin_lazyload','dependants');
  private $_edata = array();

  public function __construct($module_name,$can_load = false)
  {
    parent::__construct($module_name,$can_load);
    $ops = ModuleOperations::get_instance();

    $minfo = $ops->GetInstalledModuleInfo();
    $this['installed'] = false;
    $this['system_module'] = $ops->IsSystemModule($module_name);
    if( isset($minfo[$module_name]) ) {
      $this['installed'] = true;
      $this['status'] = $minfo[$module_name]['status'];
      $this['installed_version'] = $minfo[$module_name]['version'];
      $this['admin_only'] = $minfo[$module_name]['admin_only'];
      $this['active'] = $minfo[$module_name]['active'];
      $this['allow_fe_lazyload'] = $minfo[$module_name]['allow_fe_lazyload'];
      $this['allow_admin_lazyload'] = $minfo[$module_name]['allow_admin_lazyload'];
      if( isset($minfo[$module_name]['dependants']) ) $this['dependants'] = $minfo[$module_name]['dependants'];
    }
  }

  public function OffsetGet($key)
  {
    if( !in_array($key,self::$_ekeys) ) return parent::OffsetGet($key);
    if( isset($this->_edata[$key]) ) return $this->_edata[$key];
  }

  public function OffsetSet($key,$value)
  {
    if( !in_array($key,self::$_ekeys) ) parent::OffsetSet($key,$value);
    $this->_edata[$key] = $value;
  }

  public function OffsetExists($key)
  {
    if( !in_array($key,self::$_ekeys) ) return parent::OffsetExists($key,$value);
    return isset($this->_edata[$key]);
  }
} // end of class

?>