<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
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
	protected $aros_table = 'aros';
	protected $acos_table = 'acos';
	protected $acos_aros_table = 'acos_aros';
	protected $object_id_field = 'object_id';
	
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
	
	public function check_permission($module, $extra_attr, $permission, $object_id, $group = null, $user = null)
	{
		$groups = array();
		
		$local_acos_table = cms_db_prefix() . $this->acos_table;
		$local_aros_table = cms_db_prefix() . $this->aros_table;
		$local_acos_aros_table = cms_db_prefix() . $this->acos_aros_table;
		$local_object_id_field = $this->object_id_field;
		
		if ($group == null && $user != null)
		{
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
		
		$groupids = array();
		foreach ($groups as $group)
		{
			if ($group != null)
			{
				if ($group->name == 'Admin')
					return true;
				$groupids[] = $group->id;
			}
		}
		
		$groupids = implode(',', $groupids);
		if ($groupids != '')
		{
			$groupids = "AND r.{$local_object_id_field} in ({$groupids})";
		}
		
		$query = "SELECT cr.has_access
					FROM {$local_acos_table} c, {$local_acos_table} c2
					INNER JOIN {$local_acos_aros_table} cr ON cr.acos_id = c.id
					INNER JOIN {$local_aros_table} r ON r.id = cr.aros_id
					WHERE c2.lft BETWEEN c.lft AND c.rgt AND c2.object_id = ? AND c2.module = ? AND c2.extra_attr = ? {$groupids} AND r.type = 'Group'
					ORDER BY c.lft DESC LIMIT 1";

		$result = cms_db()->GetOne($query, array($object_id, $module, $extra_attr));
		if (!$result)
		{
			return false;
		}

		return $result;
	}
	
	static public function add_aro($object_id, $type = 'Group')
	{
		$max = cms_db()->GetOne("SELECT max(rgt) FROM cms_aros");
		
		$query = "INSERT INTO cms_aros (object_id, type, lft, rgt) VALUES (?, ?, ?, ?)";
		$result = cms_db()->Execute($query, array($object_id, $type, $max + 1, $max + 2));
		
		if ($result)
			return cms_db()->Insert_ID();

		return false;
	}
	
	static public function delete_aro($object_id, $type = 'Group')
	{
		$query = "DELETE FROM cms_aros INNER JOIN cms_acos_aros ON cms_acos_aros.aro_id = cms_aros.id WHERE cms_aros.object_id = ? AND cms_aros.type = ?";
		cms_db()->Execute($query, array($object_id, $type));
		
		$query = "DELETE FROM cms_aros WHERE object_id = ? AND type = ?";
		cms_db()->Execute($query, array($object_id, $type));
	}
}

# vim:ts=4 sw=4 noet
?>