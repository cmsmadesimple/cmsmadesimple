<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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
#
#$Id$

/**
 * PageInfo class definition and related functions
 * @package CMS 
 * @license GPL
 * @deprecated
 */


/**
 * Page Info -- Represents a "page" which consists of different variables virtually
 * composited together.
 *
 * @since		0.11
 * @package		CMS
 * @license		GPL
 * @deprecated
 */
class PageInfo
{
	function __get($name)
	{
		global $gCms;
		$contentobj = $gCms->variables['content_obj'];
		if ($contentobj != null)
		{
			$template = null;
			$templateops =& $gCms->GetTemplateOperations();
			if ($contentobj->TemplateId() && $contentobj->TemplateId() > -1)
			{
				$template = $templateops->LoadTemplateByID($contentobj->TemplateId());
			}
			else
			{
				$template = $templateops->LoadDefaultTemplate();
			}
			
			switch ($name)
			{
				case 'content_id':
				{
					return $contentobj->Id();
				}
				case 'content_title':
				{
					return $contentobj->Name();
				}
				case 'content_alias':
				{
					return $contentobj->Alias();
				}
				case 'content_menutext':
				{
					return $contentobj->MenuText();
				}
				case 'content_titleattribute':
				{
					return $contentobj->TitleAttribute();
				}
				case 'content_hierarchy':
				{
					return $contentobj->Hierarchy();
				}
				case 'content_id_hierarchy':
				{
					return $contentobj->IdHierarchy();
				}
				case 'content_type':
				{
					return $contentobj->Type();
				}
				case 'content_props':
				{
					return $contentobj->mProperties->mPropertyNames;
				}
				case 'content_metadata':
				{
					return $contentobj->Metadata();
				}
				case 'content_modified_date':
				{
					return $contentobj->Metadata();
				}
				case 'content_created_date':
				{
					return $contentobj->GetCreationDate();
				}
				case 'content_last_modified_date':
				{
					return $contentobj->GetModifiedDate();
				}
				case 'content_last_modified_by_id':
				{
					return $contentobj->LastModifiedBy();
				}
				case 'template_id':
				{
					return ($template != null ? $template->Id() : null);
				}
				case 'template_encoding':
				{
					return ($template != null ? $template->encoding : null);
				}
				case 'template_modified_date':
				{
					return ($template != null ? $template->modified_date : null);
				}
				case 'cachable':
				{
					return $contentobj->Metadata();
				}
				default:
				{
					return null;
				}
			}
		}
	}
}

# vim:ts=4 sw=4 noet
?>