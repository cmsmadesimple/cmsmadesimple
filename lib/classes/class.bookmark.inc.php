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
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * This file contains a class that defines a bookmark in the CMSMS admin console.
 * @package CMS
 */

/**
 * Bookmark class for the CMSMS admin console.
 *
 * @package CMS
 * @license GPL
 */
class Bookmark
{
	/**
	 * @var int $bookmark_id The bookmark id
	 */
	var $bookmark_id;

	/**
	 * @var int $user_id Admin user (owner) ID
	 */
	var $user_id;

	/**
	 * @var string $title The bookmark title
	 */
	var $title;

	/**
	 * @var string $url The bookmark URL
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
	 */
	function SetInitialValues()
	{
		$this->bookmark_id = -1;
		$this->title = '';
		$this->url = '';
		$this->user_id = -1;
	}


	/**
	 * Saves the bookmark to the database.
	 *
	 * If no id is set, then a new record is created.
	 * Otherwise, the record is updated to all values in the Bookmark object.
	 *
	 * @return bool
	 */
	function Save()
	{
		$result = false;
		$bookops = cmsms()->GetBookmarkOperations();

		if ($this->bookmark_id > -1) {
			$result = $bookops->UpdateBookmark($this);
		}
		else {
			$newid = $bookops->InsertBookmark($this);
			if ($newid > -1) {
				$this->bookmark_id = $newid;
				$result = true;
			}

		}

		return $result;
	}

	/**
	 * Delete the record for this Bookmark from the database.
	 * All values in the object are reset to their initial values.
	 *
	 * @return bool
	 */
	function Delete()
	{
		$result = false;
		$bookops = cmsms()->GetBookmarkOperations();

		if ($this->bookmark_id > -1) {
			$result = $bookops->DeleteBookmarkByID($this->bookmark_id);
			if ($result) $this->SetInitialValues();
		}

		return $result;
	}
}

?>