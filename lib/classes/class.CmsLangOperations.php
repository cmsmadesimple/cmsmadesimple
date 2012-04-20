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
 * @author Robert Campbell
 * @since 1.11
 */
final class CmsLangOperations
{
  private static $_langdata;
  private static $_engdata;
  private static $_do_conversions;
  private static $_allow_nonadmin_lang;
  private function __construc() {}

  private static function _load_realm($realm)
  {
    if( !$realm ) $realm = 'admin';
    if( is_array(self::$_langdata) && isset(self::$_langdata[$realm]) ) return;
    if( !is_array(self::$_langdata) ) self::$_langdata = array();

    $config = cmsms()->GetConfig();
    $curlang = CmsNlsOperations::get_current_language();

    // load the english file first.
    $dir = '';
    $has_realm = 0;
    $filename = 'en_US.php';
    if( $realm == 'admin' )
      {
	$dir = cms_join_path($config['root_path'],$config['admin_dir'],'lang');
	$filename = 'admin.inc.php';
	$fn = cms_join_path($dir,'en_US',$filename);
	$has_realm = 1;
      }
    else
      {
	$dir = cms_join_path($config['root_path'],'lib','lang',$realm);
	$fn = cms_join_path($dir,$filename);
      }
    if( !file_exists($fn) ) return FALSE;

    if( $has_realm )
      {
	$lang = array();
	include($fn);
	if( isset($lang[$realm]) )
	  {
	    self::$_langdata[$realm] = $lang[$realm];
	  }
	unset($lang);
      }
    else
      {
	$lang = array();
	include($fn);
	self::$_langdata[$realm] = $lang;
	unset($lang);
      }

    // backup the english data... in case we need to get it later.
    if( !is_array(self::$_engdata) ) self::$_engdata = array();
    self::$_engdata[$realm] = self::$_langdata[$realm];

    if( $curlang != 'en_US' )
      {
	// load the lang file itself.
	if( $realm == 'admin' )
	  {
	    $dir = cms_join_path($dir,'ext',$curlang);
	  }
	else
	  {
	    $dir = cms_join_path($dir,'ext');
	  }
	$fn = cms_join_path($dir,$filename);
	if( file_exists($fn) )
	  {
	    if( $has_realm )
	      {
		$lang = array();
		include($fn);
		if( isset($lang[$realm]) )
		  {
		    self::$_langdata[$realm] = array_merge(self::$_langdata[$realm],$lang[$realm]);
		  }
		unset($lang);
	      }
	    else
	      {
		$lang = array();
		include($fn);
		if( isset($lang[$realm]) )
		  {
		    self::$_langdata[$realm] = array_merge(self::$_langdata[$realm],$lang[$realm]);
		  }
		unset($lang);
	      }	
	  } // file exists.
      } // not english
	
    if( $realm  != 'admin' ) return TRUE;

    // load custom admin realm.
    $dir = cms_join_path($config['root_path'],$config['admin_dir'],'custom',$curlang);
    $fn = cms_join_path($dir,$filename);
    if( !file_exists($fn) ) return TRUE;
	
    $lang = array();
    include($fn);
    if( isset($lang[$realm]) )
      {
	self::$_langdata[$realm] = array_merge(self::$_langdata[$realm],$lang[$realm]);
      }
    unset($lang);

    return TRUE;
  }

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

  public static function lang_from_realm()
  {
    global $CMS_ADMIN_PAGE;
    global $CMS_STYLESHEET;
    global $CMS_INSTALL_PAGE;
    
    if (!isset($CMS_ADMIN_PAGE) && !isset($CMS_STYLESHEET) && !isset($CMS_INSTALL_PAGE) && !self::$_allow_nonadmin_lang )
      {
	trigger_error('Attempt to load admin realm from non admin action');
	return '';
      }
  
    $args = func_get_args();
    if( count($args) == 1 && is_array($args[0]) )
      {
	$args = $args[0];
      }
    if( count($args) < 2 ) return;

    $realm  = $args[0];
    $key    = $args[1];
    if( !$realm || !$key ) return;

    $params = array();
    if( count($args) > 2 )
      {
	$params = array_slice($args,2);
      }

    self::_load_realm($realm);
    if( !isset(self::$_langdata[$realm][$key]) ) return "-- Add me: $key --";

    if( count($params) )
      {
	$result = vsprintf(self::$_langdata[$realm][$key], $params);
      }
    else
      {
	$result = self::$_langdata[$realm][$key];
      }

    // conversion?
    return self::_convert_encoding($result);
  }


  public static function lang_from_realm_en()
  {
    global $CMS_ADMIN_PAGE;
    global $CMS_STYLESHEET;
    global $CMS_INSTALL_PAGE;
    
    if (!isset($CMS_ADMIN_PAGE) && !isset($CMS_STYLESHEET) && !isset($CMS_INSTALL_PAGE) && !self::$_allow_nonadmin_lang )
      {
	trigger_error('Attempt to load admin realm from non admin action');
	return '';
      }
  
    $args = func_get_args();
    if( count($args) == 1 && is_array($args[0]) )
      {
	$args = $args[0];
      }
    if( count($args) < 2 ) return;

    $realm  = $args[0];
    $key    = $args[1];
    if( !$realm || !$key ) return;

    $params = array();
    if( count($args) > 2 )
      {
	$params = array_slice($args,2);
      }

    self::_load_realm($realm);
    if( !isset(self::$_engdata[$realm][$key]) ) return '-- Add me: $key --';

    if( count($params) )
      {
	$result = vsprintf(self::$_engdata[$realm][$name], $params);
      }
    else
      {
	$result = self::$_engdata[$realm][$name];
      }

    // conversion?
    return self::_convert_encoding($result);
  }


  public static function lang()
  {
    $args = func_get_args();
    if( count($args) == 1 && is_array($args[0]) )
      {
	$args = $args[0];
      }
    array_unshift($args,'admin');
    return self::lang_from_realm($args);
  }


  public static function lang_en()
  {
    $args = func_get_args();
    if( count($args) == 1 && is_array($args[0]) )
      {
	$args = $args[0];
      }
    array_unshift($args,'admin');
    return self::lang_from_realm_en($args);
  }


  /**
   * Allow nonadmin requests to call lang functions.
   * normally, an error would be generated if calling core lang functions from an frontend action.
   * this method will disable or enable that check.
   *
   * @internal
   */
  public static function allow_nonadmin_lang($flag = TRUE)
  {
    self::$_allow_nonadmin_lang = $flag;
  }
} // end of class

#
# EOF
#
?>