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

class CmsModulePermissionExtension extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}

	/**
	* Create's a new permission for use by the module.
	*
	* @param string Name of the permission to create
	* @param string Description of the permission
	*/
	public function create($permission_name, $permission_text)
	{
		$db = cms_db();

		try
		{
			$query = "SELECT permission_id FROM {permissions} WHERE permission_name = ?";
			$count = $db->GetOne($query, array($permission_name));

			if (intval($count) == 0)
			{
				$new_id = $db->GenID(cms_db_prefix()."permissions_seq");
				$time = $db->DBTimeStamp(time());
				$query = "INSERT INTO {permissions} (id, permission_name, permission_text, create_date, modified_date) VALUES (?,?,?,".$time.",".$time.")";
				$db->Execute($query, array($new_id, $permission_name, $permission_text));
			}
		}
		catch (Exception $e)
		{
			//trigger_error("Could not run CreatePermission", E_WARNING);
		}
	}

	/**
	* Checks a permission against the currently logged in user.
	*
	* @param string The name of the permission to check against the current user
	*/
	public function check($permission_name)
	{
		$userid = get_userid();
		return check_permission($userid, $permission_name);
	}

	/**
	* Removes a permission from the system.  If recreated, the
	* permission would have to be set to all groups again.
	*
	* @param string The name of the permission to remove
	*/
	public function remove($permission_name)
	{
		$db = cms_db();

		$query = "SELECT id FROM {permissions} WHERE permission_name = ?";
		$row = $db->GetRow($query, array($permission_name));

		if ($row)
		{
			$id = $row["id"];

			$query = "DELETE FROM {group_perms} WHERE permission_id = ?";
			$db->Execute($query, array($id));

			$query = "DELETE FROM {permissions} WHERE id = ?";
			$db->Execute($query, array($id));
		}
	}
}

# vim:ts=4 sw=4 noet
?>