<?php

namespace __appbase\tests;

class matchany_test extends test_base
{
  private $_children;
  private $minimum;
  private $maximum;
  private $recommended;
  private $success_key;
  private $pass_key;
  private $fail_key;

  public function __construct($name)
  {
    parent::__construct($name,'');
  }

  public function add_child(test_base $obj)
  {
    if( !is_array($this->_children) )
      $this->_children = array();

    $this->_children[] = $obj;
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
    if( count($this->_children) )
    {
      for( $i = 0; $i < count($this->_children); $i++ )
      {
        $res = $this->_children[$i]->execute();
        if( $res == parent::TEST_PASS )
        {
          return parent::TEST_PASS;
        }
      }
    }
    return parent::TEST_FAIL;
  }
}

?>
