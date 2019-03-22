<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
 * Class definition and methods for the main Content class.
 *
 * @package CMS
 * @subpackage content_types
 * @license GPL
 */

/**
 * Implenents the Content (page) content type.
 *
 * This is the primary content type. This represents an HTML page generated by smarty.
 *
 * @package CMS
 * @subpackage content_types
 * @license GPL
 */
class Content extends ContentBase
{

    /**
     * @ignore
     */
    private $_contentBlocks = null;

    /**
     * Indicates whether or not this content type may be copied.
     * Content pages are copyable (for those with sufficient permission)
     *
     * @return bool TRUE
     */
    public function IsCopyable()
    {
        return TRUE;
    }

    /**
     * Indicates wether this content object can be used in search
     * (does not test if individual properties are indexable or not)
     *
     * @since 2.0
     * @return bool
     */
    public function IsSearchable()
    {
        if( !parent::IsSearchable() ) return FALSE;
        return ($this->GetPropertyValue('searchable') == 0)?FALSE:TRUE;
    }

    public function HasSearchableContent()
    {
        return TRUE;
    }

    /**
     * Indicates wether ths page type uses a template.
     * Content pages do use a template.
     *
     * @since 2.0
     * @return true
     */
    public function HasTemplate()
    {
        return TRUE;
    }

    /**
     * Friendly name for this content type.
     *
     * Returns a translated string for the name of this content type
     * "Content" in english.
     *
     * @return string
     */
    public function FriendlyName()
    {
        return lang('contenttype_content');
    }

    /**
     * Indicates whether or not objects of this type may be made the default content object.
     * "Content" pages can become default.
     *
     * @return bool
     */
    public function IsDefaultPossible()
    {
        return TRUE;
    }

    /**
     * Set up base property attributes for this content type
     *
     * This property type adds these properties: design_id, template, searchable, disable_wysiwyg, pagemetadata, pagedata
     */
    public function SetProperties()
    {
        parent::SetProperties();
        $this->AddProperty('design_id',0,self::TAB_OPTIONS);
        $this->AddProperty('template_rsrc',0,self::TAB_OPTIONS);
        $this->AddProperty('searchable',20,self::TAB_OPTIONS);
        $this->AddProperty('disable_wysiwyg',60,self::TAB_OPTIONS);
        $this->AddProperty('pagemetadata',1,self::TAB_LOGIC);
        $this->AddProperty('pagedata',2,self::TAB_LOGIC);
        $this->AddProperty('defaultcontent',10,self::TAB_OPTIONS);
        $this->AddProperty('wantschildren',10,self::TAB_OPTIONS);
    }

    /**
     * Indicates wether pages of this type can be previewed.
     * "Content" pages can be previewed in the editor.
     *
     * @return bool TRUE
     */
    public function HasPreview()
    {
        return TRUE;
    }

    public function WantsChildren()
    {
        // an empty/null response defaults to true.
        $tmp = $this->GetPropertyValue('wantschildren');
        if( $tmp === '0' ) return FALSE;
        return TRUE;
    }


    /**
     * Set content attribute values (from parameters received from admin add/edit form)
     *
     * @param array $params Hash of parameters to load into content attributes
     * @param bool  $editing Whether we in an add or edit operation.
     */
    public function FillParams(array $params,bool $editing = false)
    {
        if (isset($params)) {
            $parameters = array('pagedata','searchable','disable_wysiwyg','design_id','wantschildren');

            //pick up the template id before we do parameters
            if (isset($params['template_rsrc'])) {
                $newvalue = filter_var( $params['template_rsrc'], FILTER_SANITIZE_STRING );
                $oldvalue = $this->GetPropertyValue('template_rsrc');
                if( $newvalue != $oldvalue ) $this->_contentBlocks = null;
                $this->SetPropertyValue('template_rsrc',$newvalue);
            }

            if( $this->IsDefaultPossible() && isset($params['defaultcontent']) ) {
                $this->mDefaultContent = (int) $params['defaultcontent'];
            }

            // add content blocks
            $blocks = $this->get_content_blocks();
            if( is_array($blocks) && count($blocks) ) {
                foreach($blocks as $blockName => $blockInfo) {
                    $name = $blockInfo['id'];
                    $parameters[] = $name;
                    if( isset($blockInfo['type']) && $blockInfo['type'] == 'module' ) {
                        $module = cms_utils::get_module($blockInfo['module']);
                        if( !is_object($module) ) continue;
                        if( !$module->HasCapability(CmsCoreCapabilities::CONTENT_BLOCKS) ) continue;
                        $tmp = $module->GetContentBlockFieldValue($blockName,$blockInfo['params'],$params,$this);
                        if( $tmp != null ) $params[$name] = $tmp;
                    }
                }
            }

            // do the content property parameters
            foreach ($parameters as $oneparam) {
                         if( !isset($params[$oneparam]) ) continue;
                         $val = $params[$oneparam];
                switch( $oneparam ) {
                    case 'pagedata':
                        // nothing
                        break;
                    default:
                        if( count($blocks) && isset($blocks[$oneparam]) ) {
                            // it's a content block.
                            $val = $val;
                        } else {
                            $val = (int) $val;
                        }
                        break;
                }
                $this->SetPropertyValue($oneparam,$val);
            }

            // metadata
            if (isset($params['metadata'])) $this->mMetadata = $params['metadata'];
        }
        parent::FillParams($params,$editing);
    }

