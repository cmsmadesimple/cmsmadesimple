<?php

namespace __appbase\tests;

class boolean_test extends test_base
{
  private $_data = array();

  public function __construct($name,$value)
  {
    $value = (bool)$value;
    parent::__construct($name,$value);
  }

  public function execute()
  {
    $val = \__appbase\utils::to_bool($this->value);
    if( $val ) return self::TEST_PASS;
    return self::TEST_FAIL;
  }
}
