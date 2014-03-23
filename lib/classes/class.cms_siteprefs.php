<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
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
 * A class and utilities for working with site preferences.
 * @package CMS
 */

/**
 * A class for working with site preferences
 *
 * @package CMS
 * @since 1.10
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 */
final class cms_siteprefs
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
	private static function _read()
	{
		if( is_array(self::$_prefs) ) return;
		$db = cmsms()->GetDb();

		if( !$db ) return;
		$query = 'SELECT sitepref_name,sitepref_value FROM '.cms_db_prefix().'siteprefs';
		$dbr = $db->GetArray($query);
		if( is_array($dbr) ) {
			self::$_prefs = array();
			for( $i = 0; $i < count($dbr); $i++ ) {
				$row = $dbr[$i];
				self::$_prefs[$row['sitepref_name']] = $row['sitepref_value'];
			}
		}
	}

	/**
	 * @ignore
	 */
	private static function _reset()
	{
		self::$_prefs = null;
	}

	/**
	 * Retrieve a site preference
	 *
	 * @param string $key The preference name
	 * @param string $dflt Optional default value
	 * @return string
	 */
	public static function get($key,$dflt = '')
	{
		self::_read();
		if( isset(self::$_prefs[$key]) )  return self::$_prefs[$key];
		return $dflt;
	}


	/**
	 * Test if a site preference exists
	 *
	 * @param string $key The preference name
	 * @return bool
	 */
	public static function exists($key)
	{
		self::_read();
		if( is_array(self::$_prefs) && in_array($key,array_keys(self::$_prefs)) ) return TRUE;
		return FALSE;
	}


	/**
	 * Set a site preference
	 *
	 * @param string $key The preference name
	 * @param string $value The preference value
	 */
	public static function set($key,$value)
	{
		$db = cmsms()->GetDb();
		if( !self::exists($key) ) {
			$query = 'INSERT INTO '.cms_db_prefix().'siteprefs (sitepref_name, sitepref_value) VALUES (?,?)';
			$dbr = $db->Execute($query,array($key,$value));
		}
		else {
			$query = 'UPDATE '.cms_db_prefix().'siteprefs SET sitepref_value = ? WHERE sitepref_name = ?';
			$dbr = $db->Execute($query,array($value,$key));
		}
		self::$_prefs[$key] = $value;
	}


	/**
	 * Remove a site preference
	 *
	 * @param string $key The preference name
	 * @param bool $like Whether to use preference name approximation
	 */
	public static function remove($key,$like = FALSE)
	{
		$query = 'DELETE FROM '.cms_db_prefix().'siteprefs WHERE sitepref_name = ?';
		if( $like ) {
			$query = 'DELETE FROM '.cms_db_prefix().'siteprefs WHERE sitepref_name LIKE ?';
			$key .= '%';
		}
		$db = cmsms()->GetDb();
		$db->Execute($query,array($key));
		self::_reset();
	}

	/**
	 * List preferences by prefix.
	 *
	 * @param string $prefix
	 * @return mixed list of preferences name that match the prefix, or null
	 * @since 2.0
	 */
	public static function list_by_prefix($prefix)
	{
		if( !$prefix ) return;
		$query = 'SELECT sitepref_name FROM '.cms_db_prefix().'siteprefs WHERE sitepref_name LIKE ?';
		$db = cmsms()->GetDb();
		$dbr = $db->GetCol($query,array($prefix.'%'));
		if( is_array($dbr) && count($dbr) ) return $dbr;
	}
} // end of class

#
# EOF
#
# vim:ts=4 sw=4 noet
?>