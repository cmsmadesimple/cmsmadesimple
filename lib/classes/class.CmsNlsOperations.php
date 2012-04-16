<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
#$Id: translation.functions.php 7867 2012-04-13 18:00:14Z calguy1000 $

/**
 * Translation functions/classes
 *
 * @package CMS
 */

/**
 * A singleton class to provide simple, generic mechanism for dealing with languages
 * encodings, and locales.  This class does not handle translation strings.
 *
 * @author Robert Campbell
 * @since 1.11
 */
final class CmsNlsOperations
{
  private function __construct() {}
  private static $_nls;
  private static $_cur_lang; // each request is either for the admin or for the frontend.
  private static $_encoding;
  private static $_locale;
  private static $_fe_language_detector;
  
  private static function _load_nls()
  {
    if( !is_array(self::$_nls) )
      {
	self::$_nls = array();
	$config = cmsms()->GetConfig();
	$dir = cms_join_path($config['root_path'],$config['admin_dir'],'lang');
	$files = glob($dir.'/*nls*php');
	if( is_array($files) && count($files) )
	  {
	    for( $i = 0; $i < count($files); $i++ )
	      {
		if( !is_file($files[$i]) ) continue;
		$fn = basename($files[$i]);
		$tlang = substr($fn,0,strpos($fn,'.'));
		if( $tlang != 'en_US' && !file_exists(cms_join_path($dir,'ext',$tlang,'admin.inc.php')) )
		  {
		    continue;
		  }

		unset($nls);
		include($files[$i]);
		if( isset($nls) )
		  {
		    $obj = CmsNls::from_array($nls);
		    unset($nls);
		    self::$_nls[$obj->key()] = $obj;
		  }
	      }
	  }
      }
  }

  /**
   * Get an array of all languages that are known
   * (installed).  Udes the NLS files to handle this 
   *
   * @return array Array of language names
   */
  public static function get_installed_languages()
  {
    self::_load_nls();
    if( is_array(self::$_nls) )
      {
	return array_keys(self::$_nls);
      }
  }

  /**
   * Get language info about a particular language.
   *
   * @param string language name.
   * @return CmsNls object repesenting the named language.  or null.
   */
  public static function get_language_info($lang)
  {
    self::_load_nls();
    if( isset(self::$_nls[$lang]) )
      {
	return self::$_nls[$lang];
      }
  }

  /**
   * Get the current language.
   * If not explicitly set this method will try to detect the current language.
   * different detection mechanisms are used for admin requests vs. frontend requests.
   * if no match could be found in any way, en_US is returned.
   *
   * @return string Language name.
   */
  public static function get_current_language()
  {
    global $CMS_ADMIN_PAGE;
    global $CMS_STYLESHEET;
    global $CMS_INSTALL_PAGE;
    
    if( isset(self::$_cur_lang) ) return self::$_cur_lang;

    self::_load_nls();
    if (isset($CMS_ADMIN_PAGE) || isset($CMS_STYLESHEET) || isset($CMS_INSTALL_PAGE))
      {
	return self::get_admin_language();
      }
    return self::get_frontend_language();
  }

  /**
   * Use detection mechanisms to find a suitable frontend language.
   * the language returned must be available as specified by the
   * available NLS Files.
   *
   * @return string language name.
   */
  protected static function get_frontend_language()
  {
    if( is_object(self::$_fe_language_detector) )
      {
	return self::$_fe_language_detector->find_language();
      }
    return get_site_preference('frontendlang','en_US');
  }

  /**
   * Set a current language.
   * The language specified must be an empty string, which will assume detection
   * or must be an available language as identified in the NLS FIles. If no suitable
   * value is detected, this function will return en_US.
   *
   * @param string The desired language.
   * @return boolean
   */
  public static function set_language($lang)
  {
    if( empty($lang) )
      {
	self::$_cur_lang = null;
	return TRUE;
      }

    self::_load_nls();
    if( isset(self::$_nls[$lang]) )
      {
	self::$_cur_fe_lang = $lang;
	return TRUE;
      }
    return FALSE;
  }


