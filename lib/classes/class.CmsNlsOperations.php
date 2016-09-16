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
#$Id: translation.functions.php 7867 2012-04-13 18:00:14Z calguy1000 $

/**
 * Translation functions/classes
 *
 * @package CMS
 * @license GPL
 */

/**
 * A singleton class to provide simple, generic mechanism for dealing with languages
 * encodings, and locales.  This class does not handle translation strings.
 *
 * @author Robert Campbell
 * @since 1.11
 * @package CMS
 * @license GPL
 */
final class CmsNlsOperations
{
    /**
     * @ignore
     */
    private function __construct() {}

    /**
     * @ignore
     */
    private static $_nls;

    /**
     * @ignore
     */
    private static $_cur_lang; // each request is either for the admin or for the frontend.

    /**
     * @ignore
     */
    private static $_encoding;

    /**
     * @ignore
     */
    private static $_locale;

    /**
     * @ignore
     */
    private static $_fe_language_detector;

    /**
     * @ignore
     */
    private static $_stored_dflt_language;

    /**
     * @ignore
     */
    private static function _load_nls()
    {
        if( !is_array(self::$_nls) ) {
            self::$_nls = array();
            $config = \cms_config::get_instance();
            $nlsdir = cms_join_path(CMS_ROOT_PATH,'lib','nls');
            $langdir = cms_join_path(CMS_ROOT_PATH,$config['admin_dir'],'lang');
            $files = glob($nlsdir.'/*nls.php');
            if( is_array($files) && count($files) ) {
                for( $i = 0, $n = count($files); $i < $n; $i++ ) {
                    if( !is_file($files[$i]) ) continue;
                    $fn = basename($files[$i]);
                    $tlang = substr($fn,0,strpos($fn,'.'));
                    if( $tlang != 'en_US' && !is_file(cms_join_path($langdir,'ext',$tlang.'.php')) ) continue;

                    unset($nls);
                    include($files[$i]);
                    if( isset($nls) ) {
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
        if( is_array(self::$_nls) ) return array_keys(self::$_nls);
    }

    /**
     * Get language info about a particular language.
     *
     * @param string $lang language name.
     * @return CmsNls object repesenting the named language.  or null.
     */
    public static function get_language_info($lang)
    {
        self::_load_nls();
        if( isset(self::$_nls[$lang]) ) return self::$_nls[$lang];
    }

  /**
   * Set a current language.
   * The language specified may be an empty string, which will assume that the system
   * should try to detect an appropriate language.  If no default can be found for
   * some reason, en_US will be assumed.
   *
   * When a language is found, the system will automatically set the locale for the request.
   *
   * Note: CMSMS 1.11 and above will not support multiple languages per request.
   * therefore, it should be assumed that this function can only be called once per request.
   *
   * @internal
   * @see set_locale
   * @param string The desired language.
   * @return bool
   */
  public static function set_language($lang = null)
  {
	  $curlang = '';
	  if( self::$_cur_lang != '') $curlang = self::$_cur_lang;
	  if( $lang == '' && \CmsApp::get_instance()->is_frontend_request() && is_object(self::$_fe_language_detector) ) $lang = self::$_fe_language_detector->find_language();
	  if( $lang != '' ) $lang = self::find_nls_match($lang); // resolve input string
	  if( $lang == '' ) $lang = self::get_default_language();
	  if( $curlang == $lang ) return TRUE; // nothing to do.

        self::_load_nls();
        if( isset(self::$_nls[$lang]) ) {
            // lang is okay... now we can set it.
            self::$_cur_lang = $lang;
            // and set the locale along with this language.
            self::set_locale();
            return TRUE;
        }
        return FALSE;
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
	  if( isset(self::$_cur_lang) ) return self::$_cur_lang;
	  if( is_object(self::$_fe_language_detector) && \CmsApp::get_instance()->is_frontend_request() ) return self::$_fe_language_detector->find_language();
	  return self::get_default_language();
  }

    /**
     * Get a default language.
     * This method will behave differently for admin or frontend requests.
     *
     * For admin requests first the preference is checked.  Secondly,
     * an attempt is made to find a language understood by the browser that is compatible
     * with what is avaialable.  If no match can be found, en_US is assumed.
     *
     * For frontend requests if a language detector has been set into this object it will
     * be called to attempt to find a language.  If that fails, then the frontend language preference
     * is used.  Thirdly, if no match is found en_US is assumed.
     *
     * @see set_language_detector
     * @return string
     */
    public static function get_default_language()
    {
        global $CMS_ADMIN_PAGE;
        global $CMS_STYLESHEET;
        global $CMS_INSTALL_PAGE;

        if( self::$_stored_dflt_language ) return self::$_stored_dflt_language;

        self::_load_nls();
        $lang = '';
        if (isset($CMS_ADMIN_PAGE) || isset($CMS_STYLESHEET) || isset($CMS_INSTALL_PAGE)) {
            $lang = self::get_admin_language();
        }
        else {
            $lang = self::get_frontend_language();
        }
        if( !$lang ) $lang = 'en_US';
        self::$_stored_dflt_language = $lang;
        return $lang;
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
	  $x = trim(get_site_preference('frontendlang'));
	  if( !$x ) $x = 'en_US';
	  return $x;
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
        global $CMS_LOGIN_PAGE;
        $uid = $lang = null;
        if( !isset($CMS_LOGIN_PAGE) ) {
            $uid = get_userid(false);
            if( $uid ) {
                $lang = cms_userprefs::get_for_user($uid,'default_cms_language');
                if( $lang ) {
                    self::_load_nls();
                    if( !isset(self::$_nls[$lang]) ) $lang = null;
                }
            }
        }

        if( !$lang ) $lang = self::detect_browser_language();

        if( $uid && isset($_POST['default_cms_language']) ) {
            // a hack to handle the editpref case of the user changing his language
            // this is needed because the lang stuff is included before the preference may
            // actually be set.
            self::_load_nls();
            $a2 = basename(trim($_POST['default_cms_language']));
            if( $a2 && isset(self::$_nls[$a2]) ) $lang = $a2;
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
        foreach( $langs as $onelang => $weight ) {
            if( isset(self::$_nls[$onelang]) ) return $onelang;

            foreach( self::$_nls as $key => $obj ) {
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

        $in = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $in, $lang_parse);

        if (count($lang_parse[1])) {
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
        // has it been explicity set somewhere?
        if( self::$_encoding ) return self::$_encoding;

        // is it specified in the config.php?
        $config = \cms_config::get_instance();
        if( isset($config['default_encoding']) && $config['default_encoding'] != '' ) return $config['default_encoding'];

        $lang = self::get_current_language();
        if( !$lang ) return 'UTF-8'; // no language.. weird.

        // get it from the nls stuff.
        return self::$_nls[$lang]->encoding();
    }

    /**
     * Set the current encoding
     *
     * @param mixed $str The encoding (comma separated encodings is acceptable.
     */
    public static function set_encoding($str)
    {
        if( !$str ) {
            self::$_encoding = null;
            return;
        }
        self::$_encoding = $str;
    }

    /**
     * Set the locale for the current language.
     * if language has not been set... does nothing.
     * will use the locale from the nls information for the current locale
     * if config entry is set... it will be used, but subsequent calls to this
     * method will do nothing.
     */
    protected static function set_locale()
    {
        $config = \cms_config::get_instance();
        static $_locale_set;

        $locale = '';
        if( isset($config['locale']) && $config['locale'] != '' ) {
            if( $_locale_set ) return;

            $locale = $config['locale'];
        }
        else {
            if( self::$_cur_lang == '' ) return;

            self::_load_nls();
            $locale = self::$_nls[self::$_cur_lang]->locale();
        }

        if( $locale ) {
            if( !is_array($locale) ) $locale = explode(',',$locale);
            $res = setlocale(LC_ALL,$locale);
            $_locale_set = 1;
        }
    }

    /**
     * Used to allow third party modules to override the language detection mechanism for frontend requests.
     * The module can provide an object derived from CmsLanguageDetector to this method.
     *
     * i.e:  CmsNlsOperations::set_language_detector(myLanguageDetector)
     *
     * Note, this module must return a language for which there is an available NLS file.
     *
     * @param CmsLanguageDetector Object containing methods to detect a compatible, desired language
     */
    public static function set_language_detector(CmsLanguageDetector& $obj)
    {
        if( is_object(self::$_fe_language_detector) ) die('language detector already set');
        self::$_fe_language_detector = $obj;
    }


    /**
     * Find a match for a specific language
     * This method will try to find the NLS information closest to the language specified.
     *
     * @param string $str An approximate language specification (an alias matchis done if possible).
     * @return hash containing NLS information.
     */
    protected static function find_nls_match($str)
    {
        self::_load_nls();
        foreach( self::$_nls as $key => $obj ) {
            if( $obj->matches($str) ) return $obj->name();
        }
    }
} // end of class

#
# EOF
#
?>
