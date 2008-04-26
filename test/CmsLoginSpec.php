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

class DescribeCmsLogin extends PHPSpec_Context
{
	var $callback_test = false;
	var $originator = '';
	var $event_name = '';

	public function before()
	{
		CmsCache::clear();
		cms_db()->Execute('DELETE FROM ' . cms_db_prefix() . 'users WHERE username = ?', array('sometestuser'));
	}
	
	public function after()
	{
		$this->before();
	}
	
	public function add_test_user()
	{
		$user = new CmsUser();
		$user->username = 'sometestuser';
		$user->set_password('some_password');
		return $user->save();
	}
	
	function temp_callback($originator, $event_name, &$params)
	{
		$this->originator = $originator;
		$this->event_name = $event_name;
		$this->callback_test = $this->callback_test + 1;
	}

	public function itShouldBeAbleToSaveANewUser()
	{
		$this->spec($this->add_test_user())->should->beTrue();
	}
	
	public function itShouldNotBeAbleToLogInWithBadPassword()
	{
		$this->add_test_user();
		$this->spec(CmsLogin::login('sometestuser', 'some_bad_password'))->should->beFalse();
		$this->spec(CmsLogin::login('sometestuser', 'some_password'))->should->beTrue();
	}
	
	public function itShouldProperlyCallTheLoginFailedEvent()
	{
		$this->callback_test = 1;
		$this->originator = '';
		$this->event_name = '';
		CmsEventOperations::add_temp_event_handler('Core', 'LoginFailed', array($this, 'temp_callback'));
		$this->spec(CmsLogin::login('sometestuser', 'some_password'))->should->beFalse();
		$this->spec($this->callback_test)->should->be(2);
		$this->spec($this->originator)->should->be('Core');
		$this->spec($this->event_name)->should->be('LoginFailed');
		$this->add_test_user();
		$this->spec(CmsLogin::login('sometestuser', 'some_password'))->should->beTrue();
		$this->spec($this->callback_test)->should->be(2);
		$this->spec($this->originator)->should->be('Core');
		$this->spec($this->event_name)->should->be('LoginFailed');
	}
}

# vim:ts=4 sw=4 noet
?>