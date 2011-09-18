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
 * A simple static class providing convenience utilities for working with cookies.
 *
 * @package CMS
 * @uthor Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.10
 */
class cms_cookies
{
  private static $_parts;
  /**
   * @ignore
   */
  final private function __construct() {}


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


  private static function __https()
  {
    if( !isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ) return FALSE;
    return TRUE;
  }


  private static function __setcookie($key,$value,$expire)
  {
    return setcookie($key,$value,$expire,
		     self::__path(),
		     self::__domain(),
		     TRUE,
		     self::__https());
  }


  public static function set($key,$value,$expire = 0)
  {
    return self::__setcookie($key,$value,$expire);
  }


  public static function get($key)
  {
    if( isset($_COOKIE[$key]) ) return $_COOKIE[$key];
  }


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