<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id$

class CmsContentEditor extends CmsContentEditorBase
{
    private $_contentBlocks;
    private $_contentBlocksLoaded;
	private $_stylesheet;

	public function __construct($content_obj)
	{
		parent::__construct($content_obj);

		$profile = $this->get_profile();
		$profile->add_attribute(new CmsContentTypeProfileAttribute('searchable','options'),'secure');
		$profile->add_attribute(new CmsContentTypeProfileAttribute('template','options'),'alias');
		$profile->add_attribute(new CmsContentTypeProfileAttribute('pagemetadata','options'),'thumbnail');
		$profile->add_attribute(new CmsContentTypeProfileAttribute('pagedata','options'),'pagemetadata');
		$profile->add_attribute(new CmsContentTypeProfileAttribute('disable_wysiwyg','options'));

	    $this->stylesheet = '../stylesheet.php?templateid='.$content_obj->template_id();
	}


	public function get_tab_elements($tabname,$adding = FALSE)
	{
		$gCms = cmsms();
		$config = cms_config();
		$templateops = $gCms->GetTemplateOperations();

		$ret = parent::get_tab_elements($tabname,$adding);
		if( $tabname != 'main' ) return $ret;

		$this->parse_content_blocks(); 
		$content_obj = $this->get_content();

		foreach($this->_contentBlocks as $blockName => $blockInfo)
			{
				$data = $content_obj->get_property_value($blockInfo['id']);
				if( empty($data) && isset($blockInfo['default']) ) $data = $blockInfo['default'];
				$tmp = $this->display_content_block($blockName,$blockInfo,$data,$adding);
				if( !$tmp ) continue;
				$ret[] = $tmp;
			}

		return $ret;
	}


	protected function get_single_element($content_obj,&$attr,$adding = false)
	{
		global $gCms;

		$prompt = '';
		$field = '';
		switch( $attr->get_name() )
			{
			case 'template':
				{
					$templateops = $gCms->GetTemplateOperations();
					$prompt = lang('template');
					$field  = $templateops->TemplateDropdown('template_id', $content_obj->template_id(), 'onchange="document.contentform.submit()"');
				}
				return array($prompt.':',$field);

			case 'pagemetadata':
				{
					$prompt = lang('page_metadata');
					$field  = create_textarea(false, $content_obj->metadata(), 'pagemetadata', 'pagesmalltextarea', 'metadata', '', '', '80', '6');
				}
				return array($prompt.':',$field);

			case 'pagedata':
				{
					$prompt = lang('pagedata_codeblock');
					$field  = create_textarea(false,$content_obj->get_property_value('pagedata'),'pagedata','pagesmalltextarea','pagedata','','','80','6');
				}
				return array($prompt.':',$field);
				
			case 'searchable':
				{
					$searchable = $content_obj->get_property_value('searchable');
					if( $searchable == '' ) $searchable = 1;
					$prompt = lang('searchable');
					$field  = '<input type="hidden" name="searchable" value="0" />
                               <input type="checkbox" name="searchable" value="1" '.($searchable==1?'checked="checked"':'').' />';
				}
				return array($prompt.':',$field);

			case 'disable_wysiwyg':
				{
					$prompt = lang('disable_wysiwyg');
					$disable_wysiwyg = $content_obj->get_property_value('disable_wysiwyg');
					if( $disable_wysiwyg == '' ) $disable_wysiwyg = 0;
					$field = '<input type="hidden" name="disable_wysiwyg" value="0" />
             <input type="checkbox" name="disable_wysiwyg" value="1"  '.($disable_wysiwyg==1?'checked="checked"':'').' onclick="this.form.submit()" />';

				}
				return array($prompt.':',$field);

			default:
				return parent::get_single_element($content_obj,$attr,$adding);
			}
	}


