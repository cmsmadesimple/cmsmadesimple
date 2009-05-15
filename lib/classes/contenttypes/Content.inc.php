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

class Content extends ContentBase
{
    var $_contentBlocks;
    var $_contentBlocksLoaded;
    var $doAutoAliasIfEnabled;
    var $stylesheet;
	
    function Content()
    {
	$this->ContentBase();
	$this->_contentBlocks = array();
	$this->_contentBlocksLoaded = false;
	$this->doAutoAliasIfEnabled = true;
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
      parent::SetProperties();
      $this->AddBaseProperty('template',4);
      $this->AddBaseProperty('pagemetadata',20);
      $this->AddContentProperty('searchable',8);
      $this->AddContentProperty('pagedata',25);
      $this->AddContentProperty('disable_wysiwyg',60);

      #Turn on preview
      $this->mPreview = true;
    }

    /**
     * Use the ReadyForEdit callback to get the additional content blocks loaded.
     */
    function ReadyForEdit()
    {
	$this->get_content_blocks();
    }

    function FillParams($params)
    {
	global $gCms;
	$config =& $gCms->config;

	if (isset($params))
	{
	  $parameters = array('pagedata','searchable','disable_wysiwyg');

	  //pick up the template id before we do parameters
	  if (isset($params['template_id']))
	    {
	      if ($this->mTemplateId != $params['template_id'])
		{
		  $this->_contentBlocksLoaded = false;
		}
	      $this->mTemplateId = $params['template_id'];
	    }
	  
	  // add content blocks
	  $this->get_content_blocks();
	  foreach($this->_contentBlocks as $blockName => $blockInfo)
	    {
	      $this->AddExtraProperty($blockName);
	      $parameters[] = $blockInfo['id'];
	    }
	  
	  // do the content property parameters
	  foreach ($parameters as $oneparam)
	    {
	      if (isset($params[$oneparam]))
		{
		  $this->SetPropertyValue($oneparam, $params[$oneparam]);
		}
	    }

	  // metadata
	  if (isset($params['metadata']))
	    {
	      $this->mMetadata = $params['metadata'];
	    }

	}

	parent::FillParams($params);

    }

    function Show($param = 'content_en')
    {
	// check for additional content blocks
	$this->get_content_blocks();
	
	return $this->GetPropertyValue($param);
    }