    /**
     * Gets the main content
     *
     * @param string $param which attribute to return (content_en is assumed)
     * @return string the specified content
     */
    public function Show(string $param = 'content_en')
    {
        $param = trim($param);
        if( !$param ) $param = 'content_en';
        $param = str_replace(' ','_',$param);
        return $this->GetPropertyValue($param);
    }

    /**
     * Return a list of all of the properties that may be edited by the current user when editing this content item
     * in a content editor form.
     *
     * This method calls the same method in the base class, then parses the content blocks in the templates and adds
     * the appropriate information for all detected content blocks.
     *
     * @see ContentBase::GetEditableProperties()
     * @return array Array of stdclass objects containing name (string), tab (string), priority (integer), required (boolean) members
     */
    public function GetEditableProperties()
    {
        $props = parent::GetEditableProperties();

        // add in content blocks
        $blocks = $this->get_content_blocks();
        if( is_array($blocks) && count($blocks) ) {
            $priority = 100;
            foreach( $blocks as $block ) {
                // todo, skip this block if permissions don't allow.

                $prop = new stdClass;
                $prop->name = $block['name'];
                $prop->extra = $block;
                if( !isset($block['tab']) || $block['tab'] == '' ) $block['tab'] = self::TAB_MAIN;
                $prop->tab = $block['tab'];
                if( isset($block['priority']) ) {
                    $prop->priority = $block['priority'];
                }
                else {
                    $prop->priority = $priority++;
                }
                $props[] = $prop;
            }
        }

        return $props;
    }

    /**
     * Validate the user's entries in the content add/edit form
     *
     * This method also calls the parent method to validate the standard properties
     *
     * @return mixed either an array of validation error strings, or false to indicate no errors
     */
    public function ValidateData()
    {
        $errors = parent::ValidateData();
        if( $errors === FALSE ) $errors = array();

        if( !$this->GetPropertyValue('template_rsrc') ) {
            $errors[] = lang('nofieldgiven',[ lang('template') ]);
            $result = false;
        }

        $blocks = $this->get_content_blocks();
        if( !$blocks ) {
            $errors[] = lang('error_parsing_content_blocks');
            $result = false;
        }

        $have_content_en = FALSE;
        if( is_array($blocks) && count($blocks) ) {
            foreach($blocks as $blockName => $blockInfo) {
                if( $blockInfo['id'] == 'content_en' ) $have_content_en = TRUE;
                if( isset($blockInfo['required']) && $blockInfo['required'] && ($val = $this->GetPropertyValue($blockName)) == '' ) {
                    $errors[] = lang('emptyblock',array($blockName));
                }
                if( isset($blockInfo['type']) && $blockInfo['type'] == 'module' ) {
                    $module = cms_utils::get_module($blockInfo['module']);
                    if( !is_object($module) ) continue;
                    if( !$module->HasCapability(CmsCoreCapabilities::CONTENT_BLOCKS) ) continue;
                    $value = $this->GetPropertyValue($blockInfo['id']);
                    $tmp = $module->ValidateContentBlockFieldValue($blockName,$value,$blockInfo['params'],$this);
                    if( !empty($tmp) ) {
                        $errors[] = $tmp;
                        $result = false;
                    }
                }
            }
        }

        if( !$have_content_en ) {
            $errors[] = lang('error_no_default_content_block');
            $result = false;
        }

        return (count($errors) > 0?$errors:FALSE);
    }

