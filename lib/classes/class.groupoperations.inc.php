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
	/**
	 * Loads all the groups from the database and returns them
	 *
	 * @return array The list of groups
	 */
	function LoadGroups()
	{
		$db = cmsms()->GetDb();

		$result = array();

		$query = "SELECT group_id, group_name, active FROM ".cms_db_prefix()."groups ORDER BY group_id";
		$dbresult = $db->Execute($query);

		while ($dbresult && $row = $dbresult->FetchRow())
		{
			$onegroup = new Group();
			$onegroup->id = $row['group_id'];
			$onegroup->name = $row['group_name'];
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
	function & LoadGroupByID($id)
	{

		$result = false;

		$db = cmsms()->GetDb();

		$query = "SELECT group_id, group_name, active FROM ".cms_db_prefix()."groups WHERE group_id = ? ORDER BY group_id";
		$dbresult = $db->Execute($query, array($id));

		while ($dbresult && $row = $dbresult->FetchRow())
		{
			$onegroup = new Group();
			$onegroup->id = $row['group_id'];
			$onegroup->name = $row['group_name'];
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
	function InsertGroup($group)
	{
		$result = -1; 

		$db = cmsms()->GetDb();

		$query = 'SELECT group_id FROM '.cms_db_prefix().'groups WHERE group_name = ?';
		$tmp = $db->GetOne($query,array($group->name));
		if( $tmp )
		  {
		    return $result;
		  }

		$new_group_id = $db->GenID(cms_db_prefix()."groups_seq");
		$time = $db->DBTimeStamp(time());
		$query = "INSERT INTO ".cms_db_prefix()."groups (group_id, group_name, active, create_date, modified_date) VALUES (?,?,?,".$time.", ".$time.")";
		$dbresult = $db->Execute($query, array($new_group_id, $group->name, $group->active));
		if ($dbresult !== false)
		{
			$result = $new_group_id;
		}

		return $result;
	}

	/**
	 * Given a group object, update its attributes in the database.
	 *
	 * @param mixed $group The group to update
	 * @return boolean True if the update was successful, false if not
	 */
	function UpdateGroup($group)
	{
		$result = false; 

		$db = cmsms()->GetDb();

		$query = 'SELECT group_id FROM '.cms_db_prefix().'groups WHERE group_name = ? AND group_id != ?';
		$tmp = $db->GetOne($query,array($group->name,$group->id));
		if( $tmp )
		  {
		    return $result;
		  }

		$time = $db->DBTimeStamp(time());
		$query = "UPDATE ".cms_db_prefix()."groups SET group_name = ?, active = ?, modified_date = ".$time." WHERE group_id = ?";
		$dbresult = $db->Execute($query, array($group->name, $group->active, $group->id));
		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	/**
	 * Given a group id, delete it from the database along with all its associations.
	 *
	 * @param integer $id The group's id to delete
	 * @return boolean True if the delete was successful. False if not.
	 */
	function DeleteGroupByID($id)
	{
		$result = false;

		$db = cmsms()->GetDb();

		$query = 'DELETE FROM '.cms_db_prefix().'user_groups where group_id = ?';
		$dbresult = $db->Execute($query, array($id));

		$query = "DELETE FROM ".cms_db_prefix()."group_perms where group_id = ?";
		$dbresult = $db->Execute($query, array($id));

		$query = "DELETE FROM ".cms_db_prefix()."groups where group_id = ?";
		$dbresult = $db->Execute($query, array($id));

		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>