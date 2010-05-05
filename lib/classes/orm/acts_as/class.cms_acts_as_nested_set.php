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

/**
 * Class to easily add nested set functionality to your ORM class.
 * In order to use this, do the following:
 * 1. Add interger fields named lft, rgt, parent_id and item_order to your table.
 * 2. Initialize lft, rgt, parent_id and item_order in your orm class.  
 *    ex. var $params = array('lft' => 1, 'rgt' => 1, 'parent_id' => -1, 'item_order' => 0);
 * 3. Optionally, create a "dummy" root item with lft = 1, rgt = 2, parent_id = -1 and item_oder = 1,
 *    depending on how you want to treat the root of the tree.
 *
 * @package default
 * @author Ted Kulp
 **/
class CmsActsAsNestedSet extends CmsActsAs
{
	function __construct()
	{
		parent::__construct();
	}
	
	function after_save(&$obj, &$result)
	{
		$result = $obj->complete_transaction();
	}
	
	function before_save(&$obj)
	{
		//$this->prop_names = implode(',', $this->get_loaded_property_names());
		$db = cms_db();
		$table_name = $obj->get_table();
		
		$obj->begin_transaction();

		if ($obj->id == -1)
		{
			$this->make_space_for_child($obj);
		}
		else
		{
			$old_content = $obj->find_by_id($obj->id);
			if ($old_content)
			{
				if ($obj->parent_id != $old_content->parent_id)
				{
					//TODO: Make this whole bit a transaction (after_save included)

					//First reorder
					$query = "UPDATE {$table_name} SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
					$result = $db->Execute($query, array($old_content->parent_id, $old_content->item_order));
					
					//Flip all children over to the negative side so they don't get in the way
					$query = "UPDATE {$table_name} SET lft = (lft * -1), rgt = (rgt * -1) WHERE lft > ? AND rgt < ?";
					$result = $db->Execute($query, array($old_content->lft, $old_content->rgt));
					
					//Then move lft and rgt so that this node doesn't "exist"
					$diff = $old_content->rgt - $old_content->lft + 1;

					$query = "UPDATE {$table_name} SET lft = (lft - ?) WHERE lft > ?";
					$result = $db->Execute($query, array($diff, $old_content->lft));
					
					$query = "UPDATE {$table_name} SET rgt = (rgt - ?) WHERE rgt > ?";
					$result = $db->Execute($query, array($diff, $old_content->rgt));
					
					//Now make a new hole under the new child
					$this->make_space_for_child($obj);
					
					//Update the ones currently in the negative space the distance that we've moved
					$moved_by_how_much = $old_content->lft - $obj->lft;
					$query = "UPDATE {$table_name} SET lft = (lft + ?), rgt = (rgt + ?) WHERE lft < 0 AND rgt < 0";
					$result = $db->Execute($query, array($moved_by_how_much, $moved_by_how_much));
					
					//And flip those back over to the positive side...  hopefully in the correct place now
					$query = "UPDATE {$table_name} SET lft = (lft * -1), rgt = (rgt * -1) WHERE lft < 0 AND rgt < 0";
					$result = $db->Execute($query);
				}
			}
		}
	}
	
	function make_space_for_child(&$obj)
	{
		$db = cms_db();
		$table_name = $obj->get_table();

		$diff = $obj->rgt - $obj->lft;
		if ($diff < 2)
		{
			$diff = 1;
		}
		
		$right = $db->GetOne("SELECT max(rgt) FROM {$table_name}");
		
		if ($obj->parent_id > -1)
		{
			$row = $db->GetRow("SELECT rgt FROM {$table_name} WHERE {$obj->id_field} = ?", array($obj->parent_id));
			if ($row)
			{
				$right = $row['rgt'];
			}

			$db->Execute("UPDATE {$table_name} SET lft = lft + ? WHERE lft > ?", array($diff + 1, $right));
			$db->Execute("UPDATE {$table_name} SET rgt = rgt + ? WHERE rgt >= ?", array($diff + 1, $right));
		}
		else
		{
			if ($obj->rgt != $right) //We're already at the max -- don't want to increase it
				$right = $right + $diff;
		}
		
		$obj->lft = $right;
		$obj->rgt = $right + $diff;
		
		$query = "SELECT max(item_order) as new_order FROM {$table_name} WHERE parent_id = ?";
		$row = $db->GetRow($query, array($obj->parent_id));
		if ($row)
		{
			if ($row['new_order'] < 1)
			{
				$obj->item_order = 1;
			}
			else
			{
				$obj->item_order = $row['new_order'] + 1;
			}
		}
	}
	
