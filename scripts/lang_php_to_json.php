#!/usr/bin/env php
<?php
$fn = $argv[1];
if( !is_file($fn) ) exit(1);
$ext = strtolower(substr($fn,-3));
if( $ext != 'php' ) die('not a php file');
$destfile = dirname($fn).'/'.substr(basename($fn),0,-3).'json';

include($fn);
if( !isset($lang) || !is_array($lang) || empty($lang) ) die();

$str = json_encode($lang,JSON_PRETTY_PRINT);
file_put_contents($destfile, $str);
