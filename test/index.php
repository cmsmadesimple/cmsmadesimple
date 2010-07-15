<?php
require_once(dirname(__FILE__).'/simpletest/autorun.php');

class AllTests extends TestSuite
{
	function AllTests()
	{
		$this->TestSuite('Core Tests');
		
		$this->addFile(dirname(__FILE__) . '/cms_api_test.php');
		$this->addFile(dirname(__FILE__) . '/cms_orm_test.php');
		$this->addFile(dirname(__FILE__) . '/cms_nested_set_test.php');
		$this->addFile(dirname(__FILE__) . '/cms_template_test.php');
	}
}
?>