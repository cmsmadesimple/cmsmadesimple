<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
 * This file contains classes and constants for working with system and user defined events.
 *
 * @package CMS
 */
use CMSMS\internal\hook_mapping;

/**
 * Class for handling and dispatching system and user defined events.
 *
 * @package CMS
 * @license GPL
 * @deprecated
 */
final class Events
{
	/**
	 * @ignore
	 */
	static private $_handlercache;

	/**
	 * @ignore
	 */
	private function __construct() {}

	/**
	 * Inform the system about a new event that can be generated.
	 *
     * @deprecated
	 * @param string $modulename The name of the module that is sending the event
	 * @param string $eventname The name of the event
	 */
	static public function CreateEvent( $modulename, $eventname )
	{
        return;
	}


	/**
	 * Remove an event from the CMS system.
	 * This function removes all handlers to the event, and completely removes
	 * all references to this event from the database
	 *
	 * Note, only events created by this module can be removed.
	 *
     * @deprecated
	 * @param string $modulename The name of the module that is sending the event
	 * @param string $eventname The name of the event
	 */
	static public function RemoveEvent( $modulename, $eventname )
	{
        $hook = $modulename.'::'.$eventname;
        $mgr = \CmsApp::get_instance()->GetHookMappingManager();
        $mgr->remove_hook( $hook );
        $mgr->write_mapping();
	}


	/**
	 * Trigger an event.
	 * This function will call all registered event handlers for the event
	 *
     * @deprecated
	 * @param string $modulename The name of the module that is sending the event
	 * @param string $eventname The name of the event
	 * @param array $params The parameters associated with this event.
	 */
	static public function SendEvent( $modulename, $eventname, $params = array() )
	{
        $hook = $modulename.'::'.$eventname;
        HookManager::do_hook($hook,$params);
	}


	/**
	 * Add an event handler for a module event.
	 *
     * @deprecated
	 * @param string $modulename The name of the module sending the event
	 * @param string $eventname The name of the event
	 * @param string $tag_name The name of a user defined tag. If not passed, no user defined tag is set.
	 * @param string $module_handler The name of the module. If not passed, no module is set.
	 * @param bool $removable Can this event be removed from the list? Defaults to true.
	 * @return bool If successful, true.  If it fails, false.
	 */
	static public function AddEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false, $removable = true)
	{
		if( $tag_name == false && $module_handler == false ) return false;
		if( $tag_name != false && $module_handler != false ) return false;

        // use the hook mapper
        $mgr = \CmsApp::get_instance()->GetHookMappingManager();
        if( $tag_name ) {
            // deprecated
            $mgr->add_handler( $modulename.'::'.$eventname, hook_mapping::TYPE_SIMPLE, $tag_name );
        }
        else {
            $mgr->add_handler( $modulename.'::'.$eventname, hook_mapping::TYPE_MODULE, $module_handler);
        }
        $mgr->write_mapping();
        return true;
	}

	/**
	 * Remove an event handler for a particular event.
	 *
     * @deprecated
	 * @param string $modulename The name of the module sending the event
	 * @param string $eventname The name of the event
	 * @param string $tag_name The name of a user defined tag. If not passed, no user defined tag is set.
	 * @param string $module_handler The name of the module. If not passed, no module is set.
	 * @return bool If successful, true.  If it fails, false.
	 */
	static public function RemoveEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false )
	{
		if( $tag_name == false && $module_handler == false ) return false;
		if( $tag_name != false && $module_handler != false ) return false;

        // replace with calls to the hook_mapper
        $mgr = \CmsApp::get_instance()->GetHookMappingManager();
        if( $tag_name ) {
            // deprecated
            $mgr->remove_handler( $modulename.'::'.$eventname, hook_mapping::TYPE_SIMPLE, $tag_name );
        }
        else {
            $mgr->remove_handler( $modulename.'::'.$eventname, hook_mapping::TYPE_MODULE, $module_handler);
        }
        $mgr->write_mapping();
        return true;
	}

} // class
