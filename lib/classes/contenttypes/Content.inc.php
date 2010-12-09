<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#$Id$

/**
 * Class definition and methods for Content
 *
 * @package CMS
 * @license GPL
 */

/**
 * Main class for CMS Made Simple content
 *
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class Content extends ContentBase
{
	/**
	 * @access private
	 * @var array
	 */
    var $_contentBlocks;
	/**
	 * @access private
	 * @var array
	 */
    var $_contentBlocksLoaded;
	/**
	 * @access private
	 * @var boolean
	 */
    var $doAutoAliasIfEnabled;
	/**
	 * @access private
	 * @var string
	 */
    var $stylesheet;
	
	/**
	 * Constructor
	 */
    function Content()
    {
	$this->ContentBase();
	$this->_contentBlocks = array();
	$this->_contentBlocksLoaded = false;
	$this->doAutoAliasIfEnabled = true;
    }

	/**
	 * Indicate whether or not this content type may be copied
	 *
	 * @return boolean whether or not it's copyable
	 */
    function IsCopyable()
    {
        return TRUE;
    }

	/**
	 * Get the friendly (e.g., human-readable) name for this content type
	 *
	 * @return string a human-readable name for this type of content
	 */
    function FriendlyName()
    {
      return lang('contenttype_content');
    }

	/**
	 * Set up base property attributes for this content type
	 *
	 * @return void
	 */
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
     * @return void
     */
    function ReadyForEdit()
    {
	$this->get_content_blocks();
    }

	/**
	 * Set content attribute values (from parameters received from admin add/edit form) 
	 *
	 * @param array $params hash of parameters to load into content attributes
	 * @return void
	 */
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
	  $this->parse_content_blocks();
	  foreach($this->_contentBlocks as $blockName => $blockInfo)
	    {
	      $this->AddExtraProperty($blockInfo['id']);
	      $parameters[] = $blockInfo['id'];

	      if( isset($blockInfo['type']) && $blockInfo['type'] == 'module' )
		{
		  if( !isset($gCms->modules[$blockInfo['module']]['object']) ) continue;
		  $module =& $gCms->modules[$blockInfo['module']]['object'];
		  if( !is_object($module) ) continue;
		  if( !$module->HasCapability('contentblocks') ) continue;
		  $tmp = $module->GetContentBlockValue($blockName,$blockInfo['params'],$params);
		  if( $tmp != null ) $params[$blockInfo['id']] = $tmp;
		}
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

	/**
	 * Gets the main content
	 *
	 * @param string $param which attribute to return
	 * @return string the specified content
	 */
    function Show($param = 'content_en')
    {
	// check for additional content blocks
	$this->get_content_blocks();
	
	return $this->GetPropertyValue($param);
    }

	/**
	 * test whether or not content may be made the default content
	 *
	 * @return boolean whether or not content may be made the default content
	 */
    function IsDefaultPossible()
    {
	return TRUE;
    }

	/**
	 * Get a list of custom tabs this content type shows in the admin for adding / editing
	 *
	 * @return array list of custom tabs
	 */
    function TabNames()
    {
      $res = array(lang('main'));
      if( check_permission(get_userid(),'Manage All Content') )
	{
	  $res[] = lang('options');
	}
      return $res;
    }

	/**
	 * Get a form for editing the content in the admin
	 *
	 * @param boolean $adding  true if the content is being added for the first time
	 * @param string $tab which tab to display
	 * @param string $showadmin 
	 * @return array 
	 */
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
	  $res = $this->parse_content_blocks(); // this is needed as this is the first time we get a call to our class when editing.
	  if( $res === FALSE ) 
	    {
	      $this->SetError(lang('error_parsing_content_blocks'));
	      return $ret;
	    }

	  foreach($this->_contentBlocks as $blockName => $blockInfo)
	    {
  		  $this->AddExtraProperty($blockInfo['id']);
	      $parameters[] = $blockInfo['id'];
	    }

	  // add content blocks.
	  foreach($this->_contentBlocks as $blockName => $blockInfo)
	    {
	      $data = $this->GetPropertyValue($blockInfo['id']);
	      if( empty($data) && isset($blockInfo['default']) ) $data = $blockInfo['default'];
	      $tmp = $this->display_content_block($blockName,$blockInfo,$data,$adding);
	      if( !$tmp ) continue;
	      $ret[] = $tmp;
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


	/**
	 * Validate the user's entries in the content add/edit form
	 *
	 * @return mixed either an array of validation error strings, or false to indicate no errors
	 */
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

		$res = $this->parse_content_blocks();
		if( $res === FALSE )
		  {
		    $errors[] = lang('error_parsing_content_blocks');
		    $result = false;
		  }

		$have_content_en = FALSE;
		foreach($this->_contentBlocks as $blockName => $blockInfo)
		{
		  if( $blockInfo['id'] == 'content_en' )
		    {
		      $have_content_en = TRUE;
		    }
			if( isset($blockInfo['type']) && $blockInfo['type'] == 'module' )
			{
				if( !isset($gCms->modules[$blockInfo['module']]['object']) ) continue;
				$module =& $gCms->modules[$blockInfo['module']]['object'];
				if( !is_object($module) ) continue;
				if( !$module->HasCapability('contentblocks') ) continue;
				$value = $this->GetPropertyValue($blockInfo['id']);
				$tmp = $module->ValidateContentBlockValue($blockName,$value,$blockInfo['params']);
				if( !empty($tmp) )
				{
					$errors[] = $tmp;
					$result = false;
				}
			}
		}

		if( !$have_content_en )
		  {
		    $errors[] = lang('error_no_default_content_block');
		    $result = false;
		  }

		return (count($errors) > 0?$errors:FALSE);
	}

    /**
     * Function to return an array of content blocks
     * @return array of content blocks
     */
    public function get_content_blocks()
    {
      $this->parse_content_blocks();
      return $this->_contentBlocks;
    }

	/**
	 * parse content blocks
	 *
	 * @access private
	 */
    private function parse_content_blocks()
    {
      $result = true;
      global $gCms;
      if ($this->_contentBlocksLoaded) return TRUE;

      $templateops =& $gCms->GetTemplateOperations();
      {
	  $this->_contentBlocks = array();
	  if ($this->TemplateId() && $this->TemplateId() > -1)
	    {
	      $template = $templateops->LoadTemplateByID($this->TemplateId());
	    }
	  else
	    {
	      $template = $templateops->LoadDefaultTemplate();
	    }
	  if($template !== false)
	    {
	      $content = $template->content;
	      
	      // fallthrough condition.
	      // read text content blocks
	      //$pattern = '/{content([^_}]*)}/';
	      $pattern = '/{content(?!_)([^}]*)}/';
	      $pattern2 = '/([a-zA-z0-9]*)=["\']([^"\']+)["\']/';
	      $matches = array();
	      $result2 = preg_match_all($pattern, $content, $matches);
	      if ($result2 && count($matches[1]) > 0)
		{
		  // get all the {content...} tags
		  foreach ($matches[1] as $wholetag)
		    {
		      if( preg_match('/{content_/',$wholetag) )
			{
			  continue;
			}

		      $id = '';
		      $name = '';
		      $usewysiwyg = 'true';
		      $oneline = 'false';
		      $value = '';
		      $label = '';
		      $size = '50';

		      // get the arguments.
		      $morematches = array();
		      $result3 = preg_match_all($pattern2, $wholetag, $morematches);
		      if ($result3)
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
		      
		      if( $this->is_known_property($id) || in_array($id,array_keys($this->_contentBlocks)) )
			{
			  // adding a duplicated content block.
			  return FALSE;
			}
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
		  $result = TRUE;
		}
	      
	      // read image content blocks
	      $pattern = '/{content_image\s([^}]*)}/';
	      $pattern2 = '/([a-zA-z0-9]*)=["\']([^"\']+)["\']/';
	      $matches = array();
	      $result2 = preg_match_all($pattern, $content, $matches);
	      if ($result2 && count($matches[1]) > 0)
		{
		  $blockcount = 0;
		  foreach ($matches[1] as $wholetag)
		    {
		      $morematches = array();
		      $result3 = preg_match_all($pattern2, $wholetag, $morematches);
		      if ($result3)
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
			  if( $this->is_known_property($id) || in_array($id,array_keys($this->_contentBlocks)) )
			    {
			      // adding a duplicated content block.
			      return FALSE;
			    }
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
		  $result = TRUE;
		}

	      // match module content tags
	      $pattern = '/{content_module\s([^}]*)}/';
	      $pattern2 = '/([a-zA-z0-9]*)=["\']([^"\']+)["\']/';
	      $matches = array();
	      $result2 = preg_match_all($pattern, $content, $matches);
	      if ($result2 && count($matches[1]) > 0)
		{
		  foreach ($matches[1] as $wholetag)
		    {
		      $morematches = array();
		      $result3 = preg_match_all($pattern2, $wholetag, $morematches);
		      if ($result3)
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
			  if( $this->is_known_property($id) || in_array($id,array_keys($this->_contentBlocks)) )
			    {
			      // adding a duplicated content block.
			      return FALSE;
			    }
			  $this->_contentBlocks[$name]['type'] = 'module';
			  $this->_contentBlocks[$name]['blocktype'] = $blocktype;
			  $this->_contentBlocks[$name]['id'] = $id;
			  $this->_contentBlocks[$name]['label'] = $label;
			  $this->_contentBlocks[$name]['module'] = $module;
			  $this->_contentBlocks[$name]['params'] = $parms;
			}
		    }
		  
		  // force a load 
		  $this->mProperties->Load($this->mId);
		  $result = TRUE;
		}
	      
	      $this->_contentBlocksLoaded = true;
	    }

	  return $result;
	}
    }
	
	/**
	 * undocumented function
	 *
	 * @param string $tpl_source 
	 */
    function ContentPreRender($tpl_source)
    {
	// check for additional content blocks
	$this->get_content_blocks();

	return $tpl_source;
    }

	/**
	 * undocumented function
	 *
	 * @param string $one 
	 * @param string $adding 
	 * @return void
	 */
    function display_single_element($one,$adding)
    {
      global $gCms;

      switch($one) {
      case 'template':
	{
	  $templateops =& $gCms->GetTemplateOperations();
	  return array(lang('template').':', $templateops->TemplateDropdown('template_id', $this->mTemplateId, 'onchange="document.Edit_Content.submit()"'));
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
            <input type="checkbox" name="searchable" value="1" '.($searchable==1?'checked="checked"':'').' />',
				   lang('help_page_searchable'));
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

	/*
	* return the HTML to create the text area in the admin console.
	* does not include a label.
	*/
	private function _display_text_block($blockInfo,$value,$adding)
	{
		$ret = '';
		if (isset($blockInfo['oneline']) && $blockInfo['oneline'] == '1' || $blockInfo['oneline'] == 'true')
		{
			$size = (isset($blockInfo['size']))?$blockInfo['size']:50;
			$ret = '<input type="text" size="'.$size.'" name="'.$blockInfo['id'].'" value="'.cms_htmlentities($value, ENT_NOQUOTES, get_encoding('')).'" />';
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

			$ret = create_textarea($block_wysiwyg, $value, $blockInfo['id'], '', $blockInfo['id'], '', $this->stylesheet);
		}
		return $ret;
	}

	/*
	* return the HTML to create an image dropdown in the admin console.
	* does not include a label.
	*/
	private function _display_image_block($blockInfo,$value,$adding)
	{
		global $gCms;
		$config = $gCms->GetConfig();
		$adddir = get_site_preference('contentimage_path');
		if( $blockInfo['dir'] != '' )
			{
				$adddir = $blockInfo['dir'];
			}
		$dir = cms_join_path($config['uploads_path'],$adddir);
		$optprefix = '';
		//$optprefix = 'uploads';
		//if( !empty($blockInfo['dir']) ) $optprefix .= '/'.$blockInfo['dir'];
		$inputname = $blockInfo['id'];
		if( isset($blockInfo['inputname']) )
		{
			$inputname = $blockInfo['inputname'];
		}
		$dropdown = create_file_dropdown($inputname,$dir,$value,'jpg,jpeg,png,gif',$optprefix,true);
		if( $dropdown === false )
		{
			$dropdown = lang('error_retrieving_file_list');
		}
		return $dropdown;
	}


/*
	* return the HTML to create the text area in the admin console.
	* may include a label.
*/
	private function _display_module_block($blockName,$blockInfo,$value,$adding)
	{
		global $gCms;
		$ret = '';
		if( !isset($blockInfo['module']) ) return FALSE;
		if( !isset($gCms->modules[$blockInfo['module']]['object']) ) return FALSE;
		$module =& $gCms->modules[$blockInfo['module']]['object'];
		if( !is_object($module) ) continue;
		if( !$module->HasCapability('contentblocks') ) return FALSE;
		if( isset($blockInfo['inputname']) && !empty($blockInfo['inputname']) )
		{
			// a hack to allow overriding the input field name.
			$blockName = $blockInfo['inputname'];
		}
		$tmp = $module->GetContentBlockInput($blockName,$value,$blockInfo['params'],$adding);
		return $tmp;
	}


	/**
	* Return an array of two elements
	* the first is the string for the label for the field
	* the second is the html for the input field
*/
	public function display_content_block($blockName,$blockInfo,$value,$adding = false)
	{
		// it'd be nice if the content block was an object..
		// but I don't have the time to do it at the moment.
		$field = '';
		$label = '';
		if( isset($blockInfo['label']) )
		{
			$label = $blockInfo['label'];
		}
		switch( $blockInfo['type'] )
		{
			case 'text':
			{
				if( $blockName == 'content_en' && $label == '' )
				{
					$label = lang('content').'*';
				}
				$field = $this->_display_text_block($blockInfo,$value,$adding);
			}
			break;

			case 'image':
			$field = $this->_display_image_block($blockInfo,$value,$adding);
			break;

			case 'module':
			{
				$tmp = $this->_display_module_block($blockName,$blockInfo,$value,$adding);
				if( is_array($tmp) )
				{
					if( count($tmp) == 2 )
					{
						$label = $tmp[0];
						$field = $tmp[1];
					}
					else
					{
						$field = $tmp[0];
					}
				}
				else
				{
					$field = $tmp;
				}
			}
			break;
		}
		if( empty($field) ) return FALSE;
		if( empty($label) )
		{
			$label = $blockName;
		}
		return array($label.':',$field);
	}

} // end of class

# vim:ts=4 sw=4 noet
?>
