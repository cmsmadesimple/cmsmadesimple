<?php

final class CmsFormUtils 
{
  private static $_activated_wysiwyg;
  private static $_activated_syntax;

  private function __construct() {}

  public static function create_option($key,$value,$selected = '',$title = '') 
  {
    $out = '';
    if( is_array($value) ) {
      if( isset($value['key']) && isset($value['value']) ) {
	// hash with key, value, and optional title.
	$title2 = '';
	if( isset($value['title']) ) $title2 = $value['title'];
	return self::create_option($value['key'],$value['value'],$selected,$title2);
      }
      else {
	// array of options
	$out .= "<optgroup value=\"$value\">\n";
	foreach( $value as $key => $value ) {
	  $out .= self::create_option($key,$value,$selected);
	}
      }
      $out .= "</optgroup>\n";
    }
    else {
      $addtext = '';
      if( $title != '' ) $addtext=" title=\"{$title}\"";
      if( (is_array($selected) && in_array($key,$selected)) || $selected == $key ) {
	$out .= "<option value=\"$key\" selected=\"selected\"{$addtext}>$value</option>\n";
      }
      else {
	$out .= "<option value=\"$key\"{$addtext}>$value</option>\n";
      }
    }
    return $out;
  }

  public static function create_options($options,$selected = '')
  {
    if( !is_array($options) || count($options) == 0 ) return;

    $out = '';
    foreach( $options as $key => $value ) {
      $out .= self::create_option($key,$value,$selected);
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
   *
   * @internal
   * @access private
   * @param boolean Wether or not we are enabling a wysiwyg.  If false, and forcewysiwyg is not empty then a syntax area is used.
   * @param string  The contents of the text area
   * @param string  The name of the text area
   * @param string  An optional class name
   * @param string  An optional ID (HTML ID) value
   * @param string  The optional encoding
   * @param string  Optional style information
   * @param integer Width (the number of columns) (CSS can and will override this)
   * @param integer Hieght (the number of rows) (CSS can and will override this)
   * @param string  Optional name of the syntax hilighter or wysiwyg to use.  If empty, preferences indicate which a syntax editor or wysiwyg should be used.
   * @param string  Optional name of the language used.  If non empty it indicates that a syntax highlihter will be used.
   * @param string  Optional additional text to include in the textarea tag
   * @return string
   * @deprecated
   */
//   public static function create_textarea($enablewysiwyg, $text, $name, $classname = '', $id = '', 
// 			   $encoding = '', $stylesheet = '', $width = '80', $height = '15', 
// 			   $forcewysiwyg = '', $wantedsyntax = '', $addtext = '')
  public static function create_textarea($parms)
  {
    // todo: rewrite me with var args... to accept a numeric array of arguments, or a hash.
    $gCms = cmsms();
    $haveit = FALSE;
    $result = '';
    $uid = get_userid(false);
    $attribs = array();
    $attribs['name'] = get_parameter_value($parms,'name');
    if( !$attribs['name'] ) throw new CmsInvalidDataException('"name" is a required parameter"');
    $attribs['id'] = get_parameter_value($parms,'id',$attribs['name']);
    $attribs['class'] = get_parameter_value($parms,'class','cms_textarea');
    $attribs['class'] = get_parameter_value($parms,'classname',$attribs['class']);

    $enablewysiwyg = cms_to_bool(get_parameter_value($parms,'enablewysiwyg','false'));
    $forcewysiwyg = cms_to_bool(get_parameter_value($parms,'forcewysiwyg'));
    if ($enablewysiwyg == true || $forcewysiwyg) {
      $haveit = TRUE;
      $module = cms_utils::get_wysiwyg_module($forcewysiwyg);
      if( $module ) {
	self::_add_wysiwyg($module->GetName());
	$attribs['class'] .= " cms_wysiwyg ".$module->GetName();
      }
    }

    $wantedsyntax = get_parameter_value($parms,'wantedsyntax');
    if( !$haveit && $wantedsyntax ) {
      // here we should get a list of installed/available modules.
      $haveit = TRUE;
      $module = cmsms()->GetModuleOperations()->GetSyntaxHighlighter($wantedsyntax);
      if( $module ) {
	self::_add_syntax($module->GetName());
	$attribs['class'] .= " cms_syntaxarea ".$module->GetName();
      }
    }

    $attribs['cols'] = get_parameter_value($parms,'cols');
    $attribs['cols'] = get_parameter_value($parms,'width',$attribs['cols']);
    if( $attribs['cols'] < 0 ) $attribs['cols'] = '';
    $attribs['rows'] = get_parameter_value($parms,'rows');
    $attribs['rows'] = get_parameter_value($parms,'height',$attribs['rows']);
    if( $attribs['rows'] < 0 ) $attribs['rows'] = '';
    $attribs['maxlength'] = get_parameter_value($parms,'maxlength');
    if( $attribs['maxlength'] < 0 ) $attribs['maxlength'] = '';
    $attribs['placeholder'] = get_parameter_value($parms,'placeholder');
    $attribs['required'] = get_parameter_value($parms,'required');

    $addtext = get_parameter_value($parms,'addtext');
    $text = get_parameter_value($parms,'value');
    $text = get_parameter_value($parms,'text',$text);
    $encoding = get_parameter_value($parms,'encoding');

    $result = '<textarea';
    foreach( $attribs as $key => $val ) {
      if( $val != '' ) $result .= " {$key}=\"{$val}\"";
    }
    if( !empty( $addtext ) ) $result .= ' '.$addtext;
    $result .= '>'.cms_htmlentities($text,ENT_NOQUOTES,get_encoding($encoding)).'</textarea>';
    return $result;
  }

} // end of class

?>