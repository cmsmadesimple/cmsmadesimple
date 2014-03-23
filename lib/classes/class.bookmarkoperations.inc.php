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
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Bookmark related functions.
 *
 * @package CMS
 * @license GPL
 */


/**
 * Class for doing bookmark related functions.  Maybe of the Bookmark object functions
 * are just wrappers around these.
 *
 * @package CMS
 * @license GPL
 */
class BookmarkOperations
{
  /**
   * Prepares a url for saving by replacing security tags with a holder
   * string so it can be replaced when retrieved and not break security.
   *
   * @param string $url The url to save
   * @return string The fixed url
   * @internal
   */
  private function _prep_for_saving($url)
  {
	  $config = cmsms()->GetConfig();
	  $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

	  $map = array('[SECURITYTAG]'=>$urlext,'[ROOT_URL]'=>$config['root_url']);
	  foreach( $map as $to => $from ) {
		  $url = str_replace($from,$to,$url);
      }
	  return $url;
  }

  /**
   * Prepares a url for displaying by replacing the holder for the security
   * tag with the actual value.
   *
   * @param string $url The url to display
   * @return string The fixed url
   * @internal
   */
  private function _prep_for_display($url)
  {
	  $config = cmsms()->GetConfig();
	  $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

	  $map = array('[SECURITYTAG]'=>$urlext,'[ROOT_URL]'=>$config['root_url']);
	  foreach( $map as $from => $to ) {
		  $url = str_replace($from,$to,$url);
      }

	  $url = str_replace($from,$to,$url);
	  return $url;
  }

  /**
   * Gets a list of all bookmarks for a given user
   *
   * @param int $user_id The desired user id.
   * @return array An array of Bookmark objects
   */
  public function LoadBookmarks($user_id)
  {
	  $gCms = cmsms();
	  $db = $gCms->GetDb();
	  $config = $gCms->GetConfig();

	  $result = array();
	  $query = "SELECT bookmark_id, user_id, title, url FROM ".cms_db_prefix()."admin_bookmarks WHERE user_id = ? ORDER BY title";
	  $dbresult = $db->Execute($query, array($user_id));

	  while ($dbresult && $row = $dbresult->FetchRow()) {
		  $onemark = new Bookmark();
		  $onemark->bookmark_id = $row['bookmark_id'];
		  $onemark->user_id = $row['user_id'];
		  $onemark->url = $this->_prep_for_display($row['url']);
		  $onemark->title = $row['title'];
		  $result[] = $onemark;
	  }

	  return $result;
  }

	/**
	 * Loads a bookmark by bookmark_id.
	 *
	 * @param int $id bookmark_id to load
	 * @return Bookmark
	 * @since 0.6.1
	 */
	function &LoadBookmarkByID($id)
	{
		$result = null;
		$db = cmsms()->GetDb();

		$query = "SELECT bookmark_id, user_id, title, url FROM ".cms_db_prefix()."admin_bookmarks WHERE bookmark_id = ?";
		$dbresult = $db->Execute($query, array($id));

		while ($dbresult && $row = $dbresult->FetchRow()) {
			$onemark = new Bookmark();
			$onemark->bookmark_id = $row['bookmark_id'];
			$onemark->user_id = $row['user_id'];
			$onemark->url = $this->_prep_for_display($row['url']);
			$onemark->title = $row['title'];
			$result = $onemark;
		}

		return $result;
	}

	/**
	 * Saves a new bookmark to the database.
	 *
	 * @param Bookmark $bookmark Bookmark object to save
	 * @return int The new bookmark_id.  If it fails, it returns -1.
	 */
	function InsertBookmark(Bookmark $bookmark)
	{
		$result = -1;
		$db = cmsms()->GetDb();

		$bookmark->url = $this->_prep_for_saving($bookmark->url);
		$new_bookmark_id = $db->GenID(cms_db_prefix()."admin_bookmarks_seq");
		$query = "INSERT INTO ".cms_db_prefix()."admin_bookmarks (bookmark_id, user_id, url, title) VALUES (?,?,?,?)";
		$dbresult = $db->Execute($query, array($new_bookmark_id, $bookmark->user_id, $bookmark->url, $bookmark->title));
		if ($dbresult !== false) $result = $new_bookmark_id;

		return $result;
	}

	/**
	 * Updates an existing bookmark in the database.
	 *
	 * @param Bookmark $bookmark object to save
	 * @return bool
	 */
	function UpdateBookmark(Bookmark $bookmark)
	{
		$result = false;
		$db = cmsms()->GetDb();

		$bookmark->url = $this->_prep_for_saving($bookmark->url);
		$query = "UPDATE ".cms_db_prefix()."admin_bookmarks SET user_id = ?, title = ?, url = ? WHERE bookmark_id = ?";
		$dbresult = $db->Execute($query, array($bookmark->user_id, $bookmark->title, $bookmark->url, $bookmark->bookmark_id));
		if ($dbresult !== false) $result = true;

		return $result;
	}

	/**
	 * Deletes an existing bookmark from the database.
	 *
	 * @param int $id Id of the bookmark to delete
	 * @return bool
	 */
	function DeleteBookmarkByID($id)
	{
		$result = false;
		$db = cmsms()->GetDb();

		$query = "DELETE FROM ".cms_db_prefix()."admin_bookmarks where bookmark_id = ?";
		$dbresult = $db->Execute($query, array($id));
		if ($dbresult !== false) $result = true;
		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>