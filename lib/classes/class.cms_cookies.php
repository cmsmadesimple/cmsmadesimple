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
 * A simple static class providing convenience utilities for working with cookies.
 *
 * @package CMS
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
    if( !is_array(self::$_parts) )
      {
		  $config = cmsms()->GetConfig();
		  self::$_parts = parse_url($config['root_url']);
      }
    if( !isset(self::$_parts['path']) || self::$_parts['path'] == '' )
		{
			self::$_parts['path'] = '/';
		}
    return self::$_parts['path'];
  }

  /**
   * @ignore
   */
  private static function __domain()
  {
    if( !is_array(self::$_parts) )
      {
		  $config = cmsms()->GetConfig();
		  self::$_parts = parse_url($config['root_url']);
      }
    if( !isset(self::$_parts['host']) || self::$_parts['host'] == '' )
		{
			self::$_parts['host'] = $config['root_url'];
		}
    return self::$_parts['host'];
  }

  /**
   * @ignore
   */
  private static function __https()
  {
    if( !isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ) return FALSE;
    return TRUE;
  }

  /**
   * @ignore
   */
  private static function __setcookie($key,$value,$expire)
  {
    $res = setcookie($key,$value,$expire,
					 self::__path(),
					 self::__domain(),
					 self::__https(),
					 TRUE);
  }


  /**
   * Set a cookie
   *
   * @param string The cookie name
   * @param string The cookie value
   * @param int    Unix timestamp of the time the cookie will expire.   By default cookies that expire when the browser closes will be created.
   * @return boolean
   */
  public static function set($key,$value,$expire = 0)
  {
    return self::__setcookie($key,$value,$expire);
  }


  /**
   * Get the value of a cookie
   *
   * @param string The cookie name
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
   * @param string The cookie name.
   * @return boolean
   */
  public static function exists($key)
  {
	  return isset($_COOKIE[$key]);
  }

  /**
   * Erase a cookie
   *
   * @param string The cookie name
   * @return void
   */
  public static function erase($key)
  {
    unset($_COOKIE[$key]);
    self::__setcookie($key,null,time()-3600);
  }

} // end of class

#
# EOF
#
?>