<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#Rewritten by Jeff Bosch (jeff@ajprogramming.com)
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

class CmsAcl extends CmsObject
{	
	static private $instance = NULL;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Returns an instance of the CmsAcl singleton.
	 *
	 * @return CmsAcl The singleton CmsAcl instance
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsAcl();
		}
		return self::$instance;
	}
	
	public static function check_core_permission($permission, $user, $extra_attr = '')
	{
		return self::check_permission($permission, $user, 'Core', $extra_attr);
	}
	
	public static function check_permission($permission, $user = null, $module = 'Core', $extra_attr = '', $object_id = -1, $group = null)
	{
		$groups = array();

		$userid = -1;
		if ($user == null)
		{
			$user = CmsLogin::get_current_user();
		}
		if (!is_object($user))
		{
			$userid = intval($user);
		}
		else if ($user != null)
		{
			$userid = $user->id;
		}
		
		if ($group == null && $user != null)
		{
			//Return true if we're the #1 account
			if ($userid == 1)
				return true;
			//Sil was here below was $user, now $userid
			if (is_int(intval($userid)))
			{
				$user = cms_orm('CmsUser')->find_by_id($userid);
			}

			$groups = $user->groups;
		}
		else if ($group != null && $user == null)
		{
			$groups[] = $group;
		}
		else
		{
			return false;
		}
		
		$groupids = array('-1');
		foreach ($groups as $group)
		{
			if ($group != null)
			{
				//Return true if we're in the Admin group
				if ($group->id == 2) //1 is Anonymous
					return true;

				$groupids[] = $group->id;
			}
		}
		
		$groupids = implode(',', $groupids);

		if ($groupids != '')
		{
			$groupids = "AND gp.group_id in (".$groupids.")";
		}

		$defn = self::get_permission_definition($permission, $module, $extra_attr);
		
		$result = false;
		//Sil was here below
		if (isset($defn['hierarchical']) && $defn['hierarchical'])
		{
			$query = "SELECT max(gp.has_access)
						FROM {".$defn['link_table']."} c
							INNER JOIN {".$defn['link_table']."} c2 ON c2.lft BETWEEN c.lft AND c.rgt
							LEFT OUTER JOIN {group_perms} gp ON gp.object_id = c.id 
							INNER JOIN {permissions} pd ON pd.permission_id = gp.permission_id 
						WHERE c2.id = ? 
							AND pd.module = ? 
							AND pd.extra_attr = ? 
							AND pd.name = ? 
							{$groupids}
						GROUP BY gp.object_id 
						ORDER BY c.lft DESC, gp.group_id DESC";
			
			$result = cms_db()->GetOne($query, array($object_id, $module, $extra_attr, $permission));
		}
		else
		{		
			$query = "SELECT gp.has_access
						FROM {group_perms} gp 
						INNER JOIN {permissions} pd ON pd.permission_id = gp.permission_id 
						WHERE pd.permission_id = ? ".$groupids;
			$parms = array($defn['id']);
			if ( $object_id > -1)
			{
				$query .= " gp.object_id = ?";
				$parms[] = $object_id;
			}
			//Sil was here below
			if (isset($defn['id'])) 
			{
			  $result = cms_db()->GetOne($query, $parms);
			}
		}

		//Make sure we get a real boolean...  php is so weird sometimes
		if (!$result)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	static public function get_permissions($permission, $module, $extra_attr, $object_id = -1, $replace_text = false)
	{
		$result = array();
		$cms_db_prefix = cms_db_prefix();
		$defn = self::get_permission_definition($permission, $module, $extra_attr);

		if ($defn['hierarchical'])
		{
			$result = cms_db()->GetAll("SELECT gp.*, gp.group_perm_id as id, g.group_name as group_name
						FROM {".$defn['link_table']."} c
							INNER JOIN {".$defn['link_table']."} c2 ON c2.lft BETWEEN c.lft AND c.rgt
							LEFT OUTER JOIN {group_perms} gp ON gp.object_id = c.id 
							LEFT OUTER JOIN {groups} g ON g.id = gp.group_id
						WHERE c2.id = ? 
							AND gp.permission_id = ?
						ORDER BY c.lft ASC", array($object_id, $defn['id']));
		}
		else
		{
			$result = cms_db()->GetAll("SELECT gp.*, gp.group_perm_id as id, g.group_name as group_name
						FROM {group_perms} gp
							LEFT OUTER JOIN {groups} g ON g.id = gp.group_id
						WHERE gp.permission_id = ?", array($defn['id']));
		}
		
		if ($replace_text)
		{
			foreach ($result as &$onerow)
			{
				$onerow['group_name'] = $onerow['group_id'] == -1 ? lang('Everyone') : $onerow['group_name'];
				$onerow['has_access'] = $onerow['has_access'] ? lang('true') : lang('false');
			}
		}
		
		return $result;
	}

	static public function get_group_permissions($group,$module = '',$extra_attr = '')
	{
		$gid = is_object($group) ? $group->id : $group;

		$parms = array();
		$where = array();

		$where[] = 'gp.group_id = ?';
		$parms[] = $gid;

		if( !empty($module) )
		{
			$where[] = 'module = ?';
			$parms[] = $module;
		}
		if( !empty($extra_attr) )
		{
			$where[] = 'extra_attr = ?';
			$parms[] = $extra_attr;
		}

		$result = false;
		$query = "SELECT pd.*, pd.permission_id as id, gp.has_access
					FROM {permissions} pd
                    LEFT JOIN {group_perms} gp 
                           ON gp.permission_id = pd.permission_id";
		$query .= ' WHERE ' . implode(' AND ',$where);
		return cms_db()->GetAll($query,$parms);
	}
	
	static public function get_permission_definition_by_id($id)
	{
		return cms_db()->GetRow('SELECT *, permission_id as id FROM {permissions} WHERE permission_id = ?', 
								array($id));
	}

	static public function get_permission_definition($name, $module, $extra_attr)
	{
		return cms_db()->GetRow('SELECT *, permission_id as id FROM {permissions} WHERE module = ? AND extra_attr = ? AND permission_name = ?', array($module, $extra_attr, $name));
	}
	
	static public function get_permission_definitions($module = '', $extra_attr = '', $show_hierarchical = true)
	{
		$where = array();
		$parms = array();
		if( !empty($module) )
		{
			$where[] = 'module = ?';
			$parms[] = $module;
		}
		if( !empty($extra_attr) )
		{
			$where[] = 'extra_attr = ?';
			$parms[] = $extra_attr;
		}
		if (!$show_hierarchical)
		{
			$where[] = 'hierarchical = 0';
		}

		$query = 'SELECT *, permission_id as id FROM {permissions}';
		if( count($where) )
		{
			$query .= ' WHERE '.implode(' AND ',$where);
		}

		return cms_db()->GetAll($query,$parms);
	}
	
	static public function remove_permission($name, $module, $extra_attr, $group_id, $object_id = 0)
	{
		$defn = self::get_permission_definition($name, $module, $extra_attr);		
		if (!$defn) return false;

		return remove_permission_by_id($defn['id'],$group_id,$object_id);
	}
	static public function remove_permission_by_id($permission_id, $group_id, $object_id = 0)
	{

		$id = cms_db()->GetOne("SELECT group_perm_id as id FROM {group_perms} WHERE permission_id = ? AND group_id = ? AND object_id = ?", array($permission_id, $group_id, $object_id));

		if (!$id) return false;

		return cms_db()->Execute("DELETE FROM {group_perms} WHERE group_perm_id = ?", array($id));
	}


	static public function set_permission($name, $module, $extra_attr, $object_id = 0, $group_id = -1, $allowed = true)
	{
		$defn = self::get_permission_definition($name, $module, $extra_attr);
		
		if ($defn)
		{
			set_permission_by_id($defn['id'],$group_id,$object_id, $allowed);
		}
		
		return false;
	}
	
	static public function set_permission_by_id($permission_id = -1, $group_id = -1, $object_id = 0, $allowed = true)
	{		
		if ( $permission_id > -1 && $group_id > -1)
		{
			$id = cms_db()->GetOne("SELECT group_perm_id as id FROM {group_perms} WHERE permission_id = ? AND group_id = ? AND object_id = ?", array($permission_id, $group_id, $object_id));
			if ($id)
			{
				return cms_db()->Execute("UPDATE {group_perms} SET has_access = ?, modified_date = NOW() WHERE group_perm_id = ?", array($allowed, $id));
			}
			else
			{
				return cms_db()->Execute("INSERT INTO {group_perms} (permission_id, group_id, object_id, has_access, create_date, modified_date) VALUES (?, ?, ?, ?, NOW(), NOW())", array($permission_id, $group_id, $object_id, $allowed));
			}
		}
		
		return false;
	}

	/**
	 * TODO: Cache me!
	 */
	static public function create_permission_definition($name, $module, $extra_attr = '', $hierarchical = false, $table = '')
	{
		$result = null;
		
		$row = cms_db()->GetOne('SELECT * FROM {permissions} WHERE module = ? AND extra_attr = ? AND permission_name = ?', array($module, $extra_attr, $name));
		
		if (!$row)
		{
			$result = cms_db()->Execute('INSERT INTO {permissions} (module, extra_attr, permission_name, hierarchical, link_table) VALUES (?, ?, ?, ?, ?)', array($module, $extra_attr, $name, $hierarchical, $table));
		}
		
		if ($result)
			return cms_db()->Insert_ID();

		return false;
	}
	
	/**
	 * TODO: Clear cache
	 */
	static public function delete_permission_definition($name, $module = 'Core', $extra_attr = '')
	{
		$cms_db_prefix = cms_db_prefix();
		$row = cms_db()->GetOne('SELECT permission_id as id FROM {permissions} WHERE module = ? AND extra_attr = ? AND permission_name = ?', array($module, $extra_attr, $name));
		
		if ($row)
		{
			$query = "DELETE FROM {permissions} WHERE permission_id = ?";
			$result = cms_db()->Execute($query, array($row['id']));
		
			if ($result)
			{
				cms_db()->Execute("DELETE FROM {group_perms} WHERE permission_id = ?", array($row['id']));
				return true;
			}
		}
			
		return false;
	}
}

# vim:ts=4 sw=4 noet
?>
