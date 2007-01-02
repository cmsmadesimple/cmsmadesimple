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
 * Recent Page class for admin
 *
 * @package CMS
 */
class CmsRecentPage extends CmsObject
{
	/**
	 * ID
	 */
	var $id;

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
	 * Timestamp
	 */
	var $timestamp;

	/**
	 * Generic constructor.  Runs the SetInitialValues fuction.
	 */
	function __construct()
	{
		parent::__construct();
		$this->SetInitialValues();
	}

	/**
	 * Sets object to some sane initial values
	 *
	 */
	function SetInitialValues()
	{
		$this->id = -1;
		$this->title = '';
		$this->url = '';
		$this->user_id = -1;
		$this->timestamp = -1;
	}

	/**
	 * Sets object attributes in one go
	 *
	 */
	function SetValues($title, $url, $userid)
	{
		$this->title = $title;
		$this->url = $url;
		$this->user_id = $userid;
	}

	/**
	 * Saves the page to the database, creating a new record.
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function Save()
	{
		return RecentPageOperations::InsertPage($this);
	}

	/**
	 * Purges oldest records from the database, preserving only the
     * n most-recent.
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function PurgeOldPages($userid,$count=5)
	{
        return RecentPageOperations::PurgeOldPages($userid,$count);
	}
}

class RecentPage extends CmsRecentPage
{
}

# vim:ts=4 sw=4 noet
?>