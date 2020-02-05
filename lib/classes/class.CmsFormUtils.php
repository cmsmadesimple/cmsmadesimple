<?php

/**
 * Classes and utilities for generating forms
 * @package CMS
 * @license GPL
 * @author  Robert Campbell
 * @since   2.0
 */

/**
 * A static class providing functionality for building forms.
 *
 * This class will undergo changes in the future, and may no longer operate as a static class goinf forward.
 *
 * @package CMS
 * @license GPL
 * @author  Robert Campbell
 * @since   2.0
 */
final class CmsFormUtils
{

    /**
     * @ignore
     */
    private static $_activated_wysiwyg = array();

    /**
     * @ignore
     */
    private static $_activated_syntax;

    /**
     * @ignore
     */
    const NONE = '__none__';

    /**
     * @ignore
     */
    private function __construct() {
    }


    /**
     * A simple recursive utility function to create an option, or a set of options for a select list or multiselect list.
     *
     * Accepts an associative 'option' array with at least two populated keys: 'label' and 'value'.
     * If 'value' is not an array then a single '<option>' is created.  However, if 'value' is itself
     * an array then an 'optgroup' will be created with it's values.
     *
     * i.e: $tmp = array('label'=>'myoptgroup','value'=>array( array('label'=>'opt1','value'=>'value1'), array('label'=>'opt2','value'=>'value2') ) );
     *
     * The 'option' array can have additional keys for 'title' and 'class'
     *
     * i.e: $tmp = array('label'=>'opt1','value'=>'value1','title'=>'My title','class'=>'foo');
     *
     * @param array $data The option data
     * @param string[]|string $selected  The selected elements
     * @return string The generated <option> element(s).
     * @see self::create_options()
     */
    public static function create_option($data,$selected = null)
    {
        $out = '';
        if( !is_array($data) ) return;

        if( isset($data['label']) && isset($data['value']) ) {
            if( !is_array($data['value']) ) {
                $out .= '<option value="'.trim($data['value']).'"';
                if( $selected == $data['value'] || is_array($selected) && in_array($data['value'],$selected) ) $out .= ' selected="selected"';
                if( isset($data['title']) && $data['title'] ) $out .= ' title="'.trim($data['title']).'"';
                if( isset($data['class']) && $data['class'] ) $out .= ' class="'.trim($data['class']).'"';
                $out .= '>'.$data['label'].'</option>';
            }
            else {
                $out .= '<optgroup label="'.$data['label'].'">';
                foreach( $data['value'] as $one ) {
                    $out .= self::create_option($one,$selected);
                }
                $out .= '</optgroup>';
            }
        }
        else {
            foreach( $data as $rec ) {
                $out .= self::create_option($rec,$selected);
            }
        }
        return $out;
    }

    /**
     * Create a series of options suitable for use in a select input element.
     *
     * This method is intended to provide a simple way of creating options from a simple associative array
     * but can accept multiple arrays of options as specified for the CmsFormUtils::create_option method
     *
     * i.e: $tmp = array('value1'=>'label1','value2'=>'label2');
     * $options = CmsFormUtils::create_options($tmp);
     *
     * i.e: $tmp = array( array('label'=>'label1','value'=>'value1','title'=>'title1'),
     *                    array('label'=>'label2','value'=>'value2','class'=>'class2') );
     * $options = CmsFormUtils::create_options($tmp)
     *
     * @param array $options
     * @param mixed $selected
     * @return string
     * @see CmsFormUtils::create_options()
     */
    public static function create_options($options,$selected = '')
    {
        if( !is_array($options) || count($options) == 0 ) return;

        $out = '';
        foreach( $options as $key => $value ) {
            if( !is_array($value) ) {
                $out .= self::create_option(array('label'=>$value,'value'=>$key),$selected);
            }
            else {
                $out .= self::create_option($value,$selected);
            }
        }
        return $out;
    }

