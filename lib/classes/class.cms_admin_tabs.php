<?php
/**
 * This file contains a class to aide in creating a tabbed interface in the CMSMS admin console
 * @package CMS
 * @license GPL
 */

/**
 * A simple convenience class for creating a tabbed interface in the CMSMS admin console
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell
 */
final class cms_admin_tabs
{

  /**
   * @ignore
   */
  private function __construct() {}

  /**
   * @ignore
   */
  private static $_current_tab;

  /**
   * @ignore
   */
  private static $_start_headers_sent = null;

  /**
   * @ignore
   */
  private static $_end_headers_sent = null;

  /**
   * @ignore
   */
  private static $_start_content_sent = null;

  /**
   * @ignore
   */
  private static $_in_tab = null;

  /**
   * @ignore
   */
  private static $_tab_idx = 0;

  /**
   * Set the current active tab
   *
   * @param string $tab The param key
   */
  public static function set_current_tab($tab)
  {
    self::$_current_tab = $tab;
  }

  /**
   * Begin output of tab headers
   *
   * @return string
   */
  public static function start_tab_headers()
  {
    self::$_start_headers_sent = 1;
    return '<div id="page_tabs">';
  }

  /**
   * Create a tab header
   *
   * @param string $tabid The tab key
   * @param string $title The title to display in the tab
   * @param bool   $active Wether the tab is active or not.  If the current active tag matches the $tabid then the tab will be marked as active.
   * @return string
   */
  public static function set_tab_header($tabid,$title,$active = FALSE)
  {
    if( $active == FALSE ) {
      if( (self::$_tab_idx == 0 && self::$_current_tab == '') || $tabid == self::$_current_tab ) $active = TRUE;
      self::$_tab_idx++;
    }

    $a="";
    if (TRUE == $active) {
      $a=" class='active'";
      self::$_current_tab = $tabid;
    }
    $tabid = strtolower(str_replace(' ','_',$tabid));

    $out = '';
    if( !self::$_start_headers_sent ) $out .= self::start_tab_headers();
    $out .= '<div id="'.$tabid.'"'.$a.'>'.$title.'</div>';
    return $out;
  }

  /**
   * Finish outputting tab headers
   *
   * @return string
   */
  public static function end_tab_headers()
  {
    self::$_end_headers_sent = 1;
    return "</div><!-- EndTabHeaders -->";
  }

  /**
   * Start the content portion of the tabbed layout
   *
   * @return string
   */
  public static function start_tab_content()
  {
    $out = '';
    if( !self::$_end_headers_sent ) $out .= self::end_tab_headers();
    $out .= '<div class="clearb"></div><div id="page_content">';
    self::$_start_content_sent = 1;
    return $out;
  }

  /**
   * Finish the content portion of the tabbed layout
   *
   * @return string
   */
  public static function end_tab_content()
  {
    $out = '';
    if( self::$_in_tab ) $out .= self::end_tab();
    $out .= '</div> <!-- EndTabContent -->';
    return $out;
  }

  /**
   * Start the content portion of a specific tab
   *
   * @param string $tabid The tab key
   * @param array  $params Array of parameters for the tab
   * @return string
   */
  public static function start_tab($tabid,$params = array())
  {
    $message = '';
    if( $tabid == self::$_current_tab && !empty($params['tab_message']) ) {
      $theme = cms_utils::get_theme_object();
      if( is_object($theme) ) $message = $theme->ShowMessage($params['tab_message']);
    }

    $out = '';
    if( !self::$_start_content_sent ) $out .= self::start_tab_content();
    if( self::$_in_tab ) $out .= self::end_tab();
    self::$_in_tab = 1;
    $out .= '<div id="' . strtolower(str_replace(' ', '_', $tabid)) . '_c">'.$message;
    return $out;
  }

  /**
   * End the content portion of a single tab
   *
   * @return string
   */
  public static function end_tab()
  {
    if( !self::$_in_tab ) return;
    self::$_in_tab = null;
    return '</div> <!-- EndTab -->';
  }
} // end of class

?>