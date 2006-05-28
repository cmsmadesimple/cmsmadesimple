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

    // find the id
    $q = "SELECT event_id FROM ".cms_db_prefix()."events WHERE 
          originator = ? AND event_name = ?";
    $dbresult = $db->Execute( $q, array( $modulename, $eventname ) );
    if( $dbresult == false || $dbresult->RowCount() == 0 )
      {
	// query failed, event not found
	return false;
      }
    $row = $dbresult->FetchRow();
    $id = $row['event_id'];

    // now get the handlers
    $q = "SELECT tag_name,module_name,handler_order,handler_id FROM ".cms_db_prefix()."event_handlers WHERE
          event_id = ? ORDER BY handler_order ASC";
    $dbresult = $db->Execute( $q, array( $id ));

    if( $dbresult !== false )
      {
	$result = array();
	while( $row = $dbresult->FetchRow() )
	  {
	    $result[] = $row;
	  }
	return $result;
      }
    echo "DEBUG bad $q, $id<br/>";
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

    $q = "SELECT * FROM ".cms_db_prefix()."events";
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
  function AddEventHandler( $modulename, $eventname, $tag_name = false, $module_handler = false,
			    $removable = true)
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

    // find the id
    $q = "SELECT event_id FROM ".cms_db_prefix()."events WHERE 
          originator = ? AND event_name = ?";
    $dbresult = $db->Execute( $q, array( $modulename, $eventname ) );
    if( $dbresult == false || $dbresult->RowCount() == 0 )
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
    if( $dbresult != false && $dbresult->RowCount() > 0 )
      {
	// hmmm, something matches already
	return false;
      }

    // now see if we can get a new id
    $order = 1;
    $q = "SELECT max(handler_order) AS newid FROM ".cms_db_prefix()."event_handlers
          WHERE event_id = ?";
    $dbresult = $db->Execute( $q, array( $id ) );
    if( $dbresult != false && $dbresult->RowCount() != 0)
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
    $params[] = $removable;
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

    // find the id
    $q = "SELECT event_id FROM ".cms_db_prefix()."events WHERE 
          originator = ? AND event_name = ?";
    $dbresult = $db->Execute( $q, array( $modulename, $eventname ) );
    if( $dbresult == false || $dbresult->RowCount() == 0 )
      {
	// query failed, event not found
	return false;
      }
    $row = $dbresult->FetchRow();
    $id = $row['event_id'];

    // delete the record
    $params = array( $id );
    $q = "DELETE FROM ".cms_db_prefix()."event_handlers;
          WHERE event_id = ? AND ";
    if( $modulename != false )
      {
	$q .- 'module_name = ?';
	$params[] = $module_handler;
      }
    else
      {
	$q .= 'tag_name = ?';
	$params[] = $tag_name;
      }
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

    // find the id
    $q = "SELECT event_id FROM ".cms_db_prefix()."events WHERE 
          originator = ? AND event_name = ?";
    $dbresult = $db->Execute( $q, array( $modulename, $eventname ) );
    if( $dbresult == false || $dbresult->RowCount() == 0 )
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

} // class
# vim:ts=4 sw=4 noet
?>
