<?php
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
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
 * Class for doing group related functions.  Maybe of the Group object functions are just wrappers around these.
 *
 * @since		0.6
 * @package		CMS
 */

class CmsGroupOperations extends CmsObject
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
	 * @return CmsGroupOperations The instance of the singleton object.
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsGroupOperations();
		}
		return self::$instance;
	}
	
	static public function load_groups()
	{
		return cmsms()->group->find_all(array('order' => 'group_name ASC'));
	}
	
	function LoadGroups()
	{
		return self::load_groups();
	}

	static public function load_group_by_id($id)
	{
		return cmsms()->group->find_by_id($id);
	}
	
	function LoadGroupByID($id)
	{
		return self::load_group_by_id($id);
	}

	function InsertGroup($group)
	{
		$result = -1; 

		global $gCms;
		$db = &$gCms->GetDb();

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

	function UpdateGroup($group)
	{
		$result = false; 

		global $gCms;
		$db = &$gCms->GetDb();

		$time = $db->DBTimeStamp(time());
		$query = "UPDATE ".cms_db_prefix()."groups SET group_name = ?, active = ?, modified_date = ".$time." WHERE group_id = ?";
		$dbresult = $db->Execute($query, array($group->name, $group->active, $group->id));
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

?>