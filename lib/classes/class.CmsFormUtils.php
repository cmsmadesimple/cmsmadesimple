<?php

class CmsFormUtils {
  private function __construct() {}

  private static function create_option($key,$value,$selected = '') 
  {
    $out = '';
    if( is_array($value) ) {
      $out .= "<optgroup value=\"$value\">\n";
      foreach( $value as $key => $value ) {
	$out .= self::create_option($key,$value);
      }
      $out .= "</optgroup>\n";
    }
    else {
      if( $selected == $key ) {
	$out .= "<option value=\"$key\" selected=\"selected\">$value</option>\n";
      }
      else {
	$out .= "<option value=\"$key\">$value</option>\n";
      }
    }
    return $out;
  }

  public static function create_dropdown($name,$list_options,$selected,$params = array())
  {
    if( $name == '' ) return;
    if( !is_array($list_options) || count($list_options) == 0 ) return;
    
    $options = '';
    foreach( $list_options as $key => $value ) {
      $options .= self::create_option($key,$value,$selected);
    }

    $elem_id = $name;
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

} // end of class

?>