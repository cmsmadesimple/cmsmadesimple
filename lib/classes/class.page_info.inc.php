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
 * Page Info -- Represents a "page" which consists of different variables virtually
 * composited together.
 *
 * @since		0.11
 * @package		CMS
 */
class PageInfo extends Object
{
	var $content_id;
	var $content_title;
	var $content_alias;
	var $content_menutext;
	var $content_titleattribute;
	var $content_hierarchy;
	var $content_id_hierarchy;
	var $content_type;
	var $content_props;
	var $content_metadata;
	var $content_modified_date;
	var $content_created_date;
	var $content_last_modified_date;
	var $content_last_modified_by_id;
	var $template_id;
	var $template_encoding;
	var $template_modified_date;
	var $cachable;

	function __construct()
	{
		parent::__construct();
		$this->SetInitialValues();
	}

	function SetInitialValues()
	{
		$this->content_id = -1;
		$this->content_title = '';
		$this->content_alias = '';
		$this->content_menutext = '';
		$this->content_titleattribute = '';
		$this->content_hierarchy = '';
		$this->content_metadata = '';
		$this->content_id_hierarchy = '';
		$this->content_modified_date = -1;
		$this->content_created_date = -1;
		$this->content_last_modified_date = -1;
		$this->content_last_modified_by_id = -1;
		$this->content_props = array();
		$this->template_id = -1;
		$this->template_modified_date = -1;
		$this->template_encoding = '';
		$this->cachable = false;

		$db = cms_db();

		$query = 'SELECT MAX(modified_date) AS thedate FROM '.cms_db_prefix().'content c';
		$row = $db->GetRow($query);

		if ($row)
		{
			$this->content_last_modified_date = $db->UnixTimeStamp($row['thedate']);
		}
	}
}

# vim:ts=4 sw=4 noet
?>