<?php

final class CmsFormUtils 
{
  private static $_activated_wysiwyg;
  private static $_activated_syntax;

  private function __construct() {}

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
	$out .= '">'.$data['label'].'</option>';
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

  private static function _add_syntax($module_name)
  {
    if( !is_array(self::$_activated_syntax) ) self::$_activated_syntax = array();
    if( !in_array($module_name,self::$_activated_syntax) ) self::$_activated_syntax[] = $module_name;
  }

  public static function get_syntax_modules()
  {
    return self::$_activated_syntax;
  }

  private static function _add_wysiwyg($module_name)
  {
    if( !is_array(self::$_activated_wysiwyg) ) self::$_activated_wysiwyg = array();
    if( !in_array($module_name,self::$_activated_wysiwyg) ) self::$_activated_wysiwyg[] = $module_name;
  }

  public static function get_wysiwyg_modules()
  {
    return self::$_activated_wysiwyg;
  }

  /**
   * A method to create a text area control
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
   *   value/text    = (optional string) default text for the placeholder, will undergo entity conversion before returning.
   *   encoding      = (optional string) default utf-8 encoding for entity conversion.
   *   addtext       = (optional string) additional text to add to the textarea tag.
   *
   * note: if wantedsyntax is empty, AND enablewysiwyg is false, then just a plain text area is creeated.
   *
   * @param array   an associative array with parameters.
   * @return string
   */
  public static function create_textarea($parms)
  {
    // todo: rewrite me with var args... to accept a numeric array of arguments, or a hash.
    $gCms = cmsms();
    $haveit = FALSE;
    $result = '';
    $uid = get_userid(false);
    $attribs = array();
    $module = null;
    $attribs['name'] = get_parameter_value($parms,'name');
    if( !$attribs['name'] ) throw new CmsInvalidDataException('"name" is a required parameter"');
    $attribs['id'] = get_parameter_value($parms,'id',$attribs['name']);
    $attribs['class'] = get_parameter_value($parms,'class','cms_textarea');
    $attribs['class'] = get_parameter_value($parms,'classname',$attribs['class']);

    $forcemodule = get_parameter_value($parms,'forcemodule');
    $enablewysiwyg = cms_to_bool(get_parameter_value($parms,'enablewysiwyg','false')); // if not false, we want a wysiwyg area
    $wantedsyntax = get_parameter_value($parms,'wantedsyntax'); // if not null, and no wysiwyg found, use a syntax area.

    if( $enablewysiwyg ) {
      $module = cmsms()->GetModuleOperations()->GetWYSIWYGModule($forcemodule);
      if( $module && $module->HasCapability('wysiwyg') ) {
	if( $forcemodule ) {
	  $attribs['class'] .= ' '.$module->GetName();
	}
	else {
	  $attribs['class'] .= ' cms_wysiwyg';
	}
	$attribs['data-cms-lang'] = 'html';
	self::_add_wysiwyg($module->GetName());
      } else {
	// just incase forced module is not a wysiwyg module.
	$module = null;
      }
    }

    if( !$module && $wantedsyntax ) {
      $attribs['data-cms-lang'] = 'smarty';
      $module = cmsms()->GetModuleOperations()->GetSyntaxHighlighter($forcemodule);
      if( $module && $module->HasCapability('syntaxhighlighting') ) {
	if( $forcemodule ) {
	  $attribs['class'] .= ' '.$module->GetName();
	}
	else {
	  $attribs['class'] .= ' cms_syntaxarea';
	}
	$attribs['data-cms-lang'] = trim($wantedsyntax);
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
    if( $attribs['cols'] < 0 ) $attribs['cols'] = '';
    $attribs['rows'] = get_parameter_value($parms,'rows');
    $attribs['rows'] = get_parameter_value($parms,'height',$attribs['rows']);
    if( $attribs['rows'] < 0 ) $attribs['rows'] = '';
    $attribs['maxlength'] = get_parameter_value($parms,'maxlength');
    if( $attribs['maxlength'] < 0 ) $attribs['maxlength'] = '';
    $attribs['placeholder'] = get_parameter_value($parms,'placeholder');

    $addtext = get_parameter_value($parms,'addtext');
    $text = get_parameter_value($parms,'value');
    $text = get_parameter_value($parms,'text',$text);
    $encoding = get_parameter_value($parms,'encoding');

    $result = '<textarea';
    foreach( $attribs as $key => $val ) {
      if( $val != '' && $key != '' ) {
	$key = trim($key);
	$val = trim($val);
	$result .= " {$key}=\"{$val}\"";
      }
    }
    if( !empty( $addtext ) ) $result .= ' '.$addtext;
    $result .= '>'.cms_htmlentities($text,ENT_NOQUOTES,get_encoding($encoding)).'</textarea>';
    return $result;
  }

} // end of class

?>