<?php

namespace __appbase\tests;

use __appbase\utils;

class boolean_test extends test_base
{
//  private $_data = array();

  public function __construct($name,$value)
  {
    parent::__construct($name,(bool)$value);
  }

  public function execute()
  {
    $val = utils::to_bool($this->value);
    if( $val ) return parent::TEST_PASS;
    return parent::TEST_FAIL;
  }
}
