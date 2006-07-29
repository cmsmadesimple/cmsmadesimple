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
	var $addtContentBlocksLoaded;
	
	function content()
	{
		$this->ContentBase();
		$this->additionalContentBlocks = array();
		$this->addtContentBlocksLoaded = false;
	}
	
	function FriendlyName()
	{
		return 'Content';
	}

	function SetProperties()
	{
		$this->mProperties->Add("string", "content_en"); //For later language support

		#Turn on preview
		$this->mPreview = true;
	}

	/**
	 * Use the ReadyForEdit callback to get the additional content blocks loaded.
	 */
	function ReadyForEdit()
	{
		$this->GetAdditionalContentBlocks();
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
			$parameters = array('content_en');

			//pick up the template id before we do parameters
			if (isset($params['template_id']))
			{
				if ($this->mTemplateId != $params['template_id'])
					$this->addtContentBlocksLoaded = false;
				$this->mTemplateId = $params['template_id'];
			}

			// add additional content blocks
			$this->GetAdditionalContentBlocks();
			foreach($this->additionalContentBlocks as $blockName => $blockNameId)
			{
				$parameters[] = $blockNameId['id'];
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
			if (isset($params['metadata']))
			{
				$this->mMetadata = $params['metadata'];
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
			if (isset($params['menutext']))
			{
				$this->mMenuText = $params['menutext'];
			}
			if (isset($params['alias']))
			{
				$this->SetAlias($params['alias']);
			}
			/*
			else
			{
				$this->SetAlias('');
			}
			*/
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

	function Show($param = 'content_en')
	{
		// check for additional content blocks
		$this->GetAdditionalContentBlocks();

		return $this->GetPropertyValue($param);
	}

	function IsDefaultPossible()
	{
		return TRUE;
	}

	function TabNames()
	{
		return array(lang('main'), lang('options'));
	}

    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
		global $gCms;
		$config = $gCms->GetConfig();
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
			$ret[]= array(lang('title').':','<input type="text" name="title" value="'.cms_htmlentities($this->mName).'" />');
			$ret[]= array(lang('menutext').':','<input type="text" name="menutext" value="'.cms_htmlentities($this->mMenuText).'" />');
			$ret[]= array(lang('parent').':',ContentManager::CreateHierarchyDropdown($this->mId, $this->mParentId));
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
			
			$ret[]= array(lang('template').':',TemplateOperations::TemplateDropdown('template_id', $this->mTemplateId, 'onchange="document.contentform.submit()"'));
			$ret[]= array(lang('content').':',create_textarea(true, $this->GetPropertyValue('content_en'), 'content_en', '', 'content_en', '', $stylesheet));
			
			// add additional content blocks if required
			$this->GetAdditionalContentBlocks(); // this is needed as this is the first time we get a call to our class when editing.
			foreach($this->additionalContentBlocks as $blockName => $blockNameId)
			{
				if ($blockNameId['oneline'] == 'true')
				{
					$ret[]= array(ucwords($blockName).':','<input type="text" name="'.$blockNameId['id'].'" value="'.$this->GetPropertyValue($blockNameId['id']).'" />');
				}
				else
				{
					$ret[]= array(ucwords($blockName).':',create_textarea(($blockNameId['usewysiwyg'] == 'false'?false:true), $this->GetPropertyValue($blockNameId['id']), $blockNameId['id'], '', $blockNameId['id'], '', $stylesheet));
				}
			}
		}
		if ($tab == 1)
		{
		  
			$ret[]= array(lang('active').':','<input class="pagecheckbox" type="checkbox" name="active"'.($this->mActive?' checked="checked"':'').' />');
			$ret[]= array(lang('showinmenu').':','<input class="pagecheckbox" type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' />');
			$ret[]= array(lang('cachable').':','<input class="pagecheckbox" type="checkbox" name="cachable"'.($this->mCachable?' checked="checked"':'').' />');

			$ret[]=	array(lang('pagealias').':','<input type="text" name="alias" value="'.$this->mAlias.'" />');

			$ret[]= array(lang('metadata').':',create_textarea(false, $this->Metadata(), 'metadata', 'pagesmalltextarea', 'metadata', '', '', '80', '6'));

			$ret[]= array(lang('titleattribute').':','<input type="text" name="titleattribute" maxlength="255" size="80" value="'.cms_htmlentities($this->mTitleAttribute).'" />');
			$ret[]= array(lang('tabindex').':','<input type="text" name="tabindex" maxlength="10" value="'.cms_htmlentities($this->mTabIndex).'" />');
			$ret[]= array(lang('accesskey').':','<input type="text" name="accesskey" maxlength="5" value="'.cms_htmlentities($this->mAccessKey).'" />');

			if (!$adding && $showadmin)
			{
				$ret[]= array(lang('owner').':',@UserOperations::GenerateDropdown($this->Owner()));
			}

			if ($adding || $showadmin)
			{
			  	$ret[]= $this->ShowAdditionalEditors();
			}
		}
        return $ret;
    }


	function ValidateData()
	{
		$errors = array();

		if ($this->mName == '')
		{
		  if ($this->mMenuText != '')
		    {
		      $this->mName = $this->mMenuText;
		    }
		  else
		    {
		      $errors[]= lang('nofieldgiven',array(lang('title')));
		      $result = false;
		    }
		}

		if ($this->mMenuText == '')
		{
		  if ($this->mName != '')
		    {
		      $this->mMenuText = $this->mName;
		    }
		  else
		    {
		      	$errors[]=lang('nofieldgiven',array(lang('menutext')));
			$result = false;
		    }
		}
		
		if ($this->mAlias != $this->mOldAlias)
		{
			$error = @ContentManager::CheckAliasError($this->mAlias, $this->mId);
			if ($error !== FALSE)
			{
				$errors[]= $error;
				$result = false;
			}
		}

		if ($this->mTemplateId == '')
		{
			$errors[]= lang('nofieldgiven',array(lang('template')));
			$result = false;
		}

		if ($this->GetPropertyValue('content_en') == '')
		{
			$errors[]= lang('nofieldgiven',array(lang('content')));
			$result = false;
		}

		return (count($errors) > 0?$errors:FALSE);
	}

	function GetAdditionalContentBlocks()
	{
		$result = false;
		if ($this->addtContentBlocksLoaded == false)
		{
			$this->additionalContentBlocks = array();
			if ($this->TemplateId() && $this->TemplateId() > -1)
				$template = TemplateOperations::LoadTemplateByID($this->TemplateId()); /* @var $template Template */
			else
				$template = TemplateOperations::LoadDefaultTemplate();

			if($template !== false)
			{
				$content = $template->content;
				
				$pattern = '/{content([^}]*)}/';
				$pattern2 = '/([a-zA-z0-9]*)=["\']([^"\']+)["\']/';
				$matches = array();
				$result = preg_match_all($pattern, $content, $matches);

				if ($result && count($matches[1]) > 0)
				{
					foreach ($matches[1] as $wholetag)
					{
						$morematches = array();
						$result2 = preg_match_all($pattern2, $wholetag, $morematches);
						if ($result2)
						{
							$keyval = array();
							for ($i = 0; $i < count($morematches[1]); $i++)
							{
								$keyval[$morematches[1][$i]] = $morematches[2][$i];
							}

							$id = '';
							$name = '';
							$usewysiwyg = 'true';
							$oneline = 'false';

							foreach ($keyval as $key=>$val)
							{
								switch($key)
								{
									case 'block':
										$id = str_replace(' ', '_', $val);
										$name = $val;

										if(!array_key_exists($val, $this->mProperties->mPropertyTypes))
										{
											$this->mProperties->Add("string", $id);
										}

										break;
									case 'wysiwyg':
										$usewysiwyg = $val;

										break;
									case 'oneline':
										$oneline = $val;

										break;
									default:
										break;
								}
							}

							$this->additionalContentBlocks[$name]['id'] = $id;
							$this->additionalContentBlocks[$name]['usewysiwyg'] = $usewysiwyg;
							$this->additionalContentBlocks[$name]['oneline'] = $oneline;
							
						}
					}

					// force a load 
					$this->mProperties->Load($this->mId);

					$result = true;
				}
			}
			$this->addtContentBlocksLoaded = true;
		}
		return $result;
	}
	
	function ContentPreRender($tpl_source)
	{
		// check for additional content blocks
		$this->GetAdditionalContentBlocks();

		return $tpl_source;
	}
}

# vim:ts=4 sw=4 noet
?>
