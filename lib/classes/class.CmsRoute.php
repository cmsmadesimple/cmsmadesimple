<?php

/**
 * Simple global convenience object to hold CMS route information.
 * 
 * @package CMS
 */
class CmsRoute
{
  private $_module;
  private $_content_id;
  private $_regex;
  private $_defaults;

  public function __construct($regex,$module = '',$defaults = array())
  {
    $this->_regex  = $regex;
    if( is_numeric($module) )
      {
	$this->_content_id = $module;
      }
    else
      {
	$this->_module = $module;
	$this->_defaults = $defaults;
      }
  }

  public function get_module()
  {
    return $this->_module;
  }

  public function get_content()
  {
    return $this->_content_id;
  }
  
  public function is_content()
  {
    return (bool)($this->_content_id != null);
  }

  public function matches($str)
  {
    if( $this->is_content() )
      {
	if( $this->_regex == $str )
	  {
	    return TRUE;
	  }
	return FALSE;
      }
    return (bool)preg_match($this->_regex,$str);
  }
} // end of class

# vim:ts=4 sw=4 noet
?>