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

include_once(dirname(dirname(__FILE__)) . '/lib/cmsms.api.php');

class DescribeCmsUserTags extends PHPSpec_Context
{
	public function before()
	{
		CmsCache::clear();
		$cms_db_prefix = CMS_DB_PREFIX;
		cms_db()->Execute("DELETE FROM {$cms_db_prefix}userplugins WHERE userplugin_name = ?", array('TestTag'));
	}
	
	public function after()
	{
		$this->before();
	}
	
	public function create_user_tag()
	{
		return CmsUserTagOperations::set_user_tag('TestTag', "return \$params[0];\n");
	}
	
	public function select_count()
	{
		return cms_orm('CmsUserTag')->find_count_by_name('TestTag');
	}
	
	public function itShouldCreateUserTag()
	{
		$this->spec($this->create_user_tag())->should->beTrue();
		$this->spec($this->select_count())->should->be(1);
	}
	
	public function itShouldNotAllowDuplicates()
	{
		$this->spec($this->create_user_tag())->should->beTrue();
		$this->spec($this->select_count())->should->be(1);
		$this->spec($this->create_user_tag())->should->beTrue();
		$this->spec($this->select_count())->should->be(1);
	}
	
	public function itShouldProperlyDeleteATag()
	{
		$this->spec($this->create_user_tag())->should->beTrue();
		$this->spec($this->select_count())->should->be(1);
		$this->spec(CmsUserTagOperations::remove_user_tag('TestTag'))->should->beTrue();
		$this->spec($this->select_count())->should->be(0);
	}
	
	public function itShouldBeAbleToCallATag()
	{
		$this->spec($this->create_user_tag())->should->beTrue();
		$this->spec($this->select_count())->should->be(1);
		$params = array('TestValue');
		$this->spec(CmsUserTagOperations::call_user_tag('TestTag', $params))->should->beEqualTo('TestValue');
	}

	public function itShouldBeAbleToCallATagFromTheOrm()
	{
		$this->spec($this->create_user_tag())->should->beTrue();
		$this->spec($this->select_count(), 1)->should->be(1);
		$user_tag = cms_orm('CmsUserTag')->find_by_name('TestTag');
		$this->spec($user_tag)->shouldNot->beNull();
		$params = array('TestValue');
		$this->spec($user_tag->call($params))->should->beEqualTo('TestValue');
	}
}

# vim:ts=4 sw=4 noet
?>