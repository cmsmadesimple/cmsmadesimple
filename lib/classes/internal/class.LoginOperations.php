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

final class LoginOperations
{
    private static $_instance;
    private $_loginkey;
    private $_data;

    protected function __construct()
    {
        $this->_loginkey = md5(__FILE__.__CLASS__.CMS_VERSION);
    }

    public static function &get_instance()
    {
        if( !self::$_instance ) self::$_instance = new self();
        return self::$_instance;
    }

    public function deauthenticate()
    {
        \cms_cookies::erase($this->_loginkey);
        \cms_cookies::erase(CMS_USER_KEY);
        unset($_SESSION[$this->_loginkey],$_SESSION[CMS_USER_KEY]);
    }

    protected function _check_passhash($uid,$checksum)
    {
        $userops = \UserOperations::get_instance();
        $oneuser = $userops->LoadUserByID((int) $uid);
        if( !$oneuser ) return FALSE;

        $tmp = array(md5(__FILE__),$oneuser->password,$uid,\cms_utils::get_real_ip(),$_SERVER['HTTP_USER_AGENT']);
        $tmp = sha1(serialize($tmp));
        if ($oneuser && (string)$checksum != '' && $checksum === $tmp ) return TRUE;
        return FALSE;
    }

    protected function _encrypt($user_rec)
    {
        // this is not strong encryption, just enough to make the data obfuscated
        // only a username and userid, and a password hash are stored anyways.
        return str_rot13(base64_encode(serialize($user_rec)));
    }

    protected function _decrypt($data)
    {
        return unserialize(base64_decode(str_rot13($data)));
    }

    public function save_authentication(\User $user,\User $effective_user = null)
    {
        // saves session/cookie data
        if( $user->id < 1 || empty($user->password) ) throw new \LogicException('User information invalid for '.__METHOD__);

        $tmp = array(md5(__FILE__),$user->password,$user->id,\cms_utils::get_real_ip(),$_SERVER['HTTP_USER_AGENT']);
        $tmp = sha1(serialize($tmp));

        $private_data = array();
        $private_data['uid'] = $user->id;
        $private_data['username'] = $user->username;
        $private_data['cksum'] = $tmp;
        $private_data['eff_uid'] = null;
        $private_data['eff_username'] = null;
        if( $effective_user && $effective_user->id > 0 && $effective_user->id != $user->id ) {
            $private_data['eff_uid'] = $effective_user->id;
            $private_data['eff_username'] = $effective_user->username;
        }
        $_SESSION[$this->_loginkey] = $this->_encrypt($private_data);
        \cms_cookies::set($this->_loginkey,$_SESSION[$this->_loginkey]);

        $key = substr(str_shuffle(sha1(__DIR__.$user->id.time().session_id())),-19);
        $_SESSION[CMS_USER_KEY] = $key;
        \cms_cookies::set(CMS_SECURE_PARAM_NAME,$key);
        unset($this->_data);
        return $key;
    }

    protected function _get_data()
    {
        if( !empty($this->_data) ) return $this->_data;

        // using session, and-or cookie data see if we are authenticated
        $private_data = null;
        if( isset($_SESSION[$this->_loginkey]) ) {
            $private_data = $_SESSION[$this->_loginkey];
        }
        else {
            if( isset($_COOKIE[$this->_loginkey]) ) $private_data = $_SESSION[$this->_loginkey] = $_COOKIE[$this->_loginkey];
        }

        if( !$private_data ) return;
        $private_data = $this->_decrypt($private_data);
        if( !is_array($private_data) ) return;
        if( empty($private_data['uid']) ) return;
        if( empty($private_data['username']) ) return;
        if( empty($private_data['cksum']) ) return;

        // now authenticate the passhash
        // requires a database query
        if( \CmsApp::get_instance()->is_frontend_request() ) return;
        if( !$this->_check_passhash($private_data['uid'],$private_data['cksum']) ) return;

        // if we get here, the user is authenticated.
        // set the session key for all URL from the cookie if it exists.
        if( !isset($_SESSION[CMS_USER_KEY]) ) {
            if( \cms_cookies::exists(CMS_SECURE_PARAM_NAME) ) $_SESSION[CMS_USER_KEY] = \cms_cookies::get(CMS_SECURE_PARAM_NAME);
        }

        $this->_data = $private_data;
        return $this->_data;
    }

    public function validate_requestkey()
    {
        // asume we are authenticated
        // now we validate that the request has the user key in it somewhere.
        if( !isset($_SESSION[CMS_USER_KEY]) ) throw new \LogicException('Internal: User key not found in session.');

        $v = '<no$!tgonna!$happen>';
        if( isset($_REQUEST[CMS_SECURE_PARAM_NAME]) ) $v = $_REQUEST[CMS_SECURE_PARAM_NAME];

        // validate the key in the request against what we have in the session.
        if( $v != $_SESSION[CMS_USER_KEY] ) {
            $config = \cms_config::get_instance();
            if( !isset($config['stupidly_ignore_xss_vulnerability']) ) return FALSE;
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
        $user = \UserOperations::get_instance()->LoadUserByID($uid);
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
