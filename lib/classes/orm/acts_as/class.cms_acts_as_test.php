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
#$Id: class.cms_acts_as_test.php 4093 2007-08-27 00:44:25Z wishy $

class CmsActsAsTest extends CmsActsAs
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function before_load($type, $fields)
	{
		CmsProfiler::get_instance()->mark('before load -- CmsActsAsTest');
	}
	
	public function after_load(&$obj)
	{
		CmsProfiler::get_instance()->mark('after load -- CmsActsAsTest');
	}
	
	public function test_me()
	{
		CmsProfiler::get_instance()->mark('Test Me -- CmsActsAsTest');
	}
}

# vim:ts=4 sw=4 noet
?>