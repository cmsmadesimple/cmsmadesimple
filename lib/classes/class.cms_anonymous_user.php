<?php
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
#$Id: class.cms_anonymous_user.php 4093 2007-08-27 00:44:25Z wishy $

class CmsAnonymousUser extends CmsUser
{
	var $params = array('id' => 0, 'username' => 'anonymous', 'password' => '', 'firstname' => 'Anonymous', 'lastname' => 'User', 'email' => '', 'active' => true);

	function __construct()
	{
		parent::__construct();
		$this->groups = array(cms_orm()->cms_group->find_by_name('Anonymous'));
	}
	
	function groups()
	{
		return $this->groups;
	}
	
	function save()
	{
		return false;
	}
	
	function delete($id = -1)
	{
		return false;
	}
	
	public function is_anonymous()
	{
		return true;
	}
}

# vim:ts=4 sw=4 noet
?>