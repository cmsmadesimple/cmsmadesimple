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

class content extends ContentBase
{
	function FriendlyName()
	{
		return 'Content';
	}

	function SetProperties()
	{
		$this->mProperties->Add("string", "content_en"); //For later language support
		$this->mProperties->Add("string", "headtags");

		#Turn on preview
		$this->mPreview = true;
	}

	function FillParams($params)
	{
		global $gCms;
		$config = $gCms->config;

		if (isset($params))
		{
			$parameters = array('content_en', 'headtags');
			foreach ($parameters as $oneparam)
			{
				if (isset($params[$oneparam]))
				{
					$this->mProperties->SetValue($oneparam, $params[$oneparam]);
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
			if (isset($params['template_id']))
			{
				$this->mTemplateId = $params['template_id'];
			}
			if (isset($params['alias']))
			{
				$this->SetAlias($params['alias']);
			}
			else
			{
				$this->SetAlias('');
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
			if (isset($params['cachable']))
			{
				$this->mCachable = true;
			}
			else
			{
				$this->mCachable = false;
			}
		}
	}

	function Show()
	{
		return $this->mProperties->GetValue('content_en');
	}

	function Edit($adding = false)
	{
		global $gCms;
		$config = $gCms->config;
		$text = "";
		$stylesheet = '';
		if ($this->TemplateId() > 0)
		{
			$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
		}

		$text .= '<tr><td>'.lang('title').':</td><td><input type="text" name="title" value="'.$this->mName.'" /></td></tr>';
		$text .= '<tr><td>'.lang('menutext').':</td><td><input type="text" name="menutext" value="'.$this->mMenuText.'" /></td></tr>';
		#if (!($config['auto_alias_content'] == true && $this->mAlias == ''))
		if (!($config['auto_alias_content'] == true && $adding))
		{
			$text .= '<tr><td>'.lang('pagealias').':</td><td><input type="text" name="alias" value="'.$this->mAlias.'" /></td></tr>';
		}
		$additionalcall = '';
		foreach($gCms->modules as $key=>$value)
		{
			if (get_preference(get_userid(), 'wysiwyg')!="" && 
				$gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true &&
				$gCms->modules[$key]['object']->IsWYSIWYG() &&
				$gCms->modules[$key]['object']->GetName()==get_preference(get_userid(), 'wysiwyg'))
			{
				$additionalcall = $gCms->modules[$key]['object']->WYSIWYGPageFormSubmit();
			}
		}
//		$text .= '<!-- userid = '.get_userid().' wysiwyg = '.get_preference(get_userid(), 'wysiwyg').' -->';
		$text .= '<tr><td>'.lang('template').':</td><td>'.TemplateOperations::TemplateDropdown('template_id', $this->mTemplateId, 'onchange="'.$additionalcall.'document.contentform.submit()"').'</td></tr>';
		$text .= '<tr><td>'.lang('content').':</td><td>'.create_textarea(true, $this->mProperties->GetValue('content_en'), 'content_en', 'syntaxHighlight', 'content_en', '', $stylesheet).'</td></tr>'."\n";
		$text .= '<tr><td>'.lang('headtags').':</td><td>'.create_textarea(false, $this->mProperties->GetValue('headtags'), 'headtags', 'syntaxHighlight', 'headtags').'</td></tr>'."\n";
		$text .= '<tr><td>'.lang('active').':</td><td><input type="checkbox" name="active"'.($this->mActive?' checked="checked"':'').' /></td></tr>';
		$text .= '<tr><td>'.lang('showinmenu').':</td><td><input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' /></td></tr>';
		$text .= '<tr><td>'.lang('cachable').':</td><td><input type="checkbox" name="cachable"'.($this->mCachable?' checked="checked"':'').' /></td></tr>';
		$text .= '<tr><td>'.lang('parent').':</td><td>'.ContentManager::CreateHierarchyDropdown($this->mId, $this->mParentId).'</td></tr>';
		return $text;
	}

	function ValidateData()
	{
		$errors = array();

		if ($this->mName == '')
		{
			array_push($errors, lang('nofieldgiven',array(lang('title'))));
			$result = false;
		}

		if ($this->mMenuText == '')
		{
			array_push($errors, lang('nofieldgiven',array(lang('menutext'))));
			$result = false;
		}
		
		if ($this->mAlias != $this->mOldAlias)
		{
			$error = @ContentManager::CheckAliasError($this->mAlias);
			if ($error !== FALSE)
			{
				array_push($errors, $error);
				$result = false;
			}
		}

		if ($this->mTemplateId == '')
		{
			array_push($errors, lang('nofieldgiven',array(lang('template'))));
			$result = false;
		}

		if ($this->GetPropertyValue('content_en') == '')
		{
			array_push($errors, lang('nofieldgiven',array(lang('content'))));
			$result = false;
		}

		return (count($errors) > 0?$errors:FALSE);
	}

	function GetURL()
	{
		global $config;
		$url = "";

		if ($config["assume_mod_rewrite"])
		{
			if ($this->mAlias != '')
			{
				$url = $config["root_url"]."/".$this->mAlias.".shtml";
			}
			else
			{
				$url = $config["root_url"]."/".$this->mId.".shtml";
			}
		}
		else
		{
			if ($this->mAlias != '')
			{
				$url = $config["root_url"]."/index.php?".$config["query_var"]."=".$this->mAlias;
			}
			else
			{
				$url = $config["root_url"]."/index.php?".$config["query_var"]."=".$this->mId;
			}
		}

		return $url;
	}
}

# vim:ts=4 sw=4 noet
?>
