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

class Template
{
	var $id;
	var $name;
	var $content;
	var $stylesheet;
	var $encoding;
	var $active;

	function Template()
	{
		$this->SetInitialValues();
	}

	function SetInitialValues()
	{
		$this->id = -1;
		$this->name = '';
		$this->content = '';
		$this->stylesheet = '';
		$this->encoding = '';
		$this->active = false;
	}

	function Save()
	{
		$result = false;
		
		if ($this->id > -1)
		{
			$result = TemplateOperations::UpdateTemplate($this);
		}
		else
		{
			$newid = TemplateOperations::InsertTemplate($this);
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
			$result = TemplateOperations::DeleteTemplateByID($this->id);
			if ($result)
			{
				$this->SetInitialValues();
			}
		}

		return $result;
	}
}

class TemplateOperations
{
	function LoadTemplates()
	{
		global $gCms;
		$db = &$gCms->db;

		$result = array();

		$query = "SELECT template_id, template_name, template_content, stylesheet, encoding, active FROM ".cms_db_prefix()."templates ORDER BY template_id";
		$dbresult = $db->Execute($query);

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				$onetemplate = new Template();
				$onetemplate->id = $row['template_id'];
				$onetemplate->name = $row['template_name'];
				$onetemplate->active = $row['active'];
				$onetemplate->content = $row['template_content'];
				$onetemplate->encoding = $row['encoding'];
				$onetemplate->stylesheet = $row['stylesheet'];
				array_push($result, $onetemplate);
			}
		}

		return $result;
	}

	function LoadTemplateByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "SELECT template_id, template_name, template_content, stylesheet, encoding, active FROM ".cms_db_prefix()."templates WHERE template_id = ?";
		$dbresult = $db->Execute($query, array($id));

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				$onetemplate = new Template();
				$onetemplate->id = $row['template_id'];
				$onetemplate->name = $row['template_name'];
				$onetemplate->content = $row['template_content'];
				$onetemplate->stylesheet = $row['stylesheet'];
				$onetemplate->encoding = $row['encoding'];
				$onetemplate->active = $row['active'];
				$result = $onetemplate;
			}
		}

		return $result;
	}

	function InsertTemplate($template)
	{
		$result = -1; 

		global $gCms;
		$db = &$gCms->db;

		$new_template_id = $db->GenID(cms_db_prefix()."templates_seq");
		$query = "INSERT INTO ".cms_db_prefix()."templates (template_id, template_name, template_content, stylesheet, encoding, active, create_date, modified_date) VALUES (?,?,?,?,?,?,?,?)";
		$dbresult = $db->Execute($query, array($new_template_id, $template->name, $template->content, $template->stylesheet, $template->encoding, $template->active, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
		if ($dbresult !== false)
		{
			$result = $new_template_id;
		}

		return $result;
	}

	function UpdateTemplate($template)
	{
		$result = false; 

		global $gCms;
		$db = &$gCms->db;

		$query = "UPDATE ".cms_db_prefix()."templates SET template_name = ?, template_content = ?, stylesheet = ?, encoding = ?, active = ?, modified_date = ? WHERE template_id = ?";
		$dbresult = $db->Execute($query,array($template->name,$template->content,$template->stylesheet,$template->encoding,$template->active,$db->DBTimeStamp(time()),$template->id));
		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	function DeleteTemplateByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "DELETE FROM ".cms_db_prefix()."templates where template_id = ?";
		$dbresult = $db->Execute($query,array($id));

		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	function CountPagesUsingTemplateByID($id)
	{
		$result = 0;

		global $gCms;
		$db = &$gCms->db;

        $query = "SELECT count(*) AS count FROM ".cms_db_prefix()."pages WHERE template_id = ?";
        $dbresult = $db->Execute($query,array($id));

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			$row = $dbresult->FetchRow();
			if (isset($row["count"]))
			{
				$result = $row["count"];
			}
		}

		return $result;
	}

	function TouchAllTemplates()
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "UPDATE ".cms_db_prefix()."templates SET modified_date = ?";
		$dbresult = $db->Execute($query,array($db->DBTimeStamp(time())));

		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	function CheckExistingTemplateName($name)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "SELECT template_id from ".cms_db_prefix()."templates WHERE template_name = ?";
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
