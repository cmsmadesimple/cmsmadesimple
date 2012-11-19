<?php

require_once(CMSMS.'/lib/classes/class.cms_url.php');

class Test_cms_url extends UnitTestCase
{
  private static $TEST_URL = 'http://someuser:somepass@subdomain.host.com:5000/foo.bar?foo=bar&a=2#frag';

  public function TestGetScheme()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $this->assertEqual($ob->get_scheme(),'http');
  }

  public function TestSetScheme()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $ob->set_scheme('ftp');
    $this->assertEqual($ob->get_scheme(),'ftp');
  }

  public function TestGetHost()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $this->assertEqual($ob->get_host(),'subdomain.host.com');
  }

  public function TestSetHost()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $str = 'another.host2.com';
    $ob->set_host($str);
    $this->assertEqual($ob->get_host(),$str);
  }

  public function TestGetPort()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $this->assertEqual($ob->get_port(),5000);
  }

  public function TestSetPort()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $port = 1111;
    $ob->set_port($port);
    $this->assertEqual($ob->get_port(),$port);
  }

  public function TestGetUser()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $this->assertEqual($ob->get_user(),'someuser');
  }

  public function TestSetUser()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $str = 'fooo';
    $ob->set_user($str);
    $this->assertEqual($ob->get_user(),$str);
  }

  public function TestGetPass()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $this->assertEqual($ob->get_pass(),'somepass');
  }

  public function TestSetPass()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $str = 'fooo';
    $ob->set_pass($str);
    $this->assertEqual($ob->get_pass(),$str);
  }

  public function TestGetPath()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $this->assertEqual($ob->get_path(),'/foo.bar');
  }

  public function TestSetPath()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $str = 'fooo';
    $ob->set_path($str);
    $this->assertEqual($ob->get_path(),$str);
  }

  public function TestGetQuery()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $this->assertEqual($ob->get_query(),'foo=bar&a=2');
  }

  public function TestSetQuery()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $str = '?a=b&c=d';
    $ob->set_query($str);
    $this->assertEqual($ob->get_query(),$str);
  }

  public function TestGetFragment()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $this->assertEqual($ob->get_fragment(),'frag');
  }

  public function TestSetFragment()
  {
    $ob = new cms_url(self::$TEST_URL);
    
    $str = 'woot';
    $ob->set_fragment($str);
    $this->assertEqual($ob->get_fragment(),$str);
  }

  public function TestForSpecificQueryVar()
  {
    $ob = new cms_url(self::$TEST_URL);
    $this->assertTrue($ob->queryvar_exists('foo'));
  }

  public function TestGetQueryVar()
  {
    $ob = new cms_url(self::$TEST_URL);
    $this->assertEqual($ob->get_queryvar('foo'),'bar');
  }

  public function TestSetQueryVar()
  {
    $ob = new cms_url(self::$TEST_URL);
    $ob->set_queryvar('test1','999');
    $this->assertEqual($ob->get_queryvar('test1'),999);
  }

  public function TestToString()
  {
    $ob = new cms_url();
    $ob->set_scheme('https');
    $ob->set_user('myusername');
    $ob->set_pass('mypassword');
    $ob->set_host('www.cmsmadesimple.org');
    $ob->set_port(5000);
    $ob->set_path('path/a/b/c');
    $ob->set_queryvar('a','b');
    $ob->set_queryvar('c',1234);
    $ob->set_fragment('anchor');
    
    $x1 = (string)$ob;
    $x2 = 'https://myusername:mypassword@www.cmsmadesimple.org:5000/path/a/b/c?a=b&c=1234#anchor';
    $this->assertEqual($x1,$x2);
  }
}

?>