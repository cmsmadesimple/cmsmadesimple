<?php
require_once(dirname(__FILE__).'/simpletest/autorun.php');
include_once(dirname(dirname(__FILE__)) . '/lib/cmsms.api.php');

class CmsNestedSetTest extends UnitTestCase
{
	public function setUp()
	{
		$this->tearDown();
		CmsDatabase::create_table('test_nested_set_orm_table', "
			id I KEY AUTO,
			title C(255),
			version I,
			lft I,
			rgt I,
			item_order I,
			parent_id I,
			create_date T,
			modified_date T
		");
	}
	
	public function tearDown()
	{
		CmsDatabase::drop_table('test_nested_set_orm_table');
		CmsCache::clear();
	}
	
	public function create_node($title, $parent_id = -1)
	{
		$obj = new TestNestedSetOrmTable();
		$obj->title = $title;
		$obj->parent_id = $parent_id;
		$obj->save();
	}
	
	public function assertNodeValues($obj, $lft, $rgt, $parent_id, $item_order)
	{
		$this->assertEqual($lft, $obj->lft);
		$this->assertEqual($rgt, $obj->rgt);
		$this->assertEqual($parent_id, $obj->parent_id);
		$this->assertEqual($item_order, $obj->item_order);
	}
	
	public function testCreateMultipleNodes()
	{
		$this->create_node('node 1');
		$this->create_node('node 2');
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(2, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertEqual(1, $node1->lft);
		$this->assertEqual(2, $node1->rgt);
		$this->assertEqual(-1, $node1->parent_id);
		$this->assertEqual(1, $node1->item_order);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertEqual(3, $node2->lft);
		$this->assertEqual(4, $node2->rgt);
		$this->assertEqual(-1, $node2->parent_id);
		$this->assertEqual(2, $node2->item_order);
	}
	
	public function testCreateChildNode()
	{
		$this->create_node('node 1');
		$this->create_node('node 2', 1);
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(2, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertEqual(1, $node1->lft);
		$this->assertEqual(4, $node1->rgt);
		$this->assertEqual(-1, $node1->parent_id);
		$this->assertEqual(1, $node1->item_order);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertEqual(2, $node2->lft);
		$this->assertEqual(3, $node2->rgt);
		$this->assertEqual(1, $node2->parent_id);
		$this->assertEqual(1, $node2->item_order);
	}
	
	public function testCreateAHappyLittleTreeOverThere()
	{
		$this->create_node('node 1');
		$this->create_node('node 2');
		$this->create_node('node 1.1', 1);
		$this->create_node('node 1.2', 1);
		$this->create_node('node 2.1', 2);
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(5, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertEqual(1, $node1->lft);
		$this->assertEqual(6, $node1->rgt);
		$this->assertEqual(-1, $node1->parent_id);
		$this->assertEqual(1, $node1->item_order);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertEqual(7, $node2->lft);
		$this->assertEqual(10, $node2->rgt);
		$this->assertEqual(-1, $node2->parent_id);
		$this->assertEqual(2, $node2->item_order);
		
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertEqual(2, $node1_1->lft);
		$this->assertEqual(3, $node1_1->rgt);
		$this->assertEqual(1, $node1_1->parent_id);
		$this->assertEqual(1, $node1_1->item_order);
		
		$node1_2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.2');
		$this->assertEqual(4, $node1_2->lft);
		$this->assertEqual(5, $node1_2->rgt);
		$this->assertEqual(1, $node1_2->parent_id);
		$this->assertEqual(2, $node1_2->item_order);
		
		$node2_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2.1');
		$this->assertEqual(8, $node2_1->lft);
		$this->assertEqual(9, $node2_1->rgt);
		$this->assertEqual(2, $node2_1->parent_id);
		$this->assertEqual(1, $node2_1->item_order);
	}
	
	public function testMoveNodeToBeAChildOfAnotherNode()
	{
		$this->create_node('node 1');
		$this->create_node('node 2');
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(2, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertEqual(1, $node1->lft);
		$this->assertEqual(2, $node1->rgt);
		$this->assertEqual(-1, $node1->parent_id);
		$this->assertEqual(1, $node1->item_order);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertEqual(3, $node2->lft);
		$this->assertEqual(4, $node2->rgt);
		$this->assertEqual(-1, $node2->parent_id);
		$this->assertEqual(2, $node2->item_order);
		
		$node2->parent_id = $node1->id; //It's 1
		$node2->save();
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertEqual(1, $node1->lft);
		$this->assertEqual(4, $node1->rgt);
		$this->assertEqual(-1, $node1->parent_id);
		$this->assertEqual(1, $node1->item_order);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertEqual(2, $node2->lft);
		$this->assertEqual(3, $node2->rgt);
		$this->assertEqual(1, $node2->parent_id);
		$this->assertEqual(1, $node2->item_order);
		
		$node2->parent_id = -1; //Now move it back to being a parent
		$node2->save();
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertEqual(1, $node1->lft);
		$this->assertEqual(2, $node1->rgt);
		$this->assertEqual(-1, $node1->parent_id);
		$this->assertEqual(1, $node1->item_order);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertEqual(3, $node2->lft);
		$this->assertEqual(4, $node2->rgt);
		$this->assertEqual(-1, $node2->parent_id);
		$this->assertEqual(2, $node2->item_order);
	}
	
	public function testMoveMultipleChildNodesBackToRoot()
	{
		$this->create_node('node 1');
		$this->create_node('node 1.1', 1);
		$this->create_node('node 1.2', 1);
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(3, count($result));
		
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertEqual(2, $node1_1->lft);
		$this->assertEqual(3, $node1_1->rgt);
		$this->assertEqual(1, $node1_1->parent_id);
		$this->assertEqual(1, $node1_1->item_order);
		
		$node1_1->parent_id = -1;
		$node1_1->save();
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertEqual(1, $node1->lft);
		$this->assertEqual(4, $node1->rgt);
		$this->assertEqual(-1, $node1->parent_id);
		$this->assertEqual(1, $node1->item_order);
		
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertEqual(5, $node1_1->lft);
		$this->assertEqual(6, $node1_1->rgt);
		$this->assertEqual(-1, $node1_1->parent_id);
		$this->assertEqual(2, $node1_1->item_order);
		
		$node1_2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.2');
		$this->assertEqual(2, $node1_2->lft);
		$this->assertEqual(3, $node1_2->rgt);
		$this->assertEqual(1, $node1_2->parent_id);
		$this->assertEqual(1, $node1_2->item_order);
		
		$node1_2->parent_id = -1;
		$node1_2->save();
		
		$node1_2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.2');
		$this->assertEqual(5, $node1_2->lft);
		$this->assertEqual(6, $node1_2->rgt);
		$this->assertEqual(-1, $node1_2->parent_id);
		$this->assertEqual(3, $node1_2->item_order);
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertEqual(1, $node1->lft);
		$this->assertEqual(2, $node1->rgt);
		$this->assertEqual(-1, $node1->parent_id);
		$this->assertEqual(1, $node1->item_order);
		
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertEqual(3, $node1_1->lft);
		$this->assertEqual(4, $node1_1->rgt);
		$this->assertEqual(-1, $node1_1->parent_id);
		$this->assertEqual(2, $node1_1->item_order);
	}
	
	public function testMoveItemDown()
	{
		$this->create_node('node 1');
		$this->create_node('node 2');
		$this->create_node('node 3');
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(3, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 1, 2, -1, 1);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 3, 4, -1, 2);
		
		$node3 = cms_orm('test_nested_set_orm_table')->find_by_title('node 3');
		$this->assertNodeValues($node3, 5, 6, -1, 3);
		
		$node1->move_down();
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 3, 4, -1, 2);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 1, 2, -1, 1);
		
		$node3 = cms_orm('test_nested_set_orm_table')->find_by_title('node 3');
		$this->assertNodeValues($node3, 5, 6, -1, 3);
		
		$node1->move_down();
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 5, 6, -1, 3);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 1, 2, -1, 1);
		
		$node3 = cms_orm('test_nested_set_orm_table')->find_by_title('node 3');
		$this->assertNodeValues($node3, 3, 4, -1, 2);
		
		$node1->move_down(); //Make sure we don't fall off the bottom
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 5, 6, -1, 3);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 1, 2, -1, 1);
		
		$node3 = cms_orm('test_nested_set_orm_table')->find_by_title('node 3');
		$this->assertNodeValues($node3, 3, 4, -1, 2);
	}
	
	public function testMoveItemUp()
	{
		$this->create_node('node 1');
		$this->create_node('node 2');
		$this->create_node('node 3');
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(3, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 1, 2, -1, 1);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 3, 4, -1, 2);
		
		$node3 = cms_orm('test_nested_set_orm_table')->find_by_title('node 3');
		$this->assertNodeValues($node3, 5, 6, -1, 3);
		
		$node3->move_up();
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 1, 2, -1, 1);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 5, 6, -1, 3);
		
		$node3 = cms_orm('test_nested_set_orm_table')->find_by_title('node 3');
		$this->assertNodeValues($node3, 3, 4, -1, 2);
		
		$node3->move_up();
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 3, 4, -1, 2);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 5, 6, -1, 3);
		
		$node3 = cms_orm('test_nested_set_orm_table')->find_by_title('node 3');
		$this->assertNodeValues($node3, 1, 2, -1, 1);
		
		$node3->move_up(); //Make sure we don't go above the top
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 3, 4, -1, 2);
		
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 5, 6, -1, 3);
		
		$node3 = cms_orm('test_nested_set_orm_table')->find_by_title('node 3');
		$this->assertNodeValues($node3, 1, 2, -1, 1);
	}
	
	public function testMoveItemBefore()
	{
		$this->create_node('node 1');
		$this->create_node('node 1.1', 1);
		$this->create_node('node 2');
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(3, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		
		$node1_1->move_before($node2);
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 1, 2, -1, 1);
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertNodeValues($node1_1, 3, 4, -1, 2);
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 5, 6, -1, 3);
	}
	
	public function testMoveItemBeforeChild()
	{
		$this->create_node('node 1');
		$this->create_node('node 1.1', 1);
		$this->create_node('node 2');
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(3, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		
		$node2->move_before($node1_1);
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 1, 6, -1, 1);
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertNodeValues($node1_1, 4, 5, 1, 2);
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 2, 3, 1, 1);
	}
	
	public function testMoveItemBeforeTopItem()
	{
		$this->create_node('node 1');
		$this->create_node('node 1.1', 1);
		$this->create_node('node 2');
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(3, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		
		$node1_1->move_before($node1);
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 3, 4, -1, 2);
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertNodeValues($node1_1, 1, 2, -1, 1);
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 5, 6, -1, 3);
	}
	
	public function testMoveItemAfter()
	{
		$this->create_node('node 1');
		$this->create_node('node 1.1', 1);
		$this->create_node('node 2');
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(3, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 1, 4, -1, 1);
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertNodeValues($node1_1, 2, 3, 1, 1);
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		
		$node1_1->move_after($node1);
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 1, 2, -1, 1);
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertNodeValues($node1_1, 3, 4, -1, 2);
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 5, 6, -1, 3);
	}
	
	public function testMoveItemAfterChild()
	{
		$this->create_node('node 1');
		$this->create_node('node 1.1', 1);
		$this->create_node('node 2');
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(3, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		
		$node2->move_after($node1_1);
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 1, 6, -1, 1);
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertNodeValues($node1_1, 2, 3, 1, 1);
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 4, 5, 1, 2);
	}
	
	public function testMoveItemAfterBottomItem()
	{
		$this->create_node('node 1');
		$this->create_node('node 1.1', 1);
		$this->create_node('node 2');
		
		$result = cms_orm('test_nested_set_orm_table')->find_all();
		$this->assertEqual(3, count($result));
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		
		$node1_1->move_after($node2);
		
		$node1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1');
		$this->assertNodeValues($node1, 1, 2, -1, 1);
		$node1_1 = cms_orm('test_nested_set_orm_table')->find_by_title('node 1.1');
		$this->assertNodeValues($node1_1, 5, 6, -1, 3);
		$node2 = cms_orm('test_nested_set_orm_table')->find_by_title('node 2');
		$this->assertNodeValues($node2, 3, 4, -1, 2);
	}
}

class TestNestedSetOrmTable extends CmsObjectRelationalMapping
{
	var $params = array('id' => -1, 'lft' => 1, 'rgt' => 1, 'parent_id' => -1, 'item_order' => 0);
	
	public function setup()
	{
		$this->assign_acts_as('NestedSet');
	}
}