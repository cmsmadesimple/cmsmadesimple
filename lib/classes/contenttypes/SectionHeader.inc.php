<?php
# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

class SectionHeader extends ContentBase
{

    function FriendlyName()
    {
      return lang('contenttype_sectionheader');
    }

    function SetProperties()
    {
      parent::SetProperties();
      $this->RemoveProperty('alias','');
      $this->RemoveProperty('accesskey','');
      $this->RemoveProperty('title','');
      $this->RemoveProperty('showinmenu',true);
      $this->RemoveProperty('cachable',true);
      $this->RemoveProperty('target','');

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
      if( check_permission(get_userid(),'Modify Page Structure') )
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
      $this->mTemplateId = -1;
      return $res;
    }

    function GetURL($rewrite = true)
    {
	return '#';
    }
}

# vim:ts=4 sw=4 noet
?>
