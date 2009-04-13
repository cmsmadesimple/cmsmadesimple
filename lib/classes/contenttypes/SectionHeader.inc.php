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

    function SectionHeader() {
        $this->ContentBase();
        $this->mProperties->SetAllowedPropertyNames(array('extra1','extra2','extra3',
							  'image','thumbnail'));
    }

    function FriendlyName()
    {
      return lang('contenttype_sectionheader');
    }

    function SetProperties()
    {
      parent::SetProperties();

      #Turn off caching
      $this->mCachable = false;
    }

    function HasUsableLink()
    {
	return false;
    }

    function FillParams($params)
    {
	if (isset($params))
	{
	  if( isset($params['extra1']) )
	    {
	      $this->SetPropertyValue('extra1',trim($params['extra1']));
	    }
	  if( isset($params['extra2']) )
	    {
	      $this->SetPropertyValue('extra2',trim($params['extra2']));
	    }
	  if( isset($params['extra3']) )
	    {
	      $this->SetPropertyValue('extra3',trim($params['extra3']));
	    }
	  if( isset($params['image']) )
	    {
	      $this->SetPropertyValue('image',trim($params['image']));
	    }
	  if( isset($params['thumbnail']) )
	    {
	      $this->SetPropertyValue('thumbnail',trim($params['thumbnail']));
	    }
	    if (isset($params['title']))
	    {
		$this->mName = $params['title'];
	    }
	    if (isset($params['menutext']))
	    {
		$this->mMenuText = $params['menutext'];
	    }
	    if (isset($params['parent_id']))
	    {
		if ($this->mParentId != $params['parent_id'])
		{
		    $this->mHierarchy = '';
		    $this->mItemOrder = -1;
		}
		$this->mParentId = $params['parent_id'];
	    }
	    if (isset($params['active']))
	    {
		$this->mActive = true;
	    }
	    else
	    {
		$this->mActive = false;
	    }
	    if (isset($params['showinmenu']))
	    {
		$this->mShowInMenu = true;
	    }
	    else
	    {
		$this->mShowInMenu = false;
	    }
	    if (isset($params['alias']))
	    {
	      $this->SetAlias(trim($params['alias']),$this->doAutoAliasIfEnabled);
	    }
	    else if( $this->doAutoAliasIfEnabled)
            {
	      $this->SetAlias('');
	    }
	}
    }

    function ValidateData()
    {
	$result = true;
	$errors = array();
	
	if ($this->mName == '')
	{
	    $errors[]= lang('nofieldgiven',array(lang('title')));
	    $result = false;
	}

	if ($this->mMenuText == '')
	{
	    $errors[]= lang('nofieldgiven',array(lang('menutext')));
	    $result = false;
	}

	return (count($errors) > 0?$errors:FALSE);
    }

    function Show()
    {
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

    function GetURL($rewrite = true)
    {
	return '#';
    }
}

# vim:ts=4 sw=4 noet
?>
