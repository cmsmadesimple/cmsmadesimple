<?php

namespace __appbase\tests;

class informational_test extends test_base
{
  public function __construct($name,$value,$message = '',$key = '')
  {
    parent::__construct($name,$value,$key);
    if( $message )
    {
      $this->msg_key = $message;
    }
  }

  /**
   * Execute the test
   *
   * @return integer -1 for fail
   */
  public function execute() {}
} // end of class

?>
