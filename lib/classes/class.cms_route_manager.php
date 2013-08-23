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

if( !function_exists('__internal_cmp_routes') ) {
	/**
	 * @internal
	 * @ignore
	 */
	function __internal_cmp_routes($a,$b)
	{
		return strcmp($a['term'],$b['term']);
	}
}

/**
 * A class to manage all recognized routes in the system.
 * 
 * @package CMS
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since  1.9
 */
final class cms_route_manager
{
	// this class cannot be instantiated.
	private function __construct() {}

	private static $_routes_loaded = FALSE;
	private static $_routes;
	private static $_dynamic_routes;

	static private function _find_match($needle,$haystack,$exact)
	{
		// split the haystack into an array of 'absolute' or 'regex' matches
		$absolute = array();
		$regex = array();
		foreach( $haystack as $sig => $rec ) {
			if( $exact || (isset($rec['absolute']) && $rec['absolute']) ) {
				$absolute[] = $rec;
			}
			else {
				$regex[] = $rec;
			}
		}

		// sort the list of absolutes
		usort($absolute,'__internal_cmp_routes');

		// do a binary search on the absolute routes
		if( count($absolute) ) {
			$res = self::route_binarySearch($needle,$absolute,'strcmp');
			if( $res !== FALSE ) return $absolute[$res];
		}

		// do the linear regex thing.
		for( $i = 0; $i < count($regex); $i++ ) {
			$rec = $regex[$i];
			if( $rec->matches($needle) ) return $rec;
		}

		return FALSE;
	}

	// this function should go into a global utils class somewhere.
	static private function route_binarySearch($needle,$haystack,$comparator)
	{
		if( count($haystack) == 0 ) return FALSE;

		// credits: temporal dot pl at gmail dot com
		// reference: http://php.net/manual/en/function.array-search.php
		$high = Count( $haystack ) -1;
		$low = 0;
		while ( $high >= $low ) {
			$probe = (int)Floor( ( $high + $low ) / 2 );
			$comparison = $comparator( $haystack[$probe]['term'], $needle );
			if ( $comparison < 0 ) {
				$low = $probe +1;
			}
			elseif ( $comparison > 0 ) {
				$high = $probe -1;
			}
			else {
				return $probe;
			}
		}

		//The loop ended without a match 
		//Compensate for needle greater than highest haystack element
		if($comparator($haystack[count($haystack)-1]['term'], $needle) < 0) {
			$probe = count($haystack);
		}
		return FALSE;
	}

	/**
	 * Test wether the specified route exists.
	 *
	 * @param CmsRoute The route object
	 * @param boolean  A flag indicating that only static routes should be checked.
	 * @return boolean
	 */
	static public function route_exists(CmsRoute $route,$static_only = FALSE)
	{
		self::_load_static_routes();
		if( is_array(self::$_routes) ) {
			if( isset(self::$_routes[$route->signature()]) ) return TRUE;
		}

		if( $static_only ) return FALSE;


		if( is_array(self::$_dynamic_routes) ) {
			if( isset(self::$_dynamic_routes[$route->signature()]) ) return TRUE;
		}
		return FALSE;
	}


	/**
	 * Find a route that matches the specified string
	 *
	 * @param string The string to test against (usually an incoming url request)
	 * @param boolean Perform an exact string match rather than a regex match.
	 * @param boolean A flag indicating that only static routes should be checked.
	 * @return CmsRoute the matching route, or null.
	 */
	static public function find_match($str,$exact = false,$static_only = FALSE)
	{
		self::_load_static_routes();

		if( is_array(self::$_routes) ) {
			$res = self::_find_match($str,self::$_routes,$exact);
			if( is_object($res) ) return $res;
		}

		if( $static_only ) return;

		if( is_array(self::$_dynamic_routes) ) {
			$res = self::_find_match($str,self::$_dynamic_routes,$exact);
			if( is_object($res) ) return $res;
		}
	}


	/**
	 * Add a static route.
	 * This method will return TRUE, and do nothing if the route already exists.
	 * The route cache will be removed if the route is successfully added to the database.
	 *
	 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
	 * @since 1.11
	 * @param CmsRoute the route to add.
	 * @return boolean
	 */
	public static function add_static(CmsRoute& $route)
	{
		self::_load_static_routes();
		if( self::route_exists($route) ) return TRUE;

		$query = 'INSERT INTO '.cms_db_prefix().'routes (term,key1,key2,key3,data,created) VALUES (?,?,?,?,?,NOW())';
		
		$db = cmsms()->GetDb();
		$dbr = $db->Execute($query,array($route['term'], $route['key1'], $route['key2'], $route['key3'], serialize($route)));
		if( !$dbr ) {
			die($db->sql.' -- '.$db->ErrorMsg());
			return FALSE;
		}

		self::_clear_cache();
		return TRUE;
	}
  

