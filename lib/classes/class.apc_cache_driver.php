<?php
namespace CMSMS;
use \cms_cache_driver;
use \APCIterator;

class apc_cache_driver extends cms_cache_driver
{

    protected $ttl_seconds;

    protected $group;

    public function __construct( int $ttl_seconds )
    {
        $this->ttl_seconds = max(60,$ttl_seconds);
        if( !function_exists('apc_store') || !class_exists('\\APCIterator') ) throw new \LogicException('Cannot create instance of '.__CLASS__.' APC functions are not available');
    }

    protected function get_prefix( string $group = '')
    {
        $group = trim($group);
        if( !$group ) $group = $this->group;
        return md5(__FILE__.$group);
    }

    protected function get_key(string $key, string $group = '')
    {
        if( !$key ) throw new \InvalidArgumentException("Invalid key passed to ".__METHOD__);
        return $this->get_prefix($group).'_'.md5(__FILE__.$key);
    }

    public function clear($group = '')
    {
        // clear all, or clear all in group
        if( !$group || $group == '*' ) {
            apc_clear_cache('user');
            return;
        }

        $prefix = $this->get_prefix($group);
        foreach( new APCIterator('user','/^'.$prefix.'/') as $item ) {
            apc_delete($item['key']);
        }
    }

    public function get($key, $group = '')
    {
        return apc_fetch($this->get_key($key,$group));
    }

    public function exists($key, $group = '')
    {
        return apc_exists($this->get_key($key,$group));
    }

    public function erase($key,$group = '')
    {
        return apc_delete($this->get_key($key,$group));
    }

    public function set($key,$value,$group = '')
    {
        $key = $this->get_key( $key, $group );
        return apc_store( $key, $value, $this->ttl_seconds );
    }

    public function set_group($group)
    {
        $group = trim($group);
        $this->group = $group;
    }
} // class
