<?php

class ModuleManagerModuleInfo extends CmsExtendedModuleInfo
{
  private static $_mmkeys = array('e_status');
  private $_mmdata = array();

  public function __construct($module_name,$can_load = TRUE)
  {
    parent::__construct($module_name,$can_load);

    // add in some info that only module manager can tell us.
    // like: is there a newer version available
    // extended status (db version newer then file version) / needs upgrade
    $tmp = version_compare($this['installed_version'],$this['version']);
    if( $tmp < 0 ) {
      $this['e_status'] = 'need_upgrade';
    }
    else if( $tmp > 0 ) {
      $this['e_status'] = 'db_newer';
    }
    else {
      $rep_info = modulerep_client::get_upgrade_module_info($module_name);
      if( is_array($rep_info) ) {
	if( ($res = version_compare($this['version'],$rep_info['version'])) < 0 ) $this['e_status'] = 'newer_available';
      }
    }
  }

  public function OffsetGet($key)
  {
    if( !in_array($key,self::$_mmkeys) ) return parent::OffsetGet($key);
    if( isset($this->_mmdata[$key]) ) return $this->_mmdata[$key];
  }

  public function OffsetSet($key,$value)
  {
    if( !in_array($key,self::$_mmkeys) ) parent::OffsetSet($key,$value);
    $this->_mmdata[$key] = $value;
  }

  public function OffsetExists($key)
  {
    if( !in_array($key,self::$_mmkeys) ) return parent::OffsetExists($key);
    return isset($this->_mmdata[$key]);
  }
} // end of class

?>