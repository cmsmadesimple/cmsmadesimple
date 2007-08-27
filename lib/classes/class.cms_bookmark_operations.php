<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id$

/**
 * Class for doing bookmark related functions.  Maybe of the Bookmark object functions
 * are just wrappers around these.
 *
 * @package CMS
 */

class CmsBookmarkOperations extends CmsObject
{
	static private $instance = NULL;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get an instance of this object, though most people should be using
	 * the static methods instead.  This is more for compatibility than
	 * anything else.
	 *
	 * @return CmsBookmarkOperations The instance of the singleton object.
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsBookmarkOperations();
		}
		return self::$instance;
	}

	/**
	 * Gets a list of all bookmarks for a given user
	 *
	 * @return array An array of CmsBookmark objects
	 */
	static public function load_bookmarks($user_id)
	{
		return cmsms()->bookmark->find_all_by_user_id($user_id, array('order' => 'title ASC'));
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsBookmarkOperations::load_bookmarks instead.
	 **/
	function LoadBookmarks($user_id)
	{
		return self::load_bookmarks($user_id);
	}

	/**
	 * Loads a bookmark by bookmark_id.
	 *
	 * @param mixed $id bookmark_id to load
	 * @return mixed If successful, the filled CmsBookmark object.  If it fails, it returns null.
	 * @since 0.6.1
	 **/
	static public function load_bookmark_by_id($id)
	{
		return cmsms()->bookmark->find_by_id($id);
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsBookmarkOperations::load_bookmark_by_id instead.
	 **/
	function LoadBookmarkByID($id)
	{
		return self::load_bookmark_by_id($id);
	}

	/**
	 * Saves a new bookmark to the database.
	 *
	 * @param mixed $bookmark CmsBookmark object to save
	 * @return mixed The new bookmark_id.  If it fails, it returns -1.
	 **/
	function InsertBookmark($bookmark)
	{
		$bookmark->save();
		return $bookmark->id;
	}

	/**
	 * Updates an existing bookmark in the database.
	 *
	 * @param mixed $bookmark CmsBookmark object to save
	 * @return mixed If successful, true.  If it fails, false.
	 **/
	function UpdateBookmark($bookmark)
	{
		return $bookmark->save();
	}

	/**
	 * Deletes an existing bookmark from the database.
	 *
	 * @param mixed $id Id of the bookmark to delete
	 * @return mixed If successful, true.  If it fails, false.
	 **/
	function DeleteBookmarkByID($id)
	{
		return cmsms()->bookmark->delete($id);
	}
}

/**
 * @deprecated Deprecated.  Use CmsBookmarkOperations instead.
 **/
class BookmarkOperations extends CmsBookmarkOperations
{
}

?>