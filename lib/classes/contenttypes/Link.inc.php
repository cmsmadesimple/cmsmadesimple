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

class Link extends ContentBase
{

    function Link() {
        $this->ContentBase();
        $this->mProperties->SetAllowedPropertyNames(array('url', 'target'));
    }

    function FriendlyName()
    {
	return 'Link';
    }

    function SetProperties()
    {
	$this->mProperties->Add('string', 'url');
	$this->mProperties->Add('string', 'target');

        #Turn off caching
	$this->mCachable = false;
    }

    function FillParams($params)
    {
	if (isset($params))
	{
	    $parameters = array('url', 'target');
	    foreach ($parameters as $oneparam)
	    {
		if (isset($params[$oneparam]))
		{
		    $this->SetPropertyValue($oneparam, $params[$oneparam]);
		}
	    }
	    if (isset($params['title']))
	    {
		$this->mName = $params['title'];
	    }
	    if (isset($params['menutext']))
	    {
		$this->mMenuText = $params['menutext'];
	    }
	    if (isset($params['accesskey']))
	    {
		$this->mAccessKey = $params['accesskey'];
	    }
	    if (isset($params['titleattribute']))
	    {
		$this->mTitleAttribute = $params['titleattribute'];
	    }
	    if (isset($params['tabindex']))
	    {
		$this->mTabIndex = $params['tabindex'];
	    }
	    if (isset($params['alias']))
	    {
		$this->mAlias = $params['alias'];
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
	}
    }

    function ValidateData()
    {
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

	if ($this->GetPropertyValue('url') == '')
	{
	    $errors[]= lang('nofieldgiven',array(lang('url')));
	    $result = false;
	}

	return (count($errors) > 0?$errors:FALSE);
    }

    function Show()
    {
    }

    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
	global $gCms;
	
	$ret = array();
	
	$ret[]= array(lang('title').':','<input type="text" name="title" value="'.cms_htmlentities($this->mName).'" />');
	$ret[]= array(lang('menutext').':','<input type="text" name="menutext" value="'.cms_htmlentities($this->mMenuText).'" />');
    if (check_permission(get_userid(), 'Modify Page Structure') || ($adding == true && check_permission(get_userid(), 'Add Pages')))
    {
		$contentops =& $gCms->GetContentOperations();
    	$ret[]= array(lang('parent').':', $contentops->CreateHierarchyDropdown($this->mId, $this->mParentId));
    }
	$ret[]= array(lang('url').':','<input type="text" name="url" size="80" value="'.cms_htmlentities($this->GetPropertyValue('url')).'" />');
	$text = '<option value="">(none)</option>';
	$text .= '<option value="_blank"'.($this->GetPropertyValue('target')=='_blank'?' selected="selected"':'').'>_blank</option>';
	$text .= '<option value="_parent"'.($this->GetPropertyValue('target')=='_parent'?' selected="selected"':'').'>_parent</option>';
	$text .= '<option value="_self"'.($this->GetPropertyValue('target')=='_self'?' selected="selected"':'').'>_self</option>';
	$text .= '<option value="_top"'.($this->GetPropertyValue('target')=='_top'?' selected="selected"':'').'>_top</option>';
	$ret[]= array(lang('target').':','<select name="target">'.$text.'</select>');
	$ret[]= array(lang('active').':','<input type="checkbox" name="active"'.($this->mActive?' checked="checked"':'').' />');
	$ret[]= array(lang('showinmenu').':','<input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' />');
	$ret[]= array(lang('titleattribute').':','<input type="text" name="titleattribute" maxlength="255" size="80" value="'.cms_htmlentities($this->mTitleAttribute).'" />');
	$ret[]= array(lang('tabindex').':','<input type="text" name="tabindex" maxlength="10" value="'.cms_htmlentities($this->mTabIndex).'" />');
	$ret[]= array(lang('accesskey').':','<input type="text" name="accesskey" maxlength="5" value="'.cms_htmlentities($this->mAccessKey).'" />');

	if (!$adding && $showadmin)
	{
		$userops =& $gCms->GetUserOperations();
	    $ret[]= array(lang('owner').':', $userops->GenerateDropdown($this->Owner()));
	}

	if ($adding || $showadmin)
	{
	    $ret[]= $this->ShowAdditionalEditors();
	}

	return $ret;
    }

    function GetURL($rewrite = true)
    {
	return cms_htmlentities($this->GetPropertyValue('url'));
    }
}

# vim:ts=4 sw=4 noet
?>
