<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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

class CmsDatabaseEventManager extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	public static function load_events_into_manager()
	{
		$result = cms_db()->Execute("SELECT h.tag_name, h.module_name, e.originator, e.event_name FROM {event_handlers} h INNER JOIN {events} e ON e.event_id = h.event_id ORDER BY e.originator, e.event_name, h.handler_order");
		while ($result && $row = $result->FetchRow())
		{
			$event_name = $row['originator'] . ':' . $row['event_name'];
			CmsEventManager::register_event_handler($event_name, array('CmsDatabaseEventManager', 'callback_database_event'));
		}
	}
	
	public static function callback_database_event($event_name, &$params)
	{
		list($module_name, $sub_name) = explode(':', $event_name);
		$results = self::list_event_handlers($module_name, $sub_name);

		if ($results != false)
		{		
			foreach( $results as $row )
			{
				if( isset( $row['tag_name'] ) && $row['tag_name'] != '' )
				{
					debug_buffer('calling user tag ' . $row['tag_name'] . ' from event ' . $sub_name);
					$ops = cmsms()->GetUserTagOperations();
					$ops->call_user_tag( $row['tag_name'], $params );
				}
				else if( isset( $row['module_name'] ) && $row['module_name'] != '' )
				{
					// here's a quick check to make sure that we're not calling the module
					// DoEvent function for an event originated by the same module.
					if( $row['module_name'] == $module_name )
					{
						continue;
					}

					// and call the module event handler.
					$obj = CmsModule::get_module_instance($row['module_name']);
					if( $obj )
					{
						debug_buffer('calling module ' . $row['module_name'] . ' from event ' . $sub_name);
						$obj->DoEvent( $module_name, $sub_name, $params );
					}
				}
			}
		}
	}
	
	/**
	 * Inform the system about a new event that can be generated
	 *
	 * @param string The name of the module that is sending the event
	 * @param string The name of the event
	 * @return void
	 **/
	public static function create_event( $module_name, $event_name )
	{
		$event = cms_orm('CmsDatabaseEvent')->find_by_module_name_and_event_name($module_name, $event_name);
		if ($event == null)
		{
			$event = new CmsDatabaseEvent();
			$event->module_name = $module_name;
			$event->event_name = $event_name;
			return $event->save();
		}
	}

	/**
	 * @deprecated Deprecated.  Use create_event instead.
	 **/
	public static function CreateEvent( $modulename, $eventname )
	{
		self::create_event( $modulename, $eventname );
	}
	
	/**
	 * Remove an event from the CMS system
	 * This function removes all handlers to the event, and completely removes
	 * all references to this event from the database
	 *
	 * Note, only events created by this module can be removed.
	 *
	 * @param string The name of the module that is sending the event
	 * @param string The name of the event
	 * @return void
	 **/
	public static function remove_event( $module_name, $event_name )
	{
		$event = cms_orm('CmsDatabaseEvent')->find_by_module_name_and_event_name($module_name, $event_name);
		if ($event)
		{
			$event->delete();
		}
	}

	/**
	 * @deprecated Deprecated.  Use remove_event.
	 **/
	public static function RemoveEvent( $modulename, $eventname )
	{
		self::remove_event( $modulename, $eventname );
	}
	
	/**
	 * Trigger an event.
	 * This function will call all registered event handlers for the event
	 *
	 * @param string The name of the module that is sending the event
	 * @param string The name of the event
	 * @param array  The parameters associated with this event.
	 * @return void
	 *
	 * @deprecated Deprecated. Use CmsEventManager::send_event().
	 **/
	public static function send_event( $modulename, $eventname, $params = array() )
	{
		CmsEventManager::send_event($modulename . ':' . $eventname, $params);
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsEventManager::send_event().
	 **/
	public static function SendEvent( $modulename, $eventname, $params = array() )
	{
		self::send_event( $modulename, $eventname, $params );
	}

	/**
	 * Return the list of event handlers for a particular event
	 *
	 * @param string $modulename The name of the module sending the event
	 * @param string $eventname  The name of the event
	 * @return mixed If successful, an array of arrays, each element
	 * in the array contains two elements 'handler_name', and 'module_handler',
	 * any one of these could be null. If it fails, false is returned.
	 **/
	public static function list_event_handlers($module_name, $event_name)
	{
		return CmsCache::get_instance()->call('CmsDatabaseEventManager::list_event_handlers_impl', $module_name, $event_name);
		//return CmsDatabaseEventManager::list_event_handlers_impl($module_name, $event_name);
	}

	public static function ListEventHandlers($modulename, $eventname)
	{
		return self::list_event_handlers($modulename, $eventname);
	}

	public static function list_event_handlers_impl($module_name, $event_name)
	{
		$event = cms_orm('CmsDatabaseEvent')->find_by_module_name_and_event_name($module_name, $event_name);
		if ($event)
		{
			return $event->event_handlers;
		}

		return false;
	}
	
	/**
	 * Get a list of all of the known events
	 *
	 * @return mixed If successful, a list of all the known events.  If it fails, null
	 */
	public static function list_events()
	{
		return cms_orm('CmsDatabaseEvent')->find_all(array('order' => 'module_name, event_name'));
	}

	/**
	 * @deprecated Deprecated.  Use list_events() instead.
	 */
	public static function ListEvents()
	{
		return self::list_events();
	}
	
	/**
	 * Add an event handler for a module event
	 *
	 * @param string $modulename      The name of the module sending the event
	 * @param string $eventname       The name of the event
	 * @param string $tag_name        The name of a user defined tag
	 * @param string $module_handler  The name of the module
	 * @param boolean $removable      Can this event be removed from the list?
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 */
	public static function add_event_handler( $modulename, $eventname, $tag_name = false, $module_handler = false, $removable = true)
	{
		if( $tag_name == false && $module_handler == false )
		{
			return false;
		}
		if( $tag_name != false && $module_handler != false )
		{
			return false;
		}

		$handler = null;
		$event = cms_orm('CmsDatabaseEvent')->find_by_module_name_and_event_name($modulename, $eventname);

		if ($event != null)
		{	
			if ($tag_name != '')
				$handler = cms_orm('CmsDatabaseEventHandler')->find_by_event_id_and_tag_name($event->id, $tag_name);
			else
				$handler = cms_orm('CmsDatabaseEventHandler')->find_by_event_id_and_module_name($event->id, $module_handler);

			if ($handler != null)
			{
				return false;
			}
		}
		else
		{
			return false;
		}

		$handler = new CmsDatabaseEventHandler();
		$handler->event_id = $event->id;
		if ($tag_name != '')
			$handler->tag_name = $tag_name;
		else
			$handler->module_name = $module_handler;
		$handler->removable = $removable;

		return $handler->save();
	}
	
	/**
	 * @deprecated Deprecated.  Use add_event_handler instead.
	 **/
	public static function AddEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false, $removable = true)
	{
		return self::add_event_handler( $modulename, $eventname, $tag_name, $module_handler, $removable );
	}
	
	/**
	 * Remove an event handler for a particular event
	 *
	 * @param string $modulename The name of the module sending the event
	 * @param string $eventname  The name of the event
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 */
	public static function remove_event_handler( $modulename, $eventname, $tag_name = false, $module_handler = false )
	{
		if( $tag_name != false && $module_handler != false )
		{
			return false;
		}

		$handler = null;
		$event = cms_orm('CmsDatabaseEvent')->find_by_module_name_and_event_name($modulename, $eventname);

		if ($event != null)
		{	
			if ($tag_name != '')
				$handler = cms_orm('CmsDatabaseEventHandler')->find_by_event_id_and_tag_name($event->id, $tag_name);
			else
				$handler = cms_orm('CmsDatabaseEventHandler')->find_by_event_id_and_module_name($event->id, $module_handler);

			if ($handler != null)
			{
				return $handler->delete();
			}
		}

		return false;
	}

	/**
	 * @deprecated Deprecated.  Use remove_event_handler instead.
	 **/
	public static function RemoveEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false )
	{
		return self::remove_event_handler( $modulename, $eventname, $tag_name, $module_handler );
	}
	
	/**
	 * Removes all event handlers for the given event.
	 *
	 * @param string $modulename The name of the module sending the event
	 * @param string $eventname  The name of the event
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 **/
	public static function remove_all_event_handlers( $modulename, $eventname )
	{
		$handlers = self::list_event_handlers($modulename, $eventname);

		if ($handlers != null)
		{
			foreach ($handlers as $handler)
			{
				$handler->delete();
			}
		}
	}

	/**
	 * @deprecated Deprecated.  Use remove_all_event_handlers instead.
	 **/
	public static function RemoveAllEventHandlers( $modulename, $eventname )
	{
		return self::remove_all_event_handlers( $modulename, $eventname );
	}

}

# vim:ts=4 sw=4 noet
?>