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
#$Id: class.user.inc.php 2961 2006-06-25 04:49:31Z wishy $

/**
 * User related functions.
 *
 * @package CMS
 * @license GPL
 */

/**
 * Include user class definition
 */
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'class.user.inc.php');

/**
 * Class for doing user related functions.  Maybe of the User object functions
 * are just wrappers around these.
 *
 * @package CMS
 * @license GPL
 * @since 0.6.1
 */
class UserOperations
{
	/**
	 * @ignore
	 */
	protected function __construct() {}

	/**
	 * @ignore
	 */
	private static $_instance;

	/**
	 * @ignore
	 */
	private static $_user_groups;

	/**
	 * @ignore
	 */
	private $_users;

	/**
	 * @ignore
	 */
	private $_saved_users = array();

	/**
	 * Get the reference to the only instance of this object
	 *
	 * @return UserOperations
	 */
	public static function &get_instance()
	{
		if( !is_object(self::$_instance) ) self::$_instance = new UserOperations();
		return self::$_instance;
	}


	/**
	 * Gets a list of all users
	 *
	 * @param int $limit The maximum number of users to return
	 * @param int $offset The offset
	 * @returns array An array of User objects
	 * @since 0.6.1
	 */
	function LoadUsers($limit = 10000,$offset = 0)
	{
		if( !is_array($this->_users) ) {
			$gCms = CmsApp::get_instance();
			$db = $gCms->GetDb();
			$result = array();

			$query = "SELECT user_id, username, password, first_name, last_name, email, active, admin_access
                      FROM ".CMS_DB_PREFIX."users ORDER BY username";
			$dbresult = $db->SelectLimit($query,$limit,$offset);

			while( $dbresult && !$dbresult->EOF ) {
				$row = $dbresult->fields;
				$oneuser = new User();
				$oneuser->id = $row['user_id'];
				$oneuser->username = $row['username'];
				$oneuser->firstname = $row['first_name'];
				$oneuser->lastname = $row['last_name'];
				$oneuser->email = $row['email'];
				$oneuser->password = $row['password'];
				$oneuser->active = $row['active'];
				$oneuser->adminaccess = $row['admin_access'];
				$result[] = $oneuser;
				$dbresult->MoveNext();
			}

			$this->_users = $result;
		}

		return $this->_users;
	}


	/**
	 * Gets a list of all users in a given group
	 *
	 * @param mixed $groupid Group for the loaded users
	 * @return array An array of User objects
	 */
	function LoadUsersInGroup($groupid)
	{
		$gCms = CmsApp::get_instance();
		$db = $gCms->GetDb();
		$result = array();

		$query = "SELECT u.user_id, u.username, u.password, u.first_name, u.last_name, u.email, u.active, u.admin_access FROM ".CMS_DB_PREFIX."users u, `".CMS_DB_PREFIX."groups` g, ".CMS_DB_PREFIX."user_groups cg where cg.user_id = u.user_id and cg.group_id = g.group_id and g.group_id =? ORDER BY username";
		$dbresult = $db->Execute($query, array($groupid));

		while ($dbresult && $row = $dbresult->FetchRow()) {
			$oneuser = new User();
			$oneuser->id = $row['user_id'];
			$oneuser->username = $row['username'];
			$oneuser->firstname = $row['first_name'];
			$oneuser->lastname = $row['last_name'];
			$oneuser->email = $row['email'];
			$oneuser->password = $row['password'];
			$oneuser->active = $row['active'];
			$oneuser->adminaccess = $row['admin_access'];
			$result[] = $oneuser;
		}

		return $result;
	}

	/**
	 * Loads a user by username.
	 * Does not use a cache, so use sparingly.
	 *
	 * @param mixed $username Username to load
	 * @param mixed $password Password to check against
	 * @param mixed $activeonly Only load the user if they are active
	 * @param mixed $adminaccessonly Only load the user if they have admin access
	 * @return mixed If successful, the filled User object.  If it fails, it returns false.
	 * @since 0.6.1
	 */
	function LoadUserByUsername($username, $password = '', $activeonly = true, $adminaccessonly = false)
	{
		// note: does not use cache
		$result = null;
		$gCms = CmsApp::get_instance();
		$db = $gCms->GetDb();

		$params = array();
		$where = array();
		$joins = array();

		$query = "SELECT u.user_id FROM ".CMS_DB_PREFIX."users u";
		$where[] = 'username = ?';
		$params[] = $username;

		if ($password != '') {
			$where[] = 'password = ?';
			$params[] = md5(get_site_preference('sitemask','').$password);
		}

		if ($activeonly == true) {
			$joins[] = CMS_DB_PREFIX."user_groups ug ON u.user_id = ug.user_id";
			$where[] = "u.active = 1";
		}

		if ($adminaccessonly == true) {
			$where[] = "admin_access = 1";
		}

		if( !empty($joins) ) $query .= ' LEFT JOIN '.implode(' LEFT JOIN ',$joins);
		if( !empty($where) ) $query .= ' WHERE '.implode(' AND ',$where);

		$id = $db->GetOne($query,$params);
		if( $id ) $result = self::LoadUserByID($id);

		return $result;
	}

