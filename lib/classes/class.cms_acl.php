<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
		return self::check_permission('Core', $extra_attr, $permission, -1, null, $user);
	}
	
	public static function check_permission($module, $extra_attr, $permission, $object_id = -1, $group = null, $user = null)
	{
		$groups = array();

		$userid = -1;
		if (!is_object($user))
			$userid = intval($user);
		else if ($user != null)
			$userid = $user->id;
		
		if ($group == null && $user != null)
		{
			//Return true if we're the #1 account
			if ($userid == 1)
				return true;

			if (is_int(intval($user)))
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
			$groupids = "AND gp.group_id in ({$groupids})";
		}
		
		$cms_db_prefix = cms_db_prefix();
		$defn = self::get_permission_definition($module, $extra_attr, $permission);
		
		$result = false;
		
		if ($defn['hierarchical'])
		{
			$query = "SELECT max(gp.has_access)
						FROM {$cms_db_prefix}{$defn['link_table']} c
							INNER JOIN {$cms_db_prefix}{$defn['link_table']} c2 ON c2.lft BETWEEN c.lft AND c.rgt
							LEFT OUTER JOIN {$cms_db_prefix}group_permissions gp ON gp.object_id = c.id 
							INNER JOIN {$cms_db_prefix}permission_defns pd ON pd.id = gp.permission_defn_id 
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
						FROM {$cms_db_prefix}group_permissions gp 
						INNER JOIN {$cms_db_prefix}permission_defns pd ON pd.id = gp.permission_defn_id 
						WHERE gp.object_id = ? AND pd.id = ? {$groupids}";
					
			$result = cms_db()->GetOne($query, array($object_id, $defn['id']));
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
	
	static public function get_permissions($module, $extra_attr, $permission, $object_id = -1, $replace_text = false)
	{
		$result = array();
		$cms_db_prefix = cms_db_prefix();
		$defn = self::get_permission_definition($module, $extra_attr, $permission);

		if ($defn['hierarchical'])
		{
			$result = cms_db()->GetAll("SELECT gp.*, g.group_name as group_name
						FROM {$cms_db_prefix}{$defn['link_table']} c
							INNER JOIN {$cms_db_prefix}{$defn['link_table']} c2 ON c2.lft BETWEEN c.lft AND c.rgt
							LEFT OUTER JOIN {$cms_db_prefix}group_permissions gp ON gp.object_id = c.id 
							LEFT OUTER JOIN {$cms_db_prefix}groups g ON g.id = gp.group_id
						WHERE c2.id = ? 
							AND gp.permission_defn_id = ?
						ORDER BY c.lft ASC", array($object_id, $defn['id']));
		}
		else
		{
			$result = cms_db()->GetAll("SELECT gp.*, g.group_name as group_name
						FROM {$cms_db_prefix}group_permissions gp
							LEFT OUTER JOIN {$cms_db_prefix}groups g ON g.id = gp.group_id
						WHERE gp.permission_defn_id = ?", array($defn['id']));
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
		$query = "SELECT pd.*
					FROM ".cms_db_prefix()."permission_defns pd
                    LEFT JOIN ".cms_db_prefix()."group_permissions gp 
                           ON gp.permission_defn_id = pd.id";
		$query .= ' WHERE ' . implode(' AND ',$where);
		return cms_db()->GetAll($query,$parms);
	}
	
	static public function get_permission_definition_by_id($id)
	{
		return cms_db()->GetRow('SELECT * FROM ' . cms_db_prefix() . 'permission_defns WHERE id = ?', 
								array($id));
	}

	static public function get_permission_definition($module, $extra_attr, $name)
	{
		return cms_db()->GetRow('SELECT * FROM ' . cms_db_prefix() . 'permission_defns WHERE module = ? AND extra_attr = ? AND name = ?', array($module, $extra_attr, $name));
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

		$query = 'SELECT * FROM '.cms_db_prefix().'permission_defns';
		if( count($where) )
		{
			$query .= ' WHERE '.implode(' AND ',$where);
		}

		return cms_db()->GetAll($query,$parms);
	}
	
	static public function remove_permission($module, $extra_attr, $permission, $group_id, $object_id = -1)
	{
		$cms_db_prefix = cms_db_prefix();
		$defn = self::get_permission_definition($module, $extra_attr, $permission);
		
		if (!$defn) return false;

		$id = cms_db()->GetOne("SELECT id FROM {$cms_db_prefix}group_permissions WHERE permission_defn_id = ? AND group_id = ? AND object_id = ?", array($defn['id'], $group_id, $object_id));
		
		if (!$id) return false;

		return cms_db()->Execute("DELETE FROM {$cms_db_prefix}group_permissions 
                                   WHERE permission_defn_id = ? AND group_id = ? AND object_id = ?",
								 array($defn['id'],$group_id,$object_id));
	}

	static public function set_permission($module, $extra_attr, $permission, $object_id = -1, $group_id = -1, $allowed = false)
	{
		$cms_db_prefix = cms_db_prefix();
		$defn = self::get_permission_definition($module, $extra_attr, $permission);
		
		if ($defn)
		{
			$id = cms_db()->GetOne("SELECT id FROM {$cms_db_prefix}group_permissions WHERE permission_defn_id = ? AND group_id = ? AND object_id = ?", array($defn['id'], $group_id, $object_id));
			if ($id)
			{
				return cms_db()->Execute("UPDATE {$cms_db_prefix}group_permissions SET has_access = ? WHERE id = ?", array($allowed, $id));
			}
			else
			{
				return cms_db()->Execute("INSERT INTO {$cms_db_prefix}group_permissions (permission_defn_id, group_id, object_id, has_access) VALUES (?, ?, ?, ?)", array($defn['id'], $group_id, $object_id, $allowed));
			}
		}
		
		return false;
	}
	
	/**
	 * TODO: Cache me!
	 */
	static public function create_permission_definition($module, $extra_attr, $name, $hierarchical = false, $table = '')
	{
		$result = null;
		
		$row = cms_db()->GetOne('SELECT * FROM ' . cms_db_prefix() . 'permission_defns WHERE module = ? AND extra_attr = ? AND name = ?', array($module, $extra_attr, $name));
		
		if (!$row)
		{
			$result = cms_db()->Execute('INSERT INTO ' . cms_db_prefix() . 'permission_defns (module, extra_attr, name, hierarchical, link_table) VALUES (?, ?, ?, ?, ?)', array($module, $extra_attr, $name, $hierarchical, $table));
		}
		
		if ($result)
			return cms_db()->Insert_ID();

		return false;
	}
	
	/**
	 * TODO: Clear cache
	 */
	static public function delete_permission_definition($module, $extra_attr, $name)
	{
		$cms_db_prefix = cms_db_prefix();
		$row = cms_db()->GetOne('SELECT id FROM ' . cms_db_prefix() . 'permission_defns WHERE module = ? AND extra_attr = ? AND name = ?', array($module, $extra_attr, $name));
		
		if ($row)
		{
			$query = "DELETE FROM " . cms_db_prefix() . "permission_defns WHERE id = ?";
			$result = cms_db()->Execute($query, array($row['id']));
		
			if ($result)
			{
				cms_db()->Execute("DELETE FROM {$cms_db_prefix}group_permissions WHERE permission_defn_id = ?", array($row['id']));
				return true;
			}
		}
			
		return false;
	}
}

# vim:ts=4 sw=4 noet
?>