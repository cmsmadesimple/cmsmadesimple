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
#$Id$

//Clear out old values when the request shuts down
CmsEventManager::register_event_handler('Core:shutdown_soon', array('CmsKeyCache', 'expire_entries'));

/**
 * Class to represent a key/value cache.  The model is based on memcached, and will
 * work with memcached if a server exists, is configured, and the memcache module is
 * compiled into php.  Otherwise, the values will go into the cache table in the CMSMS
 * database.
 *
 * @author Ted Kulp
 */
class CmsKeyCache extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	public static function get_impl_class()
	{
		if(class_exists('Memcache') && CmsConfig::get('use_memcache') == true)
		{
			return CmsMemcacheKeyCache::get_instance();
		}
		return CmsAdodbKeyCache::get_instance();
	}
	
	/**
	 * Clears out any expired entries from the database
	 *
	 * @return void
	 * @author Ted Kulp
	 */
	public static function expire_entries()
	{
		return self::get_impl_class()->expire_entries();
	}
	
	/**
	 * Return the value with the given key.  If the key doesn't exist (or is expired)
	 * it will return null.
	 *
	 * @param string $key The key to look up
	 * @return mixed The value retrieved. null if no valid data for key is found.
	 * @author Ted Kulp
	 */
	public static function get($key)
	{
		return self::get_impl_class()->get($key);
	}
	
	/**
	 * Sets the value with the given key. Values are serialized before being inserted
	 * the database. Expiry is the number of seconds that this value is valid.  Once
	 * it expires, further lookups will be null. If expiry is set to 0, then it will
	 * be valid for 10 years (just an arbitrary far in the future).
	 *
	 * @param string $key The key to set
	 * @param string $value The value to set.  This will be serialized, so all values must be serializable.
	 * @param string $expiry Number of seconds it will be valid. 0 is equivalent to 10 years.
	 * @return void
	 * @author Ted Kulp
	 */
	public static function set($key, $value, $expiry = 3600)
	{
		return self::get_impl_class()->set($key, $value, $expiry);
	}
	
	/**
	 * Deletes the key/value pair from the database. This is a blind delete, so no
	 * check is done if it exists first.
	 *
	 * @param string $key The key of the pair to delete
	 * @return void
	 * @author Ted Kulp
	 */
	public static function delete($key)
	{
		return self::get_impl_class()->delete($key);
	}
}

# vim:ts=4 sw=4 noet
?>