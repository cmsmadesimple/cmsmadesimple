<?php
namespace CMSMS;

// this class implements secure/signed cookies, with auto encoding and decoding of objects.
// everything is stored as a string.
class AutoCookieManager extends SignedCookieManager
{
    const KEY_OBJ = 'OBJ__';
    const KEY_ASSOC = 'ASSOC__';

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