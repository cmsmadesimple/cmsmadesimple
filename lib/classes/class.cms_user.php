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

/**
 * Generic user class.  This can be used for any logged in user or user related function.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsUser extends CmsObjectRelationalMapping
{
	var $params = array('id' => -1, 'username' => '', 'password' => '', 'firstname' => '', 'lastname' => '', 'email' => '', 'active' => false);
	var $field_maps = array('first_name' => 'firstname', 'last_name' => 'lastname', 'admin_access' => 'adminaccess', 'username' => 'name');
	var $table = 'users';
	
	var $attr_module = 'Core';
	var $attr_extra = 'User';
	
	public function __construct()
	{
		parent::__construct();
		$this->assign_acts_as('Attributed');
	}

	public function validate()
	{
		$this->validate_not_blank('name', lang('nofieldgiven',array(lang('username'))));
		
		// Username validation
		if ($this->name != '')
		{
			// Make sure the name is unique
			$result = $this->find_by_name($this->name);
			if ($result)
			{
				if ($result->id != $this->id)
				{
					$this->add_validation_error(lang('The username is already in use'));
				}
			}
			
			// Make sure the name has no illegal characters
			if ( !preg_match("/^[a-zA-Z0-9\.]+$/", $this->name) ) 
			{
				$this->add_validation_error(lang('illegalcharacters', array(lang('username'))));
			} 
		}
		
		//Make sure the open id is unique
		if ($this->openid != '')
		{
			$result = $this->find_by_openid($this->openid);
			if ($result)
			{
				if ($result->id != $this->id)
				{
					$this->add_validation_error(lang('The openid address is already in use'));
				}
			}
		}
	}	

	function setup($first_time = false)
	{
		$this->create_has_many_association('bookmarks', 'bookmark', 'user_id');
		$this->create_has_and_belongs_to_many_association('groups', 'group', 'user_groups', 'group_id', 'user_id');
	}

	/**
	 * Encrypts and sets password for the User
	 *
	 * @param string The password to encrypt and set for the user
	 *
	 * @since 0.6.1
	 */
	public function set_password($password)
	{
		//Set params directly so that we don't get caught in a loop
		$this->params['password'] = md5($password);
	}
	
	/**
	 * @deprecated Deprecated.  Use set_password instead.
	 **/
	function SetPassword($password)
	{
		$this->set_password($password);
	}
	
	//Callback handlers
	protected function before_save()
	{
		CmsEvents::send_event( 'Core', ($this->id == -1 ? 'AddUserPre' : 'EditUserPre'), array('user' => &$this));
	}
	
	protected function after_save()
	{
		CmsEvents::send_event( 'Core', ($this->create_date == $this->modified_date ? 'AddUserPost' : 'EditUserPost'), array('user' => &$this));
	}
	
	protected function before_delete()
	{
		CmsEvents::send_event('Core', 'DeleteUserPre', array('user' => &$this));
	}
	
	protected function after_delete()
	{
		CmsEvents::send_event('Core', 'DeleteUserPost', array('user' => &$this));
	}
	
	public function is_anonymous()
	{
		return false;
	}
	
	public function full_name()
	{
		return $this->firstname . ' ' . $this->lastname;
	}
}

/**
 * @deprecated Deprecated.  Use CmsUser instead.
 **/
class User extends CmsUser
{
	public function __construct()
	{
		parent::__construct();
	}
}

# vim:ts=4 sw=4 noet
?>