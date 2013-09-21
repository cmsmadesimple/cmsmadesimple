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
#
#$Id: class.bookmark.inc.php 2746 2006-05-09 01:18:15Z wishy $

/**
 * @package CMS 
 */

/**
 * Class for handling and dispatching system and user defined events.
 *
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
final class Events
{ 
	static private $_handlercache;

	/**
	 * Inform the system about a new event that can be generated
	 *
	 * @param string $modulename The name of the module that is sending the event
	 * @param string $eventname The name of the event
	 * @return void
	 */
	static public function CreateEvent( $modulename, $eventname )
	{
		$db = cmsms()->GetDb();
		$count = $db->GetOne('SELECT count(*) from '.cms_db_prefix().'events where originator = ? and event_name = ?', array($modulename, $eventname));
		if ($count < 1)
		{
			$id = $db->GenID( cms_db_prefix()."events_seq" );
			$q = "INSERT INTO ".cms_db_prefix()."events values (?,?,?)";
			$db->Execute( $q, array( $modulename, $eventname, $id ));
		}
	}
	

	/**
	 * Remove an event from the CMS system
	 * This function removes all handlers to the event, and completely removes
	 * all references to this event from the database
	 *
	 * Note, only events created by this module can be removed.
	 *
	 * @param string $modulename The name of the module that is sending the event
	 * @param string $eventname The name of the event
	 * @return void
	 */
	static public function RemoveEvent( $modulename, $eventname )
	{
		$gCms = cmsms();
		$db = $gCms->GetDb();

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
	 * Trigger an event.
	 * This function will call all registered event handlers for the event
	 *
	 * @param string $modulename The name of the module that is sending the event
	 * @param string $eventname The name of the event
	 * @param array $params The parameters associated with this event.
	 * @return void
	 */
	static public function SendEvent( $modulename, $eventname, $params = array() )
	{
		global $CMS_INSTALL_PAGE;
		if( isset($CMS_INSTALL_PAGE) ) return;

		$gCms = cmsms();
		$results = Events::ListEventHandlers($modulename, $eventname);
		
		if ($results != false) {
			foreach( $results as $row ) {
				if( isset( $row['tag_name'] ) && $row['tag_name'] != '' ) {
					debug_buffer('calling user tag ' . $row['tag_name'] . ' from event ' . $eventname);
					$usertagops = $gCms->GetUserTagOperations();
					$usertagops->CallUserTag( $row['tag_name'], $params );
				}
				else if( isset( $row['module_name'] ) && $row['module_name'] != '' ) {
					// here's a quick check to make sure that we're not calling the module
					// DoEvent function for an event originated by the same module.
					if( $row['module_name'] == $modulename ) {
						continue;
					}

					// and call the module event handler.
					$obj =& CMSModule::GetModuleInstance($row['module_name']);
					if( $obj ) {
						debug_buffer('calling module ' . $row['module_name'] . ' from event ' . $eventname);
						$obj->DoEvent( $modulename, $eventname, $params );
					}
				}
			}
		}
	}
	
	
	/**
	 * Return the list of event handlers for a particular event
	 *
	 * @param string $modulename The name of the module sending the event
	 * @param string $eventname The name of the event
	 * @return mixed If successful, an array of arrays, each element
	 *               in the array contains two elements 'handler_name', and 'module_handler',
	 *               any one of these could be null. If it fails, false is returned.
	 */
	static public function ListEventHandlers( $modulename, $eventname )
	{
		$gCms = cmsms();
		$db = $gCms->GetDb();
		
		$params['module'] = $modulename;
		$params['event'] = $eventname;	
		$handlers = array();
		
		if( !is_array(self::$_handlercache) ) {
			$q = "SELECT eh.tag_name, eh.module_name, e.originator, e.event_name, eh.handler_order, eh.handler_id, eh.removable FROM ".cms_db_prefix()."event_handlers eh
				INNER JOIN ".cms_db_prefix()."events e ON e.event_id = eh.event_id
				ORDER BY eh.handler_order ASC";

			self::$_handlercache = $db->GetArray( $q );
		}

		if( is_array(self::$_handlercache) && count(self::$_handlercache) ) {
			foreach (self::$_handlercache as $row) {
				if ($row['originator'] == $modulename && $row['event_name'] == $eventname) $handlers[] = $row;
			}
		}
		
		if (count($handlers) > 0) {
			return $handlers;
		}
		else {
			return false;
		}
	}


	/**
	 * Get a list of all of the known events
	 *
	 * @return mixed If successful, a list of all the known events.  If it fails, false
	 */
	static public function ListEvents()
	{
		$gCms = cmsms();
		$db = $gCms->GetDb();

		//$q = "SELECT * FROM ".cms_db_prefix()."events ORDER BY originator,event_name";
		$q = 'SELECT e.*, count(eh.event_id) as usage_count FROM '.cms_db_prefix().
			'events e left outer join '.cms_db_prefix().
			'event_handlers eh on e.event_id=eh.event_id GROUP BY e.event_id ORDER BY originator,event_name';
		
		$dbresult = $db->Execute( $q );
		if( $dbresult == false )
		{
			return false;
		}

		$result = array();
		while( $row = $dbresult->FetchRow() )
		{
			if(!cms_utils::module_available($row['originator']) && $row['originator'] !== 'Core')
				continue;
				
			$result[] = $row;
		}
		return $result;
	}


	/**
	 * Add an event handler for a module event
	 *
	 * @param string $modulename The name of the module sending the event
	 * @param string $eventname The name of the event
	 * @param string $tag_name The name of a user defined tag. If not passed, no user defined tag is set.
	 * @param string $module_handler The name of the module. If not passed, no module is set.
	 * @param boolean $removable Can this event be removed from the list? Defaults to true.
	 * @return boolean If successful, true.  If it fails, false.
	 */
	static public function AddEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false, $removable = true)
	{
		if( $tag_name == false && $module_handler == false )
		{
			return false;
		}
		if( $tag_name != false && $module_handler != false )
		{
			return false;
		}

		$gCms = cmsms();
		$db = $gCms->GetDb();

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
	 * Remove an event handler for a particular event
	 *
	 * @param string $modulename The name of the module sending the event
	 * @param string $eventname The name of the event
	 * @param string $tag_name The name of a user defined tag. If not passed, no user defined tag is set.
	 * @param string $module_handler The name of the module. If not passed, no module is set.
	 * @return boolean If successful, true.  If it fails, false.
	 */
	static public function RemoveEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false )
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

		$gCms = cmsms();
		$db = $gCms->GetDb();

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
	 * Clears all the event handlers for the given event.
	 *
	 * @param string $modulename The name of the module sending the event
	 * @param string $eventname The name of the event
	 * @return boolean If successful, true.  If it fails, false.
	 */
	static public function RemoveAllEventHandlers( $modulename, $eventname )
	{
		$gCms = cmsms();
		$db = $gCms->GetDb();

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
	 * Place to handle the help messages for core events.  Basically just going to
	 * call out to the lang() function.
	 *
	 * @param string $eventname The name of the event
	 * @return string Returns the help string for the event.  Empty string if nothing
	 *                is found.
	 */
	static function GetEventHelp($eventname)
	{
		return lang('event_help_'.strtolower($eventname));
	}
	
	
	
	/**
	 * Place to handle the description strings for core events.  Basically just going to
	 * call out to the lang() function.
	 *
	 * @param string $eventname The name of the event
	 * @return string Returns the description string for the event.  Empty string if nothing
	 *                is found.
	 */
	static public function GetEventDescription($eventname)
	{
		return lang('event_desc_'.strtolower($eventname));
	}
	
	/**
	 * Used to setup all core event on system installation.
	 *
	 * @ignore
	 * @return void
	 */
	static public function SetupCoreEvents()
	{
		$modulename = 'Core';

		Events::CreateEvent( $modulename, 'LoginPost');
		Events::CreateEvent( $modulename, 'LogoutPost');
		
		Events::CreateEvent( $modulename, 'AddUserPre');
		Events::CreateEvent( $modulename, 'AddUserPost');
		Events::CreateEvent( $modulename, 'EditUserPre');
		Events::CreateEvent( $modulename, 'EditUserPost');
		Events::CreateEvent( $modulename, 'DeleteUserPre');
		Events::CreateEvent( $modulename, 'DeleteUserPost');
		
		Events::CreateEvent( $modulename, 'AddGroupPre');
		Events::CreateEvent( $modulename, 'AddGroupPost');
		Events::CreateEvent( $modulename, 'EditGroupPre');
		Events::CreateEvent( $modulename, 'EditGroupPost');
		Events::CreateEvent( $modulename, 'DeleteGroupPre');
		Events::CreateEvent( $modulename, 'DeleteGroupPost');
		
		Events::CreateEvent( $modulename, 'AddStylesheetPre');
		Events::CreateEvent( $modulename, 'AddStylesheetPost');
		Events::CreateEvent( $modulename, 'EditStylesheetPre');
		Events::CreateEvent( $modulename, 'EditStylesheetPost');
		Events::CreateEvent( $modulename, 'DeleteStylesheetPre');
		Events::CreateEvent( $modulename, 'DeleteStylesheetPost');
		
		Events::CreateEvent( $modulename, 'AddTemplatePre');
		Events::CreateEvent( $modulename, 'AddTemplatePost');
		Events::CreateEvent( $modulename, 'EditTemplatePre');
		Events::CreateEvent( $modulename, 'EditTemplatePost');
		Events::CreateEvent( $modulename, 'DeleteTemplatePre');
		Events::CreateEvent( $modulename, 'DeleteTemplatePost');
		Events::CreateEvent( $modulename, 'TemplatePreCompile');
		Events::CreateEvent( $modulename, 'TemplatePostCompile');
		
		Events::CreateEvent( $modulename, 'AddGlobalContentPre');
		Events::CreateEvent( $modulename, 'AddGlobalContentPost');
		Events::CreateEvent( $modulename, 'EditGlobalContentPre');
		Events::CreateEvent( $modulename, 'EditGlobalContentPost');
		Events::CreateEvent( $modulename, 'DeleteGlobalContentPre');
		Events::CreateEvent( $modulename, 'DeleteGlobalContentPost');
		Events::CreateEvent( $modulename, 'GlobalContentPreCompile');
		Events::CreateEvent( $modulename, 'GlobalContentPostCompile');
		
		Events::CreateEvent( $modulename, 'ContentEditPre');
		Events::CreateEvent( $modulename, 'ContentEditPost');
		Events::CreateEvent( $modulename, 'ContentDeletePre');
		Events::CreateEvent( $modulename, 'ContentDeletePost');
		
		Events::CreateEvent( $modulename, 'AddUserDefinedTagPre');
		Events::CreateEvent( $modulename, 'AddUserDefinedTagPost');
		Events::CreateEvent( $modulename, 'EditUserDefinedTagPre');
		Events::CreateEvent( $modulename, 'EditUserDefinedTagPost');
		Events::CreateEvent( $modulename, 'DeleteUserDefinedTagPre');
		Events::CreateEvent( $modulename, 'DeleteUserDefinedTagPost');
		
		Events::CreateEvent( $modulename, 'ModuleInstalled');
		Events::CreateEvent( $modulename, 'ModuleUninstalled');
		Events::CreateEvent( $modulename, 'ModuleUpgraded');
		
		Events::CreateEvent( $modulename, 'ContentStylesheet');
		Events::CreateEvent( $modulename, 'ContentPreCompile');
		Events::CreateEvent( $modulename, 'ContentPostCompile');
		Events::CreateEvent( $modulename, 'ContentPostRender');
		Events::CreateEvent( $modulename, 'SmartyPreCompile');
		Events::CreateEvent( $modulename, 'SmartyPostCompile');
		
		Events::CreateEvent( $modulename, 'ChangeGroupAssignPre');
		Events::CreateEvent( $modulename, 'ChangeGroupAssignPost');
	}

} // class
# vim:ts=4 sw=4 noet
?>
