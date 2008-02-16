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

/**
 * Class of methods for handling Events creation, sending and handling.
 *
 * @author Robert Campbell, Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsEventOperations extends CmsObject
{
	private static $handlercache;
	private static $instance = null;
	
	//arrays to handle "on the fly" event handlers
	//either before or after the actual calls to the stored handlers
	//(defaults to after)
	public static $before_temp_calls = null;
	public static $after_temp_calls = null;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get an instance of this object, though most people should be using
	 * the static methods instead.  This is more for compatibility than
	 * anything else.
	 *
	 * @return CmsUserTagOperations The instance of the singleton object.
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsEventOperations();
		}
		return self::$instance;
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
		$event = cms_orm('CmsEvent')->find_by_module_name_and_event_name($module_name, $event_name);
		if ($event == null)
		{
			$event = new CmsEvent();
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
		$event = cms_orm('CmsEvent')->find_by_module_name_and_event_name($module_name, $event_name);
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
		CmsModule::remove_event( $modulename, $eventname );
	}
	
	
	/**
	 * Trigger an event.
	 * This function will call all registered event handlers for the event
	 *
	 * @param string The name of the module that is sending the event
	 * @param string The name of the event
	 * @param array  The parameters associated with this event.
	 * @return void
	 **/
	public static function send_event( $modulename, $eventname, $params = array() )
	{
		//Send "before" temp handlers
		if (self::$before_temp_calls != null)
		{
			if (isset(self::$before_temp_calls[$modulename][$eventname]))
			{
				foreach (self::$before_temp_calls[$modulename][$eventname] as &$one_method)
				{
					call_user_func_array($one_method, $params);
				}
			}
		}

		$results = self::list_event_handlers($modulename, $eventname);
		
		if ($results != false)
		{		
			foreach( $results as $row )
			{
				if( isset( $row['tag_name'] ) && $row['tag_name'] != '' )
				{
					debug_buffer('calling user tag ' . $row['tag_name'] . ' from event ' . $eventname);
					CmsUserTagOperations::call_user_tag( $row['tag_name'], $params );
				}
				else if( isset( $row['module_name'] ) && $row['module_name'] != '' )
				{
					// here's a quick check to make sure that we're not calling the module
					// DoEvent function for an event originated by the same module.
					if( $row['module_name'] == $modulename )
					{
						continue;
					}

					// and call the module event handler.
					$obj = CmsModule::get_module_instance($row['module_name']);
					if( $obj )
					{
						debug_buffer('calling module ' . $row['module_name'] . ' from event ' . $eventname);
						$obj->DoEvent( $modulename, $eventname, $params );
					}
				}
			}
		}
		
		//Send "after" temp handlers
		if (self::$after_temp_calls != null)
		{
			if (isset(self::$after_temp_calls[$modulename][$eventname]))
			{
				foreach (self::$after_temp_calls[$modulename][$eventname] as &$one_method)
				{
					call_user_func_array($one_method, $params);
				}
			}
		}
	}
	
	/**
	 * @deprecated Deprecated.  Use send_event instead.
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
		return CmsCache::get_instance()->call('CmsEventOperations::list_event_handlers_impl', $module_name, $event_name);
	}
	
	public static function ListEventHandlers($modulename, $eventname)
	{
		return self::list_event_handlers($modulename, $eventname);
	}

	public static function list_event_handlers_impl($module_name, $event_name)
	{
		$event = cms_orm('CmsEvent')->find_by_module_name_and_event_name($module_name, $event_name);
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
		return cms_orm('CmsEvent')->find_all(array('order' => 'module_name, event_name'));
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
		$event = cms_orm('CmsEvent')->find_by_module_name_and_event_name($modulename, $eventname);

		if ($event != null)
		{	
			if ($tag_name != '')
				$handler = cms_orm('CmsEventHandler')->find_by_event_id_and_tag_name($event->id, $tag_name);
			else
				$handler = cms_orm('CmsEventHandler')->find_by_event_id_and_module_name($event->id, $module_handler);
				
			if ($handler != null)
			{
				return false;
			}
		}
		else
		{
			return false;
		}
		
		$handler = new CmsEventHandler();
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
	 * Add an event handler "on the fly" for a module event.  This means that
	 * it only lasts for the duration of the request.
	 *
	 * @param string $module_name  The name of the module sending the event
	 * @param string $event_name  The name of the event
	 * @param array $method  The callback method.  This should be in the standard array($object, $method) structure
	 * @param boolean $before_calls  Do we call this before or after the stored handlers?  Defaults to false (after)
	 *
	 * @return void
	 */
	public static function add_temp_event_handler($module_name, $event_name, $method, $before_calls = false)
	{	
		if ($before_calls)
		{
			if (self::$before_temp_calls == null)
				self::$before_temp_calls = array();

			self::$before_temp_calls[$module_name][$event_name][] = $method;
		}
		else
		{
			if (self::$after_temp_calls == null)
				self::$after_temp_calls = array();

			self::$after_temp_calls[$module_name][$event_name][] = $method;
		}
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
		$event = cms_orm('CmsEvent')->find_by_module_name_and_event_name($modulename, $eventname);

		if ($event != null)
		{	
			if ($tag_name != '')
				$handler = cms_orm('CmsEventHandler')->find_by_event_id_and_tag_name($event->id, $tag_name);
			else
				$handler = cms_orm('CmsEventHandler')->find_by_event_id_and_module_name($event->id, $module_handler);
				
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
	
	/**
	 * Place to handle the help messages for core events.  Basically just going to
	 * call out to the lang() function.
	 *
	 * @param string $eventname  The name of the event
	 *
	 * @return string Returns the help string for the event.  Empty string if nothing
	 * is found.
	 **/
	public static function get_event_help( $eventname )
	{
		return lang('event_help_'.strtolower($eventname));
	}
	
	/**
	 * @deprecated Deprecated.  Use get_event_help instead.
	 **/
	public static function GetEventHelp( $eventname )
	{
		return self::get_event_help( $eventname );
	}
	
	/**
	 * Place to handle the description strings for core events.  Basically just going to
	 * call out to the lang() function.
	 *
	 * @param string $eventname  The name of the event
	 *
	 * @return string Returns the description string for the event.  Empty string if nothing
	 * is found.
	 *
	 **/
	public static function get_event_description( $eventname )
	{
		return lang('event_desc_'.strtolower($eventname));
	}
	
	/**
	 * @deprecated Deprecated.  Use get_event_description instead.
	 **/
	public static function GetEventDescription( $eventname )
	{
		return self::get_event_description( $eventname );
	}
	
	/**
	 * Used by the install script to setup the core events
	 * for a default install.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public static function setup_core_events()
	{
		$modulename = 'Core';

		self::create_event( $modulename, 'LoginPost');
		self::create_event( $modulename, 'LogoutPost');
		self::create_event( $modulename, 'LoginFailed');
		
		self::create_event( $modulename, 'AddUserPre');
		self::create_event( $modulename, 'AddUserPost');
		self::create_event( $modulename, 'EditUserPre');
		self::create_event( $modulename, 'EditUserPost');
		self::create_event( $modulename, 'DeleteUserPre');
		self::create_event( $modulename, 'DeleteUserPost');
		
		self::create_event( $modulename, 'AddGroupPre');
		self::create_event( $modulename, 'AddGroupPost');
		self::create_event( $modulename, 'EditGroupPre');
		self::create_event( $modulename, 'EditGroupPost');
		self::create_event( $modulename, 'DeleteGroupPre');
		self::create_event( $modulename, 'DeleteGroupPost');
		
		self::create_event( $modulename, 'AddStylesheetPre');
		self::create_event( $modulename, 'AddStylesheetPost');
		self::create_event( $modulename, 'EditStylesheetPre');
		self::create_event( $modulename, 'EditStylesheetPost');
		self::create_event( $modulename, 'DeleteStylesheetPre');
		self::create_event( $modulename, 'DeleteStylesheetPost');
		
		self::create_event( $modulename, 'AddTemplatePre');
		self::create_event( $modulename, 'AddTemplatePost');
		self::create_event( $modulename, 'EditTemplatePre');
		self::create_event( $modulename, 'EditTemplatePost');
		self::create_event( $modulename, 'DeleteTemplatePre');
		self::create_event( $modulename, 'DeleteTemplatePost');
		self::create_event( $modulename, 'TemplatePreCompile');
		self::create_event( $modulename, 'TemplatePostCompile');
		
		self::create_event( $modulename, 'AddGlobalContentPre');
		self::create_event( $modulename, 'AddGlobalContentPost');
		self::create_event( $modulename, 'EditGlobalContentPre');
		self::create_event( $modulename, 'EditGlobalContentPost');
		self::create_event( $modulename, 'DeleteGlobalContentPre');
		self::create_event( $modulename, 'DeleteGlobalContentPost');
		self::create_event( $modulename, 'GlobalContentPreCompile');
		self::create_event( $modulename, 'GlobalContentPostCompile');
		
		self::create_event( $modulename, 'ContentEditPre');
		self::create_event( $modulename, 'ContentEditPost');
		self::create_event( $modulename, 'ContentDeletePre');
		self::create_event( $modulename, 'ContentDeletePost');
		
		self::create_event( $modulename, 'AddUserDefinedTagPre');
		self::create_event( $modulename, 'AddUserDefinedTagPost');
		self::create_event( $modulename, 'EditUserDefinedTagPre');
		self::create_event( $modulename, 'EditUserDefinedTagPost');
		self::create_event( $modulename, 'DeleteUserDefinedTagPre');
		self::create_event( $modulename, 'DeleteUserDefinedTagPost');
		
		self::create_event( $modulename, 'ModuleInstalled');
		self::create_event( $modulename, 'ModuleUninstalled');
		self::create_event( $modulename, 'ModuleUpgraded');
		self::create_event( $modulename, 'AllModulesLoaded');
		
		self::create_event( $modulename, 'ContentStylesheet');
		self::create_event( $modulename, 'ContentPreCompile');
		self::create_event( $modulename, 'ContentPostCompile');
		self::create_event( $modulename, 'ContentPostRender');
		self::create_event( $modulename, 'SmartyPreCompile');
		self::create_event( $modulename, 'SmartyPostCompile');
	}
	
	/**
	 * @deprecated Deprecated.  Use setup_core_events instead.
	 **/
	public static function SetupCoreEvents()
	{
		self::setup_core_events();
	}
}

/**
 * @deprecated Deprecated.  Use CmsEvents instead.
 **/
class Events extends CmsEventOperations {}

# vim:ts=4 sw=4 noet
?>
