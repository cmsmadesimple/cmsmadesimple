<?php

/**
 * A cache driver for CMSMS that uses the APC cache.
 * @package CMS
 * @license GPL
 */
namespace CMSMS;
use \cms_cache_driver;
use \APCIterator;

/**
 * This is a cache driver for CMSMS that stores cached data in memory using the APC library
 *
 * This class will obfuscate all keys, and groups for added security.
 *
 * @since 2.3
 * @package CMS
 * @licwnse 2.3
 * @author  Robert Campbell
 */
class apc_cache_driver extends cms_cache_driver
{
    /**
     * @var The number of seconds before cached items expire.
     */
    protected $ttl_seconds;

    /**
     * @var The current group
     */
    protected $group;

    /**
     * Constructor
     *
     * @param int $ttl_seconds the number of seconds before cached items should expire.
     */
    public function __construct( int $ttl_seconds )
    {
        $this->ttl_seconds = max(60,$ttl_seconds);
        if( !function_exists('apc_store') || !class_exists('\\APCIterator') ) throw new \LogicException('Cannot create instance of '.__CLASS__.' APC functions are not available');
    }

    /**
     * calculate a prefix for keys given a group.
     *
     * @param string $group
     * @return string
     */
    protected function get_prefix( string $group = '') : string
    {
        $group = trim($group);
        if( !$group ) $group = $this->group;
        return md5(__FILE__.$group);
    }

    /**
     * Given a key, and an optional group, generate an obfuscated key
     *
     * @param string $key
     * @param string $group
     * @return string
     */
    protected function get_key(string $key, string $group = '') : string
    {
        if( !$key ) throw new \InvalidArgumentException("Invalid key passed to ".__METHOD__);
        return $this->get_prefix($group).'_'.md5(__FILE__.$key);
    }

    /**
     * Remove all of the cached entries in the named group.
     *
     * @param string $group The group name, if not specified the 'current' group will be used.
     */
    public function clear($group = '')
    {
        // clear all, or clear all in group
        if( !$group || $group == '*' ) {
            foreach( new APCIterator('user') as $item ) {
                apc_delete($item['key']);
            }
            //apc_clear_cache('user');
            //apc_clear_cache();
            return;
        }

        $prefix = $this->get_prefix($group).'_';
    }

    /**
     * Get a cached entry if it exists.
     *
     * @param string $key
     * @param string $group The group name, if not specified the 'current' group will be used.
     * @return mixed
     */
    public function get($key, $group = '')
    {
        return apc_fetch($this->get_key($key,$group));
    }

    /**
     * Test if an entry exists in the cache.
     *
     * @param string $key
     * @param string $group The group name, if not specified the 'current' group will be used.
     * @return bool
     */
    public function exists($key, $group = '') : bool
    {
        return apc_exists($this->get_key($key,$group));
    }

    /**
     * Erase an entry from the cache
     *
     * @param string $key
     * @param string $group The group name, if not specified the 'current' group will be used.
     */
    public function erase($key,$group = '')
    {
        return apc_delete($this->get_key($key,$group));
    }

    /**
     * Set an item into the chache
     *
     * @param string $key
     * @param mixed $value
     * @param string $group The group name, if not specified the 'current' group will be used.
     */
    public function set($key,$value,$group = '')
    {
        $key = $this->get_key( $key, $group );
        return apc_store( $key, $value, $this->ttl_seconds );
    }

    /**
     * Set the current group for subsequent method calls.
     *
     * @param string $group the group name
     */
    public function set_group($group = '')
    {
        $group = trim($group);
        $this->group = $group;
    }
} // class