    function IsDefaultPossible()
    {
	return TRUE;
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
	global $gCms;
	
	$config = $gCms->GetConfig();
	$templateops =& $gCms->GetTemplateOperations();
	$ret = array();
	$this->stylesheet = '';
	if ($this->TemplateId() > 0)
	{
	    $this->stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
	}


	if ($tab == 0)
	{
	  // now do basic attributes
	  $tmp = $this->display_attributes($adding);
	  if( !empty($tmp) )
	    {
	      foreach( $tmp as $one ) {
		$ret[] = $one;
	      }
	    }

	  // and the content blocks
	  $this->get_content_blocks(); // this is needed as this is the first time we get a call to our class when editing.
	  foreach($this->_contentBlocks as $blockName => $blockInfo)
	    {
	      $this->AddExtraProperty($blockName);
	      $parameters[] = $blockInfo['id'];
	    }

	    // add additional content blocks if required
	    foreach($this->_contentBlocks as $blockName => $blockInfo)
	    {
                $label = ucwords($blockName);
		if( $blockName == 'content_en' ) 
		  {
		    $label = ucwords('content');
		  }
		$data = $this->GetPropertyValue($blockInfo['id']);
		if( empty($data) && isset($blockInfo['default']) ) $data = $blockInfo['default'];
                if( !empty($blockInfo['label']) ) $label = $blockInfo['label'];
		switch($blockInfo['type'])
		  {
		  case 'module':
		    {
		      if( !isset($blockInfo['module']) ) continue;
		      if( !isset($gCms->modules[$blockInfo['module']]['object']) ) continue;
		      $module =& $gCms->modules[$blockInfo['module']]['object'];
		      if( !is_object($module) ) continue;
		      if( $module->HasContentBlocks() === FALSE ) continue;
		      $tmp = $module->GetContentBlockInputBase($blockName,$blockInfo['blocktype'],$blockInfo['params']);
		      if( $tmp === FALSE ) continue;
		      $ret[]= array($label.':',$tmp);
		    }
		    break;

		  case 'image':
		    {
		      $dir = cms_join_path($config['uploads_path'],$blockInfo['dir']);
		      $optprefix = 'uploads';
		      if( !empty($blockInfo['dir']) ) $optprefix .= '/'.$blockInfo['dir'];
		      $dropdown = create_file_dropdown($blockInfo['id'],$dir,$data,'jpg,jpeg,png,gif',
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
		    if (isset($blockInfo['oneline']) && $blockInfo['oneline'] == '1' || $blockInfo['oneline'] == 'true')
		      {
			$size = (isset($blockInfo['size']))?$blockInfo['size']:50;
			$ret[]= array($label.':','<input type="text" size="'.$size.'" name="'.$blockInfo['id'].'" value="'.cms_htmlentities($data, ENT_NOQUOTES, get_encoding('')).'" />');
		      }
		    else
		      { 
			$block_wysiwyg = true;
			$hide_wysiwyg = $this->GetPropertyValue('disable_wysiwyg');
			
			if ($hide_wysiwyg)
			  {
			    $block_wysiwyg = false;
			  }
			else
			  {
			    $block_wysiwyg = $blockInfo['usewysiwyg'] == 'false'?false:true;
			  }
			
			$ret[]= array($label.':',create_textarea($block_wysiwyg, $data, $blockInfo['id'], '', $blockInfo['id'], '', $this->stylesheet));
		      }
		  }
	    }
	}

	if ($tab == 1)
	{
	  // now do advanced attributes
	  $tmp = $this->display_attributes($adding,1);
	  if( !empty($tmp) )
	    {
	      foreach( $tmp as $one ) {
		$ret[] = $one;
	      }
	    }

	    $tmp = get_preference(get_userid(),'date_format_string','%x %X');
	    if( empty($tmp) ) $tmp = '%x %X';
	    $ret[]=array(lang('last_modified_at').':', strftime($tmp, strtotime($this->mModifiedDate) ) );
	    $userops =& $gCms->GetUserOperations();
	    $modifiedbyuser = $userops->LoadUserByID($this->mLastModifiedBy);
	    if($modifiedbyuser) $ret[]=array(lang('last_modified_by').':', $modifiedbyuser->username); 
	}

	return $ret;
    }


    function ValidateData()
    {
      $errors = parent::ValidateData();
      if( $errors === FALSE )
	{
	  $errors = array();
	}

	if ($this->mTemplateId <= 0 )
	  {
	    $errors[] = lang('nofieldgiven',array(lang('template')));
	    $result = false;
	  }

	if ($this->GetPropertyValue('content_en') == '')
	{
	    $errors[]= lang('nofieldgiven',array(lang('content')));
	    $result = false;
	}

	return (count($errors) > 0?$errors:FALSE);
    }

    /* private */
    function get_content_blocks()
    {
      $result = false;
      global $gCms;
      if ($this->_contentBlocksLoaded) return;

      $templateops =& $gCms->GetTemplateOperations();
	{
	  $this->_contentBlocks = array();
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
	      //$pattern = '/{content\s([^}]*)}/';
	      $pattern = '/{content([^}]*)}/';
	      $pattern2 = '/([a-zA-z0-9]*)=["\']([^"\']+)["\']/';
	      $matches = array();
	      $result = preg_match_all($pattern, $content, $matches);
	      if ($result && count($matches[1]) > 0)
		{
		  // get all the {content...} tags
		  foreach ($matches[1] as $wholetag)
		    {
		      $id = '';
		      $name = '';
		      $usewysiwyg = 'true';
		      $oneline = 'false';
		      $value = '';
		      $label = '';
		      $size = '50';

		      // get the arguments.
		      $morematches = array();
		      $result2 = preg_match_all($pattern2, $wholetag, $morematches);
		      if ($result2)
			{
			  $keyval = array();
			  for ($i = 0; $i < count($morematches[1]); $i++)
			    {
			      $keyval[$morematches[1][$i]] = $morematches[2][$i];
			    }
			  
			  foreach ($keyval as $key=>$val)
			    {
			      switch($key)
				{
				case 'block':
				  $id = str_replace(' ', '_', $val);
				  $name = $val;
				  break;
				case 'wysiwyg':
				  $usewysiwyg = $val;
				  break;
				case 'oneline':
				  $oneline = $val;
				  break;
				case 'size':
				  $size = $val;
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
			}

		      if( empty($name) ) { $name = 'content_en'; $id = 'content_en'; }
		      $this->mProperties->Add('string',$id);
		      if( !isset($this->_contentBlocks[$name]) )
			{
			  $this->_contentBlocks[$name]['type'] = 'text';
			  $this->_contentBlocks[$name]['id'] = $id;
			  $this->_contentBlocks[$name]['usewysiwyg'] = $usewysiwyg;
			  $this->_contentBlocks[$name]['oneline'] = $oneline;
			  $this->_contentBlocks[$name]['default'] = $value;
			  $this->_contentBlocks[$name]['label'] = $label;
			  $this->_contentBlocks[$name]['size'] = $size;
			}
		    }
		  
		  // force a load 
		  $this->GetPropertyValue('extra1');
		  
		  $result = true;
		}
	      
	      // read image content blocks
	      $pattern = '/{content_image\s([^}]*)}/';
	      $pattern2 = '/([a-zA-z0-9]*)=["\']([^"\']+)["\']/';
	      $matches = array();
	      $result = preg_match_all($pattern, $content, $matches);
	      if ($result && count($matches[1]) > 0)
		{
		  $blockcount = 0;
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

			  $blockcount++;
			  if( empty($name) ) $name = 'image_'.$blockcount;;
			  $this->_contentBlocks[$name]['type'] = 'image';
			  $this->_contentBlocks[$name]['id'] = $id;
			  $this->_contentBlocks[$name]['upload'] = $upload;
			  $this->_contentBlocks[$name]['dir'] = $dir;
			  $this->_contentBlocks[$name]['default'] = $value;
			  $this->_contentBlocks[$name]['label'] = $label;					
			}
		    }
		  
		  // force a load 
		  $this->GetPropertyValue('extra1');
		  
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
			  $this->_contentBlocks[$name]['type'] = 'module';
			  $this->_contentBlocks[$name]['blocktype'] = $blocktype;
			  $this->_contentBlocks[$name]['id'] = $id;
			  $this->_contentBlocks[$name]['module'] = $module;
			  $this->_contentBlocks[$name]['params'] = $parms;
			}
		    }
		  
		  // force a load 
		  $this->mProperties->Load($this->mId);
		  
		  $result = true;
		}
		*
		* end disabled code
		*/
	      
	      $this->_contentBlocksLoaded = true;
	    }

	  return $result;
	}
    }
	

    function ContentPreRender($tpl_source)
    {
	// check for additional content blocks
	$this->get_content_blocks();

	return $tpl_source;
    }

    function display_single_element($one,$adding)
    {
      global $gCms;

      switch($one) {
      case 'template':
	{
	  $templateops =& $gCms->GetTemplateOperations();
	  return array(lang('template').':', $templateops->TemplateDropdown('template_id', $this->mTemplateId, 'onchange="document.contentform.submit()"'));
	}
	break;
	
      case 'pagemetadata':
	{
	  return array(lang('page_metadata').':',create_textarea(false, $this->Metadata(), 'metadata', 'pagesmalltextarea', 'metadata', '', '', '80', '6'));
	}
	break;
	
      case 'pagedata':
	{
	  return array(lang('pagedata_codeblock').':',create_textarea(false,$this->GetPropertyValue('pagedata'),'pagedata','pagesmalltextarea','pagedata','','','80','6'));
	}
	break;
	
      case 'searchable':
	{
	  $searchable = $this->GetPropertyValue('searchable');
	  if( $searchable == '' )
	    {
	      $searchable = 1;
	    }
	  return array(lang('searchable').':',
			'<div class="hidden" ><input type="hidden" name="searchable" value="0" /></div>
                           <input type="checkbox" name="searchable" value="1" '.($searchable==1?'checked="checked"':'').' />');
	}
	break;
	
      case 'disable_wysiwyg':
	{
	  $disable_wysiwyg = $this->GetPropertyValue('disable_wysiwyg');
	  if( $disable_wysiwyg == '' )
	    {
	      $disable_wysiwyg = 0;
	    }
	  return array(lang('disable_wysiwyg').':',
		       '<div class="hidden" ><input type="hidden" name="disable_wysiwyg" value="0" /></div>
             <input type="checkbox" name="disable_wysiwyg" value="1"  '.($disable_wysiwyg==1?'checked="checked"':'').' onclick="this.form.submit()" />');
	}
	break;

      default:
	return parent::display_single_element($one,$adding);
      }
      
    }
} // end of class

# vim:ts=4 sw=4 noet
?>
