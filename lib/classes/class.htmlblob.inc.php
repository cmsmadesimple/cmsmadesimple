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
 * Generic html blob class. This can be used for any logged in blob or blob related function.
 *
 * @since		0.6
 * @package		CMS
 */
class HtmlBlob
{
	var $id;
	var $name;
	var $content;
	var $owner;
	var $modified_date;

	function HtmlBlob()
	{
		$this->SetInitialValues();
	}

	function SetInitialValues()
	{
		$this->id = -1;
		$this->name = '';
		$this->content = '';
		$this->owner = -1;
		$this->modified_date = -1;
	}

	function Save()
	{
		$result = false;
		
		if ($this->id > -1)
		{
			$result = HtmlBlobOperations::UpdateHtmlBlob($this);
		}
		else
		{
			$newid = HtmlBlobOperations::InsertHtmlBlob($this);
			if ($newid > -1)
			{
				$this->id = $newid;
				$result = true;
			}

		}

		return $result;
	}

	function Delete()
	{
		$result = false;

		if ($this->id > -1)
		{
			$result = HtmlBlobOperations::DeleteHtmlBlobByID($this->id);
			if ($result)
			{
				$this->SetInitialValues();
			}
		}

		return $result;
	}

	function IsOwner($user_id)
	{
		$result = false;

		if ($this->id > -1)
		{
			$result = HtmlBlobOperations::CheckOwnership($this->id, $user_id);
		}

		return $result;
	}

	function IsAuthor($user_id)
	{
		$result = false;

		if ($this->id > -1)
		{
			$result = HtmlBlobOperations::CheckAuthorship($this->id, $user_id);
		}

		return $result;
	}

	function ClearAuthors()
	{
		$result = false;

		if ($this->id > -1)
		{
			HtmlBlobOperations::ClearAdditionalEditors($this->id);
			$result = true;
		}

		return $result;
	}

	function AddAuthor($user_id)
	{
		$result = false;

		if ($this->id > -1)
		{
			HtmlBlobOperations::InsertAdditionalEditors($this->id, $user_id);
			$result = true;
		}

		return $result;
	}
}

/**
 * Class for doing html blob related functions.  Maybe of the HtmlBlob object functions are just wrappers around these.
 *
 * @since		0.6
 * @package		CMS
 */
class HtmlBlobOperations
{

/**
 * Prepares an array with the list of the html blobs $userid is an author of 
 *
 * @returns an array in whose elements are the IDs of the blobs  
 * @since 0.11
 */
function AuthorBlobs($userid)
{
	global $gCms;
	$db = $gCms->db;
    $variables = &$gCms->variables;
	if (!isset($variables['authorblobs']))
	{
		$db = $gCms->db;
		$variables['authorblobs'] = array();

		$query = "SELECT htmlblob_id FROM ".cms_db_prefix()."additional_htmlblob_users WHERE user_id = ?";
		$result = &$db->Execute($query, array($userid));

		while (!$result->EOF)
		{
			array_push($variables['authorblobs'], $result->fields['htmlblob_id']);
			$result->MoveNext();
		}
	}

	return $variables['authorblobs'];
}

	function LoadHtmlBlobs()
	{
		global $gCms;
		$db = &$gCms->db;

		$result = array();

		$query = "SELECT htmlblob_id, htmlblob_name, html, owner, modified_date FROM ".cms_db_prefix()."htmlblobs ORDER BY htmlblob_id";
		$dbresult = &$db->Execute($query);

		while (!$dbresult->EOF)
		{
			$oneblob = new HtmlBlob();
			$oneblob->id = $dbresult->fields['htmlblob_id'];
			$oneblob->name = $dbresult->fields['htmlblob_name'];
			$oneblob->content = $dbresult->fields['html'];
			$oneblob->owner = $dbresult->fields['owner'];
			$oneblob->modified_date = $db->UnixTimeStamp($dbresult->fields['modified_date']);
			array_push($result, $oneblob);
			$dbresult->MoveNext();
		}

		return $result;
	}

	function LoadHtmlBlobByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "SELECT htmlblob_id, htmlblob_name, html, owner, modified_date FROM ".cms_db_prefix()."htmlblobs WHERE htmlblob_id = ?";
		$row = &$db->GetRow($query, array($id));

