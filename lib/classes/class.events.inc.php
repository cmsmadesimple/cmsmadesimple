<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (tedkulp@users.sf.net)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: class.bookmark.inc.php 2746 2006-05-09 01:18:15Z wishy $

/**
 * Events class for admin
 *
 * @package CMS
 */
class Events
{
  /**
   * Return the list of event handlers for a particular event
   *
   * @params string $modulename The name of the module sending the event
   * @params string $eventname  The name of the event
   *
   * @returns mixed If successful, an array of arrays, each element
   * in the array contains two elements 'handler_name', and 'module_handler',
   * any one of these could be null. If it fails, false is returned.
   */
  function ListEventHandlers( $modulename, $eventname )
  {
    global $gCms;
    $db = &$gCms->GetDb();

    $q = "SELECT handler_name,module_handler FROM ".cms_db_prefix()."eventhandlers WHERE
          module_name = ? AND event_name = ?";
    $dbresult = $db->Execute( $query, array( $modulename, $eventname ));

    if( $dbresult !== false )
      {
	$result = array();
	while( $row = $dbresult->FetchRow() )
	  {
	    $result[] = $row;
	  }
	return $result;
      }
    return false;
  }


  /**
   * Get a list of all of the known events
   *
   * @returns mixed If successful, a list of all the known events.  If it fails, false
   */
  function ListEvents()
  {
    global $gCms;
    $db = &$gCms->GetDb();

    $q = "SELECT * FROM ".cms_db_prefix()."eventhandlers";
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
   * Add an event handler for a module event
   *
   * @params string $modulename The name of the module sending the event
   * @params string $eventname  The name of the event
   * @params string $tagname    The name of a user defined tag
   *
   * @returns mixed If successful, true.  If it fails, false.
   */
  function AddEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false )
  {
    if( $tag_name == false && $module_handler == false )
      {
	return false;
      }
    if( $tag_name != false && $module_handler != false )
      {
	return false;
      }

    global $gCms;
    $db = &$gCms->GetDb();

    $params = array();
    $params[] = $tag_name;
    $q = "UPDATE ".cms_db_prefix()."eventhandlers SET handler_name = ?";
    if( $module_handler != false )
      {
	$q = "UPDATE ".cms_db_prefix()."eventhandlers SET module_handler = ?";
	$params = array( $module_handler );
      }
    $q .= "WHERE module_name = ? and event_name = ?";
    $params[] = $modulename;
    $params[] = $eventname;
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
   * @params string $modulename The name of the module sending the event
   * @params string $eventname  The name of the event
   *
   * @returns mixed If successful, true.  If it fails, false.
   */
  function RemoveEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false )
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

    global $gCms;
    $db = &$gCms->GetDb();

    $q = "UPDATE ".cms_db_prefix()."eventhandlers;
          SET $field = ? 
          WHERE module_name = ? AND event_name = ?";
    $dbresult = $db->Execute( $q, array( null, $modulename, $eventname ) );
    if( $dbresult == false )
      {
	return true;
      }
    return false;
  }


  function RemoveAllEventHandlers( $modulename, $eventname )
  {
    global $gCms;
    $db = &$gCms->GetDb();

    $q = "UPDATE ".cms_db_prefix()."eventhandlers;
          SET handler_name = ?, module_handler = ?
          WHERE module_name = ? AND event_name = ?";
    $dbresul = $db->Execute( $q, array( $modulename, $eventname ) );
    if( $dbresult == false )
      {
	return true;
      }
    return false;
  }

} // class
# vim:ts=4 sw=4 noet
?>
