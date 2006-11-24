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

debug_buffer('', 'Start Loading User Operations');

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.user.inc.php');

class UserOperations
{
	/**
	 * Gets a list of all users
	 *
	 * @returns array An array of User objects
	 * @since 0.6.1
	 */
	function &LoadUsers()
	{
		global $gCms;
		$user = $gCms->GetOrmClass('user');
		$obj = $user->find_all(array('order' => 'username'));
		return $obj;
	}

	/**
	 * Gets a list of all users in a given group
	 *
	 * @param mixed $groupid Group for the loaded users
	 * @returns array An array of User objects
	 */
	function &LoadUsersInGroup($groupid)
	{
		global $gCms;
		$db = &$gCms->GetDb();
		$result = array();

		$query = "SELECT u.user_id, u.username, u.password, u.first_name, u.last_name, u.email, u.active, u.admin_access FROM ".cms_db_prefix()."users u, ".cms_db_prefix()."groups g, ".cms_db_prefix()."user_groups cg where cg.user_id = u.user_id and cg.group_id = g.group_id and g.group_id =? ORDER BY username";
		$dbresult = $db->Execute($query, array($groupid));

		while ($dbresult && $row = $dbresult->FetchRow())
		{
			$oneuser = new User();
			$oneuser->id = $row['user_id'];
			$oneuser->username = $row['username'];
			$oneuser->firstname = $row['first_name'];
			$oneuser->lastname = $row['last_name'];
			$oneuser->email = $row['email'];
			$oneuser->password = $row['password'];
			$oneuser->active = $row['active'];
			$oneuser->adminaccess = $row['admin_access'];
			$result[] =& $oneuser;
		}

		return $result;
	}

	/**
	 * Loads a user by username.
	 *
	 * @param mixed $username Username to load
	 * @param mixed $password Password to check against
	 * @param mixed $activeonly Only load the user if they are active
	 * @param mixed $adminaccessonly Only load the user if they have admin access
	 *
	 * @returns mixed If successful, the filled User object.  If it fails, it returns false.
	 * @since 0.6.1
	 */
	function &LoadUserByUsername($username, $password = '', $activeonly = true, $adminaccessonly = false)
	{
		global $gCms;
		$user =& $gCms->GetOrmClass('user');
		
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
		
		$obj = $user->find(array('conditions', array($condstring, $params)));
		return $obj;
	}

	/**
	 * Loads a user by user id.
	 *
	 * @param mixed $id User id to load
	 *
	 * @returns mixed If successful, the filled User object.  If it fails, it returns false.
	 * @since 0.6.1
	 */
	function &LoadUserByID($id)
	{
		global $gCms;
		$user = $gCms->GetOrmClass('user');
		$obj = $user->find_by_id($id);
		return $obj;
	}

	/**
	 * Saves a new user to the database.
	 *
	 * @param mixed $usre User object to save
	 *
	 * @returns mixed The new user id.  If it fails, it returns -1.
	 * @since 0.6.1
	 */
	function InsertUser($user)
	{
		$user->save();
	}

	/**
	 * Updates an existing user in the database.
	 *
	 * @param mixed $user User object to save
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 * @since 0.6.1
	 */
	function UpdateUser($user)
	{
		$user->save();
	}

	/**
	 * Deletes an existing user from the database.
	 *
	 * @param mixed $id Id of the user to delete
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 * @since 0.6.1
	 */
	function DeleteUserByID($id)
	{
		global $gCms;
		$user =& $gCms->GetOrmClass('user');
		return $user->delete($id);
	}

	/**
	 * Show the number of pages the given user's id owns.
	 *
	 * @param mixed $id Id of the user to count
	 *
	 * @returns mixed Number of pages they own.  0 if any problems.
	 * @since 0.6.1
	 */
	function CountPageOwnershipByID($id)
	{
		$result = 0;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT count(*) AS count FROM ".cms_db_prefix()."content WHERE owner_id = ?";
		$dbresult = $db->Execute($query, array($id));

		if ($dbresult && $dbresult->RecordCount() > 0)
		{
			$row = $dbresult->FetchRow();
			if (isset($row["count"]))
			{
				$result = $row["count"];
			}
		}

		return $result;
	}

	function GenerateDropdown($currentuserid='', $name='ownerid')
	{
		$result = '';

		$allusers = UserOperations::LoadUsers();

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
}

debug_buffer('', 'End Loading User Operations');

?>