<?php
#CMS - CMS Made Simple
#(c)2004-2011 by Ted Kulp (ted@cmsmadesimple.org)
#This projects homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: class.admintheme.inc.php 7596 2011-12-24 22:50:52Z calguy1000 $

/**
 * Classes and utilities for working with the CMSMS admin interface.
 *
 * @package CMS
 * @license GPL
 */

/**
 * A set of static utilities for assisting with admin requests
 *
 * @package CMS
 * @version $Revision$
 * @license GPL
 * @since   2.0
 * @author  Robert Campbell
 */
final class CmsAdminUtils
{
   /**
    * @ignore
    */
    private function __construct() {}

    /**
     * A regular expression to use when testing if an item has a valid name.
     */
    const ITEMNAME_REGEX = '<^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\ \/\+\-\,\.\x7f-\xff]*$>';

    /**
     * Test if a string is suitable for use as a name of an item in CMSMS.
     * For use by various modules and the core.
     * The name must begin with an alphanumeric character (but some extended characters are allowed).  And must be followed by the same alphanumeric characters
     * note the name is not necessarily guaranteed to be usable in smarty without backticks.
     *
     * @param string $str The string to test
     * @return bool|string FALSE on error or the validated string.
     */
    public static function is_valid_itemname($str)
    {
        if( !is_string($str) ) return FALSE;
        $t_str = trim($str);
        if( !$t_str ) return FALSE;
        if( !preg_match(self::ITEMNAME_REGEX,$t_str) ) return FALSE;
        return $str;
    }

    /**
     * Convert an admin request URL to a generic form that is suitable for saving to a database.
     * This is useful for things like bookmarks and homepages.
     *
     * @param string $in_url The input URL that has the session key in it.
     * @return string A URL that is converted to a generic form.
     */
    public static function get_generic_url($in_url)
    {
        if( !defined('CMS_USER_KEY') ) throw new \LogicException('This method can only be called for admin requests');
        if( !isset($_SESSION[CMS_USER_KEY]) || !$_SESSION[CMS_USER_KEY] ) throw new \LogicException('This method can only be called for admin requests');

        $len = strlen($_SESSION[CMS_USER_KEY]);
        $in_p = '+'.CMS_SECURE_PARAM_NAME.'\=[A-Za-z0-9]{'.$len.'}+';
        $out_p = '_CMSKEY_='.str_repeat('X',$len);
        $out = preg_replace($in_p,$out_p,$in_url);
        $config = \cms_config::get_instance();
        if( startswith($out,$config['root_url']) ) {
            $out = str_replace($config['root_url'],'',$out);
        }
        return $out;
    }

    /**
     * Convert a generic URL into something that is suitable for this users session.
     *
     * @param string $in_url The generic url.  usually retrieved from a preference or from the database
     * @return string A URL that has a session key in it.
     */
    public static function get_session_url($in_url)
    {
        if( !defined('CMS_USER_KEY') ) throw new \LogicException('This method can only be called for admin requests');
        IF( !isset($_SESSION[CMS_USER_KEY]) || !$_SESSION[CMS_USER_KEY] ) throw new \LogicException('This method can only be called for admin requests');

        $len = strlen($_SESSION[CMS_USER_KEY]);
        $in_p = '+_CMSKEY_=[X]{'.$len.'}+';
        $out_p = CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
        return preg_replace($in_p,$out_p,$in_url);
    }

    /**
     * Get the latest available CMSMS version.
     * This method does a remote request to the version check URL at most once per day.
     *
     * @return string
     */
    public static function fetch_latest_cmsms_ver()
    {
        $last_fetch = (int) cms_siteprefs::get('last_remotever_check');
        $remote_ver = cms_siteprefs::get('last_remotever');
        if( $last_fetch < (time() - 24 * 3600) ) {
            $req = new cms_http_request();
            $req->setTimeout(3);
            $req->execute(CMS_DEFAULT_VERSIONCHECK_URL);
            if( $req->getStatus() == 200 ) {
                $remote_ver = trim($req->getResult());
                if( strpos($remote_ver,':') !== FALSE ) {
                    list($tmp,$remote_ver) = explode(':',$remote_ver,2);
                    $remote_ver = trim($remote_ver);
                }
                cms_siteprefs::set('last_remotever',$remote_ver);
                cms_siteprefs::set('last_remotever_check',time());
            }
        }
        return $remote_ver;
    }

    /**
     * Test if the current site is in need of upgrading (a new version of CMSMS is available)
     *
     * @return bool
     */
    public static function site_needs_updating()
    {
        $remote_ver = self::fetch_latest_cmsms_ver();
        if( version_compare(CMS_VERSION,$remote_ver) < 0 ) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

}
?>
