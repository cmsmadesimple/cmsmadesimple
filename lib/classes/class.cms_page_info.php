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
class CmsPageInfo extends CmsObject
{
	var $content_id = -1;
	var $content_title = '';
	var $content_alias = '';
	var $content_menutext = '';
	var $content_titleattribute = '';
	var $content_hierarchy = '';
	var $content_id_hierarchy = '';
	var $content_type = '';
	var $content_props;
	var $content_metadata = '';
	var $content_modified_date = -1;
	var $content_created_date = -1;
	var $content_last_modified_date = -1;
	var $content_last_modified_by_id = -1;
	var $template_id = -1;
	var $template_encoding = '';
	var $template_modified_date = -1;
	var $cachable = false;

	function __construct()
	{
		parent::__construct();

		$this->content_props = array();
		$this->content_last_modified_date = CmsCache::get_instance()->call(array(&$this, 'get_max_modified_date'));
	}
	
	public function get_max_modified_date()
	{
		$db = cms_db();

		$query = 'SELECT MAX(modified_date) AS thedate FROM '.cms_db_prefix().'content c';
		$row = $db->GetRow($query);

		if ($row)
		{
			return $db->UnixTimeStamp($row['thedate']);
		}
		
		return -1;
	}
	
	public function send_headers()
	{
		header("Content-Type: " . cmsms()->variables['content-type'] . "; charset=" . 
			(isset($this->template_encoding) && $this->template_encoding != ''?$this->template_encoding:get_encoding()));
	}
	
	/**
	 * Renders the given pageinfo object based on it's assigned content and template
	 * parts.
	 *
	 * @return string The rendered html
	 **/
	public function render()
	{
		$html = '';
		$cached = false;
		
		$gCms = cmsms();
		$smarty = cms_smarty();

		if (isset($_GET["print"]))
		{
			($smarty->is_cached('print:'.$page, '', $this->template_id)?$cached="":$cached="not ");
			$html = $smarty->fetch('print:'.$page, '', $this->template_id) . "\n";
		}
		else
		{
			#If this is a case where a module doesn't want a template to be shown, just disable caching
			if (isset($smarty->id) && $smarty->id != '' && isset($_REQUEST[$smarty->id.'showtemplate']) && $_REQUEST[$smarty->id.'showtemplate'] == 'false')
			{
				$html = $smarty->fetch('template:notemplate') . "\n";
			}
			else
			{
				$smarty->caching = false;
				$smarty->compile_check = true;
				($smarty->is_cached('template:'.$this->template_id)?$cached="":$cached="not ");
				$html = $smarty->fetch('template:'.$this->template_id) . "\n";
				if (isset($_REQUEST['tmpfile']))
				{
					$smarty->clear_compiled_tpl('template:'.$this->template_id);
				}
			}
		}

		if (!$cached)
		{
			#Perform the content postrendernoncached callback
			reset($gCms->modules);
			while (list($key) = each($gCms->modules))
			{
				$value =& $gCms->modules[$key];
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->ContentPostRenderNonCached($html);
				}
			}
			//CmsEvents::send_event('Core', 'ContentPostRenderNonCached', array(&$html));
		}

		#Perform the content postrender callback
		reset($gCms->modules);
		while (list($key) = each($gCms->modules))
		{
			$value =& $gCms->modules[$key];
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentPostRender($html);
			}
		}

		CmsEvents::send_event('Core', 'ContentPostRender', array('content' => &$html));
		
		return $html;
	}
}

# vim:ts=4 sw=4 noet
?>