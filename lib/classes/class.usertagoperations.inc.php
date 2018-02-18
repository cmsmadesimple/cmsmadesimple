<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#$Id: class.bookmark.inc.php 2746 2006-05-09 01:18:15Z wishy $

/**
 * Usertag related functions.
 *
 * @package CMS
 * @license GPL
 */

/**
 * A compatibility class to manage simple plugins.
 * Formerly 'UserDefinedTags' were stored in the database.
 * In CMSMS 2.3+ this functionality was replaced with simple plugins.
 * This class provides backwards compatibility.
 *
 * @package CMS
 * @license GPL
 * @deprecated
 */
final class UserTagOperations
{
	/**
	 * @ignore
	 */
	private static $_instance;

	/**
	 * @ignore
	 */
	protected function __construct() {}

	/**
	 * Get a reference to thie only allowed instance of this class
	 * @return UserTagOperations
	 */
	public static function &get_instance()
	{
		if( !isset(self::$_instance) ) self::$_instance = new UserTagOperations();
		return self::$_instance;
	}


	/**
	 * @ignore
	 */
	public function __call($name,$arguments)
	{
        return $this->CallUserTag($name,$arguments);
	}

	/**
	 * Load all the information about user tags.
     * Since 2.3, his function is now an empty stub.
     *
     * @deprecated
	 */
    public function LoadUserTags()
    {
        // does not do anything.
    }


	/**
	 * Retrieve the body of a user defined tag
     * Since 2.3, his function is now an empty stub.
	 *
	 * @param string $name User defined tag name
     * @deprecated
	 * @return string|false
	 */
	function GetUserTag( $name )
	{
        return false;
	}

	/**
	 * Test if a user defined tag with a specific name exists
	 *
	 * @param string $name User defined tag name
	 * @return string|false
	 * @since 1.10
	 */
	function UserTagExists($name)
	{
        $gCms = \CmsApp::get_instance();
        $mgr = $gCms->GetSimplePluginOperations();
        return $mgr->plugin_exists($name);
	}


	/**
	 * Add or update a named user defined tag into the database
     * Since 2.3, his function is now an empty stub.
	 *
	 * @param string $name User defined tag name
	 * @param string $text Body of user defined tag
	 * @param string $description Description for the user defined tag.
     * @param int    $id ID of existing user tag (for updates).
	 * @return bool
	 */
	function SetUserTag( $name, $text, $description, $id = null )
	{
        return false;
	}


	/**
	 * Remove a named user defined tag from the database
     * Since 2.3, his function is now an empty stub.
	 *
	 * @param string $name User defined tag name
	 * @return bool
	 */
	function RemoveUserTag( $name )
	{
        return false;
	}


 	/**
	 * Return a list (suitable for use in a pulldown) of user tags.
	 *
	 * @return array|false
	 */
	function ListUserTags()
	{
        $gCms = \CmsApp::get_instance();
        $mgr = $gCms->GetSimplePluginOperations();
        $tmp = $mgr->get_list();
        if( !$tmp ) return;

        $out = null;
        foreach( $tmp as $name ) {
            $out[$name] = $name;
        }
        asort($out);
        return $out;
	}


	/**
	 * Execute a user defined tag
	 *
	 * @param string $name The name of the user defined tag
	 * @param array  $params Optional parameters.
	 * @return mixed|false The returned data from the user defined tag, or FALSE if the UDT could not be found. 
     * @deprecated
	 */
	function CallUserTag($name, &$params)
	{
        $gCms = \CmsApp::get_instance();
        $mgr = $gCms->GetSimplePluginOperations();
		return $mgr->call_plugin($name,$params,$gCms->GetSmarty());
	}

	/**
	 * Given a UDT name create an executable function from it
     * Since 2.3, his function is now an empty stub.
	 *
	 * @internal
	 * @param string $name The name of the user defined tag to operate with.
	 */
	function CreateTagFunction($name)
	{
        return;
	}

} // class