    public function TemplateResource()
    {
        $config = \cms_config::get_instance();
        $tmp = $this->GetPropertyValue('template_rsrc');
        if( !$tmp ) $tmp = $this->mTemplateId;
        if( $tmp ) {
            $num = (int) $tmp;
            if( $num > 0 && trim($num) == $tmp ) {
                // numeric assume design manager.
                return "cms_template:$tmp";
            } else {
                return $tmp;
            }
        }
    }

    private function get_template_resource()
    {
        // used for the parser.
        $config = \cms_config::get_instance();
        if( empty($config['page_template_list']) ) {
            return 'cms_template:'.$this->TemplateId();
        } else {
            $raw = $config['page_template_list'];
            if( is_string($raw) ) return $raw;
            $val = current($raw);
            if( strpos($val,':') === FALSE ) $val = 'cmsfile:'.$val;
            return $val;
        }
    }

    /**
     * Return content blocks in the current page's templates.
     *
     * This method can only be called once per request.
     *
     * @access private
     * @internal
     */
    private function get_content_blocks()
    {
        if( is_array($this->_contentBlocks) ) return $this->_contentBlocks;

        $this->_contentBlocks = null;
        try {
            $parser = \CMSMS\internal\page_template_parser::create( $this->TemplateResource() );
            $this->_contentBlocks = $parser->get_content_blocks();
            unset($parser);
        }
        catch( SmartyException $e ) {
            // smarty exceptions here could be a bad template, or missing template, or something else.
            throw new CmsContentException(lang('error_parsing_content_blocks').': '.$e->GetMessage());
        }
        return $this->_contentBlocks;
    }

    protected function get_template_list()
    {
        static $_list;
        if( is_array($_list) && count($_list) ) return $_list;

        $_list = null;
        $config = \cms_config::get_instance();
        if( empty($config['page_template_list']) ) {
            $_tpl = CmsLayoutTemplate::template_query( ['as_list'=>1] );
            if( is_array($_tpl) && count($_tpl) > 0 ) {
                foreach( $_tpl as $tpl_id => $tpl_name ) {
                    $_list[] = [ 'value'=>$tpl_id,'label'=>$tpl_name ];
                }
            }
        }
        else {
            $raw = $config['page_template_list'];
            if( is_string($raw) ) $raw = [ lang('default')=>$raw ];

            foreach( $raw as $label => $rsrc ) {
                $_list[] = [ 'label'=>$label, 'value'=>$rsrc ];
            }
        }
        return $_list;
    }

