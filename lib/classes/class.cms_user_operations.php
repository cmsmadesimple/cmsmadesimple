<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (tedkulp@users.sf.net)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: class.user.inc.php 2961 2006-06-25 04:49:31Z wishy $

/**
 * Class for doing user related functions.  Maybe of the User object functions
 * are just wrappers around these.
 *
 * @since 0.6.1
 * @package CMS
 */

class CmsUserOperations extends CmsObject
{
	static private $instance = NULL;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get an instance of this object, though most people should be using
	 * the static methods instead.  This is more for compatibility than
	 * anything else.
	 *
	 * @return CmsUserOperations The instance of the singleton object.
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsUserOperations();
		}
		return self::$instance;
	}

	/**
	 * Gets a list of all users
	 *
	 * @returns array An array of User objects
	 * @since 0.6.1
	 **/
	public static function load_users()
	{
		return cmsms()->cms_user->find_all(array('order' => 'username'));
	}
	
	/**
	 * @deprecated Deprecated.  Use load_users instead.
	 **/
	function LoadUsers()
	{
		return CmsUserOperations::load_users();
	}

	/**
	 * Gets a list of all users in a given group
	 *
	 * @param mixed $groupid Group for the loaded users
	 * @return array An array of User objects
	 **/
	public static function load_users_in_group($groupid)
	{
		return cmsms()->cms_user->find_all_by_query("SELECT u.user_id, u.username, u.password, u.first_name, u.last_name, u.email, u.active, u.admin_access FROM ".cms_db_prefix()."users u, ".cms_db_prefix()."groups g, ".cms_db_prefix()."user_groups cg where cg.user_id = u.user_id and cg.group_id = g.group_id and g.group_id =? ORDER BY username", array($groupid));
	}
	
	/**
	 * @deprecated Deprecated.  Use load_users_in_group instead.
	 **/
	function &LoadUsersInGroup($groupid)
	{
		return CmsUserOperations::load_users_in_group($groupid);
	}

	/**
	 * Loads a user by username.
	 *
	 * @param mixed Username to load
	 * @param mixed Password to check against
	 * @param mixed Only load the user if they are active
	 * @param mixed Only load the user if they have admin access
	 *
	 * @return mixed If successful, the filled User object.  If it fails, it returns false.
	 * @since 0.6.1
	 **/
	public static function load_user_by_username($username, $password = '', $activeonly = true, $adminaccessonly = false)
	{	
		$condstring = 'username = ?';
		$params = array();
		
		if ($password != '')
		{
			$condstring .= " AND password = ?";
			$params[] = md5($password);
		}

		if ($activeonly == true)
		{
			$condstring .= " AND active = 1";
		}

		if ($adminaccessonly == true)
		{
			$condstring .= " AND admin_access = 1";
		}
		
		return cmsms()->cms_user->find(array('conditions', array($condstring, $params)));
	}
	
	/**
	 * @deprecated Deprecated.  Use load_user_by_username instead.
	 **/
	function &LoadUserByUsername($username, $password = '', $activeonly = true, $adminaccessonly = false)
	{
		return CmsUserOperations::load_user_by_username($username, $password, $activeonly, $adminaccessonly);
	}

	/**
	 * Loads a user by user id.
	 *
	 * @param mixed User id to load
	 *
	 * @return mixed If successful, the filled User object.  If it fails, it returns null.
	 * @since 0.6.1
	 **/
	public static function load_user_by_id($id)
	{
		return cmsms()->cms_user->find_by_id($id);
	}
	
	/**
	 * @deprecated Deprecated.  Use load_user_by_id instead.
	 **/
	function LoadUserByID($id)
	{
		return CmsUserOperations::load_user_by_id($id);
	}

	/**
	 * Saves a new user to the database.
	 *
	 * @deprecated Deprecated.  User $user->save() instead.
	 *
	 * @param mixed User object to save
	 *
	 * @return mixed The new user id.  If it fails, it returns -1.
	 * @since 0.6.1
	 */
	function InsertUser($user)
	{
		$user->save();
	}

	/**
	 * Updates an existing user in the database.
	 *
	 * @deprecated Deprecated.  User $user->save() instead.
	 *
	 * @param mixed User object to save
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 * @since 0.6.1
	 */
	function UpdateUser($user)
	{
		$user->save();
	}

	/**
	 * Deletes an existing user from the database.
	 *
	 * @deprecated Deprecated.  Use cmsms()->user->delete($id) instead.
	 *
	 * @param mixed Id of the user to delete
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 * @since 0.6.1
	 */
	function DeleteUserByID($id)
	{
		return cmsms()->cms_user->delete($id);
	}

	/**
	 * Show the number of pages the given user's id owns.
	 *
	 * @param mixed Id of the user to count
	 *
	 * @return mixed Number of pages they own.  0 if any problems.
	 * @since 0.6.1
	 */
	function count_page_ownership_by_id($id)
	{
		$query = "SELECT count(*) AS count FROM ".cms_db_prefix()."content WHERE owner_id = ?";
		return cms_db()->GetOne($query, array($id));
	}
	
	/**
	 * @deprecated Deprecated.  Use count_page_ownership_by_id instead.
	 **/
	function CountPageOwnershipByID($id)
	{
		return CmsUserOperations::count_page_ownership_by_id($id);
	}

	/**
	 * Generates an HTML dropdown list of users in the system.
	 *
	 * @param string If sent, this will select that user as the default in the list.
	 * @param string The name of html select item.  Defaults to "ownerid".
	 *
	 * @return string The text for the HTML dropdown.
	 **/
	function generate_user_dropdown($currentuserid='', $name='ownerid')
	{
		$result = '';

		$allusers = CmsUserOperations::load_users();

		if (count($allusers) > 0)
		{
			$result .= '<select name="'.$name.'">';
			foreach ($allusers as $oneuser)
			{
				$result .= '<option value="'.$oneuser->id.'"';
				if ($oneuser->id == $currentuserid)
				{
					$result .= ' selected="selected"';
				}
				$result .= '>'.$oneuser->username.'</option>';
			}
			$result .= '</select>';
		}

		return $result;
	}
	
	/**
	 * @deprecated Deprecated.  Use generate_user_dropdown instead.
	 **/
	function GenerateDropdown($currentuserid='', $name='ownerid')
	{
		return CmsUserOperations::generate_user_dropdown($currentuserid, $name);
	}
}

class UserOperations extends CmsUserOperations
{}

?>