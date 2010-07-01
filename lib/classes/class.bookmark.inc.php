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
	 * @return mixed If successful, true.  If it fails, false.
	 */
	function Save()
	{
		$result = false;
		global $gCms;
		$bookops =& $gCms->GetBookmarkOperations();
		
		if ($this->bookmark_id > -1)
		{
			$result = $bookops->UpdateBookmark($this);
		}
		else
		{
			$newid = $bookops->InsertBookmark($this);
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
	 * @return mixed If successful, true.  If it fails, false.
	 */
	function Delete()
	{
		$result = false;
		global $gCms;
		$bookops =& $gCms->GetBookmarkOperations();

		if ($this->bookmark_id > -1)
		{
			$result = $bookops->DeleteBookmarkByID($this->bookmark_id);
			if ($result)
			{
				$this->SetInitialValues();
			}
		}

		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>
