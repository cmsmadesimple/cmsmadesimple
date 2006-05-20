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
   * Return the name of a user tag defined as a handler
   * for an event.
   *
   * @params string $modulename The name of the module sending the event
   * @params string $eventname  The name of the event
   *
   * @returns mixed If successful, string.  If it fails, false.
   */
  function GetEventHandler( $modulename, $eventname )
  {
    global $gCms;
    $db = &$gCms->GetDb();

    $q = "SELECT handler_name FROM ".cms_db_prefix()."eventhandlers WHERE
          module_name = ? AND event_name = ?";
    $dbresult = $db->Execute( $query, array( $modulename, $eventname ));
    if( $dbresult !== false )
      {
	$row = $dbresult->FetchRow();
	return $row['handler_name'];
      }
    return false;
  }


  /**
   * Set an event handler for a module event
   *
   * @params string $modulename The name of the module sending the event
   * @params string $eventname  The name of the event
   * @params string $tagname    The name of a user defined tag
   *
   * @returns mixed If successful, true.  If it fails, false.
   */
  function SetEventHandler( $modulename, $eventname, $tagname )
  {
    global $gCms;
    $db = &$gCms->GetDb();

    $q = "UPDATE ".cms_db_prefix()."eventhandlers SET handler_name = ?
          WHERE module_name = ?, and event_name = ?";
    $dbresult = $db->Execute( $q, array( $tagname, $modulename, $eventname ));
    if( $dbresult !== false )
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
  function RemoveEventHandler( $modulename, $eventname )
  {
    global $gCms;
    $db = &$gCms->GetDb();

    $q = "UPDATE ".cms_db_prefix()."eventhandlers 
          SET handler_name = ?
          WHERE module_name = ? AND event_name = ?";
    $dbresult = $db->Execute( $q, array( null, $modulename, $eventname ) );
    if( $dbresult !== false )
      {
	return true;
      }
    return false;
  }

} // class
# vim:ts=4 sw=4 noet
?>
