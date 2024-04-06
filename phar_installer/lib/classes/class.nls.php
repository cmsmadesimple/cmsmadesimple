<?php

namespace __appbase;

abstract class nls
{
  protected $_name;
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
    if( !\is_array($aliases) ) $aliases = \explode(',', $aliases);
    if(\is_array($aliases) && \count($aliases) )
      {
	for($i = 0, $iMax = \count($aliases); $i < $iMax; $i++ )
	  {
	    if( $aliases[$i] == $str ) return TRUE;
	  }
      }
    return FALSE;
  }

  public function name()
  {
    $name = __CLASS__;
    if( endswith($name,'_nls') )
      {
	$name = \substr($name, 0, -4);
      }
    return $name;
  }

  public function isocode()
  {
    if( !$this->_isocode )
      {
	return \substr($this->name, 0, 2);
      }
    return $this->_isocode;
  }

  public function display()
  {
    if( !$this->_display )
      {
	return $this->fullname();
      }
    return $this->_display;
  }

  public function locale()
  {
    if( !$this->_locale )
      return $this->name();
    return $this->_locale;
  }

  public function encoding()
  {
    if( !$this->_encoding )
      return 'UTF-8';
    return $this->_encoding;
  }

  public function fullname()
  {
    if( !$this->_fullname ) return $this->name();
    return $this->_fullname;
  }

  public function aliases()
  {
    if( $this->_aliases )
      {
	if( \is_array($this->_aliases) )
	  return $this->_aliases;
	return \explode(',', $this->_aliases);
      }
  }

} // end of class
?>