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
    protected $_contentBlocks = array();
	/**
	 * @access private
	 * @var array
	 */
    protected $_contentBlocksLoaded = false;
	/**
	 * @access private
	 * @var boolean
	 */
    protected $doAutoAliasIfEnabled = true;
	/**
	 * @access private
	 * @var string
	 */
    protected $stylesheet;
	/**
	 * @access private
	 * @var array
	 */
	private $__fieldhash;

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
		//$this->get_content_blocks();
    }

	/**
	 * Set content attribute values (from parameters received from admin add/edit form) 
	 *
	 * @param array $params hash of parameters to load into content attributes
	 * @return void
	 */
    function FillParams($params,$editing = false)
    {
		$gCms = cmsms();
		$config = $gCms->GetConfig();

		if (isset($params))
			{
				$this->__fieldhash = null; // clear the field hash.
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
				$blocks = $this->get_content_blocks();
				foreach($blocks as $blockName => $blockInfo)
					{
						$parameters[] = $blockInfo['id'];
			
						if( isset($blockInfo['type']) && $blockInfo['type'] == 'module' )
							{
								$module = cms_utils::get_module($blockInfo['module']);
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

		parent::FillParams($params,$editing);
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
		//$this->get_content_blocks();
	
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
	 * Setup form elements for adding or editing.
	 * This method is called internally when adding or editing a content page of this type
	 * to build a hash of editable fields.
	 *
	 * @return hash of array fields.
	 */
	protected function SetupForm($adding = FALSE)
	{
		//
		// setup
		//
		$hash = array();
		$blocks = null;
		try {
			$blocks = $this->get_content_blocks(); 
		}
		catch( CmsEditContentException $e ) 
		{
			$this->SetError($e->getMessage());
		}

		//
		// main tab
		//
		$hash[lang('main')] = array();
		$ret = array();
		$tmp = $this->display_attributes($adding);
		if( !empty($tmp) )
		{
			foreach( $tmp as $one ) 
			{
				$ret[] = $one;
			}
		}

		if( is_array($blocks) && count($blocks) )
		{
			foreach($blocks as $blockName => $blockInfo)
			{
				if( !isset($blockInfo['tab']) || $blockInfo['tab'] == '' || $blockInfo['tab'] == 'main' )
				{
					$parameters[] = $blockInfo['id'];
				
					$data = $this->GetPropertyValue($blockInfo['id']);
					if( empty($data) && isset($blockInfo['default']) ) $data = $blockInfo['default'];
					$tmp = $this->display_content_block($blockName,$blockInfo,$data,$adding);
					if( !$tmp ) continue;
					$ret[] = $tmp;
				}
			}
		}
			
		$hash[lang('main')] = $ret;

		// 
		// other tabs.
		//
		if( is_array($blocks) && count($blocks) )
		{
			foreach($blocks as $blockName => $blockInfo)
				{
					if( isset($blockInfo['tab']) && $blockInfo['tab'] != '' && $blockInfo['tab'] != 'options')
					{
						$parameters[] = $blockInfo['id'];
						
						$data = $this->GetPropertyValue($blockInfo['id']);
						if( empty($data) && isset($blockInfo['default']) ) $data = $blockInfo['default'];
						$tmp = $this->display_content_block($blockName,$blockInfo,$data,$adding);
						if( !$tmp ) continue;
						
						if( !isset($hash[$blockInfo['tab']]) )
						{
							$hash[$blockInfo['tab']] = array();
						}
						$hash[$blockInfo['tab']][] = $tmp;
					}
				}
		}

		//
		// options tab
		//
		if( check_permission(get_userid(),'Manage All Content') )
		{
			// now do advanced attributes
			$ret = array();
			$tmp = $this->display_attributes($adding,1);
			if( !empty($tmp) )
			{
				foreach( $tmp as $one ) {
					$ret[] = $one;
				}
			}

			if( is_array($blocks) && count($blocks) )
			{
				// and the content blocks
				foreach($blocks as $blockName => $blockInfo)
				{
					if( isset($blockInfo['tab']) && $blockInfo['tab'] == 'options' )
					{
						$parameters[] = $blockInfo['id'];
						
						$data = $this->GetPropertyValue($blockInfo['id']);
						if( empty($data) && isset($blockInfo['default']) ) $data = $blockInfo['default'];
						$tmp = $this->display_content_block($blockName,$blockInfo,$data,$adding);
						if( !$tmp ) continue;
						$ret[] = $tmp;
					}
				}
			}

			// and extra info.
			$tmp = get_preference(get_userid(),'date_format_string','%x %X');
			if( empty($tmp) ) $tmp = '%x %X';
			$ret[]=array(lang('last_modified_at').':', strftime($tmp, strtotime($this->mModifiedDate) ) );
			$userops = cmsms()->GetUserOperations();
			$modifiedbyuser = $userops->LoadUserByID($this->mLastModifiedBy);
			if($modifiedbyuser) $ret[]=array(lang('last_modified_by').':', $modifiedbyuser->username); 
			
			if( count($ret) ) $hash[lang('options')] = $ret;
		}

		return $hash;
	}

	private function _get_form_data()
	{
		if( isset($this->__fieldhash) && is_array($this->__fieldhash) ) return;
		$tmp = $this->SetupForm();
		if( is_array($tmp) && count($tmp) ) $this->__fieldhash = $tmp;
	}

	/**
	 * Get a list of custom tabs this content type shows in the admin for adding / editing
	 *
	 * @return array list of custom tabs
	 */
    function TabNames()
    {
		$this->_get_form_data();
		return array_keys($this->__fieldhash);
    }

	/**
	 * Get a form for editing the content in the admin
	 *
	 * @param boolean $adding  true if the content is being added for the first time
	 * @param boolean $tab which tab to display
	 * @param string $showadmin 
	 * @return array 
	 */
    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
		$this->_get_form_data();
		$tabnames = $this->TabNames(); 
	
		$templateops = cmsms()->GetTemplateOperations();
		$ret = array();
		$this->stylesheet = '';
		if ($this->TemplateId() > 0)
		{
			$this->stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
		}

		if( isset($tabnames[$tab]) )
		{
			return $this->__fieldhash[$tabnames[$tab]];
		}
    }


	/**
	 * Validate the user's entries in the content add/edit form
	 *
	 * @return mixed either an array of validation error strings, or false to indicate no errors
	 */
	function ValidateData()
	{
		$errors = parent::ValidateData();
		$gCms = cmsms();
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

		$blocks = $this->get_content_blocks();
		if( !$blocks )
		  {
		    $errors[] = lang('error_parsing_content_blocks');
		    $result = false;
		  }

		$have_content_en = FALSE;
		foreach($blocks as $blockName => $blockInfo)
		{
		  if( $blockInfo['id'] == 'content_en' )
		    {
		      $have_content_en = TRUE;
		    }
			if( isset($blockInfo['type']) && $blockInfo['type'] == 'module' )
			{
				$module = cms_utils::get_module($blockInfo['module']);
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
		if( $this->parse_content_blocks() )
		{
			return $this->_contentBlocks;
		}
    }

	public static function _dflt_compiler_tag($params,$template)
	{
		return '';
	}

	/**
	 * Dummy default plugin handler for smarty.
	 *
	 * @access private
	 * @internal
	 */
	public function _dummyDfltPluginHandler($name, $type, $template, &$callback, &$script, &$cachable)
	{
		if( $type == 'compiler' ) {
			$callback = array('Content','_dflt_compiler_tag');
			$cachable = '';
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * parse content blocks
	 *
	 * @access private
	 * @internal
	 */
    private function parse_content_blocks()
    {
		if ($this->_contentBlocksLoaded) return TRUE;
		$smarty = cmsms()->GetSmarty();
		$smarty->force_compile = TRUE;
		
		$smarty->registerDefaultPluginHandler(array(&$this,'_dummyDfltPluginHandler'));
		$smarty->registerPlugin('compiler','content',array('CMS_Content_Block','smarty_compiler_contentblock'),false);
		$smarty->registerPlugin('compiler','content_image',array('CMS_Content_Block','smarty_compiler_imageblock'),false);
		$smarty->registerPlugin('compiler','content_module',array('CMS_Content_Block','smarty_compiler_moduleblock'),false);
		$smarty->registerResource('template',new CMSPageTemplateResource(''));
		$smarty->fetch('template:'.$this->TemplateId()); // do the magic.
		$smarty->registerDefaultPluginHandler(array(&$smarty,'defaultPluginHandler'));

		$this->_contentBlocks = CMS_Content_block::get_content_blocks();

		if( !is_array($this->_contentBlocks) || !count($this->_contentBlocks) ) 
			return FALSE;

		$this->_contentBlocksLoaded = TRUE;
		return TRUE;
    }
	
	/**
	 * undocumented function
	 *
	 * @param string $one 
	 * @param string $adding 
	 * @return void
	 * @internal
	 */
    protected function display_single_element($one,$adding)
    {
		$gCms = cmsms();

		switch($one) {
		case 'template':
			{
				$templateops = $gCms->GetTemplateOperations();
				return array('<label for="template_id">'.lang('template').':</label>', $templateops->TemplateDropdown('template_id', $this->mTemplateId, 'onchange="document.Edit_Content.submit()"'));
			}
			break;
			
		case 'pagemetadata':
			{
				return array('<label for="id_pagemetadata">'.lang('page_metadata').':</label>',create_textarea(false, $this->Metadata(), 'metadata', 'pagesmalltextarea', 'metadata', '', '', '80', '6'));
			}
			break;
			
		case 'pagedata':
			{
				return array('<label for="id_pagedata">'.lang('pagedata_codeblock').':</label>',
							 create_textarea(false,$this->GetPropertyValue('pagedata'),'pagedata','pagesmalltextarea','id_pagedata','','','80','6'));
			}
			break;
			
		case 'searchable':
			{
				$searchable = $this->GetPropertyValue('searchable');
				if( $searchable == '' )
					{
						$searchable = 1;
					}
				return array('<label for="id_searchable">'.lang('searchable').':</label>',
							 '<div class="hidden" ><input type="hidden" name="searchable" value="0" /></div>
            <input id="id_searchable" type="checkbox" name="searchable" value="1" '.($searchable==1?'checked="checked"':'').' />',
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
				return array('<label for="id_disablewysiwyg">'.lang('disable_wysiwyg').':</label>',
							 '<div class="hidden" ><input type="hidden" name="disable_wysiwyg" value="0" /></div>
             <input id="id_disablewysiwyg" type="checkbox" name="disable_wysiwyg" value="1"  '.($disable_wysiwyg==1?'checked="checked"':'').' onclick="this.form.submit()" />');
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
		$oneline = isset($blockInfo['oneline']) && cms_to_bool($blockInfo['oneline']);
		if ($oneline)
		{
			$size = (isset($blockInfo['size']))?$blockInfo['size']:50;
			$maxlength = (isset($blockInfo['maxlength']))?$blockInfo['maxlength']:255;
			$ret = '<input type="text" size="'.$size.'" maxlength="'.$maxlength.'" name="'.$blockInfo['id'].'" value="'.cms_htmlentities($value, ENT_NOQUOTES, get_encoding('')).'" />';
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
		$gCms = cmsms();
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
		$gCms = cmsms();
		$ret = '';
		if( !isset($blockInfo['module']) ) return FALSE;
		$module = cms_utils::get_module($blockInfo['module']);
		if( !is_object($module) ) return FALSE;
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
	private function display_content_block($blockName,$blockInfo,$value,$adding = false)
	{
		// it'd be nice if the content block was an object..
		// but I don't have the time to do it at the moment.
		$field = '';
		$label = '';
		if( isset($blockInfo['label']) && $blockInfo['label'] != '')
		{
			$label = '<label for="'.$blockInfo['id'].'">'.$blockInfo['label'].'</label>';
		}
		switch( $blockInfo['type'] )
		{
			case 'text':
			{
				if( $blockName == 'content_en' && $label == '' )
				{
					$label = '<label for="content_en">'.lang('content').'*</label>';
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
