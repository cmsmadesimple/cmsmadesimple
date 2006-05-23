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
 * UserTags class for admin
 *
 * @package CMS
 */
class UserTags
{
  /**
   * Add a named user defined tag into the database
   *
   * @params string $name User defined tag name
   * @params string $text Body of user defined tag
   *
   * @returns mixed If successful, true.  If it fails, false.
   */
  function AddUserTag( $name, $text )
  {
    // todo
  }


  /**
   * Retrieve the body of a user defined tag
   *
   * @params string $name User defined tag name
   *
   * @returns mixed If successfull, the body of the user tag (string).  If it fails, false
   */
  function GetUserTag( $name )
  {
    // todo
  }


  /**
   * Remove a named user defined tag from the database
   *
   * @params string $name User defined tag name
   *
   * @returns mixed If successful, true.  If it fails, false.
   */
  function RemoveUserTag( $name )
  {
    // todo
  }


  /**
   * Return a list (suitable for use in a pulldown) of user tags.
   *
   * @returns mixed If successful, an array.  If it fails, false.
   */
  function ListUserTags()
  {
    // copied from the module api
    global $gCms;
    $db =& $gCms->GetDb();
    
    $plugins = array();
    
    $query = 'SELECT userplugin_name FROM '.cms_db_prefix().'userplugins ORDER BY userplugin_name';
    $result = &$db->Execute($query);
    
    while ($result && !$result->EOF)
      {
	$plugins[$result->fields['userplugin_name']] =& $result->fields['userplugin_name'];
	$result->MoveNext();
      }
    
    return $plugins;
  }

} // class
# vim:ts=4 sw=4 noet
?>
