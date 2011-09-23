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
class CmsRoute
{
  private $_dest;
  private $_content_id;
  private $_term;
  private $_defaults;
  private $_absolute;
  private $_results;

  /**
   * Construct a new route object.
   *
   * @param string The route string (or regular expression)
   * @param mixed  The destination.  Either a module name, or an integer page id.
   * @param array  An array of parameter defaults for this module.  Only applicable when the destination is a module.
   * @param boolean Flag indicating wether the term is a regular expression or an absolute string.
   */
  public function __construct($term,$dest = '',$defaults = '',$is_absolute = FALSE)
  {
    $this->_term  = $term;
    $this->_absolute = $is_absolute;
    if( is_numeric($dest) )
      {
	$this->_content_id = $dest;
      }
    else
      {
	$this->_dest = $dest;
	if( is_array($defaults) )
	  {
	    $this->_defaults = $defaults;
	  }
      }
  }

  /**
   * Retrieve the destination module name.
   *
   * @return string Destination module name. or null.
   */
  public function get_dest()
  {
    return $this->_dest;
  }


  /**
   * Retrieve the page id, if the destination is a content page.
   *
   * @return integer Page id, or null.
   */
  public function get_content()
  {
    return $this->_content_id;
  }


  /**
   * Retrieve the default parameters for this route
   *
   * @return array The default parameters for the route.. Null if no defaults specified.
   */
  public function get_defaults()
  {
    return $this->_defaults;
  }

  /**
   * Test wether this route is for a page.
   *
   * @return boolean
   */
  public function is_content()
  {
    return (bool)($this->_content_id != null);
  }


  /**
   * Get matching parameter results.
   *
   * @return array Matching parameters... or Null
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
    if( $this->is_content() || $this->_absolute || $exact )
      {
	if( !strcasecmp($this->_term,$str) )
	  {
	    return TRUE;
	  }
	return FALSE;
      }

    $tmp = array();
    $res = (bool)preg_match($this->_term,$str,$tmp);
    if( $res && is_array($tmp) )
      {
	$this->_results = $tmp;
      }
    return $res;
  }


} // end of class



# vim:ts=4 sw=4 noet
?>