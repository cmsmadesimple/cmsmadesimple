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
debug_buffer('', 'Start Loading User');

class User extends CmsObjectRelationalMappting
{
	var $params = array('id' => -1, 'username' => '', 'password' => '', 'firstname' => '', 'lastname' => '', 'email' => '', 'active' => false);
	var $field_maps = array('user_id' => 'id', 'first_name' => 'firstname', 'last_name' => 'lastname', 'admin_access' => 'adminaccess');
	var $table = 'users';
	var $sequence = 'users_seq';

	/**
	 * Encrypts and sets password for the User
	 *
	 * @since 0.6.1
	 */
	function SetPassword($password)
	{
		$this->password = md5($password);
	}
}

User::register_orm_class('User');

debug_buffer('', 'End Loading User');

# vim:ts=4 sw=4 noet
?>
