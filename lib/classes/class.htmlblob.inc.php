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

class HtmlBlob
{
	var $id;
	var $name;
	var $content;
	var $owner;

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
}

class HtmlBlobOperations
{
	function LoadHtmlBlobs()
	{
		global $gCms;
		$db = &$gCms->db;

		$result = array();

		$query = "SELECT htmlblob_id, htmlblob_name, html, owner FROM ".cms_db_prefix()."htmlblobs ORDER BY htmlblob_id";
		$dbresult = $db->Execute($query);

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				$oneblob = new HtmlBlob();
				$oneblob->id = $row['htmlblob_id'];
				$oneblob->name = $row['htmlblob_name'];
				$oneblob->content = $row['html'];
				$oneblob->owner = $row['owner'];
				array_push($result, $oneblob);
			}
		}

		return $result;
	}

	function LoadHtmlBlobByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "SELECT htmlblob_id, htmlblob_name, html, owner FROM ".cms_db_prefix()."htmlblobs WHERE htmlblob_id = ?";
		$dbresult = $db->Execute($query, array($id));

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				$oneblob = new HtmlBlob();
				$oneblob->id = $row['htmlblob_id'];
				$oneblob->name = $row['htmlblob_name'];
				$oneblob->content = $row['html'];
				$oneblob->owner = $row['owner'];
				$result = $oneblob;
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
		$dbresult = $db->Execute($query,array($name));

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			$result = true; 
		}

		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>
