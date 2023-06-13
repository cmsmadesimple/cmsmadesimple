<?php

namespace __appbase;

final class session implements \ArrayAccess
{
  private static $_instance;
  private static $_session_id;
  private static $_key;
  private $_data;
  private function __construct() {}

  private static function start()
  {
      if( !self::$_key ) {
          $session_key = substr(md5(__DIR__),0,10);
          @session_name('CMSIC'.$session_key);
          @session_cache_limiter('private');
          $res = null;
          if( !@session_id() ) $res = @session_start();
          if( !$res ) throw new \RuntimeException('Problem starting the session (system configuration problem?)');
          self::$_session_id = session_id();
          self::$_key = 'k'.md5(self::$_session_id);
      }
  }

  private function _collapse()
  {
    self::start();
    if( $this->_data ) $_SESSION[self::$_key] = serialize($this->_data);
    $this->_data = null;
  }

  private function _expand()
  {
    self::start();
    if( !is_array($this->_data) ) {
      $this->_data = array();
      if( isset($_SESSION[self::$_key]) ) {
          $this->_data = unserialize($_SESSION[self::$_key]);
      }
    }
  }

  public static function clear()
  {
      self::start();
      unset($_SESSION[self::$_key]);
  }

  public static function get()
  {
    if( !self::$_instance ) self::$_instance = new session;
    return self::$_instance;
  }

  public function reset()
  {
      $this->_data = null;
      self::clear();
      $this->_expand();
  }
  
  #[\ReturnTypeWillChange]
  public function offsetExists($key)
  {
    $this->_expand();
    if( isset($this->_data[$key]) ) return TRUE;
    return FALSE;
  }
  
  #[\ReturnTypeWillChange]
  public function offsetGet($key)
  {
    $this->_expand();
    if( isset($this->_data[$key]) ) return $this->_data[$key];
  }
  
  #[\ReturnTypeWillChange]
  public function offsetSet($key,$value)
  {
    $this->_expand();
    $this->_data[$key] = $value;
    $this->_collapse();
  }
  
  #[\ReturnTypeWillChange]
  public function offsetUnset($key)
  {
    $this->_expand();
    if( isset($this->_data[$key]) ) {
      unset($this->_data[$key]);
      $this->_collapse();
    }
  }
} // end of class

?>
