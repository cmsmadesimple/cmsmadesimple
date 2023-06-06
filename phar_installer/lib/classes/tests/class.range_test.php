<?php

namespace __appbase\tests;

class range_test extends test_base
{
  private $minimum;
  private $maximum;

  public function __construct($name,$value)
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
          $this->$key = $value;
          break;

      default:
          parent::__set($key,$value);
      }
  }


  public function execute()
  {
      if( $this->minimum )
      {
          $min = $this->returnBytes($this->minimum);
          $val = $this->returnBytes($this->value);
          if( $val < $min ) return parent::TEST_FAIL;
      }
      if( $this->recommended )
      {
          $rec = $this->returnBytes($this->recommended);
          $val = $this->returnBytes($this->value);
          if( $val < $rec ) return parent::TEST_WARN;
      }
      if( $this->maximum )
      {
          $max = $this->returnBytes($this->maximum);
          $val = $this->returnBytes($this->value);
          if( $val > $max ) return parent::TEST_FAIL;
      }
      return parent::TEST_PASS;
  }
}

?>
