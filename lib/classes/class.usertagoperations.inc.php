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

use CMSMS\simple_plugin_operations;

/**
 * A compatibility class to manage simple plugins.
 * Formerly 'UserDefinedTags' were stored in the database.
 * In CMSMS 2.3+ this functionality was replaced with simple plugins.
 * This class provides backwards compatibility.
 * This class will be removed from CMSMS at some point in the future.
 *
 * @package CMS
 * @license GPL
 * @deprecated User Tags (or UDTs) are replaced with Simple plugins since v2.3
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
    private $ops;

    /**
     * @ignore
     */
    public function __construct(simple_plugin_operations $ops)
    {
        if( self::$_instance ) throw new \LogicException("Only one instance of ".__CLASS__." is permitted");
        self::$_instance = $this;
        $this->ops = $ops;
    }

    /**
     * Get a reference to thie only allowed instance of this class
     *
     * @return UserTagOperations
     */
    public static function get_instance() : UserTagOperations
    {
        if( ! self::$_instance ) throw new \LogicException("No instance of ".__CLASS__." has been created");
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
    public function GetUserTag( $name )
    {
        return false;
    }

    /**
     * Test if a user defined tag with a specific name exists
     *
     * @param string $name User defined tag name
     * @return string|false
     * @since 1.10
     * @deprecated
     */
    public function UserTagExists($name)
    {
        return $this->ops->plugin_exists($name);
    }

    /**
     * Add or update a named user defined tag into the database
     * Since 2.3, his function is now an empty stub.
     *
     * @deprecated
     * @param string $name User defined tag name
     * @param string $text Body of user defined tag
     * @param string $description Description for the user defined tag.
     * @param int    $id ID of existing user tag (for updates).
     * @return bool
     */
    public function SetUserTag( $name, $text, $description, $id = null )
    {
        return false;
    }

    /**
     * Remove a named user defined tag from the database
     * Since 2.3, his function is now an empty stub.
     *
     * @deprecated
     * @param string $name User defined tag name
     * @return bool
     */
    public function RemoveUserTag( $name )
    {
        return false;
    }

    /**
     * Return a list (suitable for use in a pulldown) of user tags.
     *
     * @deprecated
     * @return array|false
     */
    public function ListUserTags()
    {
        $tmp = $this->ops->get_list();
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
    public function CallUserTag($name, &$params)
    {
        return $this->ops->call_plugin($name,$params,$gCms->GetSmarty());
    }

    /**
     * Given a UDT name create an executable function from it
     * Since 2.3, his function is now an empty stub.
     *
     * @internal
     * @deprecated
     * @param string $name The name of the user defined tag to operate with.
     */
    public function CreateTagFunction($name)
    {
        // nothing here
    }

} // class
