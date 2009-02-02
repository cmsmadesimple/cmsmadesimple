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

class Content extends ContentBase
{
    var $additionalContentBlocks;
    var $addtContentBlocksLoaded;
	
    function Content()
    {
	$this->ContentBase();
	$this->mProperties->SetAllowedPropertyNames(array('content_en','target','pagedata',
							  'extra1','extra2','extra3','searchable','image','thumbnail','allow_wysiwyg'));
	$this->additionalContentBlocks = array();
	$this->addtContentBlocksLoaded = false;
    }

    function IsCopyable()
    {
        return TRUE;
    }

    function FriendlyName()
    {
      return lang('contenttype_content');
    }

    function SetProperties()
    {
	$this->mProperties->Add('string', 'content_en'); //For later language support
	$this->mProperties->Add('string', 'target');
	$this->mProperties->Add('string', 'pagedata'); 
	$this->mProperties->Add('string', 'extra1'); 
	$this->mProperties->Add('string', 'extra2'); 
	$this->mProperties->Add('string', 'extra3'); 
	$this->mProperties->Add('string', 'image'); 
	$this->mProperties->Add('string', 'thumbnail'); 
	$this->mProperties->Add('string', 'searchable'); 
    $this->mProperties->Add('string', 'allow_wysiwyg');
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
	  $parameters = array('content_en','target','pagedata','extra1','extra2','extra3',
			      'image','thumbnail','searchable','allow_wysiwyg');

	    //pick up the template id before we do parameters
	    if (isset($params['template_id']))
	    {
		if ($this->mTemplateId != $params['template_id'])
		{
		    $this->addtContentBlocksLoaded = false;
		}
		$this->mTemplateId = $params['template_id'];
	    }

	    // could add file upload handling here.

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

            # make sure target keeps empty even with the new dropdown value
	    if (isset($_POST['target']) && $_POST['target'] == "---")
	    {
	    	$this->SetPropertyValue('target', '');
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
		$this->SetAlias(trim($params['alias']));
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
	$templateops =& $gCms->GetTemplateOperations();
	$ret = array();
	$stylesheet = '';
	if ($this->TemplateId() > 0)
	{
	    $stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
	}
	else
	{
	    $defaulttemplate = $templateops->LoadDefaultTemplate();
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
	  if (check_permission(get_userid(), 'Modify Page Structure') || ($adding == true && check_permission(get_userid(), 'Add Pages')))
	    {
	      $contentops =& $gCms->GetContentOperations();
	      $ret[]= array(lang('parent').':',$contentops->CreateHierarchyDropdown($this->mId, $this->mParentId));
	    }

	  if( check_permission(get_userid(), 'Modify Page Structure') || $adding ) {
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
			
	    $ret[]= array(lang('template').':', $templateops->TemplateDropdown('template_id', $this->mTemplateId, 'onchange="document.contentform.submit()"'));
    }
		$this->GetAdditionalContentBlocks(); // this is needed as this is the first time we get a call to our class when editing.
            {
              $label = lang('content');
			  $wysiwyg = true;
			  $allow_wysiwyg = false;
			  
			  $wysiwyg = ($this->GetPropertyValue('allow_wysiwyg','0') == '0') ? 0 : 1;
			  if( isset($this->additionalContentBlocks['**default**']) )
              { 
                $tmp =& $this->additionalContentBlocks['**default**'];
				if ($wysiwyg)
				{
				$wysiwyg = ($tmp['usewysiwyg'] == 'false')?false:true;
				}
        		if( !empty($tmp['label']) ) $label = $tmp['label'];
              }
	      $ret[]= array($label.':',create_textarea($wysiwyg, $this->GetPropertyValue('content_en'), 'content_en', '', 'content_en', '', $stylesheet));
            }

	    // add additional content blocks if required
	    foreach($this->additionalContentBlocks as $blockName => $blockNameId)
	    {
		if (empty($blockName) || $blockName == '**default**') continue; 
                $label = ucwords($blockName);
		$data = $this->GetPropertyValue($blockNameId['id']);
		if( empty($data) && isset($blockNameId['default']) ) $data = $blockNameId['default'];
                if( !empty($blockNameId['label']) ) $label = $blockNameId['label'];
		switch($blockNameId['type'])
		  {
		  case 'module':
		    {
		      if( !isset($blockNameId['module']) ) continue;
		      if( !isset($gCms->modules[$blockNameId['module']]['object']) ) continue;
		      $module =& $gCms->modules[$blockNameId['module']]['object'];
		      if( !is_object($module) ) continue;
		      if( $module->HasContentBlocks() === FALSE ) continue;
		      $tmp = $module->GetContentBlockInputBase($blockName,$blockNameId['blocktype'],$blockNameId['params']);
		      if( $tmp === FALSE ) continue;
		      $ret[]= array($label.':',$tmp);
		    }
		    break;

		  case 'image':
		    {
		      $dir = cms_join_path($config['uploads_path'],$blockNameId['dir']);
		      $optprefix = 'uploads';
		      if( !empty($blockNameId['dir']) ) $optprefix .= '/'.$blockNameId['dir'];
		      $dropdown = create_file_dropdown($blockNameId['id'],$dir,$data,'jpg,jpeg,png,gif',
						       $optprefix,true);
		      if( $dropdown === false )
			{
			  $dropdown = lang('error_retrieving_file_list');
			}
		      $ret[]= array($label.':',$dropdown);
		    }
		    break;

		  case 'text':
		  default:
		    if ($blockNameId['oneline'] == 'true')
		      {
			
			$ret[]= array($label.':','<input type="text" name="'.$blockNameId['id'].'" value="'.cms_htmlentities($data, ENT_NOQUOTES, get_encoding('')).'" />');
		      }
		    else
		      {  
				$ret[]= array($label.':',create_textarea(($blockNameId['usewysiwyg'] == 'false'?false:true), $data, $blockNameId['id'], '', $blockNameId['id'], '', $stylesheet));
		      }
		  }
	    }
	}
	if ($tab == 1)
	{
	  if( check_permission(get_userid(),'Modify Page Structure') || $adding ) {
	    $ret[]= array(lang('active').':','<input class="pagecheckbox" type="checkbox" name="active"'.($this->mActive?' checked="checked"':'').' />');
	    $ret[]= array(lang('showinmenu').':','<input class="pagecheckbox" type="checkbox" name="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' />');
	    $ret[]= array(lang('cachable').':','<input class="pagecheckbox" type="checkbox" name="cachable"'.($this->mCachable?' checked="checked"':'').' />');

	    $text = '<option value="---">'.lang('none').'</option>';
	    $text .= '<option value="_blank"'.($this->GetPropertyValue('target')=='_blank'?' selected="selected"':'').'>_blank</option>';
	    $text .= '<option value="_parent"'.($this->GetPropertyValue('target')=='_parent'?' selected="selected"':'').'>_parent</option>';
	    $text .= '<option value="_self"'.($this->GetPropertyValue('target')=='_self'?' selected="selected"':'').'>_self</option>';
	    $text .= '<option value="_top"'.($this->GetPropertyValue('target')=='_top'?' selected="selected"':'').'>_top</option>';
	    $ret[]= array(lang('target').':','<select name="target">'.$text.'</select>');
	  }
	  else
	    {
	      if( $this->mActive )
		{
		  $ret[]= array('','<input type="hidden" name="active" value="1" />');
		}
	      if( $this->mShowInMenu )
		{
		  $ret[]= array('','<input type="hidden" name="showinmenu" value="1" />');
		}
	      if( $this->mCachable )
		{
		  $ret[] = array('','<input type="hidden" name="cachable" value="1" />');
		}
	    }

	  if( check_permission(get_userid(),'Modify Page Structure') || $adding == true) {
	    $ret[]= array(lang('pagealias').':','<input type="text" name="alias" value="'.$this->mAlias.'" />');
	  }
	  else {
            $ret[]= array(lang('pagealias').':','<input type="text" disabled name="alias" value="'.$this->mAlias.'" />');
	  }
	  
  	  $dir = $config['image_uploads_path'];
	  $data = $this->GetPropertyValue('image');
	  $dropdown = create_file_dropdown('image',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_');
	  $ret[] = array(lang('image').':',$dropdown);
	  
	  $data = $this->GetPropertyValue('thumbnail');
	  $dropdown = create_file_dropdown('thumbnail',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_',0);
	  $ret[] = array(lang('thumbnail').':',$dropdown);

	  $ret[]= array(lang('page_metadata').':',create_textarea(false, $this->Metadata(), 'metadata', 'pagesmalltextarea', 'metadata', '', '', '80', '6'));

	  $ret[]= array(lang('titleattribute').':','<input type="text" name="titleattribute" maxlength="255" size="80" value="'.cms_htmlentities($this->mTitleAttribute).'" />');
	  $ret[]= array(lang('tabindex').':','<input type="text" name="tabindex" maxlength="10" value="'.cms_htmlentities($this->mTabIndex).'" />');
	  $ret[]= array(lang('accesskey').':','<input type="text" name="accesskey" maxlength="5" value="'.cms_htmlentities($this->mAccessKey).'" />');
	  $ret[]= array(lang('pagedata_codeblock').':',create_textarea(false,$this->GetPropertyValue('pagedata'),'pagedata','pagesmalltextarea','pagedata','','','80','6'));
	  $searchable = $this->GetPropertyValue('searchable');
	  if( $searchable == '' )
	    {
	      $searchable = 1;
	    }
	  $ret[]= array(lang('searchable').':',
			'<div class="hidden" ><input type="hidden" name="searchable" value="0" /></div>
                           <input type="checkbox" name="searchable" value="1" '.($searchable==1?'checked="checked"':'').' />');
	  $allow_wysiwyg = $this->GetPropertyValue('allow_wysiwyg');
	  if( $allow_wysiwyg == '' )
	    {
	      $allow_wysiwyg = 1;
	    }
	  $ret[]= array(lang('allow_wysiwyg').':',
			'<div class="hidden" ><input type="hidden" name="allow_wysiwyg" value="0" onclick="this.form.submit()"/></div>
             <input type="checkbox" name="allow_wysiwyg" value="1"  onclick="this.form.submit()"'.($allow_wysiwyg==1?'checked="checked"':'').' />');

	  $ret[]= array(lang('extra1').':','<input type="text" name="extra1" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra1')).'" />');
	  $ret[]= array(lang('extra2').':','<input type="text" name="extra2" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra2')).'" />');
	  $ret[]= array(lang('extra3').':','<input type="text" name="extra3" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra3')).'" />');
	  
	  $userops =& $gCms->GetUserOperations();
	  if (!$adding && $showadmin)
	    {
	      $ret[]= array(lang('owner').':', $userops->GenerateDropdown($this->Owner()));
	    }

	    if ($adding || $showadmin)
	    {
	      $addteditors = array();
	      if( $adding )
		{
		  $addeditors = get_site_preference('additional_editors','');
		  $addteditors = explode(",",$addeditors);
		  $ret[]= $this->ShowAdditionalEditors($addteditors);
		}
	      else
		{
		  $ret[]= $this->ShowAdditionalEditors();
		}
	    }
	    $tmp = get_preference(get_userid(),'date_format_string','%x %X');
	    if( empty($tmp) ) $tmp = '%x %X';
	    $ret[]=array(lang('last_modified_at').':', strftime($tmp, strtotime($this->mModifiedDate) ) );
	    $modifiedbyuser = $userops->LoadUserByID($this->mLastModifiedBy);
	    if($modifiedbyuser) $ret[]=array(lang('last_modified_by').':', $modifiedbyuser->username); 
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
		
	if ($this->mAlias != $this->mOldAlias || $this->mAlias == '') #Should only be empty if auto alias is false
	{
		global $gCms;
		$contentops =& $gCms->GetContentOperations();
	    $error = $contentops->CheckAliasError($this->mAlias, $this->mId);
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
      global $gCms;
      $templateops =& $gCms->GetTemplateOperations();
      if ($this->addtContentBlocksLoaded == false)
	{
	  $this->additionalContentBlocks = array();
	  if ($this->TemplateId() && $this->TemplateId() > -1)
	    {
	      $template = $templateops->LoadTemplateByID($this->TemplateId()); /* @var $template Template */
	    }
	  else
	    {
	      $template = $templateops->LoadDefaultTemplate();
	    }
	  if($template !== false)
	    {
	      $content = $template->content;
	      
	      // read text content blocks
	      $pattern = '/{content\s([^}]*)}/';
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
			  $value = '';
			  $label = '';
			  
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
				case 'label':
				  $label = $val;
				  break;
				case 'default':
				  $value = $val;
				  break;
				default:
				  break;
				}
			    }

			  if( empty($name) ) $name = '**default**';
			  $this->additionalContentBlocks[$name]['type'] = 'text';
			  $this->additionalContentBlocks[$name]['id'] = $id;
			  $this->additionalContentBlocks[$name]['usewysiwyg'] = $usewysiwyg;
			  $this->additionalContentBlocks[$name]['oneline'] = $oneline;
			  $this->additionalContentBlocks[$name]['default'] = $value;
			  $this->additionalContentBlocks[$name]['label'] = $label;
			  
			}
		    }
		  
		  // force a load 
		  $this->mProperties->Load($this->mId);
		  
		  $result = true;
		}
	      
