<?php
#CMS - CMS Made Simple
# (c) 2016 by Robert Campbell (calguy1000@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
#
#$Id: class.user.inc.php 2961 2006-06-25 04:49:31Z wishy $

namespace CMSMS;
use UserOperations;
use cms_siteprefs;
use cms_config;
use CmsApp;

final class LoginOperations
{

    private static $_instance;

    private $_cookie_name;

    private $_data;

    private $_cookie_manager;

    private $_userops;

    private $_ignore_xss_vulnerability;

    public function __construct( UserOperations $userops, ICookieManager $cookiemgr, bool $ignore_xss_vulnerability = false )
    {
        if( self::$_instance ) throw new \LogicException('Only one instance of '.__CLASS__.' is permitted');
        self::$_instance = $this;
        $this->_userops = $userops;
        $this->_cookie_manager = $cookiemgr;
        $this->_cookie_name = '_'.sha1( CMS_VERSION.$this->_get_salt() );
        $this->_ignore_xss_vulnerability = $ignore_xss_vulnerability;
    }

    public static function get_instance() : LoginOperations
    {
        if( !self::$_instance ) throw new \LogicException("Instance of ".__CLASS__." has not been created");
        return self::$_instance;
    }

    public function deauthenticate()
    {
        $this->_cookie_manager->erase($this->_cookie_name);
        unset($_SESSION[$this->_cookie_name],$_SESSION[CMS_USER_KEY]);
    }

    protected function _get_salt()
    {
        // if we do not have a presaved salt.. we generate one
        // this is just some randomized data that can be used in alternate salts.
        $salt = cms_siteprefs::get(__CLASS__);
        if( !$salt ) {
            trigger_error('CMSMS LOGIN: no salt for login key');
            $salt = sha1( rand().__FILE__.rand().time() );
            cms_siteprefs::set(__CLASS__,$salt);
        }
        return $salt;
    }

    protected function _check_passhash($uid,$checksum)
    {
        // we already validated that payload was not corrupt
        // now we validate that the user is valid.
        $oneuser = $this->_userops->LoadUserByID((int) $uid);
        if( !$oneuser ) return FALSE;
        if( !$oneuser->active ) return FALSE;
        $checksum = (string) $checksum;
        if( !$checksum ) return FALSE;

        if( !password_verify( $oneuser->id.$oneuser->password.__FILE__, $checksum ) ) return FALSE;
        return TRUE;
    }

    public function save_authentication(\User $user,\User $effective_user = null)
    {
        // saves session/cookie data
        if( $user->id < 1 || empty($user->password) ) throw new \LogicException('User information invalid for '.__METHOD__);

        $nonce = bin2hex(random_bytes(20));
        $_SESSION[CMS_USER_KEY] = $this->_create_csrf_token( $nonce );

        // note: we store the csrf key in the cookie so that it can seemlessly be restored
        // when the session times out.
        $private_data = array();
        $private_data['uid'] = $user->id;
        $private_data['csrf_key'] = $_SESSION[CMS_USER_KEY];
        $private_data['username'] = $user->username;
        $private_data['eff_uid'] = null;
        $private_data['eff_username'] = null;
        $private_data['nonce'] = $nonce;
        $private_data['hash'] = password_hash( $user->id.$user->password.__FILE__, PASSWORD_BCRYPT );
        if( $effective_user && $effective_user->id > 0 && $effective_user->id != $user->id ) {
            $private_data['eff_uid'] = $effective_user->id;
            $private_data['eff_username'] = $effective_user->username;
        }
        $enc = base64_encode( json_encode( $private_data ) );
        $sig = sha1( $this->_get_salt() . $enc ); // sign the private data with the salt again.
        $val = $sig.'::'.$enc; // append signature
        // note: depending on the cookie manager, this may be signed again...
        $this->_cookie_manager->set($this->_cookie_name,$val);

        // this is for CSRF stuff, doesn't technically belong here.
        unset($this->_data);
        return true;
    }

    protected function _verify_csrf_token( string $token, string $nonce )
    {
        $test = substr(sha1($nonce.__FILE__.$this->_get_salt().$_SERVER['HTTP_USER_AGENT']),0,20);
        return $token === $test;
    }

    protected function _create_csrf_token( string $nonce )
    {
        // this csrf token is stored in the cookie
        // which means that if the cookie AND the CSRF value are stolen, we can forge requests
        // so if the csrf key is not random, it shold be reproducable
        // but use some info from the database, som random info (per cookie)... and some server info, and some browser info
        return substr(sha1($nonce.__FILE__.$this->_get_salt().$_SERVER['HTTP_USER_AGENT']),0,20);
    }

