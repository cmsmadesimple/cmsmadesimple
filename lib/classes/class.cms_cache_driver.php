<?php

abstract class cms_cache_driver
{
  abstract public function clear($group = '');

  abstract public function get($key,$group = '');

  abstract public function exists($key,$group = '');

  abstract public function erase($key,$group = '');

  abstract public function set($key,$value,$group = '');
}



?>