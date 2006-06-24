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
#$Id$

/**
 * Generic group class. This can be used for any logged in group or group related function.
 *
 * @since		0.9
 * @package		CMS
 */
class Group
{
	var $id;
	var $name;
	var $active;

	function Group()
	{
		$this->SetInitialValues();
	}

	function SetInitialValues()
	{
		$this->id = -1;
		$this->name = '';
		$this->active = false;
	}

	function Save()
	{
		$result = false;
		
		if ($this->id > -1)
		{
			$result = GroupOperations::UpdateGroup($this);
		}
		else
		{
			$newid = GroupOperations::InsertGroup($this);
			if ($newid > -1)
			{
				$this->id = $newid;
				$result = true;
			}

		}

		return $result;
	}

	function Delete()
	{
		$result = false;

		if ($this->id > -1)
		{
			$result = GroupOperations::DeleteGroupByID($this->id);
			if ($result)
			{
				$this->SetInitialValues();
			}
		}

		return $result;
	}
}

/**
 * Class for doing group related functions.  Maybe of the Group object functions are just wrappers around these.
 *
 * @since		0.6
 * @package		CMS
 */
class GroupOperations
{
	function LoadGroups()
	{
		global $gCms;
		$db = &$gCms->GetDb();

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

	function LoadGroupByID($id)
	{

		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

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

	function InsertGroup($group)
	{
		$result = -1; 

		global $gCms;
		$db = &$gCms->GetDb();

		$new_group_id = $db->GenID(cms_db_prefix()."groups_seq");
		$query = "INSERT INTO ".cms_db_prefix()."groups (group_id, group_name, active, create_date, modified_date) VALUES (?,?,?,?,?)";
		$dbresult = $db->Execute($query, array($new_group_id, $group->name, $group->active, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
		if ($dbresult !== false)
		{
			$result = $new_group_id;
		}

		return $result;
	}

	function UpdateGroup($group)
	{
		$result = false; 

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "UPDATE ".cms_db_prefix()."groups SET group_name = ?, active = ?, modified_date = ? WHERE group_id = ?";
		$dbresult = $db->Execute($query, array($group->name, $group->active, $db->DBTimeStamp(time()), $group->id));
		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	function DeleteGroupByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

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
