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

class separator extends ContentBase
{
    function FriendlyName()
	{
		return 'Separator';
	}

	function SetProperties()
	{
		#Turn off caching
		$this->mCachable = false;
	}
	
	function WantsChildren()
	{
		return false;
	}

	function FillParams($params)
	{
		if (isset($params))
		{
			$this->mName = '--------';
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

	function Show()
	{
	}

	function EditAsArray($adding = false, $tab = 0, $showadmin = false)
	{
		$ret = array();

		array_push($ret,array(lang('parent').':',ContentManager::CreateHierarchyDropdown($this->mId, $this->mParentId)));
		array_push($ret,array(lang('active').':','<input type="checkbox" name="active"'.($this->mActive?' checked="checked"':'').' />'));
		array_push($ret,array(lang('showinmenu').':','<input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' />'));

		if (!$adding && $showadmin)
		{
			array_push($ret,array('Owner:',@UserOperations::GenerateDropdown($this->Owner())));
		}

		if ($adding || $showadmin)
		{
			array_push($ret,$this->ShowAdditionalEditors());
		}

		return $ret;
	}

	function GetURL($rewrite = true)
	{
		return '#';
	}
}

# vim:ts=4 sw=4 noet
?>