  /**
   * Use detection mechanisms to find a suitable language for an admin request.
   * The language returned must be an available language as specified by the
   * available NLS Files.  If no suitable language can be detected this function
   * will return en_US
   *
   * @return string The language name
   */
  protected static function get_admin_language()
  {
    $uid = get_userid(false);
    $lang = '';
    if( $uid )
      {
	$lang = get_preference($uid,'default_cms_language');
	if( $lang )
	  {
	    self::_load_nls();
	    if( !isset(self::$_nls[$lang]) ) $lang = '';
	  }

	if( !$lang )
	  {
	    $lang = self::detect_browser_language();
	  }

	if( !$lang ) $lang = 'en_US';
      }

    if( isset($_POST['change_cms_lang']) && isset($_POST['default_cms_lang']) )
      {
	// a hack to handle the editpref case of the user changing his language
	// this is needed because the lang stuff is included before the preference may
	// actually be set.
	self::_load_nls();
	$a1 = basename(trim($_POST['change_cms_lang']));
	$a2 = basename(trim($_POST['default_cms_lang']));
	if( $a1 == $a2 && isset(self::$_nls[$a1]) )
	  {
	    $lang == $a1;
	  }
      }

    if( $lang == '' ) $lang = 'en_US';
    return $lang;
  }

  /**
   * Cross reference the browser preferred language with those
   * that are available (via NLS Files).  To find the first
   * suitable language.
   *
   * @return string First suitable lang string. or nothing.
   */
  public static function detect_browser_language()
  {
    $langs = self::get_browser_languages();
    if( !is_array($langs) || !count($langs) ) return;

    self::_load_nls();
    foreach( $langs as $onelang )
      {
	if( isset(self::$_nls[$onelang]) ) return $onelang;

	foreach( self::$_nls as $key => $obj )
	  {
	    if( $obj->matches($onelang) ) return $obj->name();
	  }
      }
  }

  /**
   * Return the list of languages understood by the current browser (if any).
   *
   * @return array of Strings representing the languages the browser supports.
   */
  public static function get_browser_languages()
  {
    if( !isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) return;

    preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
    if (count($lang_parse[1])) 
      {
        // create a list like "en" => 0.8
        $langs = array_combine($lang_parse[1], $lang_parse[4]);
    	
        // set default to 1 for any without q factor
        foreach ($langs as $lang => $val) {
            if ($val === '') $langs[$lang] = 1;
        }

        // sort list based on value	
        arsort($langs, SORT_NUMERIC);
      }

    return $langs;
  }

  /**
   * Return the currently active encoding.
   * If an encoding has not been explicitly set, the default_encoding value from the config file will be used
   * If that value is empty, the encoding associated with the current language will be used.
   * If no suitable encoding can be found, UTF-8 will be assumed.
   *
   * @return string.
   */
  public static function get_encoding()
  {
    if( self::$_encoding ) return self::$_encoding;

    $config = cmsms()->GetConfig();
    if( isset($config['default_encoding']) && $config['default_encoding'] != '' )
      return $config['default_encoding'];

    $lang = self::get_current_language();
    if( !$lang ) return 'UTF-8'; // no language.. weird.

    return self::$_nls[$lang]->encoding();
  }

  /**
   * Set the current encoding
   *
   * @return void
   */
  public static function set_encoding($str)
  {
    if( !$str ) 
      {
	self::$_encoding = null;
	return;
      }
    self::$_encoding = $str;
  }

  /**
   * Return the currently active locale
   * If an locale has not been explicitly set, the locale value from the config file will be used
   * If that value is empty, the locale associated with the current language will be used.
   * If no suitable locale can be found an empty string is returned.
   *
   * @return mixed  Either an array, or a string representing the possible locale values, or an empty string.
   */
  public static function get_locale()
  {
    if( self::$_locale ) return self::$_locale;

    $config = cmsms()->GetConfig();
    if( isset($config['locale']) && $config['locale'] != '' ) 
      {
	$val = $config['locale'];
	if( is_string($val) ) 
	  $val = trim($val);
	if( strpos($val,',') !== FALSE )
	  {
	    $val = explode(',',$val);
	    for( $i = 0; $i < count($val); $i++ )
	      {
		$val[$i] = trim($val[$i]);
	      }
	  }
	return $val;
      }

    $lang = self::get_current_language();
    if( !$lang ) return ''; // no language... weird.

    return self::$_nls[$lang]->locale();
  }

  /**
   * Set the current locale.
   * Note this does not call the setlocale php function.  It only sets a value
   * that can be used by the setlocale php function.
   * CMSMS calls setlocale with the information from this class directly after loading modules.
   *
   * @return void
   */
  public static function set_locale($str)
  {
    if( !$str ) 
      {
	self::$_locale = null;
	return;
      }
    self::$_locale = $str;
  }

  public static function set_language_detector(CmsLanguageDetector& $obj)
  {
    if( is_object(self::$_fe_language_detector) )
      {
	die('language detector already set');
      }
    self::$_fe_language_detector($obj);
  }

} // end of class
#
# EOF
#
?>