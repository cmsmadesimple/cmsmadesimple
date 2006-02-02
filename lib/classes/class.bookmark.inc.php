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
#$Id$

/**
 * Bookmark class for admin
 *
 * @package CMS
 */
class Bookmark
{
	/**
	 * Bookmark ID
	 */
	var $bookmark_id;

	/**
	 * User(owner) ID
	 */
	var $user_id;

	/**
	 * Title
	 */
	var $title;

	/**
	 * Url
	 */
	var $url;

	/**
	 * Generic constructor.  Runs the SetInitialValues fuction.
	 */
	function Bookmark()
	{
		$this->SetInitialValues();
	}

	/**
	 * Sets object to some sane initial values
	 *
	 */
	function SetInitialValues()
	{
		$this->bookmark_id = -1;
		$this->title = '';
		$this->url = '';
		$this->user_id = -1;
	}


	/**
	 * Saves the bookmark to the database.  If no id is set, then a new record
	 * is created.  If the id is set, then the record is updated to all values
	 * in the Bookmark object.
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function Save()
	{
		$result = false;
		
		if ($this->bookmark_id > -1)
		{
			$result = BookmarkOperations::UpdateBookmark($this);
		}
		else
		{
			$newid = BookmarkOperations::InsertBookmark($this);
			if ($newid > -1)
			{
				$this->bookmark_id = $newid;
				$result = true;
			}

		}

		return $result;
	}

	/**
	 * Delete the record for this Bookmark from the database and resets
	 * all values to their initial values.
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function Delete()
	{
		$result = false;

		if ($this->bookmark_id > -1)
		{
			$result = BookmarkOperations::DeleteBookmarkByID($this->bookmark_id);
			if ($result)
			{
				$this->SetInitialValues();
			}
		}

		return $result;
	}
}

/**
 * Class for doing bookmark related functions.  Maybe of the Bookmark object functions
 * are just wrappers around these.
 *
 * @package CMS
 */
class BookmarkOperations
{
	/**
	 * Gets a list of all bookmarks for a given user
	 *
	 * @returns array An array of Bookmark objects
	 */
	function LoadBookmarks($user_id)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$result = array();

		$query = "SELECT bookmark_id, user_id, title, url FROM ".cms_db_prefix()."admin_bookmarks WHERE user_id = ? ORDER BY title";
		$dbresult = $db->Execute($query, array($user_id));

		if ($dbresult && $dbresult->RecordCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				$onemark = new Bookmark();
				$onemark->bookmark_id = $row['bookmark_id'];
				$onemark->user_id = $row['user_id'];
				$onemark->url = $row['url'];
				$onemark->title = $row['title'];
				array_push($result, $onemark);
			}
		}

		return $result;
	}

	/**
	 * Loads a bookmark by bookmark_id.
	 *
	 * @param mixed $id bookmark_id to load
	 *
	 * @returns mixed If successful, the filled Bookmark object.  If it fails, it returns false.
	 * @since 0.6.1
	 */
	function LoadBookmarkByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT bookmark_id, user_id, title, url FROM ".cms_db_prefix()."admin_bookmarks WHERE bookmark_id = ?";
		$dbresult = $db->Execute($query, array($id));

		if ($dbresult && $dbresult->RecordCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				$onemark = new Bookmark();
				$onemark->bookmark_id = $row['bookmark_id'];
				$onemark->user_id = $row['user_id'];
				$onemark->url = $row['url'];
				$onemark->title = $row['title'];
				$result = $onemark;
			}
		}

		return $result;
	}

	/**
	 * Saves a new bookmark to the database.
	 *
	 * @param mixed $bookmark Bookmark object to save
	 *
	 * @returns mixed The new bookmark_id.  If it fails, it returns -1.
	 */
	function InsertBookmark($bookmark)
	{
		$result = -1; 

		global $gCms;
		$db = &$gCms->GetDb();

		$new_bookmark_id = $db->GenID(cms_db_prefix()."admin_bookmarks_seq");
		$query = "INSERT INTO ".cms_db_prefix()."admin_bookmarks (bookmark_id, user_id, url, title) VALUES (?,?,?,?)";
		$dbresult = $db->Execute($query, array($new_bookmark_id, $bookmark->user_id, $bookmark->url, $bookmark->title));
		if ($dbresult !== false)
		{
			$result = $new_bookmark_id;
		}

		return $result;
	}

	/**
	 * Updates an existing bookmark in the database.
	 *
	 * @param mixed $bookmark Bookmark object to save
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function UpdateBookmark($bookmark)
	{
		$result = false; 

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "UPDATE ".cms_db_prefix()."admin_bookmarks SET user_id = ?, title = ?, url = ? WHERE bookmark_id = ?";
		$dbresult = $db->Execute($query, array($bookmark->user_id, $bookmark->title, $bookmark->url, $bookmark->bookmark_id));
		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	/**
	 * Deletes an existing bookmark from the database.
	 *
	 * @param mixed $id Id of the bookmark to delete
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function DeleteBookmarkByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "DELETE FROM ".cms_db_prefix()."admin_bookmarks where bookmark_id = ?";
		$db->Execute($query, array($id));

		if ($dbresult !== false)
		{
			$result = true;
		}
		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>
