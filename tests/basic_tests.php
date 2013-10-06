<?php
require_once('_init.php');

// this initializes cmsms.
require_once(CMSMS.'/include.php');

class BasicTests extends TestSuite 
{
  function __construct()
  {
    parent::__construct();
    $dir = dirname(__FILE__).'/basic_tests';
    $files = glob($dir.'/test_*php');
    if( is_array($files) && count($files) ) {
      foreach( $files as $one_file ) $this->addFile($one_file);
    }
  }
}

?>
