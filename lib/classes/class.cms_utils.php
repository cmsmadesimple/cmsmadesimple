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
 * A set of convenience utilities for working with CMSMS based applications and modules.
 *
 * This file provides the cms_utils class.  Which is a singleton, utility class providing
 * convenience methods.
 *
 * @author  Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.9
 * @package CMS
 */

/**
 * A Simple Static class providing various convenience utilities.
 *
 * @package CMS
 * @author  Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.9
 */
final class cms_utils
{
  /**
   * @ignore 
   */
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
   * A convenience function to return an indication if a module is availalbe.
   *
   * @see get_module()
   * @final
   * @author calguy1000
   * @since 1.11
   * @param string The module name
   * @return boolean
   */
  final public static function module_available($name)
  {
	  return ModuleOperations::get_instance()->IsModuleActive($name);
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
   * @see CmsApp::GetSmarty()
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


  /**
   * A convenient method to get the class name of the calling function
   *
   *
   * @since 1.10
   * @author calguy1000
   * @final
   * @return string
   */
  public static function get_caller_class()
  {
	$trace = debug_backtrace();
	if( isset($trace[2]['class']) ) return $trace[2]['class'];
  }


  /**
   * A convenient method to get the object pointer to the appropriate wysiwyg module
   * This is a wrapper around a similar function in the ModuleOperations class.
   *
   * This method will return the currently selected frontend wysiwyg for frontend requests (or null if none is selected)
   * For admin requests this method will return the users currently selected wysiwyg module, or null.
   *
   * @since 1.10
   * @param string The module name.
   * @return object or null
   */
  public static function &get_wysiwyg_module($module_name = '')
  {
	  return ModuleOperations::get_instance()->GetWYSIWYGModule($module_name);
  }


  /**
   * A convenient method to get the currently selected syntax highlighter
   * This is a wrapper around a similar function in the ModuleOperations class.
   *
   * @since 1.10
   * @author calguy1000
   * @return object or null
   */
  public static function &get_syntax_highlighter_module()
  {
	  return ModuleOperations::get_instance()->GetSyntaxHighlighter();
  }


  /**
   * A convenience method to get the currently selected search module.
   *
   * @since 1.10
   * @author calguy1000
   * @return object or null
   */
  public static function &get_search_module()
  {
	  return ModuleOperations::get_instance()->GetSearchModule();
  }

  /**
   * Attempt to retreive the IP address of the connected user.
   * This function attempts to compensate for proxy servers.
   *
   * @author calguy1000
   * @since 1.10
   * @returns string IP address in dotted notation, or null
   */
  public static function get_real_ip()
  {
	$ip = $_SERVER['REMOTE_ADDR'];
    if (empty($ip) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (empty($ip) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
	
	if( filter_var($ip,FILTER_VALIDATE_IP) ) return $ip;

	return null;
  }
  
  /**
   * Check if IP is valid IPV4 address.
   *
   * @author Stikki
   * @since 1.11.7
   * @returns true on correct IP address, else false.
   */
  public static function is_valid_ipv4($ip)
  {
	return preg_match('/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.'.
	'(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/', $ip) !== 0;
  }  

  /**
   * Get a reference to the current theme object
   * only returns a valid value when in an admin request.
   *
   * @author calguy1000
   * @since 1.11
   * @returns CmsAdminThemeBase derived object, or null
   */
  public static function &get_theme_object()
  {
	  return CmsAdminThemeBase::GetThemeObject();
  }

  /**
   * Create a thumbnail for an image.
   * This is a fairly smart routine that first detects if a thumbnail already exists, or if one can be written anyways, and then uses the system preferences
   * to generate the thumbnail file.
   *
   * @param string complete file specification to a source image
   * @author calguy1000
   * @since 1.11
   * @returns string complete file specification to a thumbnail for an image.  Or null.
   */
  public static function generate_thumbnail($srcfile)
  {
	  if( !file_exists($srcfile) ) return;
          $ext =  strtolower(strrchr($srcfile,'.'));
          while( startswith($ext,'.') ) {
            $ext = substr($ext,1);
          }
          if( !in_array($ext,array('jpg','jpeg','png','bmp','gif')) ) {
             return; // not gonna create a thumb on anything but an image.
          } 
	  $dn = dirname($srcfile);
	  $bn = basename($srcfile);
	  if( startswith($bn,'thumb_') ) {
		  return; // not gonna create a thumb on a thumb.
	  }

	  $thumb = cms_join_path($dn,'thumb_'.$bn);
	  if( file_exists($thumb) && filemtime($thumb) > filemtime($srcfile) ) {
		  // nothing to do, thumb exists and is newer than the source.
		  return $thumb;
	  }

	  if( !is_writable($dn) ) {
		  // can't write.
		  return;
	  }

	  $config = cmsms()->GetConfig();
	  require_once($config['root_path'].'/lib/filemanager/ImageManager/Classes/Transform.php');
	  $width = get_site_preference('thumbnail_width',96);
	  $height = get_site_preference('thumbnail_height',96);

	  $transform = new Image_Transform;
	  $img = $transform->factory($config['image_manipulation_prog']);
	  $img->load($srcfile);
	  
	  $img->resize($width,$height);
	  $img->save($thumb);
	  return $thumb;
  }
} // end of class

?>
