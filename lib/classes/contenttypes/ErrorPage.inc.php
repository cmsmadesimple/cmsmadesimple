<?php
# CMS - CMS Made Simple
# (c)2004-2009 by Ted Kulp (ted@cmsmadesimple.org)
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

require_once('Content.inc.php');
class ErrorPage extends Content
{
	var $doAliasCheck;
	var $error_types;
	
	function ErrorPage()
	{
		$this->ContentBase();
		$this->error_types = array('404' => lang('404description'));
		$this->doAliasCheck = false;
		$this->doAutoAliasIfEnabled = false;
	}
	
	function FriendlyName()
	{
		return lang('contenttype_errorpage');
	}
	
	function HasUsableLink()
	{
		return false;
	}

	function WantsChildren()
	{
		return false;
	}
	
	function FillParams($params)
	{
		parent::FillParams($params);
		$this->mShowInMenu = false;
		$this->mCachable = false;
		$this->SetPropertyValue('searchable', '0');
	}
	
	function EditAsArray($adding = false, $tab = 0, $showadmin = false)
	{
		$ret = parent::EditAsArray($adding, $tab, $showadmin);
		if (count($ret))
		{
			if ($tab == '0')
			{
				$dropdownopts = '<option value="">'.lang('none').'</option>';
				foreach ($this->error_types as $code=>$name)
				{
					$dropdownopts .= '<option value="error' . $code . '"';
					if ('error'.$code == $this->mAlias)
					{
						$dropdownopts .= ' selected="selected" ';
					}
					$dropdownopts .= ">{$name} ({$code})</option>";
				}
				
				$dropdown = array(lang('error_type').':', '<select name="alias">'.$dropdownopts.'</select>');
				$ret[2] = $dropdown;
			}
			else if ($tab == '1')
			{
				$ret[1] = array('', '<input type="hidden" name="showinmenu" value="0" />');
				$ret[2] = array('', '<input type="hidden" name="cachable" value="0" />');
				unset($ret[4]);
			}
		}
		return $ret;
	}
	
	function ValidateData()
	{
		$errors = parent::ValidateData();
		if ($errors == FALSE)
		{
			$errors = array();
		}
		
		//Do our own alias check
		if ($this->mAlias == '')
		{
			$errors[] = lang('nofieldgiven', array(lang('error_type')));
		}
		else if (in_array($this->mAlias, $this->error_types))
		{
			$errors[] = lang('nofieldgiven', array(lang('error_type')));
		}
		else if ($this->mAlias != $this->mOldAlias)
		{
			global $gCms;
			$contentops =& $gCms->GetContentOperations();
			$error = $contentops->CheckAliasError($this->mAlias, $this->mId);
			if ($error !== FALSE)
			{
				if ($error == lang('aliasalreadyused'))
				{
					$errors[] = lang('errorpagealreadyinuse');
				}
				else
				{
					$errors[] = $error;
				}
			}
		}
		
		return (count($errors) > 0 ? $errors : FALSE);
	}
}

# vim:ts=4 sw=4 noet
?>