<?php
namespace CMSMS;
use CmsApp;

/**
 * This cookie manager obfuscates all keys and signs via SHA1 the values
 * On get, if the attached signature does not match the generated signature, no value is returned.
 */
class SignedCookieManager implements ICookieManager
{
    private $_parts;
    private $_secure;

    public function __construct( CmsApp $app )
    {
        $this->_parts = parse_url(CMS_ROOT_URL);
        if( !isset($this->_parts['host']) || $this->_parts['host'] == '' ) {
            self::$parts['host'] = CMS_ROOT_URL;
        }
        if( !isset($this->_parts['path']) || $this->_parts['path'] == '' ) {
            $this->_parts['path'] = '/';
        }
        $this->_secure = $app->is_https_request();
    }

    protected function get_key(string $key) : string
    {
        return 'c'.sha1(__FILE__.$key);
    }

    protected function cookie_path() : string
    {
        return $this->_parts['path'];
    }

    protected function cookie_domain() : string
    {
        return $this->_parts['host'];
    }

    protected function cookie_secure() : bool
    {
        return $this->_secure;
    }

    protected function set_cookie(string $key, string $encoded, int $expire) : bool
    {
        $res = setcookie($key, $encoded, $expire, $this->cookie_path(), $this->cookie_domain(), $this->cookie_secure(), TRUE);
        return $res;
    }

    public function get(string $key)
    {
        $key = $this->get_key($key);
        if( isset($_COOKIE[$key]) ) {
            list($sig,$val) = explode(':::',$_COOKIE[$key],2);
            if( sha1($val.__FILE__.$key) == $sig ) return $val;
        }
    }

    public function set(string $key, $value, int $expires = 0) : bool
    {
        if( !is_string($value) ) throw new \LogicException('Cookie value passed to '.__METHOD__.' must be a string');
        $key = $this->get_key($key);
        $sig = sha1($value.__FILE__.$key);
        return $this->set_cookie($key,$sig.':::'.$value,$expires);
    }

    public function exists(string $key) : bool
    {
        $key = $this->get_key($key);
        return isset($_COOKIE[$key]);
    }

    public function erase(string $key)
    {
        $key = $this->get_key($key);
        unset($_COOKIE[$key]);
        $this->set_cookie($key,null,time()-3600);
    }

} // class
