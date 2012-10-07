<?php

class cms_admin_tabs 
{
  private function __construct() {}
  private static $_current_tab;
  private static $_start_headers_sent = null;
  private static $_end_headers_sent = null;
  private static $_start_content_sent = null;
  private static $_in_tab = null;
  private static $_tab_idx = 0;

  public static function set_current_tab($tab)
  {
    self::$_current_tab = $tab;
  }

  public static function start_tab_headers()
  {
    self::$_start_headers_sent = 1;
    return '<div id="page_tabs">';
  }

  public function set_tab_header($tabid,$title,$active = FALSE)
  {
    if( $active == FALSE ) {
      if( (self::$_tab_idx == 0 && self::$_current_tab == '') 
	  || $tabid == self::$_current_tab ) {
	$active = TRUE;
      }
      self::$_tab_idx++;
    }
	
    $a="";
    if (TRUE == $active) {
      $a=" class='active'";
      self::$_current_tab = $tabid;
    }
    $tabid = strtolower(str_replace(' ','_',$tabid));

    $out = '';
    if( !self::$_start_headers_sent ) {
      $out .= self::start_tab_headers();
    }
    $out .= '<div id="'.$tabid.'"'.$a.'>'.$title.'</div>';
    return $out;
  }

  public static function end_tab_headers()
  {
    return "</div><!-- EndTabHeaders -->";
    self::$_end_headers_sent = 1;
  }

  public static function start_tab_content()
  {
    $out = '';
    if( !self::$_end_headers_sent ) {
      $out .= self::end_tab_headers();
    }
    $out .= '<div class="clearb"></div><div id="page_content">';
    self::$_start_content_sent = 1;
    return $out;
  }

  public static function end_tab_content()
  {
    $out = '';
    if( self::$_in_tab ) {
      $out .= self::end_tab();
    }
    $out .= '</div> <!-- EndTabContent -->';
    return $out;
  }

  public static function start_tab($tabid,$params = array())
  {
    $message = '';
    if( $tabid == self::$_current_tab && !empty($params['tab_message']) ) {
      $theme = cms_utils::get_theme_object();
      if( is_object($theme) ) {
	$message = $theme->ShowMessage($this->Lang($params['tab_message']));
      }
    }
    
    $out = '';
    if( !self::$_start_content_sent ) {
      $out .= self::start_tab_content();
    }
    if( self::$_in_tab ) {
      $out .= self::end_tab();
    }
    self::$_in_tab = 1;
    $out .= '<div id="' . strtolower(str_replace(' ', '_', $tabid)) . '_c">'.$message;
    return $out;
  }

  public static function end_tab()
  {
    if( !self::$_in_tab ) return;
    self::$_in_tab = null;
    return '</div> <!-- EndTab -->';
  }
} // end of class

?>