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

class TestCmsUserTagOperations extends UnitTestCase
{
	function __construct()
	{
		parent::__construct();
	}
	
	function setUp()
	{
		CmsCache::clear();
		$cms_db_prefix = CMS_DB_PREFIX;
		cms_db()->Execute("DELETE FROM {$cms_db_prefix}userplugins WHERE userplugin_name = ?", array('TestTag'));
	}
	
	function tearDown()
	{
		$this->setUp();
	}
	
	function create_user_tag()
	{
		return CmsUserTagOperations::set_user_tag('TestTag', "return \$params[0];\n");
	}
	
	function select_count()
	{
		return cms_orm('CmsUserTag')->find_count_by_name('TestTag');
	}
	
	function test_create_user_tag()
	{
		$this->assertTrue($this->create_user_tag());
		$this->assertEqual($this->select_count(), 1);
	}
	
	function test_no_duplicates()
	{
		$this->assertTrue($this->create_user_tag());
		$this->assertEqual($this->select_count(), 1);
		$this->assertTrue($this->create_user_tag());
		$this->assertEqual($this->select_count(), 1);
	}
	
	function test_delete_tag()
	{
		$this->assertTrue($this->create_user_tag());
		$this->assertEqual($this->select_count(), 1);
		$this->assertTrue(CmsUserTagOperations::remove_user_tag('TestTag'));
		$this->assertEqual($this->select_count(), 0);
	}
	
	function test_call_tag()
	{
		$this->assertTrue($this->create_user_tag());
		$this->assertEqual($this->select_count(), 1);
		$params = array('TestValue');
		$this->assertEqual(CmsUserTagOperations::call_user_tag('TestTag', $params), 'TestValue');
	}
}

# vim:ts=4 sw=4 noet
?>