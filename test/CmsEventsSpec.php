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

include_once(dirname(dirname(__FILE__)) . '/lib/cmsms.api.php');

class DescribeCmsEvents extends PHPSpec_Context
{
	var $callback_test = false;
	var $originator = '';
	var $event_name = '';

	public function before()
	{
		CmsCache::clear();
		cms_db()->Execute('DELETE FROM ' . cms_db_prefix() . 'events WHERE originator = ?', array('TestModule'));
		cms_db()->Execute('DELETE FROM ' . cms_db_prefix() . 'event_handlers WHERE module_name = ? or tag_name = ?', array('TestModuleMethod', 'TestEventTag'));
	}
	
	public function after()
	{
		$this->before();
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
	
	public function itShouldBeAbleToCreateAnEvent()
	{
		$this->create_event();
		$this->spec($this->get_event_count())->should->be(1);
	}
	
	public function itShouldNotAllowDuplicates()
	{
		$this->create_event();
		$this->spec($this->get_event_count())->should->be(1);
		
		$this->create_event();
		$this->spec($this->get_event_count())->should->be(1);
	}
	
	public function itShouldProperlyDeleteAnEventByObject()
	{
		$this->create_event();
		$event = $this->get_event();
		$this->spec($event != null)->should->beTrue();
		$event->delete();
		$this->spec($this->get_event_count())->should->be(0);
	}
	
	public function itShouldProperlyDeleteAnEventByOperations()
	{
		$this->create_event();
		$event = $this->get_event();
		$this->spec($event != null)->should->beTrue();
		CmsEventOperations::remove_event('TestModule', 'TestEvent1');
		$this->spec($this->get_event_count())->should->be(0);
	}
	
	public function itShouldCreateAnEventHandler()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->spec($this->get_event_handler_count())->should->be(1);
	}
	
	public function itShouldNotCreateADuplicateEventHandler()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->spec($this->get_event_handler_count())->should->be(1);
		                                            
		$this->create_event_handler_for_tag();      
		$this->spec($this->get_event_handler_count())->should->be(1);
	}
	
	public function itShouldBeAbleToDeleteHandlersByOperations()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->spec($this->get_event_handler_count())->should->be(2);
		CmsEventOperations::remove_event('TestModule', 'TestEvent1');
		$this->spec($this->get_event_handler_count())->should->be(0);
		$this->spec($this->get_event_count())->should->be(0);
	}
	
	function itShouldBeAbleToRemoveHandlers()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->spec($this->get_event_handler_count())->should->be(2);
		CmsEventOperations::remove_event_handler('TestModule', 'TestEvent1', 'TestEventTag');
		$this->spec($this->get_event_handler_count())->should->be(1);
		CmsEventOperations::remove_event_handler('TestModule', 'TestEvent1', false, 'TestModuleMethod');
		$this->spec($this->get_event_handler_count())->should->be(0);
	}
	
	public function itShouldBeAbleToRemoveAllEventHandlers()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->spec($this->get_event_handler_count())->should->be(2);
		CmsEventOperations::remove_all_event_handlers('TestModule', 'TestEvent1');
		$this->spec($this->get_event_handler_count())->should->be(0);
		$this->spec($this->get_event_count())->should->be(1);
	}
	
	public function itShouldBeAbleToListEventHandlers()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$list = CmsEventOperations::list_event_handlers('TestModule', 'TestEvent1');

		$this->spec($list)->shouldNot->beNull();
		$this->spec(count($list))->should->be(2);
		CmsEventOperations::remove_all_event_handlers('TestModule', 'TestEvent1');
		
		$list = CmsEventOperations::list_event_handlers('TestModule', 'TestEvent1');
		$this->spec($list)->shouldNot->beNull();
		$this->spec(count($list))->should->be(0);
	}
	
	public function itShouldBeAbleToListEvents()
	{
		$this->create_event();
		$this->create_event_handler_for_tag();
		$this->create_event_handler_for_module();
		$this->spec(CmsEventOperations::list_events())->should->beGreaterThan(0);
	}
	
	public function itShouldBeAbleToAddTemporaryEventHandlers()
	{
		$this->callback_test = 1;
		$this->originator = '';
		$this->event_name = '';
		$this->create_event();
		CmsEventOperations::add_temp_event_handler('TestModule', 'TestEvent1', array($this, 'temp_callback'));
		CmsEventOperations::send_event('TestModule', 'TestEvent1', array('test stuff'));
		$this->spec($this->callback_test)->should->be(2);
		$this->spec($this->originator)->should->be('TestModule');
		$this->spec($this->event_name)->should->be('TestEvent1');
		CmsEventOperations::add_temp_event_handler('TestModule', 'TestEvent1', array($this, 'temp_callback'));
		CmsEventOperations::send_event('TestModule', 'TestEvent1', array('test stuff'));
		$this->spec($this->callback_test)->should->be(4);
		$this->spec($this->originator)->should->be('TestModule');
		$this->spec($this->event_name)->should->be('TestEvent1');
		CmsEventOperations::add_temp_event_handler('TestModule', 'TestEvent1', array($this, 'temp_callback'), true);
		CmsEventOperations::send_event('TestModule', 'TestEvent1', array('test stuff'));
		$this->spec($this->callback_test)->should->be(7);
		$this->spec($this->originator)->should->be('TestModule');
		$this->spec($this->event_name)->should->be('TestEvent1');
	}

}

# vim:ts=4 sw=4 noet
?>