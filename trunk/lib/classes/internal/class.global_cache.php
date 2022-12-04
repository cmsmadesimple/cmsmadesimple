<?php
namespace CMSMS\internal;

class global_cache
{
    const TIMEOUT = 604800;
    private static $_types = array();
    private static $_dirty;
    private static $_cache;

    private function __construct() {}

    public static function add_cachable(global_cachable $obj)
    {
        $name = $obj->get_name();
        self::$_types[$name] = $obj;
    }

    public static function get($type)
    {
        // if( !isset(self::$_types[$type]) ) throw new \LogicException('Unknown type '.$type);
        if( !isset(self::$_types[$type]) ) return;
        if( !is_array(self::$_cache) ) self::_load();

        if( !isset(self::$_cache[$type]) ) {
            self::$_cache[$type] = self::$_types[$type]->fetch();
            self::$_dirty[$type] = 1;
            self::save();
        }
        return self::$_cache[$type];
    }

    public static function release($type)
    {
        if( isset(self::$_cache[$type]) ) unset(self::$_cache[$type]);
    }

    public static function clear($type)
    {
        // clear it from the cache
        $driver = self::_get_driver();
        $driver->erase($type);
        unset(self::$_cache[$type]);
    }

    public static function save()
    {
        global $CMS_INSTALL_PAGE;
        if( !empty($CMS_INSTALL_PAGE) ) return;
        $driver = self::_get_driver();
        $keys = array_keys(self::$_types);
        foreach( $keys as $key ) {
            if( !empty(self::$_dirty[$key]) && isset(self::$_cache[$key]) ) {
                $driver->set($key,self::$_cache[$key]);
                unset(self::$_dirty[$key]);
            }
        }
    }

    private static function _get_driver()
    {
        static $_driver = null;
        if( !$_driver ) {
            $_driver = new \cms_filecache_driver(array('lifetime'=>self::TIMEOUT,'autocleaning'=>1,'group'=>__CLASS__));
        }
        return $_driver;
    }

    private static function _load()
    {
        debug_buffer('initialize internal global cache');
        $driver = self::_get_driver();
        $keys = array_keys(self::$_types);
        self::$_cache = array();
        foreach( $keys as $key ) {
            $tmp = $driver->get($key);
            self::$_cache[$key] = $tmp;
            unset($tmp);
        }
        debug_buffer('done initializing global cache');
    }

    public static function clear_all()
    {
        self::_get_driver()->clear();
        self::$_cache = array();
    }

} // end of class
