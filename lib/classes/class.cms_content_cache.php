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
 * A static class to manage caching content objects.
 *
 * @package CMS
 * @since 1.9
 */
final class cms_content_cache
{
  private static $_alias_map;
  private static $_id_map;
  private static $_content_cache;


  /**
   * @ignore
   */
  private static function &get_content_obj($hash)
  {
    $res = null;
    if( self::$_content_cache ) 
      {
	if( isset(self::$_content_cache[$hash]) )
	  {
	    $res = self::$_content_cache[$hash];
	  }
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
    if( $hash === FALSE )
      {
		  // content doesn't exist...
		  if( is_numeric($identifier) )
		  {
			  // so add a null object, so we don't request it from the database again.
			  self::_add_content($identifier,'',$res);
			  return $res;
		  }
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
   * @return boolean
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
   * @param integer The content Id.
   * @param string  The content alias
   * @param ContentBase The content object.
   * @return boolean
   */
  private static function _add_content($id,$alias,&$obj)
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
   * @param integer The content Id.
   * @param string  The content alias
   * @param ContentBase The content object.
   * @return boolean
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
	  if( is_array(self::$_id_map) )  return array_keys(self::$_id_map);
  }


  /**
   * Retrieve a pageid given an alias.
   *
   * @param string Page alias
   * @return integer id, or FALSE if alias cannot be found in cache.
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
   * @param integer page id.
   * @return string alias, or FALSE if id cannot be found in cache.
   */
  public static function get_alias_from_id($id)
  {
    if( !isset(self::$_id_map) ) return FALSE;
    if( !isset(self::$_id_map[$id]) ) return FALSE;
    $hash = self::$_id_map[$id];
    return array_search($hash,self::$_alias_map);
  }
}

?>