	/**
	 * Loads a user by user id.
	 *
	 * @param mixed $id User id to load
	 * @return mixed If successful, the filled User object.  If it fails, it returns false.
	 * @since 0.6.1
	 */
	function LoadUserByID($id)
	{
		$id = (int)$id;
		if( $id < 1 ) return false;
		if( isset($this->_saved_users[$id]) ) return $this->_saved_users[$id];

		$result = false;
		$gCms = CmsApp::get_instance();
		$db = $gCms->GetDb();

		$query = "SELECT username, password, active, first_name, last_name, admin_access, email FROM ".CMS_DB_PREFIX."users WHERE user_id = ?";
		$dbresult = $db->Execute($query, array($id));

		while ($dbresult && $row = $dbresult->FetchRow()) {
			$oneuser = new User();
			$oneuser->id = $id;
			$oneuser->username = $row['username'];
			$oneuser->password = $row['password'];
			$oneuser->firstname = $row['first_name'];
			$oneuser->lastname = $row['last_name'];
			$oneuser->email = $row['email'];
			$oneuser->adminaccess = $row['admin_access'];
			$oneuser->active = $row['active'];
			$result = $oneuser;
		}

		$this->_saved_users[$id] = $result;
		return $result;
	}

	/**
	 * Saves a new user to the database.
	 *
	 * @param mixed $user User object to save
	 * @return mixed The new user id.  If it fails, it returns -1.
	 * @since 0.6.1
	 */
	function InsertUser($user)
	{
		$result = -1;

		$gCms = CmsApp::get_instance();
		$db = $gCms->GetDb();

		// check for conflict in username
		$query = 'SELECT user_id FROM '.CMS_DB_PREFIX.'users WHERE username = ?';
		$tmp = $db->GetOne($query,array($user->username));
		if( $tmp ) return $result;

		$time = $db->DBTimeStamp(time());
		$new_user_id = $db->GenID(CMS_DB_PREFIX."users_seq");
		$query = "INSERT INTO ".CMS_DB_PREFIX."users (user_id, username, password, active, first_name, last_name, email, admin_access, create_date, modified_date) VALUES (?,?,?,?,?,?,?,?,".$time.",".$time.")";
		$dbresult = $db->Execute($query, array($new_user_id, $user->username, $user->password, $user->active, $user->firstname, $user->lastname, $user->email, 1)); //Force admin access on
		if ($dbresult !== false) $result = $new_user_id;

		return $result;
	}

	/**
	 * Updates an existing user in the database.
	 *
	 * @since 0.6.1
	 * @param mixed $user User object to save
	 * @return mixed If successful, true.  If it fails, false.
	 */
	function UpdateUser($user)
	{
		$result = false;
		$gCms = CmsApp::get_instance();
		$db = $gCms->GetDb();

		// check for username conflict
		$query = 'SELECT user_id FROM '.CMS_DB_PREFIX.'users WHERE username = ? and user_id != ?';
		$tmp = $db->GetOne($query,array($user->username,$user->id));
		if( $tmp ) return $result;

		$time = $db->DBTimeStamp(time());
		$query = "UPDATE ".CMS_DB_PREFIX."users SET username = ?, password = ?, active = ?, modified_date = ".$time.", first_name = ?, last_name = ?, email = ?, admin_access = ? WHERE user_id = ?";
		#$dbresult = $db->Execute($query, array($user->username, $user->password, $user->active, $user->firstname, $user->lastname, $user->email, $user->adminaccess, $user->id));
		$dbresult = $db->Execute($query, array($user->username, $user->password, $user->active, $user->firstname, $user->lastname, $user->email, 1, $user->id));
		if ($dbresult !== false) $result = true;

		return $result;
	}

