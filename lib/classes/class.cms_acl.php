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
	
	static public function check_permission($module, $extra_attr, $permission, $object_id, $group = null, $user = null)
	{
		$groups = array();
		
		if ($group == null && $user != null)
		{
			$groups = $user->groups->children;
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
			if ($group->name == 'Admin')
				return true;
			$groupids[] = $group->id;
		}
		$groupids = implode(',', $groupids);
		
		$query = "select cr.has_access FROM cms_acos c, cms_acos c2 INNER JOIN cms_acos_aros cr ON cr.acos_id = c.id INNER JOIN cms_aros r ON r.id = cr.aros_id  WHERE c2.lft BETWEEN c.lft AND c.rght AND c2.object_id = ? AND c2.module = ? AND c2.extra_attr = ? AND r.object_id in ({$groupids}) AND r.type = 'Group'  ORDER BY c.lft DESC LIMIT 1";

		return cms_db()->GetOne($query, array($object_id, $module, $extra_attr));
	}
}

# vim:ts=4 sw=4 noet
?>