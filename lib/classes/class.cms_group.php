<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
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
 * Represents a user group in the database.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsGroup extends CmsObjectRelationalMapping
{
	var $params = array('id' => -1, 'name' => '', 'active' => true);
	var $field_maps = array('group_name' => 'name');
	var $table = 'groups';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function setup()
	{
		$this->create_has_and_belongs_to_many_association('users', 'user', 'user_groups', 'user_id', 'group_id');
	}
	
	public function add_user($user)
	{
		if ($this->id > -1)
		{
			$date = cms_db()->DBTimeStamp(time());
			return cms_db()->Execute('INSERT INTO ' . cms_db_prefix() . "user_groups (user_id, group_id, create_date, modified_date) VALUES (?,?,{$date},{$date})", array($user->id, $this->id));
		}
		
		return false;
	}
	
	//Callback handlers
	public function before_save()
	{
		CmsEvents::send_event( 'Core', ($this->id == -1 ? 'AddGroupPre' : 'EditGroupPre'), array('group' => &$this));
	}
	
	public function after_save()
	{
		//Add the group to the aro table so we can do acls on it
		//Only happens on a new insert
		if ($this->create_date == $this->modified_date)
		{
			CmsAcl::add_aro($this->id, 'Group');
		}
		CmsEvents::send_event( 'Core', ($this->create_date == $this->modified_date ? 'AddGroupPost' : 'EditGroupPost'), array('group' => &$this));
	}
	
	public function before_delete()
	{
		CmsEvents::send_event('Core', 'DeleteGroupPre', array('group' => &$this));
	}
	
	public function after_delete()
	{
		CmsAcl::delete_aro($this->id, 'Group');
		CmsEvents::send_event('Core', 'DeleteGroupPost', array('group' => &$this));
	}
	
	public static function get_groups_for_dropdowm($add_everyone = false)
	{
		$result = array();
		
		if ($add_everyone)
		{
			$result[-1] = lang('Everyone');
		}
		
		$groups = cmsms()->group->find_all(array('order' => 'name ASC'));
		foreach ($groups as $group)
		{
			$result[$group->id] = $group->name;
		}
		
		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>