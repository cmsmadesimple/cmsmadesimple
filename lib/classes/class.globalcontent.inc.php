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

/**
 * @package CMS 
 */

/**
 * Global content object. These are abstract simple content objects (in contrast to CMS Content Types)
 * that can be subclassed and used for simple storage.
 *
 * @since 0.6
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class GlobalContent
{
	/**
	* Database ID for this Global Content Block
	* @access private
	*/
	var $id;
	/**
	* Name for this Global Content Block
	* @access private
	*/
	var $name;
	/**
	* Actual contents of this Global Content Block
	* @access private
	*/
	var $content;
	/**
	* Owner ID for this Global Content Block
	* @access private
	*/
	var $owner;
	/**
	* Modification date for this Global Content Block
	* @access private
	*/
	var $modified_date;

	/**
	 * Flag wether the wysiwyg should be enabled or not.
	 * @access private
	 */
	var $use_wysiwyg;

	/**
	 * The description for the Global Content Block
	 * @access private
	 */
	var $description;

	/**
	 * Constructor
	 */
	function GlobalContent()
	{
		$this->SetInitialValues();
	}

	/**
	 * Set attributes of global content block to blank (but predictable) values.
	 * @access private
	 */
	function SetInitialValues()
	{
		$this->id = -1;
		$this->name = '';
		$this->content = '';
		$this->owner = -1;
		$this->modified_date = -1;
		$this->use_wywiwyg = 1;
		$this->description = '';
	}

	/**
	* Returns the database ID for this Global Content Block
	* @final
	* @return int opaque database ID for the Global Content Block
	*/
	function Id()
	{
		return $this->id;
	}

	/**
	 * Returns the name for this Global Content Block
	 *
	 * @final
	 * @return string name of the global content block
	 */
	function Name()
	{
		return $this->name;
	}

	/**
	 * Returns the description for this global content block
	 *
	 * @final
	 * @return string Description of the global content block
	 */
	function Description()
	{
		return $this->description;
	}

	/**
	 * Saves the Global Content Block to the database
	 *
	 * @return boolean true indicates success, false indicates failure
	 */
	function Save()
	{
		$result = false;
		
		$gcbops = cmsms()->GetGlobalContentOperations();
		
		if ($this->id > -1)
		{
			$result = $gcbops->UpdateHtmlBlob($this);
		}
		else
		{
			$newid = $gcbops->InsertHtmlBlob($this);
			if ($newid > -1)
			{
				$this->id = $newid;
				$result = true;
			}

		}
		return $result;
	}

	/**
	 * Deletes the Global Content Block from the database
	 *
	 * @return boolean  true indicates success, false indicates failure
	 */
	function Delete()
	{
		$result = false;
		
		$gcbops = cmsms()->GetGlobalContentOperations();

		if ($this->id > -1)
		{
			$result = $gcbops->DeleteHtmlBlobByID($this->id);
			if ($result)
			{
				$this->SetInitialValues();
			}
		}
		return $result;
	}

	/**
	 * Test to see if a specified User (specified as admin-side user's database ID)
	 * is the owner of this Global Content Block
	 *
	 * @param string $user_id User ID to test
	 * @return boolean indicates whether specified user owns this Global Content Block
	 */
	function IsOwner($user_id)
	{
		$result = false;
		
		$gcbops = cmsms()->GetGlobalContentOperations();

		if ($this->id > -1)
		{
			$result = $gcbops->CheckOwnership($this->id, $user_id);
		}

		return $result;
	}

	/**
	 * Test to see if a specified User (specified as admin-side user's database ID)
	 * is the author of this Global Content Block
	 *
	 * @param string $user_id User ID to test
	 * @return boolean indicates whether specified user is the author of this Global Content Block
	 */
	function IsAuthor($user_id)
	{
		$result = false;
		
		$gcbops = cmsms()->GetGlobalContentOperations();

		if ($this->id > -1)
		{
			$result = $gcbops->CheckAuthorship($this->id, $user_id);
		}

		return $result;
	}


	/**
	 * Clears all of the Additional Editors from this Global Content Block in the database
	 *
	 * @return boolean true indicates success, false indicates failure
	 */
	function ClearAuthors()
	{
		$result = false;
		
		$gcbops = cmsms()->GetGlobalContentOperations();

		if ($this->id > -1)
		{
			$gcbops->ClearAdditionalEditors($this->id);
			$result = true;
		}

		return $result;
	}

	/**
	 * Adds a specified User (specified as admin-side user's database ID) as an editor for
	 * this Global Content Block
	 *
	 * @param string $user_id User ID to add
	 * @return boolean true indicates success, false indicates failure
	 */
	function AddAuthor($user_id)
	{
		$result = false;
		
		$gcbops = cmsms()->GetGlobalContentOperations();

		if ($this->id > -1)
		{
			$gcbops->InsertAdditionalEditors($this->id, $user_id);
			$result = true;
		}

		return $result;
	}
}

/**
 * The class used for "Global Content Blocks" in CMS Made Simple.
 *
 * @ignore
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class HtmlBlob extends GlobalContent
{
}

# vim:ts=4 sw=4 noet
?>