<?php
require_once(dirname(__FILE__).'/simpletest/autorun.php');
include_once(dirname(dirname(__FILE__)) . '/lib/cmsms.api.php');

class CmsTemplateTest extends UnitTestCase
{
	public function setUp()
	{
		CmsCache::clear();
	}
	
	public function tearDown()
	{
		$this->setUp();
	}
	
	public function testGetPageBlocks()
	{
		$tpl = new CmsTemplate();
		
		$tpl->name = 'Test';
		$tpl->content = "{content name='thing'} {content} {content name='doo \"dad\"'}";
		$blocks = $tpl->get_page_blocks();
		$this->assertEqual(3, count($blocks));
		$this->assertTrue(array_key_exists('thing', $blocks));
		$this->assertTrue(array_key_exists('default', $blocks));
		$this->assertTrue(array_key_exists('doo "dad"', $blocks));
		$tpl->content = "{content name=\"bob's block\"} {content name=\"'joes' block\"}";
		$blocks = $tpl->get_page_blocks();
		$this->assertEqual(2, count($blocks));
		$this->assertTrue(array_key_exists("bob's block", $blocks));
		$this->assertTrue(array_key_exists("'joes' block", $blocks));
	}
}
?>