<?php

namespace __appbase;

class request implements \ArrayAccess
{
  private static $_instance;
  private $_data;
  const METHOD_POST = 'POST';
  const METHOD_GET  = 'GET';

  private function __construct()
  {
  }

  public static function get()
  {
    if( !self::$_instance ) self::$_instance = new request();
    return self::$_instance;
  }
  
  #[\ReturnTypeWillChange]
  public function offsetExists($key)
  {
    if( isset($_REQUEST[$key]) ) return TRUE;
    return FALSE;
  }
  
  #[\ReturnTypeWillChange]
  public function offsetGet($key)
  {
    if( isset($_REQUEST[$key]) ) return $_REQUEST[$key];
  }
  
  #[\ReturnTypeWillChange]
  public function offsetSet($key,$value)
  {
    if( isset($_REQUEST[$key]) ) return $_REQUEST[$key];
  }
  
  
  #[\ReturnTypeWillChange]
  public function offsetUnset($key)
  {
    throw new \RuntimeException('Attempt to unset a request variable');
  }

  public function raw_server($key)
  {
    if( isset($_SERVER[$key]) )
      return $_SERVER[$key];
  }

  public function __call($fn,$args)
  {
    $key = \strtoupper($fn);
    if( isset($_SERVER[$key]) )	return $this->raw_server($key);
    throw new \RuntimeException('Call to unknown method ' . $fn . ' in request object');
  }

  public function self()
  {
    return $this->raw_server('PHP_SELF');
  }

  public function method()
  {
    if( $this->raw_server('REQUEST_METHOD') == 'POST' ) {
      return self::METHOD_POST;
    }
    elseif( $this->raw_server('REQUEST_METHOD') == 'GET' ) {
      return self::METHOD_GET;
    }
    throw new \RuntimeException('Unhandled request method ' . $_SERVER['REQUEST_METHOD']);
  }

  public function is_post()
  {
    return ($this->method() == self::METHOD_POST)?TRUE:FALSE;
  }

  public function is_get()
  {
    return ($this->method() == self::METHOD_GET)?TRUE:FALSE;
  }

  public function accept()
  {
    return $this->raw_server('HTTP_ACCEPT');
  }

  public function accept_charset()
  {
    return $this->raw_server('HTTP_ACCEPT_CHARSET');
  }

  public function accept_encoding()
  {
    return $this->raw_server('HTTP_ACCEPT_ENCODING');
  }

  public function accept_language()
  {
    return $this->raw_server('HTTP_ACCEPT_LANGUAGE');
  }

  public function host()
  {
    return $this->raw_server('HTTP_HOST');
  }

  public function referer()
  {
    return $this->raw_server('HTTP_REFERER');
  }

  public function user_agent()
  {
    return $this->raw_server('HTTP_USER_AGENT');
  }

  public function https()
  {
    if( isset($_SERVER['HTTPS']) && 'on' != \strtolower($_SERVER['HTTPS'])) return TRUE;
    return FALSE;
  }

} // end of class

?>