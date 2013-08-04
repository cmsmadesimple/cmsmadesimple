<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_tree (c) 2010 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A simple php tree class.
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin 
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE

/**
 * @package CMS 
 */

/**
 * Simple global convenience object to hold information for a single route.
 * 
 * @package CMS
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since  1.9
 */
class CmsRoute implements ArrayAccess
{
	private $_data;
	private $_results;
	static private $_keys = array('term','key1','key2','key3','defaults','absolute','results');

  /**
   * Construct a new route object.
   *
   * @param string The route string (or regular expression)
   * @param mixed  The first key. Usually a module name.
   * @param array  An array of parameter defaults for this module.  Only applicable when the destination is a module.
   * @param boolean Flag indicating wether the term is a regular expression or an absolute string.
   * @param string The second key.
   */
  public function __construct($term,$key1 = '',$defaults = '',$is_absolute = FALSE,$key2 = null,$key3 = null)
  {
	  $this->_data['term'] = $term;
	  $this->_data['absolute'] = $is_absolute;

	  if( is_numeric($key1) && empty($key2) ) {
		  $this->_data['key1'] = '__CONTENT__';
		  $this->_data['key2'] = (int)$key1;
	  }
	  else {
		  $this->_data['key1'] = $key1;
		  $this->_data['key2'] = $key2;
	  }
	  if( is_array($defaults) ) {
		  $this->_data['defaults'] = $defaults;
      }
	  if( !empty($key3) ) {
		  $this->_data['key3'] = $key3;
	  }
  }


  public static function &new_builder($term,$key1,$key2 = '',$defaults = '',$is_absolute = FALSE,$key3 = null)
  {
	  $obj = new CmsRoute($term,$key1,$defaults,$is_absolute,$key2,$key3);
	  return $obj;
  }

  public function signature()
  {
	  $tmp = serialize($this->_data);
	  $tmp = md5($tmp);
	  return $tmp;
  }

  public function OffsetGet($key)
  {
	  if( in_array($key,self::$_keys) && isset($this->_data[$key]) ) {
		  return $this->_data[$key];
	  }
  }

  public function OffsetSet($key,$value)
  {
	  if( in_array($key,self::$_keys) ) {
		  $this->_data[$key] = $value;
	  }
  }

  public function OffsetExists($key)
  {
	  if( in_array($key,self::$_keys) && isset($this->_data[$key]) ) {
		  return TRUE;
	  }
	  return FALSE;
  }

  public function OffsetUnset($key)
  {
	  if( in_array($key,self::$_keys) && isset($this->_data[$key]) ) {
		  unset($this->_data[$key]);
	  }
  }

  /**
   * Returns the route term (string or regex)
   *
   * @deprecated
   */
  public function get_term()
  {
	  return $this->_term;
  }

  /**
   * Retrieve the destination module name.
   *
   * @return string Destination module name. or null.
   * @deprecated
   */
  public function get_dest()
  {
	  return $this->_data['key1'];
  }


  /**
   * Retrieve the page id, if the destination is a content page.
   *
   * @return integer Page id, or null.
   * @deprecated
   */
  public function get_content()
  {
	  if( $this->is_content() ) return $this->_data['key2'];
  }


  /**
   * Retrieve the default parameters for this route
   *
   * @return array The default parameters for the route.. Null if no defaults specified.
   * @deprecated
   */
  public function get_defaults()
  {
	  if( isset($this->_data['defaults']) )
		  return $this->_data['defaults'];
  }

  /**
   * Test wether this route is for a page.
   *
   * @return boolean
   * @deprecated
   */
  public function is_content()
  {
	  return ($this->_data['key1'] == '__CONTENT__')?TRUE:FALSE;
  }


  /**
   * Get matching parameter results.
   *
   * @return array Matching parameters... or Null
   * @deprecated
   */
  public function get_results()
  {
	  return $this->_results;
  }

  /**
   * Test if this route matches the specified string
   * Depending upon the route, either a string comparison or regular expression match
   * is performed.
   *
   * @param string The input string
   * @param boolean Perform an exact string match rather than depending on the route values.
   * @param array   Optional array which will contain output matched parameters for regular expression
   * searches.
   * @return boolean
   */
  public function matches($str,$exact = false)
  {
    $this->_results = null;
	if( (isset($this->_data['absolute']) && $this->_data['absolute']) || $exact ) {
		$a = trim($this->_data['term']);
		$a = trim($a,'/');
		$b = trim($str);
		$b = trim($b,'/');
		
		if( !strcasecmp($a,$b) ) return TRUE;
		return FALSE;
	}

	// regular expression matches.
    $tmp = array();
    $res = (bool)preg_match($this->_data['term'],$str,$tmp);
    if( $res && is_array($tmp) ) $this->_results = $tmp;
    return $res;
  }


} // end of class



# vim:ts=4 sw=4 noet
?>
