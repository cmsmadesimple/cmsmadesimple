<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
#
#$Id$

/**
 * User class definition
 * @package CMS
 * @license GPL
 */

/**
 * Generic user class.  This can be used for any logged in user or user related function.
 *
 * @since 0.6.1
 * @package CMS
 * @license GPL
 */
class User
{
	/**
	 * User ID
	 */
	var $id;

	/**
	 * Username
	 */
	var $username;

	/**
	 * Password (md5 encoded)
	 */
	var $password;

	/**
	 * First Name
	 */
	var $firstname;

	/**
	 * Last Name
	 */
	var $lastname;

	/**
	 * Email
	 */
	var $email;

	/**
	 * Active Flag
	 */
	var $active;

	/**
	 * Flag to tell whether user can login to admin panel
	 */
	var $adminaccess;

	/**
	 * Generic constructor.  Runs the SetInitialValues fuction.
	 */
	function User()
	{
		$this->SetInitialValues();
	}

	/**
	 * Sets object to some sane initial values
	 *
	 * @since 0.6.1
	 */
	function SetInitialValues()
	{
		$this->id = -1;
		$this->username = '';
		$this->password = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->email = '';
		$this->active = false;
		$this->adminaccess = false;
	}

	/**
	 * Encrypts and sets password for the User
	 *
	 * @since 0.6.1
	 * @param string $password The plaintext password.
	 */
	function SetPassword($password)
	{
		$this->password = md5(get_site_preference('sitemask','').$password);
	}

	/**
	 * Saves the user to the database.  If no user_id is set, then a new record
	 * is created.  If the uset_id is set, then the record is updated to all values
	 * in the User object.
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 * @since 0.6.1
	 */
	function Save()
	{
		$result = false;
		
		if ($this->id > -1) {
			$gCms = cmsms();
			$userops = $gCms->GetUserOperations();
			$result = $userops->UpdateUser($this);
		}
		else {
			$gCms = cmsms();
			$userops = $gCms->GetUserOperations();
			$newid = $userops->InsertUser($this);
			if ($newid > -1) {
				$this->id = $newid;
				$result = true;
			}
		}

		return $result;
	}

	/**
	 * Delete the record for this user from the database and resets
	 * all values to their initial values.
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 * @since 0.6.1
	 */
	function Delete()
	{
		$result = false;

		if ($this->id > -1) {
			$gCms = cmsms();
			$userops = $gCms->GetUserOperations();
			$result = $userops->DeleteUserByID($this->id);
			if ($result) {
				$this->SetInitialValues();
			}
		}

		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>