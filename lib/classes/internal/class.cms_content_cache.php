<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_content_tree (c) 2010 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A caching tree for CMSMS content objects.
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
 * This file contains a static singleton class to manage caching content objects
 *
 * @ignore
 * @internal
 * @package CMS
 */

/**
 * A static class to manage caching content objects.
 *
 * @package CMS
 * @since 1.9
 * @internal
 * @ignore
 */
final class cms_content_cache
{
	/**
	 * @ignore
	 */
	private static $_instance;

	/**
	 * @ignore
	 */
	private static $_alias_map;

	/**
	 * @ignore
	 */
	private static $_id_map;

	/**
	 * @ignore
	 */
	private static $_content_cache;

	/**
	 * @ignore
	 */
	private $_preload_cache;

	/**
	 * @ignore
	 */
	private $_key;


	/**
	 * @ignore
	 */
	private function __construct()
	{
		if( !CmsApp::get_instance()->is_frontend_request() ) return;
        debug_buffer('cms_content_cache: begin load needed content objects');
		$content_ids = null;
		$deep = FALSE;
		$this->_key = 'pc'.md5($_SERVER['REQUEST_URI'].serialize($_GET));
		if( ($data = cms_cache_handler::get_instance()->get($this->_key,__CLASS__)) ) {
			list($lastmtime,$deep,$content_ids) = unserialize($data);
			if( $lastmtime < ContentOperations::get_instance()->GetLastContentModification() ) {
				$deep = null;
				$content_ids = null;
			}
		}
		if( is_array($content_ids) && count($content_ids) ) {
			$this->_preload_cache = $content_ids;
			$contentops = ContentOperations::get_instance();
			$tmp = $contentops->LoadChildren(null,$deep,false,$content_ids);
		}
        debug_buffer('cms_content_cache: end loading needed content objects');
	}

	/**
	 * @ignore
	 */
	public static function &get_instance()
	{
		if( !is_object(self::$_instance) ) self::$_instance = new cms_content_cache();
		return self::$_instance;
	}

	/**
	 * @ignore
	 */
	public function __destruct()
	{
		if( !CmsApp::get_instance()->is_frontend_request() ) return;
		if( !$this->_key ) return;

		$list = $this->get_loaded_page_ids();
		if( is_array($list) && count($list) ) {
			$dirty = false;
			if( !is_array($this->_preload_cache) || count($this->_preload_cache) == 0 ) {
				$dirty = true;
			}
			else {
				$t2 = array_diff($list,$this->_preload_cache);
				if( is_array($t2) && count($t2) ) $dirty = true;
			}

			if( $dirty ) {
                $ndeep = array();
				$deep = FALSE;
				foreach( $list as $one ) {
					$obj = self::get_content($one);
					if( !is_object($obj) ) continue;
					$tmp = $obj->Properties();
					if( is_array($tmp) && count($tmp) ) {
						$deep = TRUE;
                        $ndeep[] = $one;
						break;
					}
				}
                $deep = ($deep && count($ndeep) > (count($list) / 4)) ? TRUE : FALSE;
				$tmp = array(time(),$deep,$list);
				cms_cache_handler::get_instance()->set($this->_key,serialize($tmp),__CLASS__);
			}
		}
	}

	/**
	 * @ignore
	 */
	private static function &get_content_obj($hash)
	{
		$res = null;
		if( self::$_content_cache ) {
			if( isset(self::$_content_cache[$hash]) ) $res = self::$_content_cache[$hash];
		}
		return $res;
	}


  /**
   * Given a unique identifier, return a content object from the cache.
   *
   * If the identifier is an integer, an id search is performed.
   * If the identifier is a string, an alias search is performed.
   *
   * @param mixed Unique identifier
   * @return ContentBase The contentBase object, or null.
   */
  public static function &get_content($identifier)
  {
	  $res = null;
	  $hash = self::content_exists($identifier);
	  if( $hash === FALSE ) {
		  // content doesn't exist...
          return $res;
	  }

	  return self::get_content_obj($hash);
  }


