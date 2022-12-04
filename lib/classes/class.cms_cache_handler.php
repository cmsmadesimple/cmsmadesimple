<?php

/**
 * Contains a generic cache handler wrapper.
 * @package CMS
 * @license GPL
 */

/**
 * This singleton class provides a convenient caching capbility.
 *
 * By default this uses the cms_filecache_driver to cache data in the TMP_CACHE_LOCATION
 * for one hour.
 *
 * @package CMS
 * @license GPL
*/
class cms_cache_handler
{
  /**
   * @ignore
   */
  static private $_instance;

  /**
   * @ignore
   */
  private $_driver;

  /**
   * @ignore
   */
  private function __construct()
  {
      // todo... set a default cache handler or something.
      $driver_name = cms_siteprefs::get('cache_driver');
      if( !$driver_name ) $driver_name = 'cms_filecache_driver';

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

  /**
   * @ignore
   */
  private function __clone() {}

  /**
   * Return a reference to the only allowed instance of this object.
   *
   * @return cms_cache_handler
   */
  final public static function get_instance()
  {
    if( !is_object(self::$_instance) ) self::$_instance = new cms_cache_handler;
    return self::$_instance;
  }

  /**
   * Set the driver to use for caching
   *
   * @param cms_cache_driver $driver
   */
  final public function set_driver(cms_cache_driver& $driver)
  {
    $this->_driver = $driver;
  }

  /**
   * Get the driver to use for caching, to adjust its parameters
   *
   * @return cms_cache_driver
   */
  final public function get_driver()
  {
    return $this->_driver;
  }

  /**
   * Clear the cache.
   *
   * If the group is not specified the current set group will be used.  If that is empty
   * then all cached values will be cleared.   Use with caution.
   *
   * @see cms_cache_handler::set_group
   * @param string $group
   * @return bool
   */
  final public function clear($group = '')
  {
    if( !$this->can_cache() ) return FALSE;
    if( is_object($this->_driver) ) return $this->_driver->clear($group);
    return FALSE;
  }

  /**
   * Get a cached value
   *
   * @see cms_cache_handler::set_group
   * @param string $key The primary key for the cached value
   * @param string $group An optional cache group name.
   * @return mixed
   */
  final public function get($key,$group = '')
  {
    if( !$this->can_cache() ) return FALSE;
    if( is_object($this->_driver) ) return $this->_driver->get($key,$group);
    return FALSE;
  }

  /**
   * Test if a cached value exist
   *
   * @see cms_cache_handler::set_group
   * @param string $key The primary key for the cached value
   * @param string $group An optional cache group name.
   * @return bool
   */
  final public function exists($key,$group = '')
  {
    if( !$this->can_cache() ) return FALSE;
    if( is_object($this->_driver) ) return $this->_driver->exists($key,$group);
    return FALSE;
  }

  /**
   * Erase a cached value
   *
   * @see cms_cache_handler::set_group
   * @param string $key The primary key for the cached value
   * @param string $group An optional cache group name.
   * @return bool
   */
  final public function erase($key,$group = '')
  {
    if( !$this->can_cache() ) return FALSE;
    if( is_object($this->_driver) ) return $this->_driver->erase($key,$group);
    return FALSE;
  }

  /**
   * Add or replace a value in the cache
   *
   * @see cms_cache_handler::set_group
   * @param string $key The primary key for the cached value
   * @param mixed  $value the value to save
   * @param string $group An optional cache group name.
   * @return bool
   */
  final public function set($key,$value,$group = '')
  {
    if( !$this->can_cache() ) return FALSE;
    if( is_object($this->_driver) ) return $this->_driver->set($key,$value,$group);
    return FALSE;
  }

  /**
   * Set a current cache group
   *
   * This method allows specifying a scope to all cache methods
   * @param string $group
   * @return bool
   */
  final public function set_group($group)
  {
    if( is_object($this->_driver) ) return $this->_driver->set_group($group);
    return FALSE;
  }

  /**
   * Test if caching is possible
   * Caching is not possible if there is no driver, or in an install request.
   * @return bool
   */
  final public function can_cache()
  {
    global $CMS_INSTALL_PAGE;

    if( !is_object($this->_driver) ) return FALSE;
    if( isset($CMS_INSTALL_PAGE) ) return FALSE;

    return TRuE;
  }
}

?>