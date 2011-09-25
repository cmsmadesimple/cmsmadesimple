<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_content_tree (c) 2010 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A caching tree for CMSMS content objects.
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
 * A class to manage all recognized routes in the system.
 * 
 * @package CMS
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 * @internal
 * @access private
 * @since  1.9
 */
class cms_route_manager
{
  private static $_routes;

  /**
   * A function to load routes from a cache file.
   * This function will test if routes already exist, and will not load them (unless told to force loading).
   *
   * @param boolean Flag to indicate that loading should be forced
   * @return boolean
   * @internal
   * @access private
   */
  static public function load($force = false)
  {
    if( (!is_array(self::$_routes) || count(self::$_routes) == 0) && $force == false )
      {
	return TRUE;
      }

    $fn = self::get_filename();
    if(! file_exists($fn) )
      {
	return FALSE;
      }

    $data = @file($self);
    self::$_routes = array();
    foreach( $data as $line )
      {
	$obj = unserialize($line);
	if( is_object($obj) ) self::$_routes[] = $obj;
      }

    if( !count(self::$_routes) ) return FALSE;
    return TRUE;
  }


  /**
   * Save routes to a cache file
   *
   * @internal
   * @access private
   */
  static public function save()
  {
    if( !is_array(self::$_routes) || count(self::$_routes) == 0 )
      {
	return FALSE;
      }

    $fn = self::get_filename();
    $fp = fopen($fn,'w');
    if( !$fp ) return FALSE;
    foreach( self::$_routes as &$route )
      {
	fwrite($fp,serialize($route)."\n");
      }
    fclose($fp);
  }

  
  /**
   * Test wether the specified route exists.
   *
   * @param CmsRoute The route object
   * @return boolean
   */
  static public function route_exists(CmsRoute $route)
  {
    if( !is_array(self::$_routes) ) return FALSE;

    foreach( self::$_routes as $test )
      {
	if( $test == $route ) return TRUE;
      }
    return FALSE;
  }


  /**
   * Register a new route.
   * This method will not register duplicate routes.
   *
   * @param CmsRoute The route to register
   * @return boolean
   */
  static public function register(CmsRoute $route)
  {
    if( self::route_exists($route) ) return TRUE;

    if( !is_array(self::$_routes) )
      {
	self::$_routes = array();
      }
    self::$_routes[] = $route;
    return TRUE;
  }


  /**
   * Find a route that matches the specified string
   *
   * @param string The string to test against (usually an incoming url request)
   * @param boolean Perform an exact string match rather than a regex match.
   * @return CmsRouteMatch the matching route, or null.
   */
  static public function find_match($str,$exact = false)
  {
    $res = null;
    if( !is_array(self::$_routes) )
      {
	return $res;
      }
    
    foreach( self::$_routes as $route )
      {
	if( $route->matches($str,$exact) )
	  {
	    return $route;
	  }
      }

    return $res;
  }


  /**
   * Load existing routes from modules
   */
  public static function load_routes()
  {
    // force modules to register their routes.
    $gCms = cmsms();
    global $CMS_ADMIN_PAGE;
    $flag = false;
    if( isset($CMS_ADMIN_PAGE) )
      {
	// hack to force modules to register their routes.
	$flag = $CMS_ADMIN_PAGE;
	unset($CMS_ADMIN_PAGE);
      }

    // todo: 
    $modules = ModuleOperations::get_instance()->GetLoadedModules();
    foreach( $modules as $name => &$module )
      {
	$module->SetParameters();
      }

    if( $flag )
      {
	$CMS_ADMIN_PAGE = $flag;
      }

    // force content to register routes.
    $contentops = $gCms->GetContentOperations();
    $contentops->register_routes();
  }

  /**
   * Retrieve the cache filename.
   *
   * @ignore
   * @return string
   */
  private static function get_filename()
  {
    return TMP_CACHE_LOCATION.'/routes.dat';
  }
} // end of class


# vim:ts=4 sw=4 noet
?>