  /**
   * Test if the specified content exists
   *
   * If the identifier is an integer, an id search is performed.
   * If the identifier is a string, an alias search is performed.
   *
   * @param mixed Unique identifier
   * @return bool
   */
  public static function content_exists($identifier)
  {
	  if( !self::$_content_cache ) return FALSE;

	  if( is_numeric($identifier) ) {
		  if( !self::$_id_map ) return FALSE;
		  if( !isset(self::$_id_map[$identifier]) ) return FALSE;
		  return self::$_id_map[$identifier];
	  }
	  else if( is_string($identifier) ) {
		  if( !self::$_alias_map ) return FALSE;
		  if( !isset(self::$_alias_map[$identifier]) ) return FALSE;
		  return self::$_alias_map[$identifier];
	  }
	  return FALSE;
  }


  /**
   * Add data to the cache
   *
   * @access private
   * @internal
   * @since 1.10.1
   * @param int The content Id.
   * @param string  The content alias
   * @param ContentBase The content object.
   * @return bool
   */
  private static function _add_content($id,$alias,ContentBase& $obj)
  {
    if( !$id) return FALSE;
    if( !self::$_alias_map ) self::$_alias_map = array();
    if( !self::$_id_map ) self::$_id_map = array();
    if( !self::$_content_cache ) self::$_content_cache = array();

    $hash = md5($id.$alias);
    self::$_content_cache[$hash] = $obj;
	if( $alias ) self::$_alias_map[$alias] = $hash;
    self::$_id_map[$id] = $hash;
    return TRUE;
  }

  /**
   * Add the content object to the cache
   *
   * @param int The content Id.
   * @param string  The content alias
   * @param ContentBase The content object.
   * @return bool
   */
  public static function add_content($id,$alias,ContentBase& $obj)
  {
	  self::_add_content($id,$alias,$obj);
  }


  /**
   * Clear the contents of the entire cache
   */
  public static function clear()
  {
	  self::$_content_cache = null;
	  self::$_alias_map = null;
	  self::$_id_map = null;
  }


  /**
   * Return a list of the page ids that are in the cache
   *
   * @return Array
   */
  public static function get_loaded_page_ids()
  {
	  if( is_array(self::$_id_map) && count(self::$_id_map) )  return array_keys(self::$_id_map);
  }


  /**
   * Retrieve a pageid given an alias.
   *
   * @param string Page alias
   * @return int id, or FALSE if alias cannot be found in cache.
   */
  public static function get_id_from_alias($alias)
  {
	  if( !isset(self::$_alias_map) ) return FALSE;
	  if( !isset(self::$_alias_map[$alias]) ) return FALSE;
	  $hash = self::$_alias_map[$alias];
	  return array_search($hash,self::$_id_map);
  }


  /**
   * Retrieve a page alias given an id.
   *
   * @param int page id.
   * @return string alias, or FALSE if id cannot be found in cache.
   */
  public static function get_alias_from_id($id)
  {
	  if( !isset(self::$_id_map) ) return FALSE;
	  if( !isset(self::$_id_map[$id]) ) return FALSE;
	  $hash = self::$_id_map[$id];
	  return array_search($hash,self::$_alias_map);
  }

  /**
   * Indicates wether we have preloaded cached data
   *
   * @return bool
   */
  public static function have_preloaded()
  {
	  if( is_array(self::get_instance()->_preload_cache) ) return TRUE;
	  return FALSE;
  }

  /**
   * Unload the specified content id (numeric id or alias) if loaded.
   * Note, this should be used with caution, as the next time this page is requested it will be loaded from the database again.n
   *
   * If the identifier is an integer, an id search is performed.
   * If the identifier is a string, an alias search is performed.
   *
   * @author Robert Campbell
   * @since  2.0
   * @param mixed Unique identifier
   * @return void
   */
  public static function unload($identifier)
  {
	  $hash = self::content_exists($identifier);
	  if( $hash ) {
		  $id = array_search($identifier,self::$_id_map);
		  $alias = array_search($identifier,self::$_alias_map);
		  if( $alias !== FALSE && $id != FALSE ) {
			  unset(self::$_id_map[$id]);
			  unset(self::$_alias_map[$alias]);
			  unset(self::$_content_cache[$hash]);
		  }
	  }
  }
}

?>