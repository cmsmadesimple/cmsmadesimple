<?php
require_once(dirname(__FILE__).'/simpletest/autorun.php');
include_once(dirname(dirname(__FILE__)) . '/lib/cmsms.api.php');

class CmsApiTest extends UnitTestCase
{
	public function setUp()
	{
		CmsCache::clear();
	}
	
	public function tearDown()
	{
		$this->setUp();
	}
	
	public function testSubstrMatchShouldWorkAsIntended()
	{
		$this->assertEqual('1234567890', substr_match('1234567890', '1234567890'));
		$this->assertEqual('12345', substr_match('1234567890', '1234542343'));
		$this->assertEqual('', substr_match('1234567890', '33423423411237890'));
		$this->assertEqual('1234567890', substr_match('1234567890', '1234567890', true));
		$this->assertEqual('67890', substr_match('1234567890', '23423423423423467890', true));
		$this->assertEqual('', substr_match('1234567890', '12345678901', true));
	}
}
?>