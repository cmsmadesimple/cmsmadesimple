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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * @package CMS
 */


/**
 * A class for user preferences
 *
 * @package CMS
 * @since 1.10
 * @author Robert Campbell (calguy1000@gmail.com)
 */
final class cms_userprefs
{
  private static $_prefs;

  private function __construct() {}

  private static function _read($userid)
  {
	  if( is_array(self::$_prefs) && isset(self::$_prefs[$userid]) && is_array(self::$_prefs[$userid]) ) return;

	  $db = cmsms()->GetDb();
	  $query = 'SELECT preference,value FROM '.cms_db_prefix().'userprefs WHERE user_id = ?';
	  $dbr = $db->GetArray($query,array($userid));
	  if( is_array($dbr) )
		  {
			  if( !is_array(self::$_prefs) ) self::$_prefs = array();
			  self::$_prefs[$userid] = array();
			  for( $i = 0; $i < count($dbr); $i++ )
				  {
					  $row = $dbr[$i];
					  self::$_prefs[$userid][$row['preference']] = $row['value'];
				  }
		  }
  }

  private static function _userid($value = '')
  {
    return get_userid(false);
  }

  private static function _reset()
  {
    self::$_prefs = null;
  }

  /**
   * A method to get a preference for a specific user
   *
   * @param int The specified user id.
   * @param string The preference name
   * @param mixed The default value.
   * @return string
   */
  public static function get_for_user($userid,$key,$dflt = '')
  {
    self::_read($userid);
    if( isset(self::$_prefs[$userid][$key]) )
      {
	return self::$_prefs[$userid][$key];
      }
    return $dflt;
  }

  
  /**
   * Get a user preference
   *
   * @param string The preference name
   * @param string A default value if the preference could not be found
   * @return strung
   */
  public static function get($key,$dflt = '')
  {
    return self::get_for_user(self::_userid(),$key,$dflt);
  }


  /**
   * A method to test if a preference exists for a user
   *
   * @param int the user id
   * @param string The preference name
   * @return boolean
   */
  public static function exists_for_user($userid,$key)
  {
    self::_read($userid);
	if( in_array($key,array_keys(self::$_prefs[$userid])) ) return TRUE;
    return FALSE;
  }


  /**
   * A method to test if a preference exists for the current user
   *
   * @param string The preference name
   * @return boolean
   */
  public static function exists($key)
  {
    return self::exists_for_user(self::_userid(),$key);
  }


  /**
   * A method to set a preference for a specific user
   *
   * @param int The user id
   * @param string The preference name
   * @param string The preference value
   * @return void
   */
  public static function set_for_user($userid,$key,$value)
  {
    self::_read($userid);
    $db = cmsms()->GetDb();
    if( !self::exists_for_user($userid,$key) )
      {
		  $query = 'INSERT INTO '.cms_db_prefix().'userprefs (user_id,preference,value) VALUES (?,?,?)';
		  $dbr = $db->Execute($query,array($userid,$key,$value));
      }
    else
      {
		  $query = 'UPDATE '.cms_db_prefix().'userprefs SET value = ? WHERE user_id = ? AND preference = ?';
		  $dbr = $db->Execute($query,array($value,$userid,$key));
      }
    self::$_prefs[$userid][$key] = $value;
  }


  /**
   * A method to set a preference for the current logged in user.
   *
   * @param string The preference name
   * @param string The preference value
   * @return void
   */
  public static function set($key,$value)
  {
    return self::set_for_user(self::_userid(),$key,$value);
  }


  /**
   * A method to remove a method for the user
   *
   * @param int The user id
   * @param string (optional) The preference name.  If not specified, all preferences for this user will be removed.
   * @param boolean (optional) wether or not to use approximation in the preference name
   * @return void
   */
  public static function remove_for_user($userid,$key = '',$like = FALSE)
  {
    self::_read($userid);
	$parms = array();
	$query = 'DELETE FROM '.cms_db_prefix().'userprefs WHERE user_id = ?';
	$parms[] = $userid;
	if( $key )
	{
		$query2 = ' AND preference = ?';
		if( $like )
		{
			$query2 = ' AND preference LIKE ?';
			$key .= '%';
		}
		$query .= $query2;
		$parms[] = $key;
	}
    $db = cmsms()->GetDb();
    $db->Execute($query,$parms);
    self::_reset();
  }


  /**
   * A method to remove a preference for the current user
   *
   * @param string The preference name.
   * @param boolean (optional) wether or not to use approximation in the preference name
   * @return void
   */
  public static function remove($key,$like = FALSE)
  {
    return self::remove_for_user(self::_userid(),$key,$like);
  }
}

#
# EOF
#
?>