    /**
     * Create an advanced select field.
     *
     * @param string $name The name attribute for the select name
     * @param array  $list_options  Options as per the CmsFormUtils::create_options method
     * @param string|string[] $selected Selected value as per the CmsFormUtils::create_option method
     * @param array  $params Array of additional options including: multiple,class,title,id,size
     * @return string The HTML content for the <select> element.
     */
    public static function create_dropdown($name,$list_options,$selected,$params = array())
    {
        if( $name == '' ) return;
        if( !is_array($list_options) || count($list_options) == 0 ) return;

        $options = self::create_options($list_options,$selected);
        $elem_id = $name;

        if( isset($params['multiple']) && !endswith($name,'[]') ) {
            // auto adjust dropdown name if it allows multiple selections.
            $name .= '[]';
        }

        $out = "<select name=\"{$name}\"";
        foreach( $params as $key => $value ) {
            switch( $key ) {
                case 'id':
                    $out .= " id=\"{$value}\"";
                    $elem_id = $value;
                    break;

                case 'multiple':
                    $out .= " multiple=\"multiple\"";
                    break;

                case 'class':
                    $out .= " class=\"{$value}\"";
                    break;

                case 'title':
                    $out .= " title=\"{$value}\"";
                    break;

                case 'size':
                    $value = (int)$value;
                    $out .= " size=\"{$value}\"";
                    break;
            }
        }
        $out .= ">".$options."</select>\n";

        return $out;
    }

    /**
     * @ignore
     */
    private static function _add_syntax($module_name)
    {
        if( !is_array(self::$_activated_syntax) ) self::$_activated_syntax = array();
        if( !in_array($module_name,self::$_activated_syntax) ) self::$_activated_syntax[] = $module_name;
    }

    /**
     * @ignore
     */
    public static function get_requested_syntax_modules()
    {
        return self::$_activated_syntax;
    }

    /**
     * Method to activate a wysiwyg module (which will ensure that the headers and initialization is done.
     * In the frontend the {cms_init_editor} plugin must be included in the head part of the page template.
     *
     * @internal
     * @ignore
     * @param string module_name (required)
     * @param string id (optional) the id of the textarea element)
     * @param string stylesheet_name (optional) the name of a stylesheet to include with this area (some WYSIWYG editors may not support this)
     */
    private static function _add_wysiwyg($module_name,$id = self::NONE,$stylesheet_name = self::NONE)
    {
        if( !$module_name ) return;
        if( !isset(self::$_activated_wysiwyg[$module_name]) ) self::$_activated_wysiwyg[$module_name] = array();
        self::$_activated_wysiwyg[$module_name][] = array('id'=>$id,'stylesheet'=>$stylesheet_name);
    }

    /**
     * @ignore
     */
    public static function get_requested_wysiwyg_modules()
    {
        return self::$_activated_wysiwyg;
    }