	      // read image content blocks
	      $pattern = '/{content_image\s([^}]*)}/';
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
			  $value = '';
			  $upload = true;
			  $dir = ''; // default to uploads path
			  $label = '';
			  
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
				case 'label':
				  $label = $val;
				  break;
				case 'upload':
				  $upload = $val;
				  break;
				case 'dir':
				  $dir = $val;
				  break;
				case 'default':
				  $value = $val;
				  break;
				default:
				  break;
				}
			    }
			  
			  if( empty($name) ) $name = '**default**';
			  $this->additionalContentBlocks[$name]['type'] = 'image';
			  $this->additionalContentBlocks[$name]['id'] = $id;
			  $this->additionalContentBlocks[$name]['upload'] = $upload;
			  $this->additionalContentBlocks[$name]['dir'] = $dir;
			  $this->additionalContentBlocks[$name]['default'] = $value;
			  $this->additionalContentBlocks[$name]['label'] = $label;					
			}
		    }
		  
		  // force a load 
		  $this->mProperties->Load($this->mId);
		  
		  $result = true;
		}

	      /* 
	       * disable this for now 
	       *
	      // match module content tags
	      $pattern = '/{content_module\s([^}]*)}/';
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
			  $module = '';
			  $label = '';
			  $blocktype = '';
			  $parms = array();
			  
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
				case 'label':
				  $label = $val;
				  break;
				case 'module':
				  $module = $val;
				  break;
				case 'type':
				  $blocktype = $val;
				  break;
				default:
				  $parms[$key] = $val;
				  break;
				}
			    }
			  
			  if( empty($name) ) $name = '**default**';
			  $this->additionalContentBlocks[$name]['type'] = 'module';
			  $this->additionalContentBlocks[$name]['blocktype'] = $blocktype;
			  $this->additionalContentBlocks[$name]['id'] = $id;
			  $this->additionalContentBlocks[$name]['module'] = $module;
			  $this->additionalContentBlocks[$name]['params'] = $parms;
			}
		    }
		  
		  // force a load 
		  $this->mProperties->Load($this->mId);
		  
		  $result = true;
		}
		*
		* end disabled code
		*/
	      
	      $this->addtContentBlocksLoaded = true;
	    }
	  return $result;
	}
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
