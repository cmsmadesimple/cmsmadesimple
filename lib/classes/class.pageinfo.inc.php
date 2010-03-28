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
class PageInfo
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

		$gCms = cmsms();
		$db = cms_db();

		$query = 'SELECT MAX(modified_date) AS thedate FROM {content} c';
		$row = $db->GetRow($query);

		if ($row)
		{
			$this->content_last_modified_date = $db->UnixTimeStamp($row['thedate']);
		}
	}
	
	public function send_headers()
	{
		//header("Content-Type: " . cmsms()->variables['content-type'] . "; charset=" . CmsResponse::get_encoding());
		header("Content-Type: " . cmsms()->variables['content-type'] . "; charset=UTF-8");
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
		
		$id = CmsRequest::get_id_from_request();

		#If this is a case where a module doesn't want a template to be shown, just disable caching
		if (isset($id) && $id != '' && isset($_REQUEST[$id.'showtemplate']) && ($_REQUEST[$id.'showtemplate'] == 'false' || $_REQUEST[$id.'showtemplate'] == false))
		{
			$html = $smarty->fetch('template:notemplate') . "\n";
		}
		else
		{
			//$smarty->caching = false;
			//$smarty->compile_check = true;
			($smarty->is_cached('template:'.$this->template_id)?$cached="":$cached="not ");
			// Added HTTP_HOST here to work with multisites - SK
			$html = $smarty->fetch('template:'.$this->template_id/*.$_SERVER['HTTP_HOST']*/) . "\n";
			if (isset($_REQUEST['tmpfile']))
			{
				$smarty->clear_compiled_tpl('template:'.$this->template_id);
			}
		}

		if (!$cached)
		{
			Events::SendEvent('Core', 'ContentPostRenderNonCached', array(&$html));
		}

		Events::SendEvent('Core', 'ContentPostRender', array('content' => &$html));
		
		return $html;
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
	function LoadPageInfoFromSerializedData($data)
	{
		$pi = new PageInfo();
		$pi->SetInitialValues();
		$pi->content_id = $data['content_id'];
		$pi->content_title = $data['title'];
		$pi->content_menutext = $data['menutext'];
		$pi->content_hierarchy = $data['hierarchy'];
		$pi->template_id = $data['template_id'];
		$pi->template_encoding = $data['encoding'];
		$pi->cachable = false;

		return $pi;
	}

	static function load_page_info_by_content_alias($alias)
	{
		$result = false;

		$gCms = cmsms();
		$db = cms_db();

		$row = '';

		if (is_numeric($alias) && strpos($alias, '.') === FALSE && strpos($alias, ',') === FALSE) //Fix for postgres
		{ 
			$query = "SELECT c.content_id, c.content_name, c.content_alias, c.menu_text, c.titleattribute, c.hierarchy, c.metadata, c.id_hierarchy, c.prop_names, c.create_date AS c_create_date, c.modified_date AS c_date, c.cachable, c.last_modified_by, t.template_id, t.encoding, t.modified_date AS t_date FROM {templates} t INNER JOIN {content} c ON c.template_id = t.template_id WHERE (c.content_alias = ? OR c.content_id = ?) AND c.active = 1";
			$row = $db->GetRow($query, array($alias, $alias));
		}
		else
		{
			$query = "SELECT c.content_id, c.content_name, c.content_alias, c.menu_text, c.titleattribute, c.hierarchy, c.metadata, c.id_hierarchy, c.prop_names, c.create_date AS c_create_date, c.modified_date AS c_date, c.cachable, c.last_modified_by,t.template_id, t.encoding, t.modified_date AS t_date FROM {templates} t INNER JOIN {content} c ON c.template_id = t.template_id WHERE c.content_alias = ? AND c.active = 1";
			$row = $db->GetRow($query, array($alias));
		}

		if ($row)
		{
			$onepageinfo = new PageInfo();
			$onepageinfo->content_id = $row['content_id'];
			$onepageinfo->content_title = $row['content_name'];
			$onepageinfo->content_alias = $row['content_alias'];
			$onepageinfo->content_menutext = $row['menu_text'];
			$onepageinfo->content_titleattribute = $row['titleattribute'];
			$onepageinfo->content_hierarchy = $row['hierarchy'];
			$onepageinfo->content_id_hierarchy = $row['id_hierarchy'];
			$onepageinfo->content_metadata = $row['metadata'];
			$onepageinfo->content_created_date = $db->UnixTimeStamp($row['c_create_date']);
			$onepageinfo->content_modified_date = $db->UnixTimeStamp($row['c_date']);
            $onepageinfo->content_last_modified_by_id = $row['last_modified_by'];
			$onepageinfo->content_props = explode(',', $row['prop_names']);
			$onepageinfo->template_id = $row['template_id'];
			$onepageinfo->template_encoding = $row['encoding'];
			$onepageinfo->cachable = ($row['cachable'] == 1?true:false);
			$onepageinfo->template_modified_date = $db->UnixTimeStamp($row['t_date']);
			if( !$onepageinfo->cachable )
			  {
			    // calguy1000 - if the page is not cachable, don't cache
			    // the template either.  This trick forces smarty to 
			    // refresh the template cache.
			    $onepageinfo->template_modified_date = time() + 10;
			  }
			$result = $onepageinfo;
		}
		else
		{
			#Page isn't found.  Should we setup an alternate page?
			#if (get_site_preference('custom404template') > 0 && get_site_preference('enablecustom404') == "1")
			//First check if there is an error page set -- this overrides the enablecustom404 setting
			if ($alias == 'error404')
				return null;
			
			$result = PageInfoOperations::LoadPageInfoByContentAlias('error404');
			
			if ($result != null)
			{
				//Change header to 404
				header("HTTP/1.0 404 Not Found");
				header("Status: 404 Not Found");
			}
			else if (get_site_preference('enablecustom404') == "1")
			{
				$onepageinfo = new PageInfo();
				$onepageinfo->cachable = false;
				if (get_site_preference('custom404template') > 0)
					$onepageinfo->template_id = get_site_preference('custom404template');
				else
					$onepageinfo->template_id = 'notemplate';
				$onepageinfo->template_modified_date = time();
				$result = $onepageinfo;
			}
		}
		
		$gCms->variables['pageinfo'] = $result;
		
		return $result;
	}
	
	static function LoadPageInfoByContentAlias($alias)
	{
		return PageInfoOperations::load_page_info_by_content_alias($alias);
	}
}

# vim:ts=4 sw=4 noet
?>