    /**
     * Given information about a single property this method returns that property
     *
     * @param string $one The property name
     * @param string $adding A flag indicating whether or not we are in add or edit mode
     * @return array consisting of two elements: A label, and the input element HTML and javascript.
     * @internal
     */
    protected function display_single_element(string $one, bool $adding)
    {
        $config = \cms_config::get_instance();

        switch($one) {
            case 'design_id':
                if( $config['page_template_list'] ) break;
                try {
                    $_designlist = CmsLayoutCollection::get_list();
                    $design_id = $this->GetPropertyValue('design_id');
                    if( $design_id < 1 ) {
                        try {
                            $dflt_design = CmsLayoutCollection::load_default();
                            $design_id = $dflt_design->get_id();
                        }
                        catch( \Exception $e ) {
                            audit('','CMSContentManager','No default design found');
                        }
                    }
                    $out = '';
                    if( is_array($_designlist) && count($_designlist) ) {
                        $out = CmsFormUtils::create_dropdown('design_id',$_designlist,$this->GetPropertyValue('design_id'),
                               array('id'=>'design_id'));
                        $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','info_editcontent_design',lang('help_title_editcontent_design'));
                        return array('<label for="design_id">*'.lang('design').':</label>'.$help,$out);
                    }
                }
                catch( CmsException $e ) {
                    // nothing here yet.
                }
                break;

            case 'template_rsrc':
                try {
                       $current = $this->GetPropertyValue('template_rsrc');
                       if( !$current ) $current = $this->TemplateId();
                       $options = $this->get_template_list();

                       $out = \CmsFormUtils::create_dropdown('template_rsrc', $options, $current, ['id'=>'template_rsrc'] );
                      $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','info_editcontent_template',lang('help_title_editcontent_template'));
                      return array('<label for="template_rsrc">*'.lang('template').':</label>'.$help,$out);
                }
                catch( CmsException $e ) {
                    // nothing here yet.
                }
                break;

            case 'pagemetadata':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_pagemeta',lang('help_title_content_pagemeta'));
                return array('<label for="id_pagemetadata">'.lang('page_metadata').':</label>'.$help,
             CmsFormUtils::create_textarea(array('name'=>'metadata','value'=>$this->MetaData(),
															 'classname'=>'pagesmalltextarea',
															 'width'=>80,'height'=>3,
															 'id'=>'metadata')));

            case 'pagedata':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_pagedata',lang('help_title_content_pagedata'));
                return array('<label for="id_pagedata">'.lang('pagedata_codeblock').':</label>'.$help,
             	    CmsFormUtils::create_textarea(array('name'=>'pagedata','value'=>$this->GetPropertyValue('pagedata'),
															 'width'=>80,'height'=>3,
															 'classname'=>'pagesmalltextarea','id'=>'id_pagedata')));

            case 'searchable':
                $searchable = $this->GetPropertyValue('searchable');
                if( $searchable == '' ) $searchable = 1;
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_page_searchable',lang('help_title_page_searchable'));
                return array('<label for="id_searchable">'.lang('searchable').':</label>'.$help,
                    '<input type="hidden" name="searchable" value="0"/>
                    <input id="id_searchable" type="checkbox" name="searchable" value="1" '.($searchable==1?'checked="checked"':'').'/>');
            // phpcs shut up

            case 'defaultcontent':
                if( $this->IsDefaultPossible() && check_permission( get_userid(), 'Manage All Content') ) {
                        $default = $this->DefaultContent();
                        $help = '&nbsp'.cms_admin_utils::get_help_tag('core','help_page_default',lang('help_title_page_default'));
                        $attrtext = '';
                        $label = '<label for="id_dfltcontent">'.lang('prompt_defaultcontent').':</label>'.$help;
                    if( $default ) {
                        return [ $label,
                        '<input id="id_dfltcontent" type="checkbox" disabled value="1" checked/>'
                        ];
                    }
                    else {
                        return [ $label,
                        '<input type="hidden" name="defaultcontent" value="0"/>
						  <input id="id_dfltcontent" type="checkbox" name="defaultcontent" value="1"/>'
                        ];
                    }
                }
		              break;

            case 'disable_wysiwyg':
                $disable_wysiwyg = $this->GetPropertyValue('disable_wysiwyg');
                if( $disable_wysiwyg == '' ) $disable_wysiwyg = 0;
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_page_disablewysiwyg',lang('help_title_page_disablewysiwyg'));
                return array('<label for="id_disablewysiwyg">'.lang('disable_wysiwyg').':</label>'.$help,
                '<input type="hidden" name="disable_wysiwyg" value="0" />
                          <input id="id_disablewysiwyg" type="checkbox" name="disable_wysiwyg" value="1"  '.($disable_wysiwyg==1?'checked="checked"':'').'/>');
            // phpcs shut up

            case 'wantschildren':
                $showadmin = cmsms()->GetContentOperations()->CheckPageOwnership(get_userid(), $this->Id());
                if ( check_permission(get_userid(),'Manage All Content') || $showadmin ) {
                             $wantschildren = $this->WantsChildren();
                             $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_page_wantschildren',lang('help_title_page_wantschildren'));
                             return array('<label for="id_wantschildren">'.lang('wantschildren').':</label>'.$help,
                             '<input type="hidden" name="wantschildren" value="0"/>
                              <input id="id_wantschildren" type="checkbox" name="wantschildren" value="1" '.($wantschildren?'checked="checked"':'').'/>');
                }
                break;

            default:
                // check if it's content block
                $blocks = $this->get_content_blocks();
                if( isset($blocks[$one]) ) {
                      // its a content block
                      $block = $blocks[$one];
                      $data = $this->GetPropertyValue($block['id']);
                      return $this->display_content_block($one,$block,$data,$adding);
                }
                else {
                    // call the parent class
                    return parent::display_single_element($one,$adding);
                }
        }
    }

    private function _get_param($in,$key,$dflt = null)
    {
        if( !is_array($in) ) return $dflt;
        if( is_array($key) ) return $dflt;
        if( !isset($in[$key]) ) return $dflt;
        return $in[$key];
    }

    /**
     * @ignore
     */
    private function _display_text_block($blockInfo,$value,$adding)
    {
        $ret = '';
        $oneline = cms_to_bool($this->_get_param($blockInfo,'oneline'));
        $required = cms_to_bool($this->_get_param($blockInfo,'required'));
        $placeholder = trim($this->_get_param($blockInfo,'placeholder'));
        $maxlength = (int) $this->_get_param($blockInfo,'maxlength',255);
        $adminonly = cms_to_bool($this->_get_param($blockInfo,'adminonly',0));
        if( $adminonly ) {
            $uid = get_userid(FALSE);
            $res = cmsms()->GetUserOperations()->UserInGroup($uid,1);
            if( !$res ) return;
        }
        $adminonly = cms_to_bool(get_parameter_value($blockInfo,'adminonly',0));
        if( $adminonly ) {
            $uid = get_userid(FALSE);
            $res = cmsms()->GetUserOperations()->UserInGroup($uid,1);
            if( !$res ) return;
        }
        if( $this->Id() < 1 && empty($value) ) {
            $value = trim($this->_get_param($blockInfo,'default'));
        }
        if ($oneline) {
            $size = (int) $this->_get_param($blockInfo,'size',50);
            $ret = '<input type="text" size="'.$size.'" maxlength="'.$maxlength.'" name="'.$blockInfo['id'].'" value="'.cms_htmlentities($value, ENT_NOQUOTES, CmsNlsOperations::get_encoding('')).'"';
            if( $required ) $ret .= " required=\"required\"";
            if( $placeholder ) $ret .= " placeholder=\"{$placeholder}\"";
            $ret .= '/>';
        }
        else {
            $block_wysiwyg = true;
            $hide_wysiwyg = $this->GetPropertyValue('disable_wysiwyg');

            if ($hide_wysiwyg) {
                $block_wysiwyg = false;
            }
            else {
                $block_wysiwyg = $blockInfo['usewysiwyg'] == 'false'?false:true;
            }

            $parms = [ 'name'=>$blockInfo['id'],'enablewysiwyg'=>$block_wysiwyg,'value'=>$value,'id'=>$blockInfo['id'] ];
            if( $required ) $parms['required'] = 'required';
            if( $placeholder ) $parms['placeholder'] = $placeholder;
            $parms['width'] = (int) $this->_get_param($blockInfo,'width',80);
            $parms['height'] = (int) $this->_get_param($blockInfo,'height',10);
            if( isset($blockInfo['cssname']) && $blockInfo['cssname'] ) $parms['cssname'] = $blockInfo['cssname'];
            if( (!isset($parms['cssname']) || $parms['cssname'] == '') && cms_siteprefs::get('content_cssnameisblockname',1) ) {
                $parms['cssname'] = $blockInfo['id'];
            }
            foreach( $blockInfo as $key => $val ) {
                if( !startswith($key,'data-') ) continue;
                $parms[$key] = $val;
            }
            $ret = CmsFormUtils::create_textarea($parms);
        }
        return $ret;
    }

    /**
     * @ignore
     */
    private function _display_static_text_block($blockInfo)
    {
        $out = '<div class="static_text" data-name="'.$blockInfo['name'].'"}>';
        $out .= $blockInfo['static_content'];
        $out .= "</div>\n";
        return [ ' ',$out ];
    }

    /**
     * @ignore
     */
    private function _display_image_block($blockInfo,$value,$adding)
    {
        $adminonly = cms_to_bool($this->_get_param($blockInfo,'adminonly',0));
        if( $adminonly ) {
            $uid = get_userid(FALSE);
            $res = cmsms()->GetUserOperations()->UserInGroup($uid,1);
            if( !$res ) return;
        }
        $config = \cms_config::get_instance();
        $adddir = get_site_preference('contentimage_path');
        if( $blockInfo['dir'] != '' ) $adddir = $blockInfo['dir'];
        $dir = cms_join_path($config['uploads_path'],$adddir);
        $rp1 = realpath($config['uploads_path']);
        $rp2 = realpath($dir);

        $dropdown = null;
        if( !startswith($rp2,$rp1) ) {
            $err = lang('err_invalidcontentimgpath');
            return '<div class="red">'.$err.'</div>';
        }

        $inputname = $blockInfo['id'];
        if( isset($blockInfo['inputname']) ) $inputname = $blockInfo['inputname'];
        $prefix = '';
        if( isset($blockInfo['sort']) ) $sort = (int)$blockInfo['sort'];
        if( isset($blockInfo['exclude']) ) $prefix = $blockInfo['exclude'];
        $filepicker = \cms_utils::get_filepicker_module();
        if( $filepicker ) {
            $profile_name = get_parameter_value($blockInfo,'profile');
            $profile = $filepicker->get_profile_or_default($profile_name, $dir, get_userid() );
            $parms = ['top'=>$dir, 'type'=>'image' ];
            if( $sort ) $parms['sort'] = TRUE;
            if( $prefix ) $parms['exclude_prefix'] = $prefix;
            $profile = $profile->overrideWith( $parms );
            $input = $filepicker->get_html( $inputname, $value, $profile);
            return $input;
        } else {
            $dropdown = create_file_dropdown($inputname,$dir,$value,'jpg,jpeg,png,gif','',true,'',$prefix,1,$sort);
            if( $dropdown === false ) $dropdown = lang('error_retrieving_file_list');
            return $dropdown;
        }
    }

    /**
     * @ignore
     */
    private function _display_module_block($blockName,$blockInfo,$value,$adding)
    {
        $adminonly = cms_to_bool($this->_get_param($blockInfo,'adminonly',0));
        if( $adminonly ) {
            $uid = get_userid(FALSE);
            $res = cmsms()->GetUserOperations()->UserInGroup($uid,1);
            if( !$res ) return;
        }

        $ret = '';
        if( !isset($blockInfo['module']) ) return FALSE;
        $module = cms_utils::get_module($blockInfo['module']);
        if( !is_object($module) ) return FALSE;
        if( !$module->HasCapability(CmsCoreCapabilities::CONTENT_BLOCKS) ) return FALSE;
        if( isset($blockInfo['inputname']) && !empty($blockInfo['inputname']) ) {
            // a hack to allow overriding the input field name.
            $blockName = $blockInfo['inputname'];
        }
        $tmp = $module->GetContentBlockFieldInput($blockName,$value,$blockInfo['params'],$adding,$this);
        return $tmp;
    }

    /**
     * @ignore
     */
    private function display_content_block($blockName,$blockInfo,$value,$adding = false)
    {
        // it'd be nice if the content block was an object..
        // but I don't have the time to do it at the moment.
               $noedit = cms_to_bool($this->_get_param($blockInfo,'noedit','false'));
        if( $noedit ) return;

        $field = '';
        $help = '';
        $label = trim($this->_get_param($blockInfo,'label'));
        if( $label == '' ) $label = $blockName;
        $required = cms_to_bool($this->_get_param($blockInfo,'required','false'));
        if( $blockName == 'content_en' && $label == $blockName ) {
            $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_content_en',lang('help_title_maincontent'));
            $label = lang('content');
        }
        if( $required ) $label = '*'.$label;

        switch( $blockInfo['type'] ) {
            case 'text':
                $label = '<label for="'.$blockName.'">'.$label.':</label>';
                if( $help ) $label .= '&nbsp;'.$help;
                $field = $this->_display_text_block($blockInfo,$value,$adding);
                break;

            case 'image':
                $field = $this->_display_image_block($blockInfo,$value,$adding);
                break;

            case 'static':
                $tmp = $this->_display_static_text_block($blockInfo);
                if( is_array($tmp) ) {
                    $field = $tmp[0];
                    if( count($tmp) == 2 ) {
                        if( !$label || $label == $blockName ) $label = $tmp[0];
                        $field = $tmp[1];
                    }
                }
                else {
                    $field = $tmp;
                }
                break;

            case 'module':
                $tmp = $this->_display_module_block($blockName,$blockInfo,$value,$adding);
                if( is_array($tmp) ) {
                    if( count($tmp) == 2 ) {
                        if( !$label || $label == $blockName ) $label = $tmp[0];
                        $field = $tmp[1];
                    }
                    else {
                        $field = $tmp[0];
                    }
                }
                else {
                    $field = $tmp;
                }
                break;
        }
        if( empty($field) ) return FALSE;
        if( empty($label) ) $label = $blockName.':';
        return array($label,$field);
    }
} // end of class

?>
