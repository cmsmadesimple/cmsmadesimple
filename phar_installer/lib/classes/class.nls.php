<?php

namespace __appbase;

use function __appbase\endswith;

abstract class nls
{
  protected $_isocode;
  protected $_locale;
  protected $_fullname;
  protected $_encoding;
  protected $_aliases;
  protected $_display;

  abstract public function __construct();

  public function matches($str)
  {
    if( $str == $this->name() ) return TRUE;
    if( $str == $this->locale() ) return TRUE;
    if( $str == $this->isocode() ) return TRUE;
    if( $str == $this->fullname() ) return TRUE;
    $aliases = $this->aliases();
    if( !is_array($aliases) ) $aliases = explode(',',$aliases);
    if( is_array($aliases) && count($aliases) )
    {
      for( $i = 0; $i < count($aliases); $i++ )
      {
        if( $aliases[$i] == $str ) return TRUE;
      }
    }
    return FALSE;
  }

  public function name()
  {
    $name = get_class();
    if( endswith($name,'_nls') )
    {
      $name = substr($name,0,strlen($name)-4);
    }
    return $name;
  }

  public function isocode()
  {
    if( empty($this->_isocode) )
    {
      return substr($this->name,0,2);
    }
    return $this->_isocode;
  }

  public function display()
  {
    if( empty($this->_display) )
    {
      return $this->fullname();
    }
    return $this->_display;
  }

  public function locale()
  {
    if( empty($this->_locale) )
    {
      return $this->name();
    }
    return $this->_locale;
  }

  public function encoding()
  {
    if( empty($this->_encoding) )
    {
      return 'UTF-8';
    }
    return $this->_encoding;
  }

  public function fullname()
  {
    if( empty($this->_fullname) )
    {
      return $this->name();
    }
    return $this->_fullname;
  }

  public function aliases()
  {
    if( !empty($this->_aliases) )
    {
      if( is_array($this->_aliases) )
        return $this->_aliases;
      return explode(',',$this->_aliases);
    }
    return [];
  }

} // end of class
?>
