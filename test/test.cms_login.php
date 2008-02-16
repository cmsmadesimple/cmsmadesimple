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

include_once('../lib/page.functions.php');

class TestCmsLogin extends UnitTestCase
{
	var $callback_test = false;

	function __construct()
	{
		parent::__construct();
	}
	
	function setUp()
	{
		CmsCache::clear();
		cms_db()->Execute('DELETE FROM ' . cms_db_prefix() . 'users WHERE username = ?', array('sometestuser'));
	}
	
	function tearDown()
	{
		$this->setUp();
	}
	
	function add_test_user()
	{
		$user = new CmsUser();
		$user->username = 'sometestuser';
		$user->set_password('some_password');
		$this->assertTrue($user->save());
	}
	
	function temp_callback($originator, $event_name, &$params)
	{
		$this->assertEqual($originator, 'Core');
		$this->assertEqual($event_name, 'LoginFailed');
		$this->callback_test = $this->callback_test + 1;
	}
	
	function test_bad_passsword()
	{
		$this->add_test_user();
		$this->assertFalse(CmsLogin::login('sometestuser', 'some_bad_password'));
		$this->assertTrue(CmsLogin::login('sometestuser', 'some_password'));
	}
	
	function test_login_failed_event()
	{
		$this->callback_test = 1;
		CmsEventOperations::add_temp_event_handler('Core', 'LoginFailed', array($this, 'temp_callback'));
		$this->assertFalse(CmsLogin::login('sometestuser', 'some_password'));
		$this->assertEqual($this->callback_test, 2);
		$this->add_test_user();
		$this->assertTrue(CmsLogin::login('sometestuser', 'some_password'));
		$this->assertEqual($this->callback_test, 2);
	}
}
# vim:ts=4 sw=4 noet
?>