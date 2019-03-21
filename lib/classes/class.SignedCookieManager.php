<?php
/**
 * A class to define a cookie manager that is capable of obfuscating cookie names, and signing
 * cookie values to minimize the risk of MITM or corruption attacks.
 *
 * @package CMS
 * @license GPL
 */
namespace CMSMS;
use CmsApp;

/**
 * This cookie manager obfuscates all keys and signs via SHA1 the values
 * On get, if the attached signature does not match the generated signature, no value is returned.
 *
 * @since 2.3
 * @package CMS
 * @licwnse 2.3
 * @author  Robert Campbell
 */
class SignedCookieManager implements ICookieManager
{

    /**
     * @ignore
     */
    private $_parts;

    /**
     * @ignore
     */
    private $_secure;

    /**
     * Constructor.
     *
     * @param CmsApp $app The application instance.
     */
    public function __construct( CmsApp $app )
    {
        $this->_parts = parse_url(CMS_ROOT_URL);
        if( !isset($this->_parts['host']) || $this->_parts['host'] == '' ) {
            self::$parts['host'] = CMS_ROOT_URL;
        }
        if( !isset($this->_parts['path']) || $this->_parts['path'] == '' ) {
            $this->_parts['path'] = '/';
        }
	if( $this->_parts['path'] && !endswith($this->_parts['path'],'/') ) {
	    $this->_parts['path'] .= '/';
	}
        $this->_secure = $app->is_https_request();
    }

    /**
     * Encode a key.
     *
     * The cookie name is encoded to be obfuscated to minimize the opportunity of attacks.
     *
     * @param string $key The cookie name
     */
    protected function get_key(string $key) : string
    {
        return 'c'.sha1(__FILE__.$key.CMS_VERSION);
    }

    /**
     * Generate the cookie path.
     *
     * By default, this is the path portion of the root URL.
     */
    protected function cookie_path() : string
    {
        return $this->_parts['path'];
    }

    /**
     * Generate the cookie domain.
     *
     * By default, this is the host portion of the root URL.
     */
    protected function cookie_domain() : string
    {
        return $this->_parts['host'];
    }

    /**
     * Generate the cookie secure flag.
     *
     * By default, this is relative to whether or not the request was using HTTPS or not.
     */
    protected function cookie_secure() : bool
    {
        return $this->_secure;
    }

    /**
     * Set the actual cookie.
     *
     * @param string $key The final cookie name (may be obfuscated)
     * @param string $encoded The final cookie value.
     * @param int $expire The expiry timestamp.  0 may be provided to indicate a session cookie, a timestamp earlier than now may be
     *    provided to indicate that the cookie can be removed.
     */
    protected function set_cookie(string $key, string $encoded = null, int $expire = 0) : bool
    {
        $res = setcookie($key, $encoded, $expire, $this->cookie_path(), $this->cookie_domain(), $this->cookie_secure(), TRUE);
        return $res;
    }

    /**
     * Retrieve the value of a cookie.
     *
     * This method will retrieve the value of the cookie by first obfuscating the cookie name.
     * Then ensuring that the signature of retrieved cookie can be verified.
     *
     * @param string $okey The cookie name
     * @return string|null
     */
    public function get(string $okey)
    {
        $key = $this->get_key($okey);
        if( isset($_COOKIE[$key]) ) {
            list($sig,$val) = explode(':::',$_COOKIE[$key],2);
            if( sha1($val.__FILE__.$okey.CMS_VERSION) == $sig ) return $val;
        }
    }

    /**
     * Set a cookie.
     *
     * This method will first obfuscate the cookie name.
     * Then it will generate a signature for the cookie contents, then append it to the cookie value.
     * Then generate a standard cookie.
     *
     * @param string $okey The input cookie name
     * @param string $value The cookie value.
     * @param int $expires The expiry timestamp of the cookie.  A value of 0 indicates that a session cookie should be created.
     */
    public function set(string $okey, $value, int $expires = 0) : bool
    {
        if( !is_string($value) ) throw new \LogicException('Cookie value passed to '.__METHOD__.' must be a string');
        $key = $this->get_key($okey);
        $sig = sha1($value.__FILE__.$okey.CMS_VERSION);
        return $this->set_cookie($key,$sig.':::'.$value,$expires);
    }

    /**
     * Test if a cookie exists.
     *
     * This method will obfuscate the input cookie name.
     *
     * @param string $key The input cookie name.
     */
    public function exists(string $key) : bool
    {
        $key = $this->get_key($key);
        return isset($_COOKIE[$key]);
    }

    /**
     * Erase a cookie.
     *
     * This method will obfuscate the input cookie name.
     *
     * @param string $key The input cookie name.
     */
    public function erase(string $key)
    {
        $key = $this->get_key($key);
        unset($_COOKIE[$key]);
        $this->set_cookie($key,null,time()-3600);
    }
} // class