    /**
     * A method to create a text area control.
     * parameters:
     *   name          = (required string) name attribute for the text area element.
     *   id            = (optional string) id attribute for the text area element.  If not specified, name is used.
     *   class/classname = (optional string) class attribute for the text area element.  Some values will be added to this string.
     *                   default is cms_textarea
     *   forcemodule   = (optional string) used to specify the module to enable.  If specified, the module name will be added to the
     *                   class attribute.
     *   enablewysiwyg = (optional boolan) used to specify wether a wysiwyg textarea is required.  sets the language to html.
     *   wantedsyntax  = (optional string) used to specify the language (html,css,php,smarty) to use.  If non empty indicates that a
     *                   syntax hilighter module is requested.
     *   cols/width    = (optional integer) columns of the text area (css or the syntax/wysiwyg module may override this)
     *   rows/height   = (optional integer) rows of the text area (css or the syntax/wysiwyg module may override this)
     *   maxlength     = (optional integer) maxlength attribute of the text area (syntax/wysiwyg module may ignore this)
     *   required      = (optional boolean) indicates a required field.
     *   placeholder   = (optional string) placeholder attribute of the text area (syntax/wysiwyg module may ignore this)
     *   value/text    = (optional string) default text for the text area, will undergo entity conversion.
     *   encoding      = (optional string) default utf-8 encoding for entity conversion.
     *   addtext       = (optional string) additional text to add to the textarea tag.
     *   cssname       = (optional string) Pass this stylesheet name to the WYSIWYG area if any.
     *
     * note: if wantedsyntax is empty, AND enablewysiwyg is false, then just a plain text area is creeated.
     *
     * @param array $parms An associative array with parameters.
     * @return string
     */
    public static function create_textarea($parms)
    {
        // todo: rewrite me with var args... to accept a numeric array of arguments, or a hash.
        $haveit = FALSE;
        $result = '';
        $uid = get_userid(false);
        $attribs = array();
        $module = null;
        $attribs['name'] = get_parameter_value($parms,'name');
        if( !$attribs['name'] ) throw new CmsInvalidDataException('"name" is a required parameter');
        $attribs['id'] = get_parameter_value($parms,'id',$attribs['name']);
        $attribs['class'] = get_parameter_value($parms,'class','cms_textarea');
        $attribs['readonly'] = cms_to_bool( get_parameter_value($parms,'readonly', false ) );
        $attribs['disabled'] = cms_to_bool( get_parameter_value($parms,'disabled') );
        $attribs['class'] = get_parameter_value($parms,'classname',$attribs['class']); // classname param can override class.

        $forcemodule = get_parameter_value($parms,'forcemodule');
        $enablewysiwyg = cms_to_bool(get_parameter_value($parms,'enablewysiwyg',false)); // if true, we want a wysiwyg area
        $wantedsyntax = get_parameter_value($parms,'wantedsyntax'); // if not null, and no wysiwyg found, use a syntax area.
        $wantedsyntax = get_parameter_value($parms,'type',$wantedsyntax);
        $attribs['class'] .= ' '.$attribs['name']; // make sure the name is one of the classes.
        foreach( $parms as $key => $val ) {
            if( startswith( $key, 'data-') ) $attribs[$key] = $val;
        }

        if( $enablewysiwyg ) {
            // we want a wysiwyg
            $appendclass = 'cmsms_wysiwyg';
            $module = ModuleOperations::get_instance()->GetWYSIWYGModule($forcemodule);
            if( $module && $module->HasCapability(CmsCoreCapabilities::WYSIWYG_MODULE) ) {
                $appendclass = $module->GetName();
                $attribs['data-cms-lang'] = 'html';
                $css_name = get_parameter_value($parms,'cssname',self::NONE);
                self::_add_wysiwyg($module->GetName(),$attribs['id'],$css_name);
            } else {
                // just incase forced module is not a wysiwyg module.
                $module = null;
            }
            $attribs['class'] .= ' '.$appendclass;
        }

        if( !$module && $wantedsyntax ) {
            $attribs['data-cms-lang'] = trim($wantedsyntax);
            $module = ModuleOperations::get_instance()->GetSyntaxHighlighter($forcemodule);
            if( $module && $module->HasCapability(CmsCoreCapabilities::SYNTAX_MODULE) ) {
                $attribs['class'] .= ' '.$module->GetName();
                self::_add_syntax($module->GetName());
            } else {
                // wanted a syntax module, but couldn't find one...
                $module = null;
            }
        }

        $required = cms_to_bool(get_parameter_value($parms,'required','false'));
        if( $required ) $attribs['required'] = 'required';
        if( $attribs['readonly'] ) $attribs['readonly'] = 'readonly';
        if( $attribs['disabled'] ) $attribs['disabled'] = 'disabled';
        $attribs['cols'] = get_parameter_value($parms,'cols');
        $attribs['cols'] = get_parameter_value($parms,'width',$attribs['cols']);
        if( $attribs['cols'] <= 0 || $attribs['cols'] == '') $attribs['cols'] = 20;
        $attribs['rows'] = get_parameter_value($parms,'rows');
        $attribs['rows'] = get_parameter_value($parms,'height',$attribs['rows']);
        if( $attribs['rows'] <= 0 || $attribs['cols'] == '' ) $attribs['rows'] = 5;
        $attribs['maxlength'] = get_parameter_value($parms,'maxlength');
        if( $attribs['maxlength'] <= 0 ) $attribs['maxlength'] = '';
        $attribs['placeholder'] = get_parameter_value($parms,'placeholder');

        $addtext = get_parameter_value($parms,'addtext');
        $text = get_parameter_value($parms,'value');
        $text = get_parameter_value($parms,'text',$text);

        $result = '<textarea';
        foreach( $attribs as $key => $val ) {
            if( $val != '' && $key != '' ) {
                $key = trim($key);
                $val = trim($val);
                $result .= " {$key}=\"{$val}\"";
            }
        }
        if( !empty( $addtext ) ) $result .= ' '.$addtext;
        $result .= '>'.cms_htmlentities($text,ENT_NOQUOTES,CmsNlsOperations::get_encoding()).'</textarea>';
        return $result;
    }

