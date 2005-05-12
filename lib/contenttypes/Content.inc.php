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
	var $additionalContentBlocks;
	
	function content()
	{
		$this->ContentBase();
		$this->additionalContentBlocks = array();
	}
	
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

	function GetTabDefinitions()
	{
		return array('Basic', 'Advanced');
		#return array();
	}

	function FillParams($params)
	{
		global $gCms;
		$config = $gCms->config;

		if (isset($params))
		{
			$parameters = array('content_en', 'headtags');

			//pick up the template id before we do parameters
			if (isset($params['template_id']))
			{
				$this->mTemplateId = $params['template_id'];
			}

			// add additional content blocks
			$this->GetAdditionalContentBlocks();
			foreach($this->additionalContentBlocks as $blockName => $blockNameId)
			{
				$parameters[] = $blockNameId;
			}
			
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
		return $this->GetPropertyValue('content_en');
	}

	function IsDefaultPossible()
	{
		return TRUE;
	}


    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
		global $gCms;
		$config = $gCms->config;
		$ret = array();
		$stylesheet = '';
		if ($this->TemplateId() > 0)
		{
			$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
		}
		else
		{
			$defaulttemplate = TemplateOperations::LoadDefaultTemplate();
			if (isset($defaulttemplate))
			{
				$this->mTemplateId = $defaulttemplate->id;
				$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
			}
		}
		if ($tab == 0)
		{
			array_push($ret, '<tr><th>'.lang('title').':</th><td><input type="text" name="title" value="'.cms_htmlentities($this->mName).'" /></td></tr>');
			array_push($ret, '<tr><th>'.lang('menutext').':</th><td><input type="text" name="menutext" value="'.cms_htmlentities($this->mMenuText).'" /></td></tr>');
			if (!($config['auto_alias_content'] == true && $adding))
			{
				array_push($ret, '<tr><th>'.lang('pagealias').':</th><td><input type="text" name="alias" value="'.$this->mAlias.'" /></td></tr>');
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
			array_push($ret, '<tr><th>'.lang('template').':</th><td>'.TemplateOperations::TemplateDropdown('template_id', $this->mTemplateId, 'onchange="'.$additionalcall.'document.contentform.submit()"').'</td></tr>'."\n");
			array_push($ret, '<tr><th>'.lang('content').':</th><td>'.create_textarea(true, $this->GetPropertyValue('content_en'), 'content_en', '', 'content_en', '', $stylesheet, '80', '11').'</td></tr>'."\n");
			
			// add additional content blocks if required
			$this->GetAdditionalContentBlocks(); // this is needed as this is the first time we get a call to our class when editing.
			foreach($this->additionalContentBlocks as $blockName => $blockNameId)
			{
				array_push($ret, '<tr><th>'.ucwords($blockName).':</th><td>'.create_textarea(true, $this->GetPropertyValue($blockNameId), $blockNameId, '', $blockNameId, '', $stylesheet, 80, 10).'</td></tr>'."\n");
			}
		}
		if ($tab == 1)
		{
			array_push($ret, '<tr><th>'.lang('headtags').':</th><td>'.create_textarea(false, $this->GetPropertyValue('headtags'), 'headtags', '', 'headtags', '', '', 80, 5).'</td></tr>'."\n");
			array_push($ret, '<tr><th>'.lang('active').':</th><td><input type="checkbox" name="active"'.($this->mActive?' checked="checked"':'').' /></td></tr>');
			array_push($ret, '<tr><th>'.lang('showinmenu').':</th><td><input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' /></td></tr>');
			array_push($ret, '<tr><th>'.lang('cachable').':</th><td><input type="checkbox" name="cachable"'.($this->mCachable?' checked="checked"':'').' /></td></tr>');
			array_push($ret, '<tr><th>'.lang('parent').':</th><td>'.ContentManager::CreateHierarchyDropdown($this->mId, $this->mParentId).'</td></tr>');

			if (!$adding && $showadmin)
			{
				array_push($ret, '<tr><th>Owner:</th><td>'.@UserOperations::GenerateDropdown($this->Owner()).'</td></tr>');
			}

			if ($adding || $showadmin)
			{
				array_push($ret, $this->ShowAdditionalEditors());
			}
		}
        return $ret;
    }

	function Edit($adding = false, $tab = 0, $showadmin = false)
	{
		global $gCms;
		$config = $gCms->config;
		$text = "";
		$stylesheet = '';
		if ($this->TemplateId() > 0)
		{
			$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
		}
		else
		{
			$defaulttemplate = TemplateOperations::LoadDefaultTemplate();
			if (isset($defaulttemplate))
			{
				$this->mTemplateId = $defaulttemplate->id;
				$stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
			}
		}

		if ($tab == 0)
		{
			$text .= '<tr><th>'.lang('title').':</th><td><input type="text" name="title" value="'.cms_htmlentities($this->mName).'" /></td></tr>';
			$text .= '<tr><th>'.lang('menutext').':</th><td><input type="text" name="menutext" value="'.cms_htmlentities($this->mMenuText).'" /></td></tr>';
			#if (!($config['auto_alias_content'] == true && $this->mAlias == ''))
			if (!($config['auto_alias_content'] == true && $adding))
			{
				$text .= '<tr><th>'.lang('pagealias').':</th><td><input type="text" name="alias" value="'.$this->mAlias.'" /></td></tr>';
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
			$text .= '<tr><th>'.lang('template').':</th><td>'.TemplateOperations::TemplateDropdown('template_id', $this->mTemplateId, 'onchange="'.$additionalcall.'document.contentform.submit()"').'</td></tr>'."\n";
			$text .= '<tr><th>'.lang('content').':</th><td>'.create_textarea(true, $this->GetPropertyValue('content_en'), 'content_en', '', 'content_en', '', $stylesheet, '80', '11').'</td></tr>'."\n";
			
			// add additional content blocks if required
			$this->GetAdditionalContentBlocks(); // this is needed as this is the first time we get a call to our class when editing.
			foreach($this->additionalContentBlocks as $blockName => $blockNameId)
			{
				$text .= '<tr><th>'.ucwords($blockName).':</th><td>'.create_textarea(true, $this->GetPropertyValue($blockNameId), $blockNameId, '', $blockNameId, '', $stylesheet, 80, 10).'</td></tr>'."\n";
			}
		}

		if ($tab == 1)
		{
			$text .= '<tr><th>'.lang('headtags').':</th><td>'.create_textarea(false, $this->GetPropertyValue('headtags'), 'headtags', '', 'headtags', '', '', 80, 5).'</td></tr>'."\n";
			$text .= '<tr><th>'.lang('active').':</th><td><input type="checkbox" name="active"'.($this->mActive?' checked="checked"':'').' /></td></tr>';
			$text .= '<tr><th>'.lang('showinmenu').':</th><td><input type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' /></td></tr>';
			$text .= '<tr><th>'.lang('cachable').':</th><td><input type="checkbox" name="cachable"'.($this->mCachable?' checked="checked"':'').' /></td></tr>';
			$text .= '<tr><th>'.lang('parent').':</th><td>'.ContentManager::CreateHierarchyDropdown($this->mId, $this->mParentId).'</td></tr>';

			if (!$adding && $showadmin)
			{
				$text .= '<tr><th>Owner:</th><td>'.@UserOperations::GenerateDropdown($this->Owner()).'</td></tr>';
			}

			if ($adding || $showadmin)
			{
				$text .= $this->ShowAdditionalEditors();
			}
		}
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
			$error = @ContentManager::CheckAliasError($this->mAlias, $this->mId);
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
				$url = $config["root_url"]."/".$this->mAlias.$config["page_extension"];
			}
			else
			{
				$url = $config["root_url"]."/".$this->mId.$config["page_extension"];
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
	
	function GetAdditionalContentBlocks()
	{
		$result = false;
		$this->additionalContentBlocks = array();
		$template = TemplateOperations::LoadTemplateByID($this->TemplateId()); /* @var $template Template */
		if($template !== false)
		{
			$content = $template->content;
			
			$pattern = '{content[ ]+block[ ]*=["\']([a-zA-z0-9 -_]*)["\'][a-zA-z0-9\'" =_-]*}';
			$matches = array();
			$result = preg_match_all($pattern, $content, $matches);
			if($result)
			{
				if(count($matches[1]) > 0)
				{
					$additionalContentBlocks = $matches[1];
					
					// add the ContentProperties
					foreach($additionalContentBlocks as $blockName)
					{
						$blockNameId = str_replace(' ', '_', $blockName);
						$this->additionalContentBlocks[$blockName] = $blockNameId;
						
						if(!array_key_exists($blockName, $this->mProperties->mPropertyTypes))
						{
							$this->mProperties->Add("string", $blockNameId);
						}
					}
					
					// force a load 
					$this->mProperties->Load($this->mId);

					$result = true;
				}
			}
		}
		return $result;
	}
	
	function ContentPreRender($tpl_source)
	{
		// check for additional content blocks
		$this->GetAdditionalContentBlocks();

		// find the {content block='XXX'} and store into an array
		$pattern = '{content[ ]+block[ ]*=["\']([a-zA-z0-9 -_]*)["\'][a-zA-z0-9\'" =_-]*}';
		$matches = array();
		$result = preg_match_all($pattern, $tpl_source, $matches);
		if($result)
		{
			$count = count($matches);
			// iterate over each additional content block and replace with the text from the property
			for($i = 0; $i < $count; $i++)
			{
				$pattern = $matches[0][$i];
				$blockNameId = $this->additionalContentBlocks[$matches[1][$i]];
				$replace = $this->GetPropertyValue($blockNameId);
				$tpl_source = str_replace('{' . $pattern . '}', $replace, $tpl_source);
			}
		}		
		return $tpl_source;
	}
}

# vim:ts=4 sw=4 noet
?>
