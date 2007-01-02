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
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

class CmsRecentPageOperations extends CmsObject
{
	static private $instance = NULL;

	function __construct()
	{
		parent::__construct();
	}
	
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsRecentPageOperations();
		}
		return self::$instance;
	}
	
	/**
	 * Gets a list of all recent pages for a given user
	 *
	 * @returns array An array of RecentPage objects
	 */
	function LoadRecentPages($user_id)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$result = array();

		$query = "SELECT id, user_id, title, url, access_time FROM ".cms_db_prefix().
            "admin_recent_pages WHERE user_id = ? ORDER BY access_time DESC";
		$dbresult = $db->Execute($query, array($user_id));

		while ($dbresult && $row = $dbresult->FetchRow())
		{
			$onepage = new RecentPage();
			$onepage->id = $row['id'];
			$onepage->user_id = $row['user_id'];
			$onepage->url = $row['url'];
			$onepage->title = $row['title'];
			$onepage->timestamp = $row['access_time'];
			$result[] = $onepage;
		}

		return $result;
	}

	/**
	 * Saves a new page to the database.
	 *
	 * @param mixed $page RecentPage object to save
	 *
	 * @returns mixed The new id.  If it fails, it returns -1.
	 */
	function InsertPage($page)
	{
		$result = -1; 

		global $gCms;
		$db = &$gCms->GetDb();

		$new_page_id = $db->GenID(cms_db_prefix()."admin_recent_pages_seq");
		$time = $db->DBTimeStamp(time());
		$query = "INSERT INTO ".cms_db_prefix()."admin_recent_pages (id, user_id, url, title, access_time) VALUES (?,?,?,?,".$time.")";
		$dbresult = $db->Execute($query, array($new_page_id, $page->user_id, $page->url,
            $page->title));
		if ($dbresult !== false)
		{
			$result = $new_page_id;
		}

		return $result;
	}

	/**
	 * Purges old recent pages from the database.
	 *
	 * @param int $count - number of pages to preserve
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function PurgeOldPages($user_id, $count)
	{
		$result = false;
		$oldPages = array();

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT id FROM ".cms_db_prefix().
            "admin_recent_pages WHERE user_id = ? ORDER BY access_time DESC limit 10000 offset ?";
		$dbresult = $db->Execute($query, array($user_id,$count));
		while ($dbresult && $row = $dbresult->FetchRow())
		{
		  $oldPages[] = array( $row['id'] );
		}

		$query = "DELETE FROM ".cms_db_prefix()."admin_recent_pages where id=?";
		$db->Execute($query, $oldPages);

		if ($dbresult !== false)
		{
			$result = true;
		}
		return $result;
	}
}

class RecentPageOperations extends CmsRecentPageOperations
{
}

# vim:ts=4 sw=4 noet
?>