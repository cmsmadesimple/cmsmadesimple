<?php
// get the api
include(dirname(__FILE__).'/../cmsms.api.php');
// our test class
class CmsUserTests extends CmsUnitTest {
  public function getNumberOfTests() {
    return 5;
  }
  
  public function getTestDirectory() {
    return dirname(__FILE__);
  }
  
  public function run_tests() {
    $user = CmsUserOperations::load_user_by_id(1);

    $this->test_isset($user, "user is set and loaded");

    $this->test_isset($user->username,"username is set");
    $this->test_eq($user->username="asdf",true,"set username to asdf");
    $this->test_eq($user->username,"asdf","username is asdf");
    $this->test_eq($user->id,1,"id is one");

    $this->results();
  }
}

// get it 
$tests = new CMSUserTests();
// run it
$tests->run_tests();
