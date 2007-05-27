<?php
// define that we are on test run
DEFINE(CMS_TEST_RUN,1);
// get the api
include(dirname(__FILE__).'/../cmsms.api.php');
// our test class
class CmsUserTests extends CmsUnitTest {
  public function getNumberOfTests() {
    return 10;
  }
  
  public function getTestDirectory() {
    return dirname(__FILE__);
  }
  
  public function run_tests() {
    // load first user and check the values
    $user = CmsUserOperations::load_user_by_id(1);    
    // save old username
    $old_username = $user->username;
    $this->test_isset($user, "user is set and loaded");
    $this->test_isa($user, "CmsUser", "object is instance of class user");
    $this->test_eq($user->id,1,"id is one");
    $this->test_isset($user->username,"username is set");
    $this->test_eq($user->username="asdf",true,"set username to asdf");
    $user->save();
    unset($user);
    $user = CmsUserOperations::load_user_by_id(1);
    $this->test_eq($user->username,"asdf","username is asdf");
    // return original username :)
    $user->username=$old_username;
    $user->save();
    unset($user);

    //create new user
    $newuser = new CmsUser();
    $newuser->username = "testuser";
    $newuser->active = 0; //just in case
    $newuser->first_name = "testuser_firstname";
    $newuser->last_name = "testuser_lastname";
    $newuser->email = "testuser@cmsmadesimple.org";
    $newuser->admin_access = 0; //just in case
    $newuser->password = "testpass";

    $this->test_eq($newuser->save(), true, "user created");    
    $loadeduser = CmsUserOperations::load_user_by_id($newuser->id);

echo     var_dump($loadeduser);
echo     var_dump($newuser);

    //check that users match
    $this->test_deepCompare($newuser, $loadeduser, "users match");
    unset($newuser);

    // delete user
    $this->test_eq($loadeduser->delete(), true, "user deleted");

    $this->test_eq(CmsUserOperations::load_user_by_id($loadeduser->id), false, "user cant be found anymore");
    

    $this->results();
  }
}

// get it 
$tests = new CMSUserTests();
// run it
$tests->run_tests();
