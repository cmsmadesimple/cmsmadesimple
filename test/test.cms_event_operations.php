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

class TestCmsEventOperations extends UnitTestCase
{
	var $callback_test = false;

	function __construct()
	{
		parent::__construct();
	}
	
	function setUp()
	{
		CmsCache::clear();
		cms_db()->Execute('DELETE FROM ' . cms_db_prefix() . 'events WHERE originator = ?', array('TestModule'));
		cms_db()->Execute('DELETE FROM ' . cms_db_prefix() . 'event_handlers WHERE module_name = ? or tag_name = ?', array('TestModuleMethod', 'TestEventTag'));
	}
	
	function tearDown()
	{
		$this->setUp();
	}
	
	function create_event()
	{
		CmsEventOperations::create_event('TestModule', 'TestEvent1');
	}
	
	function get_event()
	{
		return cms_orm('CmsEvent')->find_by_module_name_and_event_name('TestModule', 'TestEvent1');
	}
	
	function get_event_count()
	{
		return cms_orm('CmsEvent')->find_count_by_module_name_and_event_name('TestModule', 'TestEvent1');
	}
	
	function create_event_handler_for_tag()
	{
		CmsEventOperations::add_event_handler('TestModule', 'TestEvent1', 'TestEventTag');
	}
	
	function create_event_handler_for_module()
	{
		CmsEventOperations::add_event_handler('TestModule', 'TestEvent1', false, 'TestModuleMethod');
	}
	
	function get_event_handler_count()
	{
		return cms_orm('CmsEventHandler')->find_count_by_module_name_or_tag_name('TestModuleMethod', 'TestEventTag');
	}
	
	function temp_callback($originator, $event_name, &$params)
	{
		$this->assertEqual($originator, 'TestModule');
		$this->assertEqual($event_name, 'TestEvent1');
		$this->callback_test = $this->callback_test + 1;
	}
	
	function test_create_event()
	{
		$this->create_event();
		$this->assertTrue($this->get_event_count() == 1);
	}
	
	function test_create_duplicate_event()
	{
		$this->create_event();
		$this->assertTrue($this->get_event_count() == 1);
		
		$this->create_event();
		$this->assertTrue($this->get_event_count() == 1);
	}
	
	function test_delete_event_by_object()
	{
		$this->create_event();
		$event = $this->get_event();
		$this->assertTrue($event != null);
		$event->delete();
		$this->assertTrue($this->get_event_count() == 0);
	}
	
	function test_delete_event_by_operations()
	{
		$this->create_event();
		$event = $this->get_event();
		$this->assertTrue($event != null);
		CmsEventOperations::remove_event('TestModule', 'TestEvent1');
		$this->assertTrue($this->get_event_count() == 0);
	}
	
	function test_create_event_handler()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->assertTrue($this->get_event_handler_count() == 1);
	}
	
	function test_create_duplicate_event_handler()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->assertTrue($this->get_event_handler_count() == 1);
		
		$this->create_event_handler_for_tag();
		$this->assertTrue($this->get_event_handler_count() == 1);
	}
	
	function test_delete_event_with_handlers_by_operations()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->assertTrue($this->get_event_handler_count() == 2);
		CmsEventOperations::remove_event('TestModule', 'TestEvent1');
		$this->assertTrue($this->get_event_handler_count() == 0);
		$this->assertTrue($this->get_event_count() == 0);
	}
	
	function test_remove_event_handler()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->assertTrue($this->get_event_handler_count() == 2);
		CmsEventOperations::remove_event_handler('TestModule', 'TestEvent1', 'TestEventTag');
		$this->assertTrue($this->get_event_handler_count() == 1);
		CmsEventOperations::remove_event_handler('TestModule', 'TestEvent1', false, 'TestModuleMethod');
		$this->assertTrue($this->get_event_handler_count() == 0);
	}
	
	function test_remove_all_event_handlers()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->assertTrue($this->get_event_handler_count() == 2);
		CmsEventOperations::remove_all_event_handlers('TestModule', 'TestEvent1');
		$this->assertTrue($this->get_event_handler_count() == 0);
		$this->assertTrue($this->get_event_count() == 1);
	}
	
	function test_list_event_handlers()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$list = CmsEventOperations::list_event_handlers('TestModule', 'TestEvent1');

		$this->assertTrue($list != null);
		$this->assertTrue(count($list) == 2);
		CmsEventOperations::remove_all_event_handlers('TestModule', 'TestEvent1');
		
		$list = CmsEventOperations::list_event_handlers('TestModule', 'TestEvent1');
		$this->assertTrue($list != null);
		$this->assertTrue(count($list) == 0);
	}
	
	function test_list_events()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->assertTrue(CmsEventOperations::list_events() > 0);
	}
	
	function test_add_temp_event_handler()
	{
		$this->callback_test = 1;
		$this->create_event();
		CmsEventOperations::add_temp_event_handler('TestModule', 'TestEvent1', array($this, 'temp_callback'));
		CmsEventOperations::send_event('TestModule', 'TestEvent1', array('test stuff'));
		$this->assertEqual($this->callback_test, 2);
		CmsEventOperations::add_temp_event_handler('TestModule', 'TestEvent1', array($this, 'temp_callback'));
		CmsEventOperations::send_event('TestModule', 'TestEvent1', array('test stuff'));
		$this->assertEqual($this->callback_test, 4);		
		CmsEventOperations::add_temp_event_handler('TestModule', 'TestEvent1', array($this, 'temp_callback'), true);
		CmsEventOperations::send_event('TestModule', 'TestEvent1', array('test stuff'));
		$this->assertEqual($this->callback_test, 7);
	}
}

# vim:ts=4 sw=4 noet
?>