	/**
	 * Deletes an existing user from the database.
	 *
	 * @since 0.6.1
	 * @param mixed $id Id of the user to delete
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function DeleteUserByID($id)
	{
 		if( $id <= 1 ) return false;
 		if( !check_permission(get_userid(),'Manage Users') ) return false;

		$result = false;
		$gCms = CmsApp::get_instance();
		$db = $gCms->GetDb();

		$query = "DELETE FROM ".CMS_DB_PREFIX."user_groups where user_id = ?";
		$db->Execute($query, array($id));

		$query = "DELETE FROM ".CMS_DB_PREFIX."additional_users where user_id = ?";
		$db->Execute($query, array($id));

		$query = "DELETE FROM ".CMS_DB_PREFIX."users where user_id = ?";
		$dbresult = $db->Execute($query, array($id));

		$query = "DELETE FROM ".CMS_DB_PREFIX."userprefs where user_id = ?";
		$dbresult = $db->Execute($query, array($id));

		if ($dbresult !== false) $result = true;
		return $result;
	}

	/**
	 * Show the number of pages the given user's id owns.
	 *
	 * @since 0.6.1
	 * @param mixed $id Id of the user to count
	 * @return mixed Number of pages they own.  0 if any problems.
	 */
	function CountPageOwnershipByID($id)
	{
		$result = 0;
		$gCms = CmsApp::get_instance();
		$db = $gCms->GetDb();

		$query = "SELECT count(*) AS count FROM ".CMS_DB_PREFIX."content WHERE owner_id = ?";
		$dbresult = $db->Execute($query, array($id));

		if ($dbresult && $dbresult->RecordCount() > 0) {
			$row = $dbresult->FetchRow();
			if (isset($row["count"])) $result = $row["count"];
		}

		return $result;
	}

    /**
     * Generate an array of admin userids to usernames, suitable for use in a dropdown.
     *
     * @return array
     * @since 2.2
     */
    public function GetList()
    {
        $allusers = $this->LoadUsers();
        if( !count($allusers) ) return;

        foreach( $allusers as $oneuser ) {
            $out[$oneuser->id] = $oneuser->username;
        }
        return $out;
    }

	/**
	 * Generate an HTML select element containing a user list
	 *
	 * @deprecated
	 * @param int $currentuserid
	 * @param string $name The HTML element name.
	 */
	function GenerateDropdown($currentuserid=null, $name='ownerid')
	{
        $result = null;
        $list = $this->GetList();
        if( count($list) ) {
			$result .= '<select name="'.$name.'">';
            foreach( $list as $uid => $username ) {
                $result .= '<option value="'.$uid.'"';
                if( $uid == $currentuserid ) $result .= ' selected="selected"';
				$result .= '>'.$username.'</option>';
            }
			$result .= '</select>';
        }
        return $result;
	}


	/**
	 * Tests $uid is a member of the group identified by $gid
	 *
	 * @param int $uid User ID to test
	 * @param int $gid Group ID to test
	 * @return true if test passes, false otherwise
	 */
	function UserInGroup($uid,$gid)
	{
		$groups = $this->GetMemberGroups($uid);
		if( in_array($gid,$groups) ) return TRUE;
		return FALSE;
	}

	/**
	 * Test if the specified user is a member of the admin group, or is the first user account
	 *
	 * @param int $uid
	 * @return bool
	 */
	public function IsSuperuser($uid)
	{
		if( $uid == 1 ) return TRUE;
		$groups = $this->GetMemberGroups();
		if( is_array($groups) && count($groups) ) {
			if( in_array($uid,$groups) ) return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get the ids of all groups to which the user belongs.
	 *
	 * @param int $uid
	 * @return array
	 */
	function GetMemberGroups($uid)
	{
		if( !is_array(self::$_user_groups) || !isset(self::$_user_groups[$uid]) ) {
			$db = CmsApp::get_instance()->GetDb();
			$query = 'SELECT group_id FROM '.CMS_DB_PREFIX.'user_groups WHERE user_id = ?';
			$col = $db->GetCol($query,array((int)$uid));
			if( !is_array(self::$_user_groups) ) self::$_user_groups = array();
			self::$_user_groups[$uid] = $col;
		}
		return self::$_user_groups[$uid];
	}

	/**
	 * Add the user to the specified group
	 *
	 * @param int $uid
	 * @param int $gid
	 */
	function AddMemberGroup($uid,$gid)
	{
		$uid = (int)$uid;
		$gid = (int)$gid;
		if( $uid < 1 || $gid < 1 ) return;

		$db = CmsApp::get_instance()->GetDb();
		$now = $db->DbTimeStamp(time());
		$query = 'INSERT INTO '.CMS_DB_PREFIX."user_groups
                  (group_id,user_id,create_date,modified_date)
                  VALUES (?,?,$now,$now)";
		$dbr = $db->Execute($query,array($gid,$uid));
		if( isset(self::$_user_groups[$uid]) ) unset(self::$_user_groups[$uid]);
	}

	/**
	 * Test if the user has the specified permission
	 *
	 * Given the users member groups, test if any of those groups have the specified permission.
	 *
	 * @param int $userid
	 * @param string $permname
	 * @return bool
	 */
	public function CheckPermission($userid,$permname)
	{
		if( $userid <= 0 ) return FALSE;
		$groups = $this->GetMemberGroups($userid);
		if( !is_array($groups) ) return FALSE;
		if( in_array(1,$groups) ) return TRUE; // member of admin group

		try {
			foreach( $groups as $gid ) {
				if( GroupOperations::get_instance()->CheckPermission($gid,$permname) ) return TRUE;
			}
		}
		catch( CmsException $e ) {
			// nothing here.
		}
		return FALSE;
	}
}

?>
