<?php
require_once('_init.php');

// this initializes cmsms.
require_once(CMSMS.'/include.php');

class BasicTests extends TestSuite
{
  public function __construct()
  {
    parent::__construct();
    $dir = __DIR__.'/basic_tests';
    $files = glob($dir.'/test_*php');
    if( $files ) {
      foreach( $files as $one_file ) $this->addFile($one_file);
    }
  }
}
