<?php

// we should have some config variable to get this
define('SIMPLETEST','/tmp/simpletest');
define('CMSMS',dirname(dirname(__FILE__)));

require_once(SIMPLETEST.'/autorun.php');

class BasicTests extends TestSuite 
{
  function __construct()
  {
    parent::__construct();
    $dir = dirname(__FILE__).'/basics';
    $files = glob($dir.'/test_*php');
    if( is_array($files) && count($files) ) {
      foreach( $files as $one_file ) $this->addFile($one_file);
    }
  }
}

?>