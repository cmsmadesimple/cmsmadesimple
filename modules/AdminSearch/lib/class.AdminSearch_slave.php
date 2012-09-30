<?php

abstract class AdminSearch_slave
{
  private $_search_text;

  public function set_text($text) {
    $this->_search_text = $text;
  }

  protected function get_text() {
    return $this->_search_text;
  }

  abstract public function check_permission();
  abstract public function get_name();
  abstract public function get_description();
  abstract public function get_matches();
}

?>