    private function parse_content_blocks()
    {
		$content_obj = $this->get_content();
		$result = false;
		global $gCms;
		if ($this->_contentBlocksLoaded) return;

		$templateops = $gCms->GetTemplateOperations();
		{
			$this->_contentBlocks = array();
			if ($content_obj->template_id() && $content_obj->template_id() > -1)
				{
					$template = $templateops->LoadTemplateByID($content_obj->template_id()); /* @var $template Template */
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
					$pattern2 = '/([a-zA-z0-9]+)=["\']([^"\']+)["\']/';
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
									$promptoncopy = 0;

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
									if( !isset($this->_contentBlocks[$name]) )
										{
											$this->_contentBlocks[$name]['type'] = 'text';
											$this->_contentBlocks[$name]['id'] = $id;
											$this->_contentBlocks[$name]['usewysiwyg'] = $usewysiwyg;
											$this->_contentBlocks[$name]['oneline'] = $oneline;
											$this->_contentBlocks[$name]['default'] = $value;
											$this->_contentBlocks[$name]['label'] = $label;
											$this->_contentBlocks[$name]['size'] = $size;
											$this->_contentBlocks[$name]['promptoncopy'] = $promptoncopy;
										}
								}
		  
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
											$exclude = '';
											$promptoncopy = 0;
			  
											foreach ($keyval as $key=>$val)
												{
													switch($key)
														{
														case 'block':
															$id = str_replace(' ', '_', $val);
															$name = $val;
															break;
														case 'promptoncopy':
															$promptoncopy = $val;
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
														case 'exclude':
															$exclude = $val;
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
											$this->_contentBlocks[$name]['exclude'] = $exclude;
											$this->_contentBlocks[$name]['default'] = $value;
											$this->_contentBlocks[$name]['label'] = $label;					
											$this->_contentBlocks[$name]['promptoncopy'] = $promptoncopy;
										}
								}
		  
							$result = true;
						}

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
											$parms = array();
											$promptoncopy = 0;
			  
											foreach ($keyval as $key=>$val)
												{
													switch($key)
														{
														case 'block':
															$id = str_replace(' ', '_', $val);
															$name = $val;
				  
															if(!array_key_exists($val, $this->Properties()->mPropertyTypes))
																{
																	$this->Properties()->Add("string", $id);
																}
															break;
														case 'promptoncopy':
															$promptoncopy = $val;
															break;
														case 'label':
															$label = $val;
															break;
														case 'module':
															$module = $val;
															break;
														case 'assign':
															// do nothing.
															break;
														default:
															$parms[$key] = $val;
															break;
														}
												}
			  
											if( empty($name) ) $name = '**default**';
											$this->_contentBlocks[$name]['type'] = 'module';
											$this->_contentBlocks[$name]['id'] = $id;
											$this->_contentBlocks[$name]['module'] = $module;
											$this->_contentBlocks[$name]['params'] = $parms;
											$this->_contentBlocks[$name]['promptoncopy'] = $promptoncopy;
										}
								}
		  
							$result = true;
						}
	      
					$this->_contentBlocksLoaded = true;
				}

			return $result;
		}
    }


    /**
     * Function to return an array of content blocks
     */
    public function get_content_blocks()
    {
      $this->parse_content_blocks();
      return $this->_contentBlocks;
    }


    /*
     * return the HTML to create the text area in the admin console.
     * does not include a label.
     */
    private function _display_text_block($blockInfo,$value,$adding)
    {
		$content_obj = $this->get_content();
		$ret = '';
		if (isset($blockInfo['oneline']) && $blockInfo['oneline'] == '1' || $blockInfo['oneline'] == 'true')
			{
				$size = (isset($blockInfo['size']))?$blockInfo['size']:50;
				$ret = '<input type="text" size="'.$size.'" name="'.$blockInfo['id'].'" value="'.cms_htmlentities($value, ENT_NOQUOTES, get_encoding('')).'" />';
			}
		else
			{ 
				$block_wysiwyg = true;
				$hide_wysiwyg = $content_obj->get_property_value('disable_wysiwyg');
	  
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
		$config =& $gCms->GetConfig();
		$dir = cms_join_path($config['image_uploads_path'],$blockInfo['dir']);
		$exclude = $blockInfo['exclude'];
		$optprefix = $config['image_uploads_url'];
		if( !empty($blockInfo['dir']) ) $optprefix .= '/'.$blockInfo['dir'];
		$inputname = $blockInfo['id'];
		if( isset($blockInfo['inputname']) )
			{
				$inputname = $blockInfo['inputname'];
			}
		$dropdown = create_file_dropdown($inputname,$dir,$value,'jpg,jpeg,png,gif',true,$exclude,1,$optprefix);
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
							$label = ucwords('content');
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


	public function fill_from_form_data($params)
	{
		parent::fill_from_form_data($params);

		$accepted = array('template_id','pagemetadata','searchable','pagedata',
						  'disable_wysiwyg');

		$content_obj = $this->get_content();
		foreach( $accepted as $oneprop )
			{
				switch($oneprop)
					{
					case 'template_id':
						if( isset($params[$oneprop]) )
							{
								$content_obj->set_template_id($params[$oneprop]);
							}
						break;

					case 'metadata':
						if( isset($params[$oneprop]) )
							{
								$content_obj->set_metadata($params[$oneprop]);
							}
						break;

					default:
						if( isset($params[$oneprop]) )
							{
								$content_obj->set_property_value($oneprop,$params[$oneprop]);
							}
						break;
					}
			}

		$this->parse_content_blocks();
		foreach($this->_contentBlocks as $blockName => $blockInfo)
			{
				$accepted[] = $blockInfo['id'];
			}

		foreach ($accepted as $one_param)
		{
			$content_obj->set_property_value($one_param, $params[$one_param]);
		}
	}


	public function validate()
	{
		$errs = parent::validate();
		if( !$errs )
			{
				$errs = array();
			}

		$this->parse_content_blocks();
		foreach($this->_contentBlocks as $blockName => $blockInfo)
			{
				if( isset($blockInfo['type']) && $blockInfo['type'] == 'module' )
					{
						if( !isset($gCms->modules[$blockInfo['module']]['object']) ) continue;
						$module =& $gCms->modules[$blockInfo['module']]['object'];
						if( !is_object($module) ) continue;
						if( !$module->HasCapability('contentblocks') ) continue;
						$value = $this->get_property_value($blockInfo['id']);
						$tmp = $module->ValidateContentBlockValue($blockName,$value,$blockInfo['params']);
						if( !empty($tmp) )
							{
								$errs[] = $tmp;
							}
					}
			}

		if( count($errs ) ) return $errs;
		return FALSE;
	}
}

#
# EOF
#
?>