		if ($row)
		{
			$oneblob = new HtmlBlob();
			$oneblob->id = $row['htmlblob_id'];
			$oneblob->name = $row['htmlblob_name'];
			$oneblob->content = $row['html'];
			$oneblob->owner = $row['owner'];
			$oneblob->modified_date = $db->UnixTimeStamp($row['modified_date']);
			$result = $oneblob;
		}

		return $result;
	}

	function LoadHtmlBlobByName($name)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;
		$cache = &$gCms->HtmlBlobCache;

		if (isset($cache[$name]))
		{
			return $cache[$name];
		}

		$query = "SELECT htmlblob_id, htmlblob_name, html, owner, modified_date FROM ".cms_db_prefix()."htmlblobs WHERE htmlblob_name = ?";
		$row = &$db->GetRow($query, array($name));

		if ($row)
		{
			$oneblob = new HtmlBlob();
			$oneblob->id = $row['htmlblob_id'];
			$oneblob->name = $row['htmlblob_name'];
			$oneblob->content = $row['html'];
			$oneblob->owner = $row['owner'];
			$oneblob->modified_date = $db->UnixTimeStamp($row['modified_date']);
			$result = $oneblob;

			if (!isset($cache[$oneblob->name]))
			{
				$cache[$oneblob->name] = $oneblob;
			}
		}

		return $result;
	}

	function InsertHtmlBlob($htmlblob)
	{
		$result = -1; 

		global $gCms;
		$db = &$gCms->db;

		$new_htmlblob_id = $db->GenID(cms_db_prefix()."htmlblobs_seq");
		$query = "INSERT INTO ".cms_db_prefix()."htmlblobs (htmlblob_id, htmlblob_name, html, owner, create_date, modified_date) VALUES (?,?,?,?,?,?)";
		$dbresult = $db->Execute($query, array($new_htmlblob_id, $htmlblob->name, $htmlblob->content, $htmlblob->owner, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
		if ($dbresult !== false)
		{
			$result = $new_htmlblob_id;
		}

		return $result;
	}

	function UpdateHtmlBlob($htmlblob)
	{
		$result = false; 

		global $gCms;
		$db = &$gCms->db;

		$query = "UPDATE ".cms_db_prefix()."htmlblobs SET htmlblob_name = ?, html = ?, owner = ?, modified_date = ? WHERE htmlblob_id = ?";
		$dbresult = $db->Execute($query,array($htmlblob->name,$htmlblob->content,$htmlblob->owner,$db->DBTimeStamp(time()),$htmlblob->id));
		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	function DeleteHtmlBlobByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "DELETE FROM ".cms_db_prefix()."htmlblobs where htmlblob_id = ?";
		$dbresult = $db->Execute($query,array($id));

		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	function CheckExistingHtmlBlobName($name)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "SELECT htmlblob_id from ".cms_db_prefix()."htmlblobs WHERE htmlblob_name = ?";
		$row = &$db->GetRow($query,array($name));

		if ($row)
		{
			$result = true; 
		}

		return $result;
	}

	function CheckOwnership($id, $user_id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "SELECT htmlblob_id FROM ".cms_db_prefix()."htmlblobs WHERE htmlblob_id = ? AND owner = ?";
		$row = &$db->GetRow($query, array($id, $user_id));

		if ($row)
		{
			$result = true;
		}

		return $result;
	}

	function CheckAuthorship($id, $user_id)
	{		
		global $gCms;
		$db = &$gCms->db;
		$result = false;

		$query = "SELECT additional_htmlblob_users_id FROM ".cms_db_prefix()."additional_htmlblob_users WHERE htmlblob_id = ? AND user_id = ?";
		$row = &$db->GetRow($query, array($id, $user_id));

		if ($row)
		{
			$result = true;
		}

		return $result;
	}

	function ClearAdditionalEditors($id)
	{
		global $gCms;
		$db = &$gCms->db;

		$query = "DELETE FROM ".cms_db_prefix()."additional_htmlblob_users WHERE htmlblob_id = ?";

		$dbresult = $db->Execute($query, array($id));
	}

	function InsertAdditionalEditors($id, $user_id)
	{
		global $gCms;
		$db = &$gCms->db;

		$new_id = $db->GenID(cms_db_prefix()."additional_htmlblob_users_seq");
		$query = "INSERT INTO ".cms_db_prefix()."additional_htmlblob_users (additional_htmlblob_users_id, htmlblob_id, user_id) VALUES (?,?,?)";
		$dbresult = $db->Execute($query, array($new_id,$id,$user_id));
	}
}

# vim:ts=4 sw=4 noet
?>
