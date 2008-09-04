<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

require_once 'PHPUnit/Framework/TestCase.php';
include_once(dirname(dirname(__FILE__)) . '/lib/cmsms.api.php');

class CmsTagTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		CmsCache::clear();
		@CmsDatabase::drop_table('test_taggable_table');
		$cms_db_prefix = CMS_DB_PREFIX;
		CmsDatabase::create_table('test_taggable_table', "
			id I KEY AUTO,
			name C(255),
			tags C(255),
			create_date T,
			modified_date T
		");
		cms_db()->Execute("DELETE FROM {$cms_db_prefix}tag_objects WHERE type = ?", array('TestTaggableTable'));
		cms_db()->Execute("DELETE FROM {$cms_db_prefix}tags WHERE name = ?", array('test'));
		cms_db()->Execute("DELETE FROM {$cms_db_prefix}tags WHERE name = ?", array('test2'));
		cms_db()->Execute("DELETE FROM {$cms_db_prefix}tags WHERE name = ?", array('test3'));
	}
	
	public function tearDown()
	{
		CmsDatabase::drop_table('test_taggable_table');
		CmsCache::clear();
	}
	
	public function testParseTagsWorksProperly()
	{
		$this->assertEquals(CmsTag::parse_tags(''), array());
		$this->assertEquals(CmsTag::parse_tags('TeSt Test2'), array("test", "test2"));
		$this->assertEquals(CmsTag::parse_tags('test test2 test3'), array("test", "test2", "test3"));
		$this->assertEquals(CmsTag::parse_tags('test,test2,test3'), array("test", "test2", "test3"));
		$this->assertEquals(CmsTag::parse_tags('test, test2, test3'), array("test", "test2", "test3"));
		$this->assertEquals(CmsTag::parse_tags('test "test2" test3'), array("test", "test2", "test3"));
		$this->assertEquals(CmsTag::parse_tags('test "test2 test3"'), array("test", "test2 test3"));
		$this->assertEquals(CmsTag::parse_tags('test, "test2 test3"'), array("test", "test2 test3"));
		$this->assertEquals(CmsTag::parse_tags('test, "test2, test3"'), array("test", "test2, test3"));
		$this->assertEquals(CmsTag::parse_tags('test "test2\'s test3"'), array("test", "test2's test3"));
	}
	
	public function testTagsAsSavedAndQueriedProperly()
	{
		$obj = new TestTaggableTable;
		$this->assertNotNull($obj);
		$obj->tags = 'test test2 TEST3';
		$obj->save();
		$this->assertEquals(CmsTag::get_tagged_object_count("test"), 1);
		$this->assertEquals(count(CmsTag::get_tagged_objects("test")), 1);
		$ary = CmsTag::get_tagged_objects("test");
		$this->assertEquals($ary[0]->id, $obj->id);
		$this->assertEquals(CmsTag::get_tagged_object_count("test2"), 1);
		$this->assertEquals(count(CmsTag::get_tagged_objects("test2")), 1);
		$ary = CmsTag::get_tagged_objects("test2");
		$this->assertEquals($ary[0]->id, $obj->id);
		$this->assertEquals(CmsTag::get_tagged_object_count("test3"), 1);
		$this->assertEquals(count(CmsTag::get_tagged_objects("test3")), 1);
		$ary = CmsTag::get_tagged_objects("test3");
		$this->assertEquals($ary[0]->id, $obj->id);
		$this->assertEquals(CmsTag::get_tagged_object_count("blah"), 0);
		$this->assertEquals(count(CmsTag::get_tagged_objects("blah")), 0);
	}
	
	public function testTagsAreRemovedProperly()
	{
		$obj = new TestTaggableTable;
		$this->assertNotNull($obj);
		$obj->tags = 'test test2 test3';
		$obj->save();
		$this->assertEquals(CmsTag::get_tagged_object_count("test3"), 1);
		$obj->tags = 'test test2';
		$obj->save();
		$this->assertEquals(CmsTag::get_tagged_object_count("test3"), 0);
	}
	
	public function testRoundTrip()
	{
		$obj = new TestTaggableTable;
		$this->assertNotNull($obj);
		$obj->name = 'Test Object';
		$obj->tags = 'test test2';
		$obj->save();

		$ary = CmsTag::get_tagged_objects("test2");
		$this->assertEquals(count($ary), 1);
		
		$this->assertEquals(get_class($ary[0]), 'TestTaggableTable');
		$this->assertEquals($ary[0]->name, 'Test Object');
		
		$obj->delete();
		
		$ary = CmsTag::get_tagged_objects("test2");
		$this->assertEquals(count($ary), 0);
	}
}

class TestTaggableTable extends CmsObjectRelationalMapping
{
	public function setup()
	{
		$this->assign_acts_as('Taggable');
	}
}

# vim:ts=4 sw=4 noet
?>