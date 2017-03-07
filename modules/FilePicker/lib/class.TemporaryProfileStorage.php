<?php
namespace FilePicker;

// store profiles temporarily in the session... uses uniqueid
// may pollute the session, but meh... we can clean it up after some time.
// note: cwd is stored separately for each instance,  as the profile won't change as we modify directories
class TemporaryProfileStorage
{
    private function __construct() {}

    public static function set(\CMSMS\FilePickerProfile $profile)
    {
        $key = md5(__FILE__);
        $sig = md5(__FILE__.serialize($profile).microtime(TRUE).'1');
        $_SESSION[$key][$sig] = serialize($profile);
        return $sig;
    }

    public static function get($sig)
    {
        $key = md5(__FILE__);
        if( isset($_SESSION[$key][$sig]) ) return unserialize($_SESSION[$key][$sig]);
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