    /**
     * Create a form start tag, and associated HTML for a form.
     *
     * This is an intelligent function, it can output either a simple <form> tag or a form for a module action.
     * This function reads all configuration options from the $params array, and then builds the form tag, and optional hidden input fields.
     * When parameters exist to build a module action, the parameters are properly encoded, and default values properly calculated.
     *
     * Accepteted parameters:
     *   url    - The action property for the form tag.  If not specified, this is automatically calculated.
     *   module - When creating a form tag that will be handled by a module action, the module name can be provided here.
     *   action - When creating a form tag that will be handled by a module action, the action name to route to.  If not specified, this is calculated.
     *   mid    - When creating a form tag that will be handled by a module action, the unique mid to use.  If not specified, this is calculated.
     *   returnid - When calculating a form tag that will be handled by a module action, the integer CMSMS page id to handle the request
     *       This then determines the page template, and other display logic that controls page rendering when the form submission is handled.
     *   inline - When creating a form tag that will be handled by a module action, this flag indicates whether the processing output will replace
     *       The output generated by the action generating the form.  The mid must be consistent through the initial form generation, and the handling.
     *   prefix - Alias for 'mid'
     *   method - The method property for the form tag.  If not specified POST is assumed.
     *   enctype - The enctype property for the form tag.  If not specified multipart/form-data is assumed
     *   id     - The id property for the form tag.  This can be empty
     *   class  - The class property for the form tag.  This can be empty.
     *   extraparms - An associative array of extra parameters to be encoded into the module action.
     *   extra_str - Extra text to append to the form tag.  For compatibility only, may be removed at some time.
     *
     * Note: additionally, any parameters who's key begins with the string form- will be stripped of the form- prefix on the key,
     *   and added verbatim to the form tag.  This is preferred over using the extra_str option.
     *   any parameters that are not listed above, and who's key does not begin with the form- string will be encoded into the
     *   mact, or into hidden form fields verbatim.
     *
     * @param array $params
     */
    public static function create_form_start(array $params)
    {
        $gCms = cmsms();

        // setup default mactparms and tagparms
        $tagparms = $mactparms = [];
        $mactparms['module'] = $mactparms['action'] = $mactparms['mid'] = $mactparms['inline'] = $mactparms['returnid'] = null;
        $tagparms['action'] = null;
        $tagparms['method'] = 'post';
        $tagparms['enctype'] = 'multipart/form-data';

        // prcess arguments
        $extra_str = null;
        $parms = array();
        foreach( $params as $key => $value ) {
            switch( $key ) {
                case 'module':
                case 'action':
                case 'mid':
                case 'returnid':
                    $mactparms[$key] = trim($value);
                    break;

                case 'inline':
                    $mactparms[$key] = cms_to_bool($value);
                    break;

                case 'prefix':
                    $mactparms['mid'] = trim($value);
                    break;

                case 'method':
                    $tagparms[$key] = strtolower(trim($value));
                    break;

                case 'url':
                    $key = 'action';
                    if( dirname($value) == '.' ) {
                        $config = $gCms->GetConfig();
                        $value = $config['admin_url'].'/'.trim($value);
                    }
                    $tagparms[$key] = trim($value);
                    break;

                case 'enctype':
                case 'id':
                case 'class':
                    $tagparms[$key] = trim((string)$value);
                    break;

                case 'extraparms':
                    if( is_array($value) && count($value) ) {
                        foreach( $value as $key=>$value2 ) {
                            $parms[$key] = $value2;
                        }
                    }
                    break;

                case 'extra_str':
                    $extra_str = trim($value);
                    break;

                case 'assign':
                    break;

                default:
                    if( startswith($key, 'form-') ) {
                        $key = substr($key, 5);
                        $tagparms[$key] = $value;
                    } else {
                        $parms[$key] = $value;
                    }
                    break;
            }
        }

        // make sure we have all the data in mactparms and tagparams, and extraparms
        $extraparms = null;
        if ($gCms->test_state(CmsApp::STATE_LOGIN_PAGE)) {
            $tagparms['action'] = 'login.php';
        }
        else if ($mactparms['module']) {
            // link to module action
            if( !$mactparms['returnid'] ) {
                // link to admin action
                $tagparms['action'] = 'moduleinterface.php';
                if( empty($mactparms['action'] ) ) $mactparms['action'] = 'defaultadmin';
                if (!$mactparms['mid']) $mactparms['mid'] = 'm1_';
            }
            else {
                // link to frontend action
                $tagparms['action'] = 'index.php';
                if( empty($mactparms['action']) ) $mactparms['action'] = 'default';
                $hm = $gCms->GetHierarchyManager();
                $node = $hm->sureGetNodeById($mactparms['returnid']);
                if (!$node) {
                    cms_error("Call to CmsFormUtils::create_form_start with an invalid returnid");
                    return;
                }

                $content_obj = $node->getContent();
                if( !$content_obj ) {
                    cms_error("Call to CmsFormUtils::create_form_start with an invalid returnid");
                    return;
                }
                $tagparms['action'] = $content_obj->GetURL();
                if( empty($mactparms['mid']) ) $mactparms['mid'] = 'cntnt01';
                if( empty($mactparms['inline']) ) $mactparms['inline'] = false;
            }
        }
        if (!$gCms->is_frontend_request()) {
            // admin request
            if( !isset($mactparms['returnid']) || $mactparms['returnid'] < 1 ) {
                // not frontend request, not linking to a frontend url.
                if( isset($_SESSION[CMS_USER_KEY]) ) $extraparms[CMS_SECURE_PARAM_NAME] = $_SESSION[CMS_USER_KEY];
            } else if( empty($tagparms['action']) ) {
                cms_error("Call to CmsFormUtils::create_form_start could not auto-determine valid form action");
                return;
            }
        }

        // encode the secure params, and the mact into an array of variables to attach to the form.
        $formdata = null;
        if( $mactparms['module'] && $mactparms['action'] ) {
            $encoder = $gCms->get_mact_encoder();
            $mact = $encoder->create_mactinfo($mactparms['module'], $mactparms['mid'], $mactparms['action'], $mactparms['inline'], $parms);
            $formdata = $encoder->encode_mact($mact);
        }
        if( !empty($extraparms) ) {
            if( empty($formdata) ) $formdata = [];
            $formdata = array_merge($formdata,$extraparms);
        }

        // assemble the output
        $out = '<form';
        foreach( $tagparms as $key => $value ) {
            if($value ) {
                $out .= " $key=\"$value\"";
            } else {
                $out .= " $key";
            }
        }
        if( $extra_str ) $out .= ' '.$extra_str;
        $out .= '>'."\n"; // end the form tag

        if( !empty($formdata) ) {
            $out .= '<div class="hidden">';
            $fmt = '<input type="hidden" name="%s" value="%s"/>';
            foreach( $formdata as $key => $val ) {
                $out .= sprintf($fmt,$key,$val);
            }
            $out .= '</div>';
        }

        return $out;
    }
} // end of class