	function after_delete(&$obj)
	{
		$table_name = $obj->get_table();

		#Fix the item_order if necessary
		$query = "UPDATE {$table_name} SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
		$result = cms_db()->Execute($query, array($obj->parent_id, $obj->item_order));
		
		#And fix the lft, rgt on items after this one
		$query = "UPDATE {$table_name} SET lft = lft - 2 WHERE lft > ?";
		$result = cms_db()->Execute($query, array($obj->lft));
		
		$query = "UPDATE {$table_name} SET rgt = rgt - 2 WHERE rgt > ?";
		$result = cms_db()->Execute($query, array($obj->rgt));
		
		CmsCache::clear('orm');
	}
	
	function move_up(&$obj)
	{
		$this->shift_position($obj, 'up');
	}
	
	function move_down(&$obj)
	{
		$this->shift_position($obj, 'down');
	}
	
	function shift_position(&$obj, $direction = 'up')
	{
		$new_item_order = $obj->item_order;
		if ($direction == 'up')
			$new_item_order--;
		else
			$new_item_order++;

		$other_content = $obj->find_by_parent_id_and_item_order($obj->parent_id, $new_item_order);
		
		if ($other_content != null)
		{
			$db = cms_db();
			$table_name = $obj->get_table();
			
			$old_lft = $other_content->lft;
			$old_rgt = $other_content->rgt;
			
			//Assume down
			$diff = $obj->lft - $old_lft;
			$diff2 = $obj->rgt - $old_rgt;

			if ($direction == 'up')
			{
				//Now up
				$diff = $obj->rgt - $old_rgt;
				$diff2 = $obj->lft - $old_lft;
			}
			
			$time = $db->DBTimeStamp(time());
			
			//Flip me and children into the negative space
			$query = "UPDATE {$table_name} SET lft = (lft * -1), rgt = (rgt * -1), modified_date = {$time} WHERE lft >= ? AND rgt <= ?";
			$db->Execute($query, array($obj->lft, $obj->rgt));
			
			//Shift the other content to the new position
			$query = "UPDATE {$table_name} SET lft = (lft + ?), rgt = (rgt + ?), modified_date = {$time} WHERE lft >= ? AND rgt <= ?";
			$db->Execute($query, array($diff, $diff, $old_lft, $old_rgt));
			
			//Shift me to the new position in the negative space
			$query = "UPDATE {$table_name} SET lft = (lft + ?), rgt = (rgt + ?), modified_date = {$time} WHERE lft < 0 AND rgt < 0";
			$db->Execute($query, array($diff2, $diff2));
			
			//Flip me back over to the positive side...  hopefully in the correct place now
			$query = "UPDATE {$table_name} SET lft = (lft * -1), rgt = (rgt * -1), modified_date = {$time} WHERE lft < 0 AND rgt < 0";
			$result = $db->Execute($query);
			
			//Now flip the item orders
			$query = "UPDATE {$table_name} SET item_order = ?, modified_date = {$time} WHERE {$obj->id_field} = ?";
			$db->Execute($query, array($other_content->item_order, $obj->id));
			$db->Execute($query, array($obj->item_order, $other_content->id));
			
			$obj->lft = $obj->lft - $diff2;
			$obj->rgt = $obj->rgt - $diff2;
			
			$obj->item_order = $other_content->item_order;
			
			CmsCache::clear('orm');
		}
	}
	
	function move_before(&$obj, $target_obj = null)
	{
		return $this->move_before_or_after($obj, $target_obj, "before");
	}
	
	function move_after(&$obj, $target_obj = null)
	{
		return $this->move_before_or_after($obj, $target_obj, "after");
	}
	
