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
#$Id$

/**
 * Class definition and methods for Section Header content type
 *
 * @package CMS
 * @license GPL
 */

/**
 * Class for CMS Made Simple Section Header content type
 *
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class SectionHeader extends ContentBase
{

    function FriendlyName()
    {
      return lang('contenttype_sectionheader');
    }

    function SetProperties()
    {
      parent::SetProperties();
      $this->RemoveProperty('secure',0);
      $this->RemoveProperty('accesskey','');
      $this->RemoveProperty('title','');
      //$this->RemoveProperty('showinmenu',true);
      $this->RemoveProperty('cachable',true);
      $this->RemoveProperty('target','');
	  $this->RemoveProperty('alias','');
	  $this->RemoveProperty('page_url','');

      #Turn off caching
      $this->mCachable = false;
    }

    function HasUsableLink()
    {
	return false;
    }


    function TabNames()
    {
      $res = array(lang('main'));
      if( check_permission(get_userid(),'Manage All Content') )
	{
	  $res[] = lang('options');
	}
      return $res;
    }

    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
      switch($tab)
	{
	case '0':
	  return $this->display_attributes($adding);
	  break;
	case '1':
	  return $this->display_attributes($adding,1);
	  break;
	}
    }

    function ValidateData()
    {
      $res = parent::ValidateData();
      if( is_array($res) && $this->mId < 1 )
	{
	  // some error occurred..
	  // reset the menu text
	  // and the alias
	  $this->mName = '';
	  $this->mMenuText = '';
	  $this->mAlias = '';
	}
      $this->mTemplateId = -1;
      return $res;
    }

    function GetURL($rewrite = true)
    {
	return '#';
    }

    function IsViewable()
    {
      return FALSE;
    }
}

# vim:ts=4 sw=4 noet
?>
