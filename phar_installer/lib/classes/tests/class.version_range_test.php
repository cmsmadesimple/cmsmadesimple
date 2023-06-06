<?php

namespace __appbase\tests;

class version_range_test extends test_base
{
  private $minimum;  
  private $maximum;  
  private $recommended;  
  private $success_key;  
  private $pass_key;  
  private $fail_key;  

  public function __construct($name,$value) // redundant
  {
    parent::__construct($name,$value);
  }

  #[\ReturnTypeWillChange]
  public function __set($key,$value)
  {
    switch( $key )
      {
      case 'minimum':
      case 'maximum':
      case 'recommended':
      case 'success_key':
      case 'pass_key':
      case 'fail_key':
        $this->$key = $value;
        break;

      default:
        parent::__set($key,$value);
      }
  }

  public function execute()
  {
    // make sure we have all of the information.
    // do the test
    // set the result.
    if( $this->minimum ) {
      if( version_compare($this->value,$this->minimum) < 0 ) return parent::TEST_FAIL;
    }
    if( $this->maximum ) {
      if( version_compare($this->value,$this->maximum) > 0 ) return parent::TEST_FAIL;
    }
    if( $this->recommended ) {
      if( version_compare($this->value,$this->recommended) < 0 ) return parent::TEST_WARN;
    }
    return parent::TEST_PASS;
  }
}

?>
