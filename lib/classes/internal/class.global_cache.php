<?php
namespace CMSMS\internal;
use cms_cache_driver;

/**
 * @deprecated
 */

class global_cache
{
    const TIMEOUT = 604800;

    private static $_driver;

    private static $_types = array ();

    private static $_dirty;

    private static $_cache;

    public function __construct (cms_cache_driver $driver)
    {
        $this->_driver = $driver;
    }

    public static function add_cachable (global_cachable $obj)
    {
        $name = $obj->get_name ();
        self::$_types[$name] = $obj;
    }

    public static function get ($type)
    {
        if (!isset(self::$_types[$type])) return;
        if (!is_array(self::$_cache))
        self::_load();

        if (!isset (self::$_cache[$type])) {
            self::$_cache[$type] = self::$_types[$type]->fetch ();
            self::$_dirty[$type] = 1;
            self::save ();
        }
        return self::$_cache[$type];
    }

    public static function release ($type)
    {
        if (isset (self::$_cache[$type]))
        unset (self::$_cache[$type]);
    }

    public static function clear ($type)
    {
        // clear it from the cache
        unset (self::$_cache[$type]);
        $driver = self::_get_driver ();
        $driver->clear($type,__CLASS__);
    }

    public static function save ()
    {
        global $CMS_INSTALL_PAGE;
        if (!empty ($CMS_INSTALL_PAGE))
        return;
        $driver = self::_get_driver ();
        $keys = array_keys (self::$_types);
        foreach ($keys as $key) {
            if (!empty (self::$_dirty[$key]) && isset (self::$_cache[$key])) {
                $driver->set ($key, self::$_cache[$key], __CLASS__);
                unset (self::$_dirty[$key]);
            }
        }
    }

    public static function set_driver (cms_cache_driver $driver)
    {
        if (!self::$_driver)
        self::$_driver = $driver;
    }

    private static function _get_driver ()
    {
        if (!self::$_driver) {
            self::$_driver =
            new \
            cms_filecache_driver([ 'lifetime' =>self::TIMEOUT, 'autocleaning' =>1, 'group' =>__CLASS__ ]);
        }
        return self::$_driver;
    }

    private static function _load ()
    {
        $driver = self::_get_driver ();
        $keys = array_keys(self::$_types);
        self::$_cache =[];
        foreach ($keys as $key) {
            if ($driver->exists ($key,__CLASS__)) {
                $tmp = $driver->get($key,__CLASS__);
                self::$_cache[$key] = $tmp;
            }
            unset ($tmp);
        }
    }

    public static function clear_all ()
    {
        self::_get_driver()->clear(__CLASS__);
        self::$_cache = [];
    }
} // end of class
