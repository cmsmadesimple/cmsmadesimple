<?php
/**
 * A class to define a cookie manager that is capable of handling different data types.
 * @package CMS
 * @license GPL
 */
namespace CMSMS;

/**
 * This class adjusts the way cookies are stored to allow identifying their primitive type
 * (string, object, array).  And then when retrieved can automatically restore them.
 *
 * This class uses json to encoe arrays and objects, for security.  Objects should implement
 * the JsonSerializable interface if they can be saved to a cookie.
 *
 * @since 2.3
 * @package CMS
 * @licwnse 2.3
 * @author  Robert Campbell
 */
class AutoCookieManager extends SignedCookieManager
{
    const KEY_OBJ = 'OBJ__';
    const KEY_ASSOC = 'ASSOC__';

    /**
     * Set a cookie
     *
     * @param string $key The cookie name
     * @param mixed $value The cookie contents
     * @param int $expires The timestamp of expiry.  If 0 it indicates a session cookie.
     * @return bool
     */
    public function set(string $key, $value, int $expires = 0) : bool
    {
        $is_empty = empty($value);
        if( is_object($value) ) {
            $tmp = json_encode($value);
            if( !$tmp ) throw new \LogicException('Could not encode object to json');
            $value = self::KEY_OBJ.$tmp;
        }
        else if( is_array($value) && array_keys($value) !== range(0, count($value) - 1) ) {
            $value = self::KEY_ASSOC.json_encode($value);
        }
        else {
            $value = json_encode($value);
        }
        if( !$value && !$is_empty ) throw new \RuntimeException('Attempt to store un-encodable data in a cookie');
        return parent::set($key, $value, $expires);
    }

    /**
     * Get a cookie.
     *
     * If the cookie exists, and appropriate info can be found, this metod will automatically decode the cookie
     * from a string into a more complex data type.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $val = parent::get($key);
        if( $val ) {
            if( startswith($val,self::KEY_OBJ) ) {
                $val = substr($val,strlen(self::KEY_OBJ));
                $val = json_decode($val);
            }
            else if( startswith($val,self::KEY_ASSOC) ) {
                $val = substr($val,strlen(self::KEY_OBJ));
                $val = json_decode($val, TRUE);
            }
            else {
                $val = json_decode($val);
            }
            return $val;
        }
    }

} // class