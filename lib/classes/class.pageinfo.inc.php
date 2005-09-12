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
 * Page Info -- Represents a "page" which consists of different variabels virtually
 * composited together.
 *
 * @since		0.11
 * @package		CMS
 */
class PageInfo
{
	var $content_id;
	var $content_title;
	var $content_alias;
	var $content_menutext;
	var $content_hierarchy;
	var $content_type;
	var $content_modified_date;
	var $template_id;
	var $template_modified_date;

	function PageInfo()
	{
		$this->SetInitialValues();
	}

	function SetInitialValues()
	{
		$this->content_id = -1;
		$this->content_title = '';
		$this->content_alias = '';
		$this->content_menutext = '';
		$this->content_hierarchy = '';
		$this->content_modified_date = -1;
		$this->template_id = -1;
		$this->template_modified_date = -1;
	}
}

/**
 * Class for doing template related functions.  Many of the Template object functions are just wrappers around these.
 *
 * @since		0.6
 * @package		CMS
 */
class PageInfoOperations
{
	function LoadPageInfoByContentAlias($alias)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		if (is_numeric($alias) && strpos($alias, '.') === FALSE && strpos($alias, ',') === FALSE) //Fix for postgres
		{ 
			$query = "SELECT c.content_id, c.content_name, c.content_alias, c.menu_text, c.hierarchy, c.modified_date AS c_date, t.template_id, t.modified_date AS t_date FROM ".cms_db_prefix()."templates t INNER JOIN ".cms_db_prefix()."content c ON c.template_id = t.template_id WHERE (c.content_alias = ? OR c.content_id = ?) AND c.active = 1";
			$dbresult = $db->Execute($query, array($alias, $alias));
		}
		else
		{
			$query = "SELECT c.content_id, c.content_name, c.content_alias, c.menu_text, c.hierarchy, c.modified_date AS c_date, t.template_id, t.modified_date AS t_date FROM ".cms_db_prefix()."templates t INNER JOIN ".cms_db_prefix()."content c ON c.template_id = t.template_id WHERE c.content_alias = ? AND c.active = 1";
			$dbresult = $db->Execute($query, array($alias));
		}

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				$onepageinfo = new PageInfo();
				$onepageinfo->content_id = $row['content_id'];
				$onepageinfo->content_title = $row['content_name'];
				$onepageinfo->content_alias = $row['content_alias'];
				$onepageinfo->content_menutext = $row['menu_text'];
				$onepageinfo->content_hierarchy = $row['hierarchy'];
				$onepageinfo->content_modified_date = $db->UnixTimeStamp($row['c_date']);
				$onepageinfo->template_id = $row['template_id'];
				$onepageinfo->template_modified_date = $db->UnixTimeStamp($row['t_date']);
				$result = $onepageinfo;
			}
		}

		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>
