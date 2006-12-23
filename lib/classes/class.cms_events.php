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
#$Id$

/**
 * Events class for admin
 *
 * @package CMS
 */
class CmsEvents extends CmsObject
{
	/**
	 * Inform the system about a new event that can be generated
	 *
	 * @param string The name of the module that is sending the event
	 * @param string The name of the event
	 * @return void
	 **/
	public static function create_event( $modulename, $eventname )
	{
		$db = cms_db();
		$id = $db->GenID( cms_db_prefix()."events_seq" );
		$q = "INSERT INTO ".cms_db_prefix()."events values (?,?,?)";
		$db->Execute( $q, array( $modulename, $eventname, $id ));
	}
	
	/**
	 * Deprecated.  Use create_event.
	 **/
	public static function CreateEvent( $modulename, $eventname )
	{
		CmsEvents::create_event( $modulename, $eventname );
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
	public static function remove_event( $modulename, $eventname )
	{
		$db = cms_db();

		// get the id
		$q = "SELECT event_id FROM ".cms_db_prefix()."events WHERE 
		originator = ? AND event_name = ?";
		$dbresult = $db->Execute( $q, array( $modulename, $eventname ) );
		if( $dbresult == false || $dbresult->RecordCount() == 0 )
		{
			// query failed, event not found
			return false;
		}
		$row = $dbresult->FetchRow();
		$id = $row['event_id'];

		// delete all the handlers
		$q = "DELETE FROM ".cms_db_prefix()."event_handlers WHERE
		event_id = ?";
		$db->Execute( $q, array( $id ) );

		// then delete the event
		$q = "DELETE FROM ".cms_db_prefix()."events WHERE
		event_id = ?";
		$db->Execute( $q, array( $id ) );
	}
	
	/**
	 * Deprecated.  Use remove_event.
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
		//var_dump("Sending $eventname on $modulename");
		global $gCms;
		$usertagops =& $gCms->GetUserTagOperations();

		$results = CmsEvents::list_event_handlers($modulename, $eventname);
		
		if ($results != false)
		{		
			foreach( $results as $row )
			{
				if( isset( $row['tag_name'] ) && $row['tag_name'] != '' )
				{
					debug_buffer('calling user tag ' . $row['tag_name'] . ' from event ' . $eventname);
					$usertagops->CallUserTag( $row['tag_name'], $params );
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
					$obj =& CmsModule::GetModuleInstance($row['module_name']);
					if( $obj )
					{
						debug_buffer('calling module ' . $row['module_name'] . ' from event ' . $eventname);
						$obj->DoEvent( $modulename, $eventname, $params );
					}
				}
			}
		}
	}
	
	/**
	 * Deprecated.  Use send_event instead.
	 **/
	public static function SendEvent( $modulename, $eventname, $params = array() )
	{
		CmsEvents::send_event( $modulename, $eventname, $params );
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
	public static function list_event_handlers( $modulename, $eventname )
	{
		global $gCms;
		$db = &$gCms->GetDb();
		$variables =& $gCms->variables;
		
		$params['module'] = $modulename;
		$params['event'] = $eventname;
		
		$handlers = array();
		
		if (!isset($variables['handlercache']))
		{
			$q = "SELECT eh.tag_name, eh.module_name, e.originator, e.event_name, eh.handler_order, eh.handler_id, eh.removable FROM ".cms_db_prefix()."event_handlers eh
				INNER JOIN ".cms_db_prefix()."events e ON e.event_id = eh.event_id
				ORDER BY eh.handler_order ASC";

			$dbresult = $db->Execute( $q );
			
			$result = array();

			while( $dbresult && $row = $dbresult->FetchRow() )
			{
				$result[] = $row;
			}
			$variables['handlercache'] = $result;
		}

		foreach ($variables['handlercache'] as $row)
		{
			if ($row['originator'] == $modulename && $row['event_name'] == $eventname)
			{
				$handlers[] = $row;
			}
		}
		
		if (count($handlers) > 0)
			return $handlers;
		else
			return false;
	}
	
	public static function ListEventHandlers( $modulename, $eventname )
	{
		return CmsEvents::list_event_handlers( $modulename, $eventname );
	}

	/**
	* Get a list of all of the known events
	*
	* @return mixed If successful, a list of all the known events.  If it fails, false
	*/
	public static function list_events()
	{
		$db = cms_db();

		$q = "SELECT * FROM ".cms_db_prefix()."events ORDER BY originator,event_name";
		$dbresult = $db->Execute( $q );
		if( $dbresult == false )
		{
			return false;
		}

		$result = array();
		while( $row = $dbresult->FetchRow() )
		{
			$result[] = $row;
		}

		return $result;
	}
	
	/**
	 * Deprecated.  Use list_events() instead.
	 */
	public static function ListEvents()
	{
		return CmsEvents::list_events();
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

		$db = cms_db();

		// find the id
		$q = "SELECT event_id FROM ".cms_db_prefix()."events WHERE 
		originator = ? AND event_name = ?";
		$dbresult = $db->Execute( $q, array( $modulename, $eventname ) );
		if( $dbresult == false || $dbresult->RecordCount() == 0 )
		{
			// query failed, event not found
			return false;
		}
		$row = $dbresult->FetchRow();
		$id = $row['event_id'];

		// now see if there's nothing already existing for this
		// tag or module and this id
		$q = "SELECT * FROM ".cms_db_prefix()."event_handlers WHERE
		event_id = ? AND ";
		$params = array();
		$params[] = $id;
		if( $tag_name != "" )
		{
			$q .= "tag_name = ?";
			$params[] = $tag_name;
		}
		else
		{
			$q .= "module_name = ?";
			$params[] = $module_handler;
		}
		$dbresult = $db->Execute( $q, $params );
		if( $dbresult != false && $dbresult->RecordCount() > 0 )
		{
			// hmmm, something matches already
			return false;
		}

		// now see if we can get a new id
		$order = 1;
		$q = "SELECT max(handler_order) AS newid FROM ".cms_db_prefix()."event_handlers
		WHERE event_id = ?";
		$dbresult = $db->Execute( $q, array( $id ) );
		if( $dbresult != false && $dbresult->RecordCount() != 0)
		{
			$row = $dbresult->FetchRow();
			$order = $row['newid'] + 1;
		}

		$handler_id = $db->GenId( cms_db_prefix()."event_handler_seq" );

		// okay, we can insert
		$params = array();
		$params[] = $id;
		$q = "INSERT INTO ".cms_db_prefix()."event_handlers ";
		if( $module_handler != false )
		{
			$q .= '(event_id,module_name,removable,handler_order,handler_id)';
			$params[] = $module_handler;
		}
		else
		{
			$q .= '(event_id,tag_name,removable,handler_order,handler_id)';
			$params[] = $tag_name;
		}
		$q .= "VALUES (?,?,?,?,?)";
		$params[] = ($removable?1:0);
		$params[] = $order;
		$params[] = $handler_id;
		$dbresult = $db->Execute( $q, $params );
		if( $dbresult != false )
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Deprecated.  Use add_event_handler instead.
	 **/
	public static function AddEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false, $removable = true)
	{
		return CmsEvents::add_event_handler( $modulename, $eventname, $tag_name, $module_handler, $removable );
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
		$field = 'handler_name';
		if( $module_handler != false )
		{
			$field = 'module_handler';
		}

		$db = cms_db();

		// find the id
		$q = "SELECT event_id FROM ".cms_db_prefix()."events WHERE 
		originator = ? AND event_name = ?";
		$dbresult = $db->Execute( $q, array( $modulename, $eventname ) );
		if( $dbresult == false || $dbresult->RecordCount() == 0 )
		{
			// query failed, event not found
			return false;
		}
		$row = $dbresult->FetchRow();
		$id = $row['event_id'];

		// delete the record
		$params = array( $id );
		$query = "DELETE FROM ".cms_db_prefix()."event_handlers
 		          WHERE event_id = ? AND ";
		if( $modulename != false )
		{
			$query .= 'module_name = ?';
			$params[] = $module_handler;
		}
		else
		{
			$query .= 'tag_name = ?';
			$params[] = $tag_name;
		}
		$dbresult = $db->Execute( $query, $params );
		if( $dbresult == false )
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Deprecated.  Use remove_event_handler instead.
	 **/
	public static function RemoveEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false )
	{
		return CmsEvents::remove_event_handler( $modulename, $eventname, $tag_name, $module_handler );
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
		$db = cms_db();

		// find the id
		$q = "SELECT event_id FROM ".cms_db_prefix()."events WHERE 
		originator = ? AND event_name = ?";
		$dbresult = $db->Execute( $q, array( $modulename, $eventname ) );
		if( $dbresult == false || $dbresult->RecordCount() == 0 )
		{
			// query failed, event not found
			return false;
		}
		$row = $dbresult->FetchRow();
		$id = $row['event_id'];

		// and delete the handlers
		$q = "DELETE FROM ".cms_db_prefix()."event_handlers 
		WHERE event_id = ?";
		$dbresult = $db->Execute( $q, array( $id ) );
		if( $dbresult == false )
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Deprecated.  Use remove_all_event_handlers instead.
	 **/
	public static function RemoveAllEventHandlers( $modulename, $eventname )
	{
		return CmsEvents::remove_all_event_handlers( $modulename, $eventname );
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
	 * Deprecated.  Use get_event_help instead.
	 **/
	public static function GetEventHelp( $eventname )
	{
		return CmsEvents::get_event_help( $eventname );
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
	 * Deprecated.  Use get_event_description instead.
	 **/
	public static function GetEventDescription( $eventname )
	{
		return CmsEvents::get_event_description( $eventname );
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

		CmsEvents::create_event( $modulename, 'LoginPost');
		CmsEvents::create_event( $modulename, 'LogoutPost');
		
		CmsEvents::create_event( $modulename, 'AddUserPre');
		CmsEvents::create_event( $modulename, 'AddUserPost');
		CmsEvents::create_event( $modulename, 'EditUserPre');
		CmsEvents::create_event( $modulename, 'EditUserPost');
		CmsEvents::create_event( $modulename, 'DeleteUserPre');
		CmsEvents::create_event( $modulename, 'DeleteUserPost');
		
		CmsEvents::create_event( $modulename, 'AddGroupPre');
		CmsEvents::create_event( $modulename, 'AddGroupPost');
		CmsEvents::create_event( $modulename, 'EditGroupPre');
		CmsEvents::create_event( $modulename, 'EditGroupPost');
		CmsEvents::create_event( $modulename, 'DeleteGroupPre');
		CmsEvents::create_event( $modulename, 'DeleteGroupPost');
		
		CmsEvents::create_event( $modulename, 'AddStylesheetPre');
		CmsEvents::create_event( $modulename, 'AddStylesheetPost');
		CmsEvents::create_event( $modulename, 'EditStylesheetPre');
		CmsEvents::create_event( $modulename, 'EditStylesheetPost');
		CmsEvents::create_event( $modulename, 'DeleteStylesheetPre');
		CmsEvents::create_event( $modulename, 'DeleteStylesheetPost');
		
		CmsEvents::create_event( $modulename, 'AddTemplatePre');
		CmsEvents::create_event( $modulename, 'AddTemplatePost');
		CmsEvents::create_event( $modulename, 'EditTemplatePre');
		CmsEvents::create_event( $modulename, 'EditTemplatePost');
		CmsEvents::create_event( $modulename, 'DeleteTemplatePre');
		CmsEvents::create_event( $modulename, 'DeleteTemplatePost');
		CmsEvents::create_event( $modulename, 'TemplatePreCompile');
		CmsEvents::create_event( $modulename, 'TemplatePostCompile');
		
		CmsEvents::create_event( $modulename, 'AddGlobalContentPre');
		CmsEvents::create_event( $modulename, 'AddGlobalContentPost');
		CmsEvents::create_event( $modulename, 'EditGlobalContentPre');
		CmsEvents::create_event( $modulename, 'EditGlobalContentPost');
		CmsEvents::create_event( $modulename, 'DeleteGlobalContentPre');
		CmsEvents::create_event( $modulename, 'DeleteGlobalContentPost');
		CmsEvents::create_event( $modulename, 'GlobalContentPreCompile');
		CmsEvents::create_event( $modulename, 'GlobalContentPostCompile');
		
		CmsEvents::create_event( $modulename, 'ContentEditPre');
		CmsEvents::create_event( $modulename, 'ContentEditPost');
		CmsEvents::create_event( $modulename, 'ContentDeletePre');
		CmsEvents::create_event( $modulename, 'ContentDeletePost');
		
		CmsEvents::create_event( $modulename, 'AddUserDefinedTagPre');
		CmsEvents::create_event( $modulename, 'AddUserDefinedTagPost');
		CmsEvents::create_event( $modulename, 'EditUserDefinedTagPre');
		CmsEvents::create_event( $modulename, 'EditUserDefinedTagPost');
		CmsEvents::create_event( $modulename, 'DeleteUserDefinedTagPre');
		CmsEvents::create_event( $modulename, 'DeleteUserDefinedTagPost');
		
		CmsEvents::create_event( $modulename, 'ModuleInstalled');
		CmsEvents::create_event( $modulename, 'ModuleUninstalled');
		CmsEvents::create_event( $modulename, 'ModuleUpgraded');
		
		CmsEvents::create_event( $modulename, 'ContentStylesheet');
		CmsEvents::create_event( $modulename, 'ContentPreCompile');
		CmsEvents::create_event( $modulename, 'ContentPostCompile');
		CmsEvents::create_event( $modulename, 'ContentPostRender');
		CmsEvents::create_event( $modulename, 'SmartyPreCompile');
		CmsEvents::create_event( $modulename, 'SmartyPostCompile');
	}
	
	/**
	 * Deprecated.  Use setup_core_events instead.
	 **/
	public static function SetupCoreEvents()
	{
		CmsEvents::setup_core_events();
	}
}

class Events extends CmsEvents {}

# vim:ts=4 sw=4 noet
?>