	function move_before_or_after(&$obj, $target_obj = null, $dir = "after")
	{
		/*
			1
				2-3
			4
			5-6
			
			becomes
			
			1-2
			3-4 (was 2-3)
			5-6
			
		*/
		if (!$target_obj)
			return false;
		
		if ($dir != 'after' && $dir != 'before')
			return false;
		
		$db = cms_db();
		$table_name = $obj->get_table();
		
		//$obj->begin_transaction();
		
		//Moving 7-8 before 4-5
		
		$diff = $obj->rgt - $obj->lft;  //1
		$time = $db->DBTimeStamp(time());
		
		//Flip obj and children into the negative space
		$query = "UPDATE {$table_name} SET lft = (lft * -1), rgt = (rgt * -1), modified_date = {$time} WHERE lft >= ? AND rgt <= ?";
		$db->Execute($query, array($obj->lft, $obj->rgt)); //2 becomes -2, 3 becomes -3
		
		//Close up the space
		$query = "UPDATE {$table_name} SET lft = (lft - ?), modified_date = {$time} WHERE lft >= ?"; //5 becomes 3
		$db->Execute($query, array($diff + 1, $obj->lft));
		$query = "UPDATE {$table_name} SET rgt = (rgt - ?), modified_date = {$time} WHERE rgt >= ?"; //4 becomes 2, 6 becomes 4
		$db->Execute($query, array($diff + 1, $obj->rgt));
		
		if ($target_obj->lft > $obj->lft) //Doesn't get called in our example, but...
		{
			$target_obj->lft = $target_obj->lft - ($diff + 1); //4 becomes 2
		}
		
		if ($target_obj->rgt > $obj->rgt)
		{
			$target_obj->rgt = $target_obj->rgt - ($diff + 1); //5 becomes 3
		}
		
		if ($obj->parent_id == $target_obj->parent_id && $target_obj->item_order > $obj->item_order) //Shouldn't get called
		{
			$target_obj->item_order = $target_obj->item_order - 1;
		}
		
		//Fix item order
		$query = "UPDATE {$table_name} SET item_order = item_order - 1 WHERE parent_id = ? AND item_order >= ?";
		$db->Execute($query, array($obj->parent_id, $obj->item_order));

		//Init the variables -- assume after
		$new_lft = $target_obj->rgt + 1; //3 + 1 = 4 // 2 + 1 = 3
		$new_rgt = $new_lft + $diff; //4 + 1 = 5 // 3 + 1 = 4
		$dist_to_move = $new_lft - $obj->lft; //4 - 2 = 2 // 3 - 2 = 1

		if ($dir == "before")
		{
			$new_lft = $target_obj->lft; //4
			$new_rgt = $target_obj->lft + $diff; //4 + 1 = 5
			$dist_to_move = $new_lft - $obj->lft; //4 - 6 = -2
		}
		
		/*
			(-2--3 => 3-4)
			1-2
			3-4
		*/
		
		//Open up the new space
		$query = "UPDATE {$table_name} SET lft = (lft + ?), modified_date = {$time} WHERE lft >= ?"; //3 becomes 5
		$db->Execute($query, array($diff + 1, $new_lft));
		$query = "UPDATE {$table_name} SET rgt = (rgt + ?), modified_date = {$time} WHERE rgt >= ?"; //4 becomes 6
		$db->Execute($query, array($diff + 1, $new_lft));
		
		//Shift to the new position in the negative space
		$query = "UPDATE {$table_name} SET lft = (lft - ?), rgt = (rgt - ?), modified_date = {$time} WHERE lft < 0 AND rgt < 0";
		$db->Execute($query, array($dist_to_move, $dist_to_move)); //-2 becomes -3, -3 becomes -4
		
		//Flip back over to the positive side...  hopefully in the correct place now
		$query = "UPDATE {$table_name} SET lft = (lft * -1), rgt = (rgt * -1), modified_date = {$time} WHERE lft < 0 AND rgt < 0";
		$db->Execute($query); //-3 becomes 3, -4 becomes 4
		
		$obj->parent_id = $target_obj->parent_id;
		if ($dir == "before")
			$obj->item_order = $target_obj->item_order;
		else
			$obj->item_order = $target_obj->item_order + 1;
		
		//Fix item order
		$query = "UPDATE {$table_name} SET item_order = item_order + 1 WHERE parent_id = ? AND item_order >= ?";
		$db->Execute($query, array($obj->parent_id, $obj->item_order));
		
		//And set values in obj in the database -- skip the save, since it could cause trouble
		$query = "UPDATE {$table_name} SET item_order = ?, parent_id = ?, modified_date = {$time} WHERE {$obj->id_field} = ?";
		$db->Execute($query, array($obj->item_order, $obj->parent_id, $obj->id));
		
		CmsCache::clear('orm');
		
		$test_obj = $obj->find_by_id($obj->id);
		
		$result = ($test_obj->lft == $new_lft && $test_obj->rgt == $new_rgt && $test_obj->parent_id = $obj->parent_id);
		
		/*
		if ($result)
		{
			if ($obj->complete_transaction())
				CmsPage::set_all_hierarchy_positions($new_lft < $target_obj->lft ? $new_lft : $target_obj->lft);
			else
				$result = false;
		}
		else
		{
			$obj->fail_transaction();
		}
		*/
		
		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>