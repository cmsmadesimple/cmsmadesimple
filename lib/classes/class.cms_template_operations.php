<?php
#CMS - CMS Made Simple
#(c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
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
 * Class for doing template related functions.  Many of the Template object functions are just wrappers around these.
 *
 * @since		0.6
 * @package		CMS
 */

//include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.template.inc.php');

class CmsTemplateOperations extends CmsObject
{
	static private $instance = NULL;

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get an instance of this object, though most people should be using
	 * the static methods instead.  This is more for compatibility than
	 * anything else.
	 *
	 * @return CmsTemplateOperations The instance of the singleton object.
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsTemplateOperations();
		}
		return self::$instance;
	}

	function LoadTemplates()
	{	
		return cmsms()->template->find_all(array('order' => 'template_name ASC'));
	}

	function LoadTemplateByID($id)
	{
		return cmsms()->template->find_by_id($id);
	}

	function LoadTemplateByContentAlias($alias)
	{
		return cmsms()->template->find_by_query("SELECT t.* FROM ".cms_db_prefix()."templates t INNER JOIN ".cms_db_prefix()."content c ON c.template_id = t.template_id WHERE (c.content_alias = ? OR c.content_id = ?) AND c.active = 1", array($alias, $alias));
	}

	function LoadTemplateAndContentDates($alias)
	{
		$result = array();

		$query = "SELECT c.modified_date AS c_date, t.modified_date AS t_date FROM ".cms_db_prefix()."templates t INNER JOIN ".cms_db_prefix()."content c ON c.template_id = t.template_id WHERE (c.content_alias = ? OR c.content_id = ?) AND c.active = 1";
		$dbresult = cms_db()->Execute($query, array($alias, $alias));

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
		return cmsms()->template->find_by_default(1);
	}

	function UsageCount($id)
	{
		$result = 0;

		$query = "SELECT count(*) as the_count FROM ".cms_db_prefix()."content WHERE template_id = ?";
		$row = cms_db()->GetRow($query, array($id));

		if($row)
		{
			$result = $row['the_count'];
		}
	
		return $result;
	}

	function InsertTemplate($template)
	{
		$result = -1; 

		$dbresult = $template->save();

		if ($dbresult !== false)
		{
			$result = $template->id;
			CmsContentOperations::do_cross_reference($result, 'template', $template->content);
		}

		return $result;
	}

	function UpdateTemplate($template)
	{
		$result = false; 

		$dbresult = $template->save();

		if ($dbresult !== false)
		{
			$result = true;
			CmsContentOperations::do_cross_reference($template->id, 'template', $template->content);
		}

		return $result;
	}

	function DeleteTemplateByID($id)
	{
		return cmsms()->template->delete($id);
	}

	function CountPagesUsingTemplateByID($id)
	{
		$result = 0;

        $query = "SELECT count(*) AS count FROM ".cms_db_prefix()."content WHERE template_id = ?";
        $row = cms_db()->GetRow($query,array($id));

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

        $query = "SELECT count(*) AS count FROM ".cms_db_prefix()."templates WHERE stylesheet is not null and stylesheet != ''";
        $row = cms_db()->GetRow($query);

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

		$dbresult = false;

		$time = cms_db()->DBTimeStamp(time());
		if ($blob_name != '')
		{
			$query = "UPDATE ".cms_db_prefix()."templates SET modified_date = ".$time." WHERE template_content like ?";
			$dbresult = cms_db()->Execute($query,array('%{html_blob name="'.$blob_name.'"}%'));
		}
		else
		{
			$query = "UPDATE ".cms_db_prefix()."templates SET modified_date = ".$time;
			$dbresult = cms_db()->Execute($query);
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

		$query = "SELECT template_id from ".cms_db_prefix()."templates WHERE template_name = ?";
		$row = cms_db()->GetRow($query,array($name));

		if ($row)
		{
			$result = true; 
		}

		return $result;
	}
	
	function TemplateDropdown($id = 'template_id', $selected_id = -1, $othertext = '', $show_hidden = false)
	{
		$result = "";
		
		$templateops =& cmsms()->GetTemplateOperations();

		$alltemplates = $templateops->LoadTemplates();
		
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

class TemplateOperations extends CmsTemplateOperations
{
}

?>