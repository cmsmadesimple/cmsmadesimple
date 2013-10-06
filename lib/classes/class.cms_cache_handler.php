<?php

class cms_cache_handler
{
  static private $_instance;
  private $_driver;

  private function __construct() 
  {
    // todo... set a default cache handler or something.
    $driver_name = cms_siteprefs::get('cache_driver');
    $driver_name = 'cms_filecache_driver'; // remove me.
    if( $driver_name && $driver_name != '-1' && class_exists($driver_name) ) {
      if( $driver_name == 'cms_filecache_driver' ) {
	$parms = array();
	$parms['lifetime'] = cms_siteprefs::get('cache_filecache_lifetime',3600);
	$parms['locking'] = cms_siteprefs::get('cache_filecache_locking',0);
	$parms['auto_cleaning'] = cms_siteprefs::get('cache_filecache_autocleaning',0);
	$parms['blocking'] = cms_siteprefs::get('cache_filecache_blocking',0);
	$driver_obj = new $driver_name($parms);
	$this->_driver = $driver_obj;
      }
    }
  }

  private function __clone() {}

  final public static function get_instance()
  {
    if( !is_object(self::$_instance) ) self::$_instance = new cms_cache_handler;
    return self::$_instance;
  }

  final public function set_driver(cms_cache_driver& $driver)
  {
    $this->_driver = $driver;
  }

  final public function get_driver()
  {
    return $this->_driver;
  }

  final public function clear($group = '')
  {
    if( !$this->can_cache() ) return FALSE;
    if( is_object($this->_driver) ) return $this->_driver->clear($group);
    return FALSE;
  }

  final public function get($key,$group = '')
  {
    if( !$this->can_cache() ) return FALSE;
    if( is_object($this->_driver) ) return $this->_driver->get($key,$group);
    return FALSE;
  }

  final public function exists($key,$group = '')
  {
    if( !$this->can_cache() ) return FALSE;
    if( is_object($this->_driver) ) return $this->_driver->exists($key,$group);
    return FALSE;
  }

  final public function erase($key,$group = '')
  {
    if( !$this->can_cache() ) return FALSE;
    if( is_object($this->_driver) ) return $this->_driver->erase($key,$group);
    return FALSE;
  }

  final public function set($key,$value,$group = '')
  {
    if( !$this->can_cache() ) return FALSE;
    if( is_object($this->_driver) ) return $this->_driver->set($key,$value,$group);
    return FALSE;
  }

  final public function set_group($group)
  {
    if( is_object($this->_driver) ) return $this->_driver->set_group($group);
    return FALSE;
  }

  final public function can_cache()
  {
    global $CMS_ADMIN_PAGE;
    global $CMS_INSTALL_PAGE;
    global $CMS_MODULE_PAGE;
    global $CMS_STYLESHEET;

    if( !is_object($this->_driver) ) return FALSE;
    if( isset($CMS_INSTALL_PAGE) ) return FALSE;
    if( isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' ) return FALSE;

//     $config = cmsms()->GetConfig();
//     if( isset($config['debug']) && $config['debug'] == true ) return FALSE;

//     $uid = get_userid(false);
//     if( $uid ) return FALSE; // caching disabled for logged in administrators

    return TRuE;
  }
}

?>