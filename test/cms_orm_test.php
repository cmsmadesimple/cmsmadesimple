<?php
require_once(dirname(__FILE__).'/simpletest/autorun.php');
include_once(dirname(dirname(__FILE__)) . '/lib/cmsms.api.php');

class CmsOrmTest extends UnitTestCase
{
	public function setUp()
	{
		$this->tearDown();
		CmsDatabase::create_table('test_orm_table', "
			id I KEY AUTO,
			test_field C(255),
			another_test_field C(255),
			some_int I,
			some_float F,
			version I,
			create_date T,
			modified_date T
		");
		cms_db()->Execute("INSERT INTO {test_orm_table} (test_field, another_test_field, some_int, some_float, create_date, modified_date) VALUES ('test', 'blah', 5, 5.501, now() - 10, now() - 10)");
		cms_db()->Execute("INSERT INTO {test_orm_table} (test_field, create_date, modified_date) VALUES ('test2', now(), now())");
		cms_db()->Execute("INSERT INTO {test_orm_table} (test_field, create_date, modified_date) VALUES ('test3', now(), now())");
		
		CmsDatabase::create_table('test_orm_table_child', "
			id I KEY AUTO,
			parent_id I,
			some_other_field C(255),
			version I,
			create_date T,
			modified_date T
		");
		
		cms_db()->Execute("INSERT INTO {test_orm_table_child} (parent_id, some_other_field, create_date, modified_date) VALUES (1, 'test', now(), now())");
	}
	
	public function tearDown()
	{
		CmsDatabase::drop_table('test_orm_table_child');
		CmsDatabase::drop_table('test_orm_table');
		CmsCache::clear();
	}
	
	public function testGetTableShouldKnowFancyPrefixStuff()
	{
		$this->assertEqual(CMS_DB_PREFIX . 'test_orm_table', cms_orm('test_orm_table')->get_table());
		$this->assertEqual(CMS_DB_PREFIX . 'test_orm_table.test_field', cms_orm('test_orm_table')->get_table('test_field'));
	}
	
	public function testGetColumnsInTableShouldWork()
	{
		$result = cms_orm('test_orm_table')->get_columns_in_table();
		$this->assertEqual(8, count($result));
		$this->assertEqual('int', $result['id']->type);
		$this->assertEqual('varchar', $result['test_field']->type);
		$this->assertEqual('datetime', $result['create_date']->type);
	}
	
	public function testFindAllShouldReturnAllRows()
	{
		$result = cms_orm('test_orm_table')->find_all();
		$this->assertIsA($result, 'array');
		$this->assertEqual(3, count($result));
	}
	
	public function testFindOneShouldReturnOneRow()
	{
		$result = cms_orm('test_orm_table')->find();
		$this->assertIsA($result, 'TestOrmTable');
		$this->assertEqual(1, count($result));
	}
	
	public function testFindCountShouldReturnACountDuh()
	{
		$result = cms_orm('test_orm_table')->find_count();
		$this->assertEqual(3, $result);
	}
	
	public function testDateTimeShouldBeACmsDateTimeObject()
	{
		$result = cms_orm('test_orm_table')->find();
		$this->assertNotA($result->test_field, 'CmsDateTime');
		$this->assertIsA($result->create_date, 'CmsDateTime');
		$this->assertIsA($result->modified_date, 'CmsDateTime');
	}
	
	public function testOtherFieldsShouldBeString()
	{
		$result = cms_orm('test_orm_table')->find();
		$this->assertIsA($result->test_field, 'string');
	}
	
	public function testAutoNumberingShouldWork()
	{
		$result = cms_orm('test_orm_table')->find();
		$this->assertEqual(1, $result->id);
	}
	
	public function testArrayAccessorsShouldWork()
	{
		$result = cms_orm('test_orm_table')->find();
		$this->assertEqual(1, $result->id);
		$this->assertEqual(1, $result['id']);
	}
	
	public function testDynamicFindersShouldRawk()
	{
		$result = cms_orm('test_orm_table')->find_all_by_test_field('test2');
		$this->assertEqual(1, count($result));
		$result = cms_orm('test_orm_table')->find_all_by_test_field_or_another_test_field('test2', 'blah');
		$this->assertEqual(2, count($result));
		$result = cms_orm('test_orm_table')->find_all_by_test_field_and_another_test_field('test', 'blah');
		$this->assertEqual(1, count($result));
		$result = cms_orm('test_orm_table')->find_all_by_test_field_and_another_test_field('test2', 'blah');
		$this->assertEqual(0, count($result));
		$result = cms_orm('test_orm_table')->find_by_test_field('test2');
		$this->assertEqual(1, count($result));
		$result = cms_orm('test_orm_table')->find_count_by_test_field('test2');
		$this->assertEqual(1, $result);
		$result = cms_orm('test_orm_table')->find_count_by_test_field_or_another_test_field('test2', 'blah');
		$this->assertEqual(2, $result);
		$result = cms_orm('test_orm_table')->find_count_by_test_field_and_another_test_field('test', 'blah');
		$this->assertEqual(1, $result);
		$result = cms_orm('test_orm_table')->find_count_by_test_field_and_another_test_field('test2', 'blah');
		$this->assertEqual(0, $result);
	}
	
	public function testFindByQueryShouldRawkAsWellJustNotQuiteAsHard()
	{
		$result = cms_orm('test_orm_table')->find_all_by_query("SELECT * FROM {test_orm_table} ORDER BY id ASC");
		$this->assertEqual(3, count($result));                                
		$result = cms_orm('test_orm_table')->find_all_by_query("SELECT * FROM {test_orm_table} WHERE test_field = ? ORDER BY id ASC", array('test'));
		$this->assertEqual(1, count($result));
	}
	
	public function testSaveShouldWorkAndBumpTimestampAndTheDirtyFlagShouldWork()
	{
		#Once without a change
		$result = cms_orm('test_orm_table')->find();
		$old_timestamp = $result->modified_date->timestamp();
		$result->save();
		$result = cms_orm('test_orm_table')->find();
		$this->assertEqual($old_timestamp, $result->modified_date->timestamp());
		
		#Once with
		$old_timestamp = $result->modified_date->timestamp();
		$result->test_field = 'test10';
		$result->save();
		$result = cms_orm('test_orm_table')->find();
		$this->assertNotEqual($old_timestamp, $result->modified_date->timestamp());
		$this->assertEqual('test10', $result->test_field);
	}
	
	public function testHasParameterDoesItsThing()
	{
		$result = cms_orm('test_orm_table')->find();
		$this->assertTrue($result->has_parameter('test_field'));
		$this->assertTrue($result->has_parameter('another_test_field'));
		$this->assertTrue($result->has_parameter('create_date'));
		$this->assertTrue($result->has_parameter('modified_date'));
		$this->assertFalse($result->has_parameter('i_made_this_up'));
	}
	
	public function testValidatorWillNotAllowSaves()
	{
		$result = cms_orm('test_orm_table')->find();
		$result->test_field = '';
		$result->another_test_field = '';
		$this->assertFalse($result->save());
		$result->test_field = 'test';
		$this->assertFalse($result->save());
		$result->another_test_field = 'blah';
		$this->assertTrue($result->save());
	}
	
	public function testNumericalityOfValidatorShouldActuallyWork()
	{
		$result = cms_orm('test_orm_table')->find();
		$result->some_int = '';  #We're testing numbers, not empty strings -- do another validation
		$this->assertTrue($result->save());
		$result->some_int = '5';
		$this->assertTrue($result->save());
		$result->some_int = 5;
		$this->assertTrue($result->save());
		$result->some_float = 'sdfsdfsdfsfd';
		$this->assertFalse($result->save());
		$result->some_float = '5.501';
		$this->assertTrue($result->save());
		$result->some_float = 5.501;
		$this->assertTrue($result->save());
	}
	
	public function testHasManyShouldWork()
	{
		$result = cms_orm('test_orm_table')->find_by_id(1);
		$this->assertNotNull($result);
		$this->assertEqual(1, count($result->children));
		$this->assertEqual('test', $result->children[0]->some_other_field);
	}
	
	public function testBelongsToShouldWorkAsWell()
	{
		$result = cms_orm('test_orm_table')->find_by_id(1);
		$this->assertNotNull($result);
		$this->assertEqual(1, count($result->children));
		$this->assertNotNull(count($result->children[0]->parent));
		$this->assertEqual(1, $result->children[0]->parent->id);
	}
	
	public function testDeleteShouldActuallyDelete()
	{
		$result = cms_orm('test_orm_table')->find_by_id(1);
		$this->assertNotNull($result);
		$result->delete();
		$result = cms_orm('test_orm_table')->find_all();
		$this->assertEqual(2, count($result));
	}
	
	public function testLoadCallbacksShouldGetCalled()
	{
		$result = cms_orm('test_orm_table')->find();
		$this->assertNotNull($result);
		$this->assertEqual(1, $result->counter);
	}
	
	public function testSaveCallbacksShouldGetCalled()
	{
		$result = cms_orm('test_orm_table')->find();
		$this->assertNotNull($result);
		$this->assertEqual(1, $result->counter);
		
		#First no updates -- before gets called, after doesn't
		$result->save();
		$this->assertEqual(2, $result->counter);
		
		#Now with updates -- before and after get called
		$result->test_field = 'test10';
		$result->save();
		$this->assertEqual(5, $result->counter);
	}
	
	public function testDeleteCallbacksShouldGetCalled()
	{
		$result = cms_orm('test_orm_table')->find();
		$this->assertNotNull($result);
		$this->assertEqual(1, $result->counter);
		
		$result->delete();
		$this->assertEqual(4, $result->counter);
		
		$result = cms_orm('test_orm_table')->find_all();
		$this->assertEqual(2, count($result));
	}
	
	public function testBadTransactionsShouldFail()
	{
		$db = cms_db();
		if ($db->hasTransactions)
		{
			cms_orm('test_orm_table')->begin_transaction();
			$db->Execute("INSERT INTO {test_orm_table} (test_field, another_test_field, some_int, some_float, create_date, modified_date) VALUES ('test good', 'blah', 5, 5.501, now() - 10, now() - 10)"); //Good SQL
			try {
				@$db->Execute("INSERT INTO {test_orm_table} (test_field, another_test_field, some_int, some_float, create_date, modified_date) VALUES ('test bad', 'blah', 5, 5.501, now() - 10, now() - 10, 'kjk')"); //Bad SQL
			}
			catch (Exception $e) {}
			$this->assertFalse(cms_orm('test_orm_table')->complete_transaction());
			$this->assertNull(cms_orm('test_orm_table')->find_by_test_field('test good'));
		}
	}
	
	public function testGoodTransactionsShouldWork()
	{
		$db = cms_db();
		if ($db->hasTransactions)
		{
			cms_orm('test_orm_table')->begin_transaction();
			$db->Execute("INSERT INTO {test_orm_table} (test_field, another_test_field, some_int, some_float, create_date, modified_date) VALUES ('test good', 'blah', 5, 5.501, now() - 10, now() - 10)"); //Good SQL
			$this->assertTrue(cms_orm('test_orm_table')->complete_transaction());
			$this->assertNotNull(cms_orm('test_orm_table')->find_by_test_field('test good'));
		}
	}
	
}

class TestOrmTable extends CmsObjectRelationalMapping
{
	var $counter = 0;

	public function setup()
	{
		$this->create_has_many_association('children', 'TestOrmTableChild', 'parent_id');
		//$this->assign_acts_as('Versioned');
	}

	public function validate()
	{
		$this->validate_not_blank('test_field');
		if (strlen($this->another_test_field) == 0)
		{
			$this->add_validation_error('can\'t be blank');
		}
		$this->validate_numericality_of('some_int');
		$this->validate_numericality_of('some_float');
	}
	
	public function after_load()
	{
		$this->counter++;
	}
	
	public function before_save()
	{
		$this->counter++;
	}
	
	public function after_save()
	{
		$this->counter++;
		$this->counter++;
	}
	
	public function before_delete()
	{
		$this->counter++;
	}
	
	public function after_delete()
	{
		$this->counter++;
		$this->counter++;
	}
}

class TestOrmTableChild extends CmsObjectRelationalMapping
{	
	public function setup()
	{
		$this->create_belongs_to_association('parent', 'test_orm_table', 'parent_id');
	}
}

?>