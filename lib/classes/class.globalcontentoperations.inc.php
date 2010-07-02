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
 * Global content related functions 
 * @package CMS 
 * @license GPL
 */

/**
 * Include global content class definition
 */
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.globalcontent.inc.php');

/**
 * Class for doing html blob related functions.  Many of the HtmlBlob object functions are just wrappers around these.
 *
 * @since 0.6
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class GlobalContentOperations
{
	/**
	 * Prepares an array with the list of the global content blocks $userid is an author of 
	 * or is authorized to edit.
	 *
	 * @return array List of ids of the found global content blocks
	 * @since 0.11
	 */
	function AuthorBlobs($userid)
	{
		global $gCms;
		$db = &$gCms->GetDb();
		$variables = &$gCms->variables;
		if (!isset($variables['authorblobs']))
		{
			$db = &$gCms->GetDb();
			$variables['authorblobs'] = array();

			// get the list of html blobs where this user is a direct owner
			// todo.

			// get the list of html blobs where this user is a direct additional editor.
			$query = "SELECT htmlblob_id FROM ".cms_db_prefix()."additional_htmlblob_users WHERE user_id = ?";
			$result = &$db->Execute($query, array($userid));
			while ($result && !$result->EOF)
			{
				$variables['authorblobs'][] = $result->fields['htmlblob_id'];
				$result->MoveNext();
			}
			if( $result ) $result->Close();

			// get the list of html blobs where this users member groups are listed as an additional editor
			// in additional_htmlblob_users groupid's are indicated with a negative value.
			$query = 'SELECT group_id FROM '.cms_db_prefix().'user_groups WHERE user_id = ?';
			$result = &$db->Execute($query, array($userid));
			$tmp = array();
			while ($result && !$result->EOF)
			  {
			    $tmp[] = $result->fields['group_id'] * -1;
			    $result->MoveNext();
			  }
			if( $result ) $result->Close();
                        if( count($tmp) > 0 ) {
			  $query = 'SELECT htmlblob_id FROM '.cms_db_prefix().'additional_htmlblob_users WHERE user_id IN (';
			  $query .= implode(',',$tmp).')';
			  $result = &$db->Execute($query);
			  while ($result && !$result->EOF)
			    {
			      $variables['authorblobs'][] = $result->fields['htmlblob_id'];
			      $result->MoveNext();
			    }
			  if( $result ) $result->Close();
                        }
		}

		return $variables['authorblobs'];
	}

	/**
	 * Loads all global content blocks from the database and returns them
	 *
	 * @return array The list of global content blocks
	 */
	function LoadHtmlBlobs()
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$result = array();

		$query = "SELECT htmlblob_id, htmlblob_name, html, owner, modified_date FROM ".cms_db_prefix()."htmlblobs ORDER BY htmlblob_name";
		$dbresult = &$db->Execute($query);

		while (is_object($dbresult) && !$dbresult->EOF)
		{
			$oneblob = new GlobalContent();
			$oneblob->id = $dbresult->fields['htmlblob_id'];
			$oneblob->name = $dbresult->fields['htmlblob_name'];
			$oneblob->content = $dbresult->fields['html'];
			$oneblob->owner = $dbresult->fields['owner'];
			$oneblob->modified_date = $db->UnixTimeStamp($dbresult->fields['modified_date']);
			$result[] = $oneblob;
			$dbresult->MoveNext();
		}
		if( $dbresult ) $dbresult->Close();
		return $result;
	}

	/**
	 * Load a global content block by its database id
	 *
	 * @param string $id The id of the block to load
	 * @return mixed If found, the global content block. If none is found, returns false.
	 */
	function LoadHtmlBlobByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT htmlblob_id, htmlblob_name, html, owner, modified_date FROM ".cms_db_prefix()."htmlblobs WHERE htmlblob_id = ?";
		$row = &$db->GetRow($query, array($id));

		if ($row)
		{
			$oneblob = new GlobalContent();
			$oneblob->id = $row['htmlblob_id'];
			$oneblob->name = $row['htmlblob_name'];
			$oneblob->content = $row['html'];
			$oneblob->owner = $row['owner'];
			$oneblob->modified_date = $db->UnixTimeStamp($row['modified_date']);
			$result =& $oneblob;
		}

		return $result;
	}

	/**
	 * Loads a global content block by its name
	 *
	 * @param string $name The name of the global content block to load
	 * @return mixed If found, the global content block. If none is found, returns false.
	 */
	function &LoadHtmlBlobByName($name)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();
		$gcbops =& $gCms->GetGlobalContentOperations();
		$cache = &$gCms->HtmlBlobCache;

		if (isset($cache[$name]))
		{
			return $cache[$name];
		}

		$query = "SELECT htmlblob_id, htmlblob_name, html, owner, modified_date FROM ".cms_db_prefix()."htmlblobs WHERE htmlblob_name = ?";
		$row = &$db->GetRow($query, array($name));

		if ($row)
		{
			$oneblob = new GlobalContent();
			$oneblob->id = $row['htmlblob_id'];
			$oneblob->name = $row['htmlblob_name'];
			$oneblob->content = $row['html'];
			$oneblob->owner = $row['owner'];
			$oneblob->modified_date = $db->UnixTimeStamp($row['modified_date']);
			$result =& $oneblob;

			if (!isset($cache[$oneblob->name]))
			{
				$cache[$oneblob->name] =& $oneblob;
			}
		}

		return $result;
	}

	/**
	 * Given a global content object, creates a new global content block in the database with its attributes.
	 *
	 * @param mixed $htmlblob The global content object to store.
	 * @return int Returns the id of the new object in the database. If failure, returns -1.
	 */
	function InsertHtmlBlob($htmlblob)
	{
		$result = -1; 

		global $gCms;
		$db = &$gCms->GetDb();

		$new_htmlblob_id = $db->GenID(cms_db_prefix()."htmlblobs_seq");
		$time = $db->DBTimeStamp(time());
		$query = "INSERT INTO ".cms_db_prefix()."htmlblobs (htmlblob_id, htmlblob_name, html, owner, create_date, modified_date) VALUES (?,?,?,?,".$time.",".$time.")";
		$dbresult = $db->Execute($query, array($new_htmlblob_id, $htmlblob->name, $htmlblob->content, $htmlblob->owner));
		if ($dbresult !== false)
		{
			$result = $new_htmlblob_id;
		}

		return $result;
	}

	/**
	 * Given a global content object, updates that global content block in the database with its updated attributes.
	 *
	 * @param mixed $htmlblob The global content object to store.
	 * @return boolean Returns true if successful, false if not.
	 */
	function UpdateHtmlBlob($htmlblob)
	{
		$result = false; 

		global $gCms;
		$db = &$gCms->GetDb();

		$time = $db->DBTimeStamp(time());
		$query = "UPDATE ".cms_db_prefix()."htmlblobs SET htmlblob_name = ?, html = ?, owner = ?, modified_date = ".$time." WHERE htmlblob_id = ?";
		$dbresult = $db->Execute($query,array($htmlblob->name,$htmlblob->content,$htmlblob->owner,$htmlblob->id));
		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	/**
	 * Given a global content block's id, delete it from the database.
	 *
	 * @param integer $id The id of the block to delete
	 * @return boolean Returns true if successful, false if not.
	 */
	function DeleteHtmlBlobByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "DELETE FROM ".cms_db_prefix()."htmlblobs where htmlblob_id = ?";
		$dbresult = $db->Execute($query,array($id));

		if ($dbresult !== false)
		{
			$result = true;
			remove_cross_references_by_child($id, 'global_content');
		}

		return $result;
	}

	/**
	 * Given a name, check to see if it already exists in the database. If the id is given,
	 * ignore it for purposes of updating an existing block.
	 *
	 * @param string $name The name to check
	 * @param integer $id The global content block to ignore. If not passed, all blocks will be checked.
	 * @return boolean Returns true if the name is used. False if it not.
	 */
	function CheckExistingHtmlBlobName($name, $id = -1)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();
		$row = null;

		$query = "SELECT htmlblob_id from ".cms_db_prefix()."htmlblobs WHERE htmlblob_name = ?";
		if ($id > -1)
		{
			$query .= ' AND htmlblob_id <> ?';
			$row = &$db->GetRow($query,array($name, $id));
		}
		else
		{
			$row = &$db->GetRow($query,array($name));
		}

		if ($row)
		{
			$result = true; 
		}

		return $result;
	}

	/**
	 * Checks to see if the given global content block's id is owned by the given user's id.
	 *
	 * @param integer $id The global content block id to check
	 * @param string $user_id The user id to check
	 * @return boolean Returns true if the user is the owner.  False if they are not.
	 */
	function CheckOwnership($id, $user_id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT htmlblob_id FROM ".cms_db_prefix()."htmlblobs WHERE htmlblob_id = ? AND owner = ?";
		$row = &$db->GetRow($query, array($id, $user_id));

		if ($row)
		{
			$result = true;
		}

		return $result;
	}

	/**
	 * Checks to see if the given user has permission to modify the given global content block. Both user and
	 * block are identified by id.
	 *
	 * @param integer $id The global content block id to check
	 * @param integer $user_id The user id to check
	 * @return boolean Returns true if the user is the permitted.  False if they are not.
	 */
	function CheckAuthorship($id, $user_id)
	{		
		$myblobs = $this->AuthorBlobs($user_id);
		return quick_check_authorship($id,$myblobs);
	}

	/**
	 * Clears the list of additional editors for the given content block id
	 *
	 * @param integer $id The global content block id to clear
	 * @return void
	 */
	function ClearAdditionalEditors($id)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$query = "DELETE FROM ".cms_db_prefix()."additional_htmlblob_users WHERE htmlblob_id = ?";

		$dbresult = $db->Execute($query, array($id));
	}

	/**
	 * Insert a user id into the additional editors list of the specified global content block
	 *
	 * @param string $id The id of the global content block
	 * @param string $user_id The id of the user to add to the list
	 * @return boolean true if successful, false if not.
	 */
	function InsertAdditionalEditors($id, $user_id)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$new_id = $db->GenID(cms_db_prefix()."additional_htmlblob_users_seq");
		$query = "INSERT INTO ".cms_db_prefix()."additional_htmlblob_users (additional_htmlblob_users_id, htmlblob_id, user_id) VALUES (?,?,?)";
		$dbresult = $db->Execute($query, array($new_id,$id,$user_id));
	}
}

/**
 * @ignore
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class HtmlBlobOperations extends GlobalContentOperations
{
}

# vim:ts=4 sw=4 noet
?>