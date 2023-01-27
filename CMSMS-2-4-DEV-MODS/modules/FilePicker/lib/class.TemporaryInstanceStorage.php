<?php
namespace FilePicker;

// store the CWD for every instance of a filepicker for each request in the session
// this may pollute the session, but meh we can deal with that later.
class TemporaryInstanceStorage
{
    private function __construct() {}

    public static function set($sig,$val)
    {
        $val = trim($val); // make sure its a string
        $key = md5(__FILE__);
        $_SESSION[$key][$sig] = $val;
        return $sig;
    }

    public static function get($sig)
    {
        $key = md5(__FILE__);
        if( isset($_SESSION[$key][$sig]) ) return $_SESSION[$key][$sig];
    }

    public static function clear($sig)
    {
        $key = md5(__FILE__);
        if( isset($_SESSION[$key][$sig]) ) unset($_SESSION[$key][$sig]);
    }

    public static function reset()
    {
        $key = md5(__FILE__);
        if( isset($_SESSION[$key]) ) unset($_SESSION[$key]);
    }
}