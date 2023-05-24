<?php

use __appbase\langtools;
use __appbase\wizard;

function ilang()
{
  $args = func_get_args();
  return langtools::get_instance()->translate($args);
}

function verbose_msg($str) {
  $obj = wizard::get_instance()->get_step();
  if( method_exists($obj,'verbose') ) return $obj->verbose($str);
}

function status_msg($str) {
  $obj = wizard::get_instance()->get_step();
  if( method_exists($obj,'message') ) return $obj->message($str);
}

function error_msg($str) {
  $obj = wizard::get_instance()->get_step();
  if( method_exists($obj,'error') ) return $obj->error($str);
}


?>