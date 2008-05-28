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

require_once 'PHPUnit/Framework/TestCase.php';
include_once(dirname(dirname(__FILE__)) . '/lib/cmsms.api.php');

class CmsEventsTest extends PHPUnit_Framework_TestCase
{
	var $callback_test = false;
	var $originator = '';
	var $event_name = '';

	public function setUp()
	{
		CmsCache::clear();
		cms_db()->Execute('DELETE FROM ' . cms_db_prefix() . 'events WHERE originator = ?', array('TestModule'));
		cms_db()->Execute('DELETE FROM ' . cms_db_prefix() . 'event_handlers WHERE module_name = ? or tag_name = ?', array('TestModuleMethod', 'TestEventTag'));
	}
	
	public function tearDown()
	{
		$this->setUp();
	}
	
	public function create_event()
	{
		CmsEventOperations::create_event('TestModule', 'TestEvent1');
	}
	
	public function get_event()
	{
		return cms_orm('CmsEvent')->find_by_module_name_and_event_name('TestModule', 'TestEvent1');
	}
	
	public function get_event_count()
	{
		return cms_orm('CmsEvent')->find_count_by_module_name_and_event_name('TestModule', 'TestEvent1');
	}
	
	public function create_event_handler_for_tag()
	{
		CmsEventOperations::add_event_handler('TestModule', 'TestEvent1', 'TestEventTag');
	}
	
	public function create_event_handler_for_module()
	{
		CmsEventOperations::add_event_handler('TestModule', 'TestEvent1', false, 'TestModuleMethod');
	}
	
	public function get_event_handler_count()
	{
		return cms_orm('CmsEventHandler')->find_count_by_module_name_or_tag_name('TestModuleMethod', 'TestEventTag');
	}
	
	public function temp_callback($originator, $event_name, &$params)
	{
		$this->originator = $originator;
		$this->event_name = $event_name;
		$this->callback_test = $this->callback_test + 1;
	}
	
	public function testShouldBeAbleToCreateAnEvent()
	{
		$this->create_event();
		$this->assertEquals(1, $this->get_event_count());
	}
	
	public function testShouldNotAllowDuplicates()
	{
		$this->create_event();
		$this->assertEquals(1, $this->get_event_count());
		
		$this->create_event();
		$this->assertEquals(1, $this->get_event_count());
	}
	
	public function testShouldProperlyDeleteAnEventByObject()
	{
		$this->create_event();
		$event = $this->get_event();
		$this->assertTrue($event != null);
		$event->delete();
		$this->assertEquals(0, $this->get_event_count());
	}
	
	public function testShouldProperlyDeleteAnEventByOperations()
	{
		$this->create_event();
		$event = $this->get_event();
		$this->assertTrue($event != null);
		CmsEventOperations::remove_event('TestModule', 'TestEvent1');
		$this->assertEquals(0, $this->get_event_count());
	}
	
	public function testShouldCreateAnEventHandler()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->assertEquals(1, $this->get_event_handler_count());
	}
	
	public function testShouldNotCreateADuplicateEventHandler()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->assertEquals(1, $this->get_event_handler_count());
		                                            
		$this->create_event_handler_for_tag();      
		$this->assertEquals(1, $this->get_event_handler_count());
	}
	
	public function testShouldBeAbleToDeleteHandlersByOperations()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->assertEquals(2, $this->get_event_handler_count());
		CmsEventOperations::remove_event('TestModule', 'TestEvent1');
		$this->assertEquals(0, $this->get_event_handler_count());
		$this->assertEquals(0, $this->get_event_count());
	}
	
	public function testShouldBeAbleToRemoveHandlers()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->assertEquals(2, $this->get_event_handler_count());
		CmsEventOperations::remove_event_handler('TestModule', 'TestEvent1', 'TestEventTag');
		$this->assertEquals(1, $this->get_event_handler_count());
		CmsEventOperations::remove_event_handler('TestModule', 'TestEvent1', false, 'TestModuleMethod');
		$this->assertEquals(0, $this->get_event_handler_count());
	}
	
	public function testShouldBeAbleToRemoveAllEventHandlers()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->assertEquals(2, $this->get_event_handler_count());
		CmsEventOperations::remove_all_event_handlers('TestModule', 'TestEvent1');
		$this->assertEquals(0, $this->get_event_handler_count());
		$this->assertEquals(1, $this->get_event_count());
	}
	
	public function testShouldBeAbleToListEventHandlers()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$list = CmsEventOperations::list_event_handlers('TestModule', 'TestEvent1');

		$this->assertNotNull($list);
		$this->assertEquals(2, count($list));
		CmsEventOperations::remove_all_event_handlers('TestModule', 'TestEvent1');
		
		$list = CmsEventOperations::list_event_handlers('TestModule', 'TestEvent1');
		$this->assertNotNull($list);
		$this->assertEquals(0, count($list));
	}
	
	public function testShouldBeAbleToListEvents()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->assertGreaterThan(0, CmsEventOperations::list_events());
	}
	
	public function testShouldBeAbleToAddTemporaryEventHandlers()
	{
		$this->callback_test = 1;
		$this->originator = '';
		$this->event_name = '';
		$this->create_event();
		CmsEventOperations::add_temp_event_handler('TestModule', 'TestEvent1', array($this, 'temp_callback'));
		CmsEventOperations::send_event('TestModule', 'TestEvent1', array('test stuff'));
		$this->assertEquals(2, $this->callback_test);
		$this->assertEquals('TestModule', $this->originator);
		$this->assertEquals('TestEvent1', $this->event_name);
		CmsEventOperations::add_temp_event_handler('TestModule', 'TestEvent1', array($this, 'temp_callback'));
		CmsEventOperations::send_event('TestModule', 'TestEvent1', array('test stuff'));
		$this->assertEquals(4, $this->callback_test);
		$this->assertEquals('TestModule', $this->originator);
		$this->assertEquals('TestEvent1', $this->event_name);
		CmsEventOperations::add_temp_event_handler('TestModule', 'TestEvent1', array($this, 'temp_callback'), true);
		CmsEventOperations::send_event('TestModule', 'TestEvent1', array('test stuff'));
		$this->assertEquals(7, $this->callback_test);
		$this->assertEquals('TestModule', $this->originator);
		$this->assertEquals('TestEvent1', $this->event_name);
	}

}

# vim:ts=4 sw=4 noet
?>