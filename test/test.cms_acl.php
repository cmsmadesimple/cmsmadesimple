<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
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

class TestCmsAcl extends UnitTestCase
{

	function setUp()
	{
		cms_db()->Execute('DELETE FROM ' . cms_db_prefix() . 'group_permissions');
	}
	
	function test_no_permissions()
	{
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1');
		$this->assertFalse($thepage->check_permission(-1));
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1.1');
		$this->assertFalse($thepage->check_permission(-1));		
	}

	function test_user_has_permissions_on_root()
	{
		$this->setup_row(3, 1, 1, 1);

		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1');
		$this->assertTrue($thepage->check_permission(-1));
	}
	
	function test_user_has_permissions_on_root_no_permission_on_1()
	{
		$this->setup_row(3, 1, 1, 1);
		$this->setup_row(3, 1, 2, 0);

		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1');
		$this->assertFalse($thepage->check_permission(-1));
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1.1');
		$this->assertFalse($thepage->check_permission(-1));
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('2');
		$this->assertTrue($thepage->check_permission(-1));
	}
	
	function test_user_has_permissions_on_root_no_permissions_on_1_has_permission_on_1_2()
	{
		$this->setup_row(3, 1, 1, 1);
		$this->setup_row(3, 1, 2, 0);
		$this->setup_row(3, 1, 4, 1);

		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1');
		$this->assertFalse($thepage->check_permission(-1));
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1.1');
		$this->assertFalse($thepage->check_permission(-1));
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1.2');
		$this->assertTrue($thepage->check_permission(-1));
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('2');
		$this->assertTrue($thepage->check_permission(-1));
	}
	
	function test_everyone_has_permissions_on_root_user_has_no_permissions_on_1()
	{
		$this->setup_row(3, -1, 1, 1);
		$this->setup_row(3, 1, 2, 0);
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1');
		$this->assertFalse($thepage->check_permission(-1));
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1.1');
		$this->assertFalse($thepage->check_permission(-1));
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('2');
		$this->assertTrue($thepage->check_permission(-1));
	}
	
	function test_everyone_has_no_permissions_on_root_user_has_permissions_on_root()
	{
		$this->setup_row(3, -1, 1, 0);
		$this->setup_row(3, 1, 1, 1);
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1');
		$this->assertTrue($thepage->check_permission(-1));
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('1.1');
		$this->assertTrue($thepage->check_permission(-1));
		
		$thepage = cms_orm()->cms_content_base->find_by_hierarchy('2');
		$this->assertTrue($thepage->check_permission(-1));
	}
	
	function setup_row($permission_defn_id, $group_id, $object_id, $has_access)
	{
		cms_db()->Execute('INSERT INTO ' . cms_db_prefix() . 'group_permissions (permission_defn_id, group_id, object_id, has_access) VALUES (?,?,?,?)', array($permission_defn_id, $group_id, $object_id, $has_access));
	}

}

# vim:ts=4 sw=4 noet
?>