<?php
/**
 * User list, page ownership count, and permission checks for UserOperations.
 *
 * @package CMS
 * @license GPL
 * @internal
 */
trait UserOperationsListPermissionsTrait
{
	/**
	 * Show the number of pages the given user's id owns.
	 *
	 * @param mixed $id Id of the user to count
	 *
	 * @return mixed Number of pages they own.  0 if any problems.
	 * @throws \Exception
	 * @since 0.6.1
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
	 *
	 * @return true if test passes, false otherwise
	 * @throws \Exception
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
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function IsSuperuser($uid)
	{
		if( $uid == 1 ) return TRUE;
		$groups = $this->GetMemberGroups($uid);
		if( is_array($groups) && count($groups) ) {
			if( in_array($uid,$groups) ) return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get the ids of all groups to which the user belongs.
	 *
	 * @param int $uid
	 *
	 * @return array
	 * @throws \Exception
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
	 *
	 * @throws \Exception
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
	 * @param int    $userid
	 * @param string $permname
	 *
	 * @return bool
	 * @throws \Exception
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
