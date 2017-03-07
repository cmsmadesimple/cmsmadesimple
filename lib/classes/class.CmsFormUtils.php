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
    private function __construct() {}


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
        if( !$attribs['name'] ) throw new CmsInvalidDataException('"name" is a required parameter"');
        $attribs['id'] = get_parameter_value($parms,'id',$attribs['name']);
        $attribs['class'] = get_parameter_value($parms,'class','cms_textarea');
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

} // end of class

?>