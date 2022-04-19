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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * Classes and utilities for working with user preferences.
 * @package CMS
 * @license GPL
 */


/**
 * A static class for preferences stored with admin user accounts
 *
 * @package CMS
 * @license GPL
 * @since 1.10
 * @author Robert Campbell (calguy1000@gmail.com)
 */
final class cms_userprefs
{
	/**
	 * @ignore
	 */
	private static $_prefs;

	/**
	 * @ignore
	 */
	private function __construct() {}

	/**
	 * @ignore
	 */
	private static function _read($userid)
	{
		if( is_array(self::$_prefs) && isset(self::$_prefs[$userid]) && is_array(self::$_prefs[$userid]) ) return;

		$db = CmsApp::get_instance()->GetDb();
		$query = 'SELECT preference,value FROM '.CMS_DB_PREFIX.'userprefs WHERE user_id = ?';
		$dbr = $db->GetArray($query,array($userid));
		if( is_array($dbr) ) {
			if( !is_array(self::$_prefs) ) self::$_prefs = array();
			self::$_prefs[$userid] = array();
			for( $i = 0, $n = count($dbr); $i < $n; $i++ ) {
				$row = $dbr[$i];
				self::$_prefs[$userid][$row['preference']] = $row['value'];
			}
		}
	}

	/**
	 * @ignore
	 */
	private static function _userid()
	{
		return get_userid(false);
	}

	/**
	 * @ignore
	 */
	private static function _reset()
	{
		self::$_prefs = null;
	}

	/**
	 * A method to get a preference for a specific user
	 *
	 * @param int $userid The specified user id.
	 * @param string $key The preference name
	 * @param mixed $dflt The default value.
	 * @return string
	 */
	public static function get_for_user($userid,$key,$dflt = '')
	{
		self::_read($userid);
		if( isset(self::$_prefs[$userid][$key]) ) return self::$_prefs[$userid][$key];
		return $dflt;
	}


	/**
	 * Get a user preference
	 *
	 * @param string $key The preference name
	 * @param string $dflt A default value if the preference could not be found
	 * @return string
	 */
	public static function get($key,$dflt = '')
	{
		return self::get_for_user(self::_userid(),$key,$dflt);
	}

	/**
	 * Return an array of all user preferences
	 *
	 * @param int $userid
	 * @return array Associative array of preferences and values.
	 */
	public static function get_all_for_user($userid)
	{
		$userid = (int)$userid;
		self::_read($userid);
		if( isset(self::$_prefs[$userid]) ) return self::$_prefs[$userid];
	}

	/**
	 * A method to test if a preference exists for a user
	 *
	 * @param int $userid the user id
	 * @param string $key The preference name
	 * @return bool
	 */
	public static function exists_for_user($userid,$key)
	{
		$userid = (int)$userid;
		self::_read($userid);
		if( in_array($key,array_keys(self::$_prefs[$userid])) ) return TRUE;
		return FALSE;
	}


	/**
	 * A method to test if a preference exists for the current user
	 *
	 * @param string $key The preference name
	 * @return bool
	 */
	public static function exists($key)
	{
		return self::exists_for_user(self::_userid(),$key);
	}


	/**
	 * A method to set a preference for a specific user
	 *
	 * @param int $userid The user id
	 * @param string $key The preference name
	 * @param string $value The preference value
	 */
	public static function set_for_user($userid,$key,$value)
	{
		$userid = (int)$userid;
		self::_read($userid);
		$db = CmsApp::get_instance()->GetDb();
		if( !self::exists_for_user($userid,$key) ) {
			$query = 'INSERT INTO '.CMS_DB_PREFIX.'userprefs (user_id,preference,value) VALUES (?,?,?)';
			$dbr = $db->Execute($query,array($userid,$key,$value));
		}
		else {
			$query = 'UPDATE '.CMS_DB_PREFIX.'userprefs SET value = ? WHERE user_id = ? AND preference = ?';
			$dbr = $db->Execute($query,array($value,$userid,$key));
		}
		self::$_prefs[$userid][$key] = $value;
	}


	/**
	 * A method to set a preference for the current logged in user.
	 *
	 * @param string $key The preference name
	 * @param string $value The preference value
	 */
	public static function set($key,$value)
	{
		return self::set_for_user(self::_userid(),$key,$value);
	}


	/**
	 * A method to remove a method for the user
	 *
	 * @param int $userid The user id
	 * @param string $key (optional) The preference name.  If not specified, all preferences for this user will be removed.
	 * @param bool $like (optional) wether or not to use approximation in the preference name
	 */
	public static function remove_for_user($userid,$key = '',$like = FALSE)
	{
		$userid = (int)$userid;
		self::_read($userid);
		$parms = array();
		$query = 'DELETE FROM '.CMS_DB_PREFIX.'userprefs WHERE user_id = ?';
		$parms[] = $userid;
		if( $key ) {
			$query2 = ' AND preference = ?';
			if( $like ) {
				$query2 = ' AND preference LIKE ?';
				$key .= '%';
			}
			$query .= $query2;
			$parms[] = $key;
		}
		$db = CmsApp::get_instance()->GetDb();
		$db->Execute($query,$parms);
		self::_reset();
	}


	/**
	 * A method to remove a preference for the current user
	 *
	 * @param string $key The preference name.
	 * @param bool $like (optional) wether or not to use approximation in the preference name
	 */
	public static function remove($key,$like = FALSE)
	{
		return self::remove_for_user(self::_userid(),$key,$like);
	}
} // end of class

/**
 * Retrieve the value of the named preference for the given userid.
 *
 * @deprecated
 * @since 0.3
 * @see cms_siteprefs::get_for_user
 * @param int $userid The user id
 * @param string  $prefname The preference name
 * @param mixed   $default The default value if the preference is not set for the given user id.
 * @return mixed
 */
function get_preference($userid, $prefname, $default='')
{
  return cms_userprefs::get_for_user($userid,$prefname,$default);
}

/**
 * Sets the given perference for the given userid with the given value.
 *
 * @deprecated
 * @since 0.3
 * @see cms_siteprefs::set_for_user
 * @param int $userid The user id
 * @param string  $prefname The preference name
 * @param mixed   $value The preference value (will be stored as a string)
 */
function set_preference($userid, $prefname, $value)
{
  return cms_userprefs::set_for_user($userid, $prefname,$value);
}

#
# EOF
#
?>