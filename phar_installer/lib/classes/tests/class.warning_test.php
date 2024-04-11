<?php

namespace __appbase\tests;

// just like a boolean test, but uses TEST_WARN installed of TEST_FAIL
class warning_test extends test_base
{
  private $_data = [];

  public function __construct($name,$value)
  {
    $value = (bool)$value;
    parent::__construct($name,$value);
  }

  public function execute()
  {
    $val = \__appbase\utils::to_bool($this->value);
    if( $val ) return self::TEST_PASS;
    return self::TEST_WARN;
  }
}
