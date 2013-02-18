<?php

class CmsFormUtils 
{
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

} // end of class

?>