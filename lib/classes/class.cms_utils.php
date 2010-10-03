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
 * A Simple Static class providing various convenience utilities.
 *
 * @package CMS
 * @author  Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.9
 */
class cms_utils
{
  private static $_vars;

  /**
   * @ignore
   */
  final private function __construct() {}


  /**
   * Get stored data
   *
   * @since 1.9
   * @param string The key to get.
   * @return mixed The stored data, or null
   */
  public static function get_app_data($key)
  {
	  if( is_array( self::$_vars ) && isset(self::$_vars[$key]) )
      {
		  return self::$_vars[$key];
      }
  }


  /**
   * Set data for later use.
   *
   * This method is typically used to store data for later use by another part of the application.
   * This data is not stored in the session, so it only exists for one request.
   *
   * @since 1.9
   * @param string The name of this data.
   * @param mixed  The data to store.
   */
  public static function set_app_data($key,$value)
  {
    if( $key == '' ) return;
    if( !is_array(self::$_vars) )
      {
		  self::$_vars = array();
      }
    self::$_vars[$key] = $value;
  }


  /**
   * A convenience function to return the object representing an installed module.
   *
   * If a version string is passed, a matching object will only be returned IF
   * the installed version is greater than or equal to the supplied version.
   *
   * @see version_compare()
   * @final
   * @since 1.9
   * @param string The module name
   * @param string A version string
   * @return CmsModule The matching module object or null
   */
  final public static function & get_module($name,$version = '')
  {
    return cmsms()->GetModuleInstance($name,$version);
  }


  /**
   * A convenience function to return the current database instance.
   *
   * @link http://phplens.com/lens/adodb/docs-adodb.htm
   * @final
   * @since 1.9
   * @return ADOConnection a handle to the ADODB database object
   */
  final public static function & get_db()
  {
    return cmsms()->GetDb();
  }


  /**
   * A convenience function to return a handle to the global CMSMS config.
   *
   * @final
   * @since 1.9
   * @return mixed An associative array of configuration values
   */
  final public static function & get_config()
  {
    return cmsms()->GetConfig();
  }


  /**
   * A convenience function to return a handle to the CMSMS Smarty object.
   *
   * @see CmsObject::GetSmarty()
   * @since 1.9
   * @final
   * @return Smarty_CMS Handle to the Smarty object
   */
  final public static function & get_smarty()
  {
    return cmsms()->GetSmarty();
  }


  /**
   * A convenience functon to return a reference to the current content object
   *
   * This function will always return NULL if called from an admin action
   *
   * @since 1.9
   * @final
   * @return Content The current content object, or null
   */
  final public static function get_current_content()
  {
    return cmsms()->get_variable('content_obj');
  }


  /**
   * A convenience function to return the alias of the current page
   *
   * This function will always return NULL if called from an admin action
   *
   * @since 1.9
   * @final
   * @return string
   */
  final public static function get_current_alias()
  {
    return cmsms()->get_variable('page_name');
  }


  /**
   * A convenience function to return the page id of the current page
   *
   * This function will always return NULL if called from an admin action
   *
   * @since 1.9
   * @final
   * @return integer
   */
  final public static function get_current_pageid()
  {
    return cmsms()->get_variable('content_id');
  }

} // end of class

?>