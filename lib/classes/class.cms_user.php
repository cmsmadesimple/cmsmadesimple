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
 * Generic user class.  This can be used for any logged in user or user related function.
 *
 * @since 0.6.1
 * @package CMS
 */

class CmsUser extends CmsObjectRelationalMapping
{
	var $params = array('id' => -1, 'username' => '', 'password' => '', 'firstname' => '', 'lastname' => '', 'email' => '', 'active' => false);
	var $field_maps = array('user_id' => 'id', 'first_name' => 'firstname', 'last_name' => 'lastname', 'admin_access' => 'adminaccess');
	var $table = 'users';
	var $sequence = 'users_seq';

	/**
	 * Encrypts and sets password for the User
	 *
	 * @param string The password to encrypt and set for the user
	 *
	 * @since 0.6.1
	 */
	function set_password($password)
	{
		$this->params['password'] = md5($password);
	}
	
	/**
	 * @deprecated Deprecated.  Use set_password instead.
	 **/
	function SetPassword($password)
	{
		$this->set_password($password);
	}
	
	function before_save()
	{
		CmsEvents::send_event( 'Core', ($this->id == -1 ? 'AddUserPre' : 'EditUserPre'), array('user' => &$this));
	}
	
	function after_save()
	{
		CmsEvents::send_event( 'Core', ($this->create_date == $this->modified_date ? 'AddUserPost' : 'EditUserPost'), array('user' => &$this));
	}
	
	function before_delete()
	{
		CmsEvents::send_event('Core', 'DeleteUserPre', array('user' => &$this));
	}
	
	function after_delete()
	{
		CmsEvents::send_event('Core', 'DeleteUserPost', array('user' => &$this));
	}
}

class User extends CmsUser {}

# vim:ts=4 sw=4 noet
?>
