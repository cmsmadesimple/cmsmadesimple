<?php
namespace CMSMS;

interface ICookieManager
{
    public function get(string $key);

    public function set(string $key, $value, int $expires = 0) : bool;

    public function exists(string $key) : bool;

    public function erase(string $key);

} // interface