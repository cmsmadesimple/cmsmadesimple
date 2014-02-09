<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
#$Id$

/**
 * Group related functions
 * @package CMS 
 * @license GPL
 */

/**
 * Include group class definition
 */
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.group.inc.php');

/**
 * Class for doing group related functions.  Maybe of the Group object functions are just wrappers around these.
 *
 * @since 0.6
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class GroupOperations
{
	protected function __construct() {}

	private static $_instance;
	private $_perm_cache;

	public static function &get_instance()
	{
		if( !is_object(self::$_instance) ) self::$_instance = new GroupOperations();
		return self::$_instance;
	}

	/**
	 * Loads all the groups from the database and returns them
	 *
	 * @return array The list of groups
	 */
	public function LoadGroups()
	{
		$db = cmsms()->GetDb();
		$result = array();
		$query = "SELECT group_id, group_name, group_desc, active FROM ".cms_db_prefix()."groups ORDER BY group_id";
		$dbresult = $db->Execute($query);
		while ($dbresult && $row = $dbresult->FetchRow()) {
			$onegroup = new Group();
			$onegroup->id = $row['group_id'];
			$onegroup->name = $row['group_name'];
			$onegroup->description = $row['group_desc'];
			$onegroup->active = $row['active'];
			$result[] = $onegroup;
		}

		return $result;
	}

	/**
	 * Load a group from the database by its id
	 *
	 * @param integer $id The id of the group to load
	 * @return mixed The group if found. If it's not found, then false
	 */
	public function &LoadGroupByID($id)
	{
		$result = false;
		$db = cmsms()->GetDb();

		$query = "SELECT group_id, group_name, group_desc, active FROM ".cms_db_prefix()."groups WHERE group_id = ? ORDER BY group_id";
		$dbresult = $db->Execute($query, array($id));

		while ($dbresult && $row = $dbresult->FetchRow()) {
			$onegroup = new Group();
			$onegroup->id = $row['group_id'];
			$onegroup->name = $row['group_name'];
			$onegroup->description = $row['group_desc'];
			$onegroup->active = $row['active'];
			$result = $onegroup;
		}

		return $result;
	}

	/**
	 * Given a group object, inserts it into the database.
	 *
	 * @param mixed $group The group object to save to the database
	 * @return integer The id of the newly created group. If none is created, -1
	 */
	public function InsertGroup($group)
	{
		$result = -1; 
		$db = cmsms()->GetDb();

		$query = 'SELECT group_id FROM '.cms_db_prefix().'groups WHERE group_name = ?';
		$tmp = $db->GetOne($query,array($group->name));
		if( $tmp ) return $result;

		$new_group_id = $db->GenID(cms_db_prefix()."groups_seq");
		$time = $db->DBTimeStamp(time());
		$query = "INSERT INTO ".cms_db_prefix()."groups (group_id, group_name, group_desc, active, create_date, modified_date) 
                  VALUES (?,?,?,?,".$time.", ".$time.")";
		$dbresult = $db->Execute($query, array($new_group_id, $group->name, $group->description, $group->active));
		if ($dbresult !== false) $result = $new_group_id;

		return $result;
	}

	/**
	 * Given a group object, update its attributes in the database.
	 *
	 * @param mixed $group The group to update
	 * @return boolean True if the update was successful, false if not
	 */
	public function UpdateGroup($group)
	{
		$result = false; 
		$db = cmsms()->GetDb();

		$query = 'SELECT group_id FROM '.cms_db_prefix().'groups WHERE group_name = ? AND group_id != ?';
		$tmp = $db->GetOne($query,array($group->name,$group->id));
		if( $tmp ) return $result;

		$time = $db->DBTimeStamp(time());
		$query = "UPDATE ".cms_db_prefix()."groups SET group_name = ?, group_desc = ?, active = ?, modified_date = ".$time." WHERE group_id = ?";
		$dbresult = $db->Execute($query, array($group->name, $group->description, $group->active, $group->id));
		if ($dbresult !== false) $result = true;

		return $result;
	}

	/**
	 * Given a group id, delete it from the database along with all its associations.
	 *
	 * @param integer $id The group's id to delete
	 * @return boolean True if the delete was successful. False if not.
	 */
	public function DeleteGroupByID($id)
	{
		$result = false;
		$db = cmsms()->GetDb();

		$query = 'DELETE FROM '.cms_db_prefix().'user_groups where group_id = ?';
		$dbresult = $db->Execute($query, array($id));

		$query = "DELETE FROM ".cms_db_prefix()."group_perms where group_id = ?";
		$dbresult = $db->Execute($query, array($id));

		$query = "DELETE FROM ".cms_db_prefix()."groups where group_id = ?";
		$dbresult = $db->Execute($query, array($id));

		if ($dbresult !== false) $result = true;

		unset($this->_perm_cache);
		return $result;
	}

	public function CheckPermission($groupid,$perm)
	{
		$permid = CmsPermission::get_perm_id($perm);
		if( $permid < 1 ) return FALSE;
		if( $groupid == 1 ) return TRUE;

		if( !isset($this->_perm_cache) || !is_array($this->_perm_cache) || !isset($this->_perm_cache[$groupid]) ) {
			$db = cmsms()->GetDb();
			$query = 'SELECT permission_id FROM '.cms_db_prefix().'group_perms WHERE group_id = ?';
			$dbr = $db->GetCol($query,array((int)$groupid));
			if( is_array($dbr) && count($dbr) ) $this->_perm_cache[$groupid] = $dbr;
		}

		return isset($this->_perm_cache[$groupid]) && in_array($permid,$this->_perm_cache[$groupid]);
	}

	public function GrantPermission($groupid,$perm)
	{
		$permid = CmsPermission::get_perm_id($perm);
		if( $permid < 1 ) return;
		if( $groupid <= 1 ) return;

		$db = cmsms()->GetDb();

		$new_id = $db->GenId(cms_db_prefix().'group_perms_seq');
		if( !$new_id ) return;

		$now = $db->DbTimeStamp(time());
		$query = 'INSERT INTO '.cms_db_prefix()."group_perms (group_perm_id,group_id,permission_id,create_date,modified_date)
                  VALUES (?,?,?,$now,$now)";
 		$dbr = $db->Execute($query,array($new_id,$groupid,$permid));
		unset($this->_perm_cache);
	}

	public function RemovePermission($groupid,$perm)
	{
		$permid = CmsPermission::get_perm_id($perm);
		if( $permid < 1 ) return;
		if( $groupid <= 1 ) return;

		$query = 'DELETE FROM '.cms_db_prefix().'group_perms WHERE group_id = ? AND perm_id = ?';
		$dbr = $db->Execute($query,array($groupid,$permid));
		unset($this->_perm_cache);
	}
}

# vim:ts=4 sw=4 noet
?>