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
 * Generic template class. This can be used for any template or template related function.
 *
 * @since		0.6
 * @package		CMS
 */
class Template
{
	var $id;
	var $name;
	var $content;
	var $stylesheet;
	var $encoding;
	var $active;
	var $default;

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
		$this->default = false;
	}

	function Id()
	{
		return $this->id;
	}

	function Name()
	{
		return $this->name;
	}

	function UsageCount()
	{
		if ($this->id > -1)
			return TemplateOperations::UsageCount($this->id);
		else
			return 0;
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

/**
 * Class for doing template related functions.  Many of the Template object functions are just wrappers around these.
 *
 * @since		0.6
 * @package		CMS
 */
class TemplateOperations
{
	function LoadTemplates()
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$result = array();

		$query = "SELECT template_id, template_name, template_content, stylesheet, encoding, active, default_template, modified_date FROM ".cms_db_prefix()."templates ORDER BY template_name";
		$dbresult = &$db->Execute($query);

		while ($dbresult && !$dbresult->EOF)
		{
			$onetemplate = new Template();
			$onetemplate->id = $dbresult->fields['template_id'];
			$onetemplate->name = $dbresult->fields['template_name'];
			$onetemplate->active = $dbresult->fields['active'];
			$onetemplate->default = $dbresult->fields['default_template'];
			$onetemplate->content = $dbresult->fields['template_content'];
			$onetemplate->encoding = $dbresult->fields['encoding'];
			$onetemplate->stylesheet = $dbresult->fields['stylesheet'];
			$onetemplate->modified_date = $db->UnixTimeStamp($dbresult->fields['modified_date']);
			$result[] = $onetemplate;
			$dbresult->MoveNext();
		}
		
		if ($dbresult) $dbresult->Close();

		return $result;
	}

	function & LoadTemplateByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();
		$cache = &$gCms->TemplateCache;

		if (isset($cache[$id]))
		{
			return $cache[$id];
		}

		$query = "SELECT template_id, template_name, template_content, stylesheet, encoding, active, default_template, modified_date FROM ".cms_db_prefix()."templates WHERE template_id = ?";
		$row = &$db->GetRow($query, array($id));

		if($row)
		{
			$onetemplate =& new Template();
			$onetemplate->id = $row['template_id'];
			$onetemplate->name = $row['template_name'];
			$onetemplate->content = $row['template_content'];
			$onetemplate->stylesheet = $row['stylesheet'];
			$onetemplate->encoding = $row['encoding'];
			$onetemplate->default = $row['default_template'];
			$onetemplate->active = $row['active'];
			$onetemplate->modified_date = $db->UnixTimeStamp($row['modified_date']);
			$result =& $onetemplate;

			if (!isset($cache[$onetemplate->id]))
			{
				$cache[$onetemplate->id] =& $onetemplate;
			}
		}

		return $result;
	}

	function LoadTemplateByContentAlias($alias)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT t.template_id, t.template_name, t.template_content, t.stylesheet, t.encoding, t.active, t.default_template, t.modified_date FROM ".cms_db_prefix()."templates t INNER JOIN ".cms_db_prefix()."content c ON c.template_id = t.template_id WHERE (c.content_alias = ? OR c.content_id = ?) AND c.active = 1";
		$row = &$db->GetRow($query, array($alias, $alias));

		if ($row)
		{
			$onetemplate = new Template();
			$onetemplate->id = $row['template_id'];
			$onetemplate->name = $row['template_name'];
			$onetemplate->content = $row['template_content'];
			$onetemplate->stylesheet = $row['stylesheet'];
			$onetemplate->encoding = $row['encoding'];
			$onetemplate->default = $row['default_template'];
			$onetemplate->active = $row['active'];
			$onetemplate->modified_date = $db->UnixTimeStamp($row['modified_date']);
			$result = $onetemplate;
		}

		return $result;
	}

	function LoadTemplateAndContentDates($alias)
	{
		$result = array();

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT c.modified_date AS c_date, t.modified_date AS t_date FROM ".cms_db_prefix()."templates t INNER JOIN ".cms_db_prefix()."content c ON c.template_id = t.template_id WHERE (c.content_alias = ? OR c.content_id = ?) AND c.active = 1";
		$dbresult = &$db->Execute($query, array($alias, $alias));

		while ($dbresult && !$dbresult->EOF)
		{
			$result[] = $dbresult->fields['c_date'];
			$result[] = $dbresult->fields['t_date'];
			$dbresult->MoveNext();
		}
		
		if ($dbresult) $dbresult->Close();

		return $result;
	}

	function LoadDefaultTemplate()
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT template_id, template_name, template_content, stylesheet, encoding, active, default_template FROM ".cms_db_prefix()."templates WHERE default_template = 1";
		$row = &$db->GetRow($query);

		if($row)
		{
			$onetemplate = new Template();
			$onetemplate->id = $row['template_id'];
			$onetemplate->name = $row['template_name'];
			$onetemplate->content = $row['template_content'];
			$onetemplate->stylesheet = $row['stylesheet'];
			$onetemplate->encoding = $row['encoding'];
			$onetemplate->default = $row['default_template'];
			$onetemplate->active = $row['active'];
			$result = $onetemplate;
		}

		return $result;
	}

	function UsageCount($id)
	{
		$result = 0;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT count(*) as the_count FROM ".cms_db_prefix()."content WHERE template_id = ?";
		$row = &$db->GetRow($query, array($id));

		if($row)
		{
			$result = $row['the_count'];
		}
	
		return $result;
	}

	function InsertTemplate($template)
	{
		$result = -1; 

		global $gCms;
		$db = &$gCms->GetDb();

		$time = $db->DBTimeStamp(time());
		$new_template_id = $db->GenID(cms_db_prefix()."templates_seq");
		$query = "INSERT INTO ".cms_db_prefix()."templates (template_id, template_name, template_content, stylesheet, encoding, active, default_template, create_date, modified_date) VALUES (?,?,?,?,?,?,?,".$time.",".$time.")";
		$dbresult = $db->Execute($query, array($new_template_id, $template->name, $template->content, $template->stylesheet, $template->encoding, $template->active, $template->default));
		if ($dbresult !== false)
		{
			$result = $new_template_id;
			do_cross_reference($new_template_id, 'template', $template->content);
		}

		return $result;
	}

	function UpdateTemplate($template)
	{
		$result = false; 

		global $gCms;
		$db = &$gCms->GetDb();

		$time = $db->DBTimeStamp(time());
		$query = "UPDATE ".cms_db_prefix()."templates SET template_name = ?, template_content = ?, stylesheet = ?, encoding = ?, active = ?, default_template = ?, modified_date = ".$time." WHERE template_id = ?";
		$dbresult = $db->Execute($query,array($template->name,$template->content,$template->stylesheet,$template->encoding,$template->active,$template->default,$template->id));
		if ($dbresult !== false)
		{
			$result = true;
			do_cross_reference($template->id, 'template', $template->content);
		}

		return $result;
	}

	function DeleteTemplateByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "DELETE FROM ".cms_db_prefix()."css_assoc WHERE assoc_type = 'template' AND assoc_to_id = ?";
		$dbresult = $db->Execute($query,array($id));

		$query = "DELETE FROM ".cms_db_prefix()."templates where template_id = ?";
		$dbresult = $db->Execute($query,array($id));

		if ($dbresult !== false)
		{
			$result = true;
			remove_cross_references($id, 'template');
		}

		return $result;
	}

	function CountPagesUsingTemplateByID($id)
	{
		$result = 0;

		global $gCms;
		$db = &$gCms->GetDb();

        $query = "SELECT count(*) AS count FROM ".cms_db_prefix()."content WHERE template_id = ?";
        $row = &$db->GetRow($query,array($id));

		if ($row)
		{
			if (isset($row["count"]))
			{
				$result = $row["count"];
			}
		}

		return $result;
	}

	function StylesheetsUsed()
	{
		$result = 0;

		global $gCms;
		$db = &$gCms->GetDb();

        $query = "SELECT count(*) AS count FROM ".cms_db_prefix()."templates WHERE stylesheet is not null and stylesheet != ''";
        $row = &$db->GetRow($query);

		if ($row)
		{
			if (isset($row["count"]))
			{
				$result = $row["count"];
			}
		}

		return $result;
	}

	function TouchAllTemplates($blob_name='')
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$dbresult = false;

		$time = $db->DBTimeStamp(time());
		if ($blob_name != '')
		{
			$query = "UPDATE ".cms_db_prefix()."templates SET modified_date = ".$time." WHERE template_content like ?";
			$dbresult = $db->Execute($query,array('%{html_blob name="'.$blob_name.'"}%'));
		}
		else
		{
			$query = "UPDATE ".cms_db_prefix()."templates SET modified_date = ".$time;
			$dbresult = $db->Execute($query);
		}

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
		$db = &$gCms->GetDb();

		$query = "SELECT template_id from ".cms_db_prefix()."templates WHERE template_name = ?";
		$row = &$db->GetRow($query,array($name));

		if ($row)
		{
			$result = true; 
		}

		return $result;
	}
	
	function TemplateDropdown($id = 'template_id', $selected_id = -1, $othertext = '', $show_hidden = false)
	{
		$result = "";

		$alltemplates = TemplateOperations::LoadTemplates();
		
		if (count($alltemplates) > 0)
		{
			$result .= '<select name="'.$id.'"';
			if ($othertext != '')
			{
				$result .= ' ' . $othertext;
			}
			$result .= '>';
			#$result .= '<option value="">Select Template</option>';
			foreach ($alltemplates as $onetemplate)
			{
				if ($onetemplate->active == true || $show_hidden == true)
				{
					$result .= '<option value="'.$onetemplate->id.'"';
					if ($onetemplate->id == $selected_id || ($selected_id == -1 && $onetemplate->default == true))
					{
						$result .= ' selected="selected"';
					}
					$result .= '>'.$onetemplate->name.'</option>';
				}
			}
			$result .= '</select>';
		}
		
		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>
