<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
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
 * Class for doing template related functions.  Many of the Template object functions are just wrappers around these.
 *
 * @since		0.6
 * @package		CMS
 */
class CmsPageInfoOperations extends CmsObject
{
	static function load_page_info_by_content_alias($alias)
	{
		$result = null;

		$db = db();

		$row = '';

		if (is_numeric($alias) && strpos($alias, '.') === FALSE && strpos($alias, ',') === FALSE) //Fix for postgres
		{ 
			$query = "SELECT c.id, c.content_name, c.content_alias, c.menu_text, c.titleattribute, c.hierarchy, c.metadata, c.id_hierarchy, c.prop_names, c.create_date AS c_create_date, c.modified_date AS c_date, c.cachable, c.last_modified_by, t.template_id, t.encoding, t.modified_date AS t_date FROM ".cms_db_prefix()."templates t INNER JOIN ".cms_db_prefix()."content c ON c.template_id = t.template_id WHERE (c.content_alias = ? OR c.id = ?) AND c.active = 1";
			$row = &$db->GetRow($query, array($alias, $alias));
		}
		else
		{
			$query = "SELECT c.id, c.content_name, c.content_alias, c.menu_text, c.titleattribute, c.hierarchy, c.metadata, c.id_hierarchy, c.prop_names, c.create_date AS c_create_date, c.modified_date AS c_date, c.cachable, c.last_modified_by,t.template_id, t.encoding, t.modified_date AS t_date FROM ".cms_db_prefix()."templates t INNER JOIN ".cms_db_prefix()."content c ON c.template_id = t.template_id WHERE c.content_alias = ? AND c.active = 1";
			$row = &$db->GetRow($query, array($alias));
		}

		if ($row)
		{
			$onepageinfo = new PageInfo();
			$onepageinfo->content_id = $row['id'];
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
			$onepageinfo->template_modified_date = $db->UnixTimeStamp($row['t_date']);
			$onepageinfo->cachable = ($row['cachable'] == 1?true:false);
			$result = $onepageinfo;
		}
		else
		{
			#Page isn't found.  Should we setup an alternate page?
			#if (get_site_preference('custom404template') > 0 && get_site_preference('enablecustom404') == "1")
			if (get_site_preference('enablecustom404') == "1")
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
		
		$gCms = cmsms();

		$gCms->variables['pageinfo'] =& $result;

		$gCms->variables['content_id'] = $result->content_id;
		$gCms->variables['page'] = $result->content_alias;
		$gCms->variables['page_id'] = $result->content_id;

		$gCms->variables['page_name'] = $result->content_alias;
		$gCms->variables['position'] = $result->content_hierarchy;

		$gCms->variables['friendly_position'] = CmsContentOperations::create_friendly_hierarchy_position($result->content_hierarchy);
		
		$smarty = smarty();

		$smarty->assign('content_id', $result->content_id);
		$smarty->assign('page', $result->content_alias);
		$smarty->assign('page_id', $result->content_id);	
		$smarty->assign('page_name', $result->content_alias);
		$smarty->assign('page_alias', $result->content_alias);
		$smarty->assign('position', $result->content_hierarchy);
		$smarty->assign('friendly_position', $gCms->variables['friendly_position']);

		return $result;
	}
	static function LoadPageInfoByContentAlias($alias)
	{
		return CmsPageInfoOperations::load_page_info_by_content_alias($alias);
	}

}

# vim:ts=4 sw=4 noet
?>