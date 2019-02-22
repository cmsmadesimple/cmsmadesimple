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
 * A convenience class for CMS Made Simple.
 *
 * The methods in this class provide simple wrappers over other class methods.
 *
 * @package CMS
 * @license GPL
 */

/**
 * A Simple Static class providing various convenience utilities.
 *
 * @package CMS
 * @license GPL
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
	 * Get data that was stored elsewhere in the application.
	 *
	 * @since 1.9
	 * @param string $key The key to get.
	 * @return mixed The stored data, or null
	 */
	public static function get_app_data($key)
	{
		if( is_array( self::$_vars ) && isset(self::$_vars[$key]) ) return self::$_vars[$key];
	}


	/**
	 * Set data for later use.
	 *
	 * This method is typically used to store data for later use by another part of the application.
	 * This data is not stored in the session, so it only exists for one request.
	 *
	 * @since 1.9
	 * @param string $key The name of this data.
	 * @param mixed  $value The data to store.
	 */
	public static function set_app_data($key,$value)
	{
		if( $key == '' ) return;
		if( !is_array(self::$_vars) ) self::$_vars = array();
		self::$_vars[$key] = $value;
	}


	/**
	 * A convenience function to return the object representing an installed module.
	 *
	 * If a version string is passed, a matching object will only be returned IF
	 * the installed version is greater than or equal to the supplied version.
	 *
	 * @see version_compare()
	 * @see ModuleOperations::get_module_instance
	 * @final
	 * @since 1.9
	 * @param string $name The module name
	 * @param string $version An optional version string
	 * @return CmsModule The matching module object or null
	 */
	public static function &get_module($name,$version = '')
	{
		return ModuleOperations::get_instance()->get_module_instance($name,$version);
	}


	/**
	 * A convenience function to return an indication if a module is availalbe.
	 *
	 * @see get_module()
	 * @final
	 * @author calguy1000
	 * @since 1.11
	 * @param string $name The module name
	 * @return bool
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
		return \CmsApp::get_instance()->GetDb();
	}


	/**
	 * A convenience function to return a handle to the global CMSMS config.
	 *
	 * @final
	 * @since 1.9
	 * @return cms_config The global configuration object.
	 */
	final public static function & get_config()
	{
		return \cms_config::get_instance();
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
		return \Smarty_CMS::get_instance();
	}


	/**
	 * A convenience functon to return a reference to the current content object.
	 *
	 * This function will always return NULL if called from an admin action
	 *
	 * @since 1.9
	 * @final
	 * @return Content The current content object, or null
	 */
	final public static function get_current_content()
	{
		return CmsApp::get_instance()->get_content_object();
	}


	/**
	 * A convenience function to return the alias of the current page.
	 *
	 * This function will always return NULL if called from an admin action
	 *
	 * @since 1.9
	 * @final
	 * @return string
	 */
	final public static function get_current_alias()
	{
		$obj = CmsApp::get_instance()->get_content_object();
		if( $obj ) return $obj->Alias();
	}


	/**
	 * A convenience function to return the page id of the current page
	 *
	 * This function will always return NULL if called from an admin action
	 *
	 * @since 1.9
	 * @final
	 * @return int
	 */
	final public static function get_current_pageid()
	{
		return CmsApp::get_instance()->get_content_id();
	}


	/**
	 * A convenient method to get the object pointer to the appropriate wysiwyg module.
	 * This is a wrapper around a similar function in the ModuleOperations class.
	 *
	 * This method will return the currently selected frontend wysiwyg for frontend requests (or null if none is selected)
	 * For admin requests this method will return the users currently selected wysiwyg module, or null.
	 *
	 * @since 1.10
	 * @param string $module_name The module name.
	 * @return CMSModule
	 */
	public static function &get_wysiwyg_module($module_name = '')
	{
		return ModuleOperations::get_instance()->GetWYSIWYGModule($module_name);
	}


	/**
	 * A convenient method to get the currently selected syntax highlighter.
	 * This is a wrapper around a similar function in the ModuleOperations class.
	 *
	 * @since 1.10
	 * @author calguy1000
	 * @return CMSModule
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
	 * @return CMSModule
	 */
	public static function &get_search_module()
	{
		return ModuleOperations::get_instance()->GetSearchModule();
	}

	/**
	 * A convenience method to get the currently selected filepicker module.
	 *
	 * @since 2.2
	 * @author calguy1000
	 * @return CMSModule
	 */
	public static function &get_filepicker_module()
	{
		return ModuleOperations::get_instance()->GetFilePickerModule();
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
        $ip = null;
        if( !empty($_SERVER['REMOTE_ADDR']) ) $ip = $_SERVER['REMOTE_ADDR'];
		elseif (empty($ip) && !empty($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif (empty($ip) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

		if( filter_var($ip,FILTER_VALIDATE_IP) ) return $ip;
		return null;
	}

	/**
	 * Get a reference to the current theme object
	 * only returns a valid value when in an admin request.
	 *
	 * @author calguy1000
	 * @since 1.11
	 * @returns CmsAdminThemeBase derived object, or null
	 */
	public static function get_theme_object()
	{
		return CmsAdminThemeBase::GetThemeObject();
	}

} // end of class

?>