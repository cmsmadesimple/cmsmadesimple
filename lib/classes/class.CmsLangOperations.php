<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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
#$Id: translation.functions.php 7907 2012-04-17 00:15:43Z calguy1000 $

/**
 * Translation functions/classes
 *
 * @package CMS
 */

/**
 * A singleton class to provide simple, generic mechanism for dealing with languages
 * encodings, and locales.  This class does not handle translation strings.
 *
 * @package CMS
 * @author Robert Campbell
 * @since 1.11
 */
final class CmsLangOperations
{

	/**
	 * @ignore
	 */
	private static $_langdata;


	/**
	 * @ignore
	 */
	private static $_do_conversions;

	/**
	 * @ignore
	 */
	private static $_allow_nonadmin_lang;

	/**
	 * A constant for the core admin realm.
	 */
	const CMSMS_ADMIN_REALM = 'admin';

	/**
	 * @ignore
	 */
	private static $_current_realm = self::CMSMS_ADMIN_REALM;

	/**
	 * @ignore
	 */
	private function __construct() {}

	/**
	 * @ignore
	 */
	private static function _load_realm($realm)
	{
	  $curlang = CmsNlsOperations::get_current_language();
	  if( !$realm ) $realm = self::$_curent_realm;

	  if( is_array(self::$_langdata) && isset(self::$_langdata[$curlang][$realm]) ) return;
	  if( !is_array(self::$_langdata) ) self::$_langdata = array();
	  if( !isset(self::$_langdata[$curlang]) ) self::$_langdata[$curlang] = array();
	  $config = cmsms()->GetConfig();

	  // load the english file first.
	  $files = array();
	  $is_module = false;
	  $filename = 'en_US.php';
	  if( $realm == self::CMSMS_ADMIN_REALM ) {
		  $files[] = cms_join_path($config['root_path'],$config['admin_dir'],'lang','en_US.php');
	  }
	  else {
		  if( is_dir(cms_join_path($config['root_path'],'modules',$realm)) ) {
			  $is_module = true;
			  $files[] = cms_join_path($config['root_path'],'modules',$realm,'lang','en_US.php');
		  }
		  $files[] = cms_join_path($config['root_path'],'lib','lang',$realm,'en_US.php');
	  }

	  // now handle other lang files.
	  if( $curlang != 'en_US' ) {
		  if( $realm == self::CMSMS_ADMIN_REALM ) {
			  $files[] = cms_join_path($config['root_path'],$config['admin_dir'],'lang','ext',$curlang.'.php');
		  }
		  else {
			  if( $is_module ) {
				  $files[] = cms_join_path($config['root_path'],'modules',$realm,'lang','ext',$curlang.'.php');
			  }
			  else {
				  $files[] = cms_join_path($config['root_path'],'lib','lang',$realm,'ext',$curlang.'.php');
			  }
		  }
	  }

	  // now load the custom stuff.
	  if( $realm == self::CMSMS_ADMIN_REALM ) {
		  $files[] = cms_join_path($config['root_path'],$config['admin_dir'],'custom','lang',$curlang.'.php');
	  }
	  else {
		  if( $is_module ) {
			  $files[] = cms_join_path($config['root_path'],'module_custom',$realm,'lang',$curlang.'.php');
			  $files[] = cms_join_path($config['root_path'],'module_custom',$realm,'lang','ext',$curlang.'.php');
		  }
	  }

	  foreach( $files as $fn ) {
		  if( !file_exists($fn) ) continue;

		  $lang = array();
		  include($fn);
		  if( !isset(self::$_langdata[$curlang][$realm]) ) self::$_langdata[$curlang][$realm] = array();
		  self::$_langdata[$curlang][$realm] = array_merge(self::$_langdata[$curlang][$realm],$lang);
		  unset($lang);
	  }
	}

	/**
	 * @ignore
	 */
	private static function _convert_encoding($str)
	{
    /*
    if( is_null(self::$_do_conversions) )
      {
	$config = cmsms()->GetConfig();
	self::$_do_conversions = 0;
	if( isset($config['admin_encoding']) && strcasecmp(CmsNlsOperations::get_encoding(),$config['admin_encoding']) )
	  {
	    if( function_exists('mb_convert_encoding') )
	      {
		self::$_do_conversions = 1;
	      }
	  }
      }
    if( self::$_do_conversions )
      {
	$result = mb_convert_encoding($result,CmsNlsOperations::get_encoding(),$config['admin_encoding']);
      }
    */
    return $str;
  }

