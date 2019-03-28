<?php
/**
 * An interface to describe a CMSMS cookie manager.
 * @package CMSMS
 * @since 2.3
 * @license GPL
 */
namespace CMSMS;

/**
 * An interface to describe a CMSMS cookie manager.
 * @package CMSMS
 * @since 2.3
 * @license GPL
 */
interface ICookieManager
{
    /**
     * Get a cookie value
     *
     * @abstract
     * @param string $key The name of the cookie
     * @return mixed The output may be null, or a string or another data type.
     */
    public function get(string $key);

    /**
     * Set a cookie value.
     *
     * @abstract
     * @param string $key The name of the cookie
     * @param mixed The cookie contents
     * @param int $expires The default expiry timestamp.  if 0 is specified, then a session cookie is created.
     * @return bool true on success
     */
    public function set(string $key, $value, int $expires = 0) : bool;

    /**
     * Test whether a cookie exists.
     *
     * @abstract
     * @param string $key The name of the cookie
     * @return bool
     */
    public function exists(string $key) : bool;

    /**
     * Erase a cookie
     *
     * @abstract
     * @param string $key The name of the cookie
     */
    public function erase(string $key);
} // interface
