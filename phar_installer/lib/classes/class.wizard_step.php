<?php

namespace __appbase;

abstract class wizard_step
{
  public function __construct() {
    echo "DEBUG: create wizard step<br/>";
  }

  /**
   * Process the results of this step's form (POST only)
   */
  abstract protected function process();

  /**
   * Display information for this step
   */
  abstract protected function display();

  public function get_name() { return get_class($this); }
  public function get_description() { return null; }

  public function &get_wizard()
  {
    return wizard::get_instance();
  }

  public function cur_step()
  {
    return wizard::get_instance()->cur_step();
  }

  public function run()
  {
    $request = request::get();
    if( $request->is_post() ) $res = $this->process();
    $this->display();
    return wizard::STATUS_OK;
  }
} // end of class

?>