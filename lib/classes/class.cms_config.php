<?php

class cms_config implements ArrayAccess
{
  private static $_instance;
  private $_data = array();

  // this is a singleton.
  private function __construct()  {}

  private function load_config()
  {
    $this->_data = cms_config_load(true);

    // Hack for changed directory and no way to upgrade config.php
    $this->_data['previews_path'] = str_replace('smarty/cms', 'tmp', $this->_data['previews_path']);
  }

  public static function get_instance()
  {
    if (!isset(self::$_instance)) {
      $c = __CLASS__;
      self::$_instance = new $c;

      // now load the config
      self::$_instance->load_config();
    }

    return self::$_instance;
  }

  public function offsetExists($key)
  {
    return isset($this->_data[$key]);
  }

  public function offsetGet($key)
  {
    if( !isset($this->_data[$key]) )
      {
	if( $this->_data['debug'] == true )
	  trigger_error('Modification of config variables is deprecated',E_USER_ERROR);
	return;
      }
    return $this->_data[$key];
  }

  public function offsetSet($key,$value)
  {
    trigger_error('Modification of config variables is deprecated',E_USER_ERROR);
    $this->_data[$key] = $value;
  }

  public function offsetUnset($key)
  {
    trigger_error('Unsetting config variable '.$key.' is invalid',E_USER_ERROR);
  }

} // end of class

?>