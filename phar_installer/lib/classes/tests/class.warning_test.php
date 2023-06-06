<?php

namespace __appbase\tests;

use __appbase\utils;

// just like a boolean test, but uses TEST_WARN instead of TEST_FAIL
class warning_test extends test_base
{
//  private $_data = array();

  public function __construct($name,$value)
  {
//    $value = (bool)$value;
    parent::__construct($name,$value);
  }

  public function execute()
  {
    $val = utils::to_bool($this->value);
    if( $val ) return parent::TEST_PASS;
    return parent::TEST_WARN;
  }
}
