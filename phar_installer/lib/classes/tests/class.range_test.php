<?php

namespace __appbase\tests;

class range_test extends test_base
{
  public function __construct($name,$value)
  {
      parent::__construct($name,$value);
  }


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
          if( $val < $min ) return self::TEST_FAIL;
      }
      if( $this->recommended )
      {
          $rec = $this->returnBytes($this->recommended);
          $val = $this->returnBytes($this->value);
          if( $val < $rec ) return self::TEST_WARN;
      }
      if( $this->maximum )
      {
          $max = $this->returnBytes($this->maximum);
          $val = $this->returnBytes($this->value);
          if( $val > $max ) return self::TEST_FAIL;
      }
      return self::TEST_PASS;
  }
}

?>