  /**
   * Given a realm name, a key, and optional parameters return a translated string
   * This function accepts variable arguments.  If no key/realm combination can be found
   * then an -- Add-Me string will be returned indicating that this key needs translating.
   * This function uses the currently set language, and will load the translations from disk
   * if necessary.
   *
   * @param string The realm name (required)
   * @param string The language string key (required)
   * @param mixed  Further arguments to this function are passed to vsprintf
   * @return string
   */
  public static function lang_from_realm()
  {
	  $args = func_get_args();
	  if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
	  if( count($args) < 2 ) return;

	  $realm  = $args[0];
	  $key    = $args[1];
	  if( !$realm || !$key ) return;

	  global $CMS_ADMIN_PAGE;
	  global $CMS_STYLESHEET;
	  global $CMS_INSTALL_PAGE;
	  if (self::CMSMS_ADMIN_REALM == $realm && !isset($CMS_ADMIN_PAGE) &&
		  !isset($CMS_STYLESHEET) && !isset($CMS_INSTALL_PAGE) &&
		  !self::$_allow_nonadmin_lang ) {
		  trigger_error('Attempt to load admin realm from non admin action');
		  return '';
	  }

	  $params = array();
	  if( count($args) > 2 ) $params = array_slice($args,2);
	  if( count($params) == 1 && is_array($params[0]) ) $params = $params[0];

	  $curlang = CmsNlsOperations::get_current_language();
	  self::_load_realm($realm);
	  if( !isset(self::$_langdata[$curlang][$realm][$key]) ) {
		  // put mention into the admin log
		  audit('', 'Languagestring: "' . $key . '"', 'Is missing in the languagefile: '.  $realm);
		  return "-- Missing Languagestring: $key --";
	  }

	  if( count($params) ) {
		  $result = vsprintf(self::$_langdata[$curlang][$realm][$key], $params);
	  }
	  else {
		  $result = self::$_langdata[$curlang][$realm][$key];
	  }

	  // conversion?
	  return self::_convert_encoding($result);
  }

  /**
   * A simple around the lang_from_realm method that assumes the self::CMSMS_ADMIN_REALM realm.
   * Note, under normal circumstances this will generate an error if called from a frontend action.
   * This function accepts variable arguments.
   *
   * @see lang_from_realm
   * @param string Key (required) the language string key
   * @param mixed  Optional further arguments.
   * @return string
   */
  public static function lang()
  {
	  $args = func_get_args();
	  if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];

	  array_unshift($args,self::$_current_realm);
	  return self::lang_from_realm($args);
  }


  /**
   * Allow nonadmin requests to call lang functions.
   * normally, an error would be generated if calling core lang functions from an frontend action.
   * this method will disable or enable that check.
   *
   * @internal
   * @param bool flag
   */
  public static function allow_nonadmin_lang($flag = TRUE)
  {
	  self::$_allow_nonadmin_lang = $flag;
  }


  /**
   * Test to see if a language key exists in the current lang file.
   * This function uses the current language.
   *
   * @param string $key The language key
   * @param string $realm The language realm
   * @return bool
   */
  public static function key_exists($key,$realm = null)
  {
	  if( $realm == null ) $realm = self::$_current_realm;
	  self::_load_realm($realm);
	  $curlang = CmsNlsOperations::get_current_language();
	  if( isset(self::$_langdata[$curlang][$realm][$key]) ) return TRUE;
	  return FALSE;
  }

  /**
   * Set the realm for further lang calls.
   *
   * @since 2.0
   * @author Robert Campbell
   * @param string $realm The realm name.  If no name specified, self::CMSMS_ADMIN_REALM is assumed'
   * @return string the old realm name.
   */
  public static function set_realm($realm = self::CMSMS_ADMIN_REALM)
  {
	  $old = self::$_current_realm;
	  if( $realm == '' ) $realm = self::CMSMS_ADMIN_REALM;
	  self::$_current_realm = $realm;
	  return $old;
  }
} // end of class

#
# EOF
#
?>