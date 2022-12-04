<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_tree (c) 2010 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A simple php tree class.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
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
 * This file contains a class that makes working with cookies in CMSMS simple.
 *
 * @author Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.10
 * @package CMS
 * @license GPL
 */

/**
 * A simple static class providing convenience utilities for working with cookies.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.10
 */
final class cms_cookies
{
  /**
   * @ignore
   */
  private static $_parts;

  /**
   * @ignore
   */
  final private function __construct() {}

  /**
   * @ignore
   */
  private static function __path()
  {
	  if( !is_array(self::$_parts) ) {
		  self::$_parts = parse_url(CMS_ROOT_URL);
      }
	  if( empty(self::$_parts['path']) ) {
		  self::$_parts['path'] = '/'; // default to whole domain
	  }
	  return self::$_parts['path'];
  }

  /**
   * @ignore
   */
  private static function __domain()
  {
	  if( !is_array(self::$_parts) ) {
		  self::$_parts = parse_url(CMS_ROOT_URL);
      }
	  if( empty(self::$_parts['host']) ) {
		  self::$_parts['host'] = CMS_ROOT_URL; // default to whole domain (including all subdomains)
	  }
	  return self::$_parts['host'];
  }


  /**
   * @ignore
   */
  private static function __setcookie($key,$value,$expire)
  {
    $res = setcookie($key,$value,$expire,
					 self::__path(),
					 self::__domain(),
                     CmsApp::get_instance()->is_https_request(),
					 TRUE);
  }


  /**
   * Set a cookie
   *
   * @param string $key The cookie name
   * @param string $value The cookie value
   * @param int    $expire Unix timestamp of the time the cookie will expire.   By default cookies that expire when the browser closes will be created.
   * @return bool
   */
  public static function set($key,$value,$expire = 0)
  {
    return self::__setcookie($key,$value,$expire);
  }


  /**
   * Get the value of a cookie
   *
   * @param string $key The cookie name
   * @return mixed.  Null if the cookie does not exist, otherwise a string containing the cookie value.
   */
  public static function get($key)
  {
    if( isset($_COOKIE[$key]) ) return $_COOKIE[$key];
  }


  /**
   * Test if a cookie exists.
   *
   * @since 1.11
   * @param string $key The cookie name.
   * @return bool
   */
  public static function exists($key)
  {
	  return isset($_COOKIE[$key]);
  }


  /**
   * Erase a cookie
   *
   * @param string $key The cookie name
   */
  public static function erase($key)
  {
    unset($_COOKIE[$key]);
    self::__setcookie($key,'',time()-3600);
  }

} // end of class

#
# EOF
#
?>