    protected function _get_data()
    {
        if( !empty($this->_data) ) return $this->_data;

        // using session, and-or cookie data see if we are authenticated
        $private_data = null;
        /* debug
        if( isset($_SESSION[$this->_cookie_name]) ) {
            $private_data = $_SESSION[$this->_cookie_name];
        }
        else
        */
        if( ($private_data = $this->_cookie_manager->get($this->_cookie_name)) ) {
            // $_SESSION[$this->_cookie_name] = $private_data; // debug
        }
        if( !$private_data ) return;
        $parts = explode('::',$private_data,2);
        if( count($parts) != 2 ) return; // invalid cookie format

        if( $parts[0] != sha1( $this->_get_salt() . $parts[1] ) ) return; // payload signature invalid
        $private_data = json_decode( base64_decode( $parts[1]), TRUE );

        if( !is_array($private_data) ) return; // payload corrupted
        if( empty($private_data['uid']) ) return;  // invalid payload
        if( empty($private_data['username']) ) return; // invalid payload
        if( empty($private_data['hash']) ) return; // invalid payload

        // now authenticate the passhash
        // requires a database query
        // so if we deactivate the user, or change his password the cookie will be invalidated.
        if( !cmsms()->is_frontend_request() && !$this->_check_passhash($private_data['uid'],$private_data['hash']) ) return;

        // if we get here, the user is authenticated.
        if( empty($_SESSION[CMS_USER_KEY]) && !empty($private_data['csrf_key']) ) {
            // sets the CSRF key into the session, from the cookie for compatibility
            // we set it from the cookie because the cookie has a longer lifetime than the session
            // and we do not want the CSRF key to change when the user walks away from the screen
            $_SESSION[CMS_USER_KEY] = $private_data['csrf_key'];
        }

        // still no csrf token,  so set a random one.
        // this probably does not belong here.  If the csrf token is not in the
        // session, and cannot be restored from the session cookie, the user should be logged out.
        /*
        if( !isset($_SESSION[CMS_USER_KEY]) ) {
            $_SESSION[CMS_USER_KEY] = $this->_create_csrf_token( $private_data['uid'] );
        }
        */

        $this->_data = $private_data;
        return $this->_data;
    }

    public function validate_requestkey()
    {
        if( !$this->_data || !isset($this->_data['nonce']) ) return FALSE;

        // asume we are authenticated
        // now we validate that the request has the user key in it somewhere.
        if( !isset($_SESSION[CMS_USER_KEY]) ) throw new \LogicException('Internal: User key not found in session.');

        // we check GET and POST vars specifically incase $_REQUEST also contains cookie values.
        $v = '<no$!tgonna!$happen>';
        if( isset($_GET[CMS_SECURE_PARAM_NAME]) ) $v = $_GET[CMS_SECURE_PARAM_NAME];
        if( isset($_POST[CMS_SECURE_PARAM_NAME]) ) $v = $_POST[CMS_SECURE_PARAM_NAME];
        // validate the key in the request against what we have in the session.
        if( $v != $_SESSION[CMS_USER_KEY] || !$this->_verify_csrf_token($v, $this->_data['nonce']) ) {
            if( !$this->_ignore_xss_vulnerability ) return FALSE;
        }
        return TRUE;
    }

    public function get_loggedin_uid()
    {
        $data = $this->_get_data();
        if( !$data ) return;
        return (int) $data['uid'];
    }

    public function get_loggedin_username()
    {
        $data = $this->_get_data();
        if( !$data ) return;
        return trim($data['username']);
    }

    public function get_loggedin_user()
    {
        $uid = $this->get_loggedin_uid();
        if( $uid < 1 ) return;
        $user = $this->_userops->LoadUserByID($uid);
        return $user;
    }

    public function get_effective_uid()
    {
        $data = $this->_get_data();
        if( !$data ) return;
        if( isset($data['eff_uid']) && $data['eff_uid'] ) return $data['eff_uid'];
        return $this->get_loggedin_uid();
    }

    public function get_effective_username()
    {
        $data = $this->_get_data();
        if( !$data ) return;
        if( isset($data['eff_username']) && $data['eff_username'] ) return $data['eff_username'];
        return $this->get_loggedin_username();
    }

    public function set_effective_user(\User $e_user = null)
    {
        $li_user = $this->get_loggedin_user();
        if( $e_user && $e_user->id == $li_user->id ) return;

        $new_key = $this->save_authentication($li_user,$e_user);
        return $new_key;
    }
}