	/**
	 * Delete a static route
	 * The route cache will be removed if the route is successfully removed from the database.
	 *
	 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
	 * @since 1.11
	 * @param mixed If a CmsRoute object is passed in, it can be removed directly.  Otherwise the term of a route (a string) can be passed in.
	 * @return boolean
	 */
	public static function del_static($term,$key1 = null,$key2 = null,$key3 = null)
	{
		$query = 'DELETE FROM '.cms_db_prefix().'routes WHERE ';
		$where = array();
		$parms = array();
		if( $term ) {
			$where[] = 'term = ?';
			$parms[] = $term;
		}

		if( !is_null($key1) ) {
			$where[] = 'key1 = ?';
			$parms[] = $key1;

			if( !is_null($key2) ) {
				$where[] = 'key2 = ?';
				$parms[] = $key2;

				if( !is_null($key3) ) {
					$where[] = 'key3 = ?';
					$parms[] = $key3;
				}
			}
		}

		if( count($where) == 0 ) return FALSE;

		$db = cmsms()->GetDb();
		$query .= implode(' AND ',$where);
		$dbr = $db->Execute($query,$parms);
		if( $dbr ) {
			self::_clear_cache();
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * Add a dynamic route
	 * Dynamic routes are not stored to the database, and are checked after static routes when searching for a match.
	 * This method will return TRUE if the route already exists (static, or dynamic)
	 *
	 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
	 * @since 1.11
	 * @param CmsRoute The dynamic route object to add
	 * @param boolean  Flag indicating wether duplicate checking should be done.
	 * @return boolean.
	 */
	public static function add_dynamic(CmsRoute& $route)
	{
		if( self::route_exists($route) ) return FALSE;
		if( !is_array(self::$_dynamic_routes) ) self::$_dynamic_routes = array();
		self::$_dynamic_routes[$route->signature()] = $route;
		return TRUE;
	}

  
	/**
	 * Register a new route.
	 * This is just an alias (for compatibility reasons) to the add_dynamc method.
	 *
	 * @see add_dynamic
	 * @param CmsRoute The route to register
	 * @param boolean  Flag indicating wether duplicate checking should be done.
	 * @return boolean
	 */
	static public function register(CmsRoute $route)
	{
		return self::add_dynamic($route);
	}


	/**
	 * Load dynamic routes from the modules.
	 * typically called by modules or places where static urls are added
	 * this method will load all modules and call setparameters to ensure
	 * that their dynamic routes are created.
	 *
	 * @deprecated
	 */
	public static function load_routes()
	{
		global $CMS_ADMIN_PAGE;
		$flag = false;
		if( isset($CMS_ADMIN_PAGE) ) {
			// hack to force modules to register their routes.
			$flag = $CMS_ADMIN_PAGE;
			unset($CMS_ADMIN_PAGE);
		}

		// todo: 
		$modules = ModuleOperations::get_instance()->GetLoadedModules();
		foreach( $modules as $name => &$module ) {
			$module->SetParameters();
		}

		if( $flag ) $CMS_ADMIN_PAGE = $flag;
	}

	/**
	 * Reset the static route table.
	 *
	 * @since 1.11
	 * @author Robert Campbell
	 * @internal
	 */
	public static function rebuild_static_routes()
	{
		// clear the route table and cache
		self::_clear_cache();
		$db = cmsms()->GetDb();
		$query = 'TRUNCATE TABLE '.cms_db_prefix().'routes';
		$db->Execute($query);

		// get content routes
		$query = 'SELECT content_id,page_url FROM '.cms_db_prefix()."content WHERE active=1 AND COALESCE(page_url,'') != ''";
		$tmp = $db->GetArray($query);
		if( is_array($tmp) && count($tmp) ) {
			for( $i = 0; $i < count($tmp); $i++ ) {
				$route = CmsRoute::new_builder($tmp[$i]['page_url'],'__CONTENT__',$tmp[$i]['content_id'],'',TRUE);
				cms_route_manager::add_static($route);
			}
		}

		// get the module routes
		$installed = ModuleOperations::get_instance()->GetInstalledModules();
		foreach( $installed as $module_name ) {
			$modobj = cms_utils::get_module($module_name);
			if( !$modobj ) continue;		
			$routes = $modobj->CreateStaticRoutes();
		}
	}
	
	/**
	 * Load existing static routes from the cache
	 * This method will also refresh the cache from the database if the cache cannot be found.
	 * Note: It should not be necessary to load routes, as this method is called internally.
	 *
	 * @return void
	 */
	private static function _load_static_routes()
	{
		if( self::$_routes_loaded ) return;

		$data = self::_get_routes_from_cache();
		if( is_array($data) && count($data) ) {
			self::$_routes = array();
			for( $i = 0; $i < count($data); $i++ ) {
				$obj = @unserialize($data[$i]['data']);
				self::$_routes[$obj->signature()] = $obj;
			}
			self::$_routes_loaded = TRUE;
		}
	}


	private static function _get_routes_from_cache()
	{
		$fn = self::_get_cache_filespec();
		if( !file_exists($fn) ) {
			$db = cmsms()->GetDb();
			$query = 'SELECT * FROM '.cms_db_prefix().'routes';
			$tmp = $db->GetArray($query);
			self::$_routes_loaded = TRUE;
			if( is_array($tmp) && count($tmp) ) {
				$fn = self::_get_cache_filespec();
				file_put_contents($fn,serialize($tmp));
				return $tmp;
			}
		}
		else {
			self::$_routes_loaded = TRUE;
			return unserialize(file_get_contents($fn));
		}
	}


	private static function _get_cache_filespec()
	{
		return TMP_CACHE_LOCATION.'/'.md5(TMP_CACHE_LOCATION.get_class()).'.dat';
	}


	private static function _clear_cache()
	{
		@unlink(self::_get_cache_filespec());
		self::$_routes = null;
		self::$_routes_loaded = FALSE;
		// note: dynamic routes don't get cleared.
	}
} // end of class


# vim:ts=4 sw=4 noet
?>