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

class UserAdmin extends CmsModuleBase
{
	function __construct()
	{
		parent::__construct();
	}
	
	function get_name()
	{
		return 'UserAdmin';
	}
	
	function get_version()
	{
		return '0.1';
	}
	
	function is_plugin_module()
	{
		return false;
	}

	function has_admin()
	{
		return true;
	}
	
	function minimum_core_version()
	{
		return '2.0-svn';
	}
	
	function get_admin_description()
	{
		return $this->Lang('module_description');
	}

	function get_admin_section()
	{
		return 'usersgroups';
	}

	function get_friendly_name()
	{
		return $this->Lang('manage_users_groups');
	}

	function visible_to_admin_user()
	{
		$user = CmsLogin::get_current_user();
		return CmsAcl::check_core_permission('Manage Users',$user) ||
			CmsAcl::check_core_permission('Manage Groups',$user) ||
			CmsAcl::check_core_permission('Manage Site Preference',$user);
	}


	function setup()
	{
// 		$this->register_route('/blog\/rss\.xml$/', array('action' => 'default', 'rss' => true, 'showtemplate' => false));
// 		$this->register_route('/blog\/category\/(?P<category>[a-zA-Z\-_\ ]+)\.xml$/', array('action' => 'list_by_category', 'rss' => true, 'showtemplate' => false));
// 		$this->register_route('/blog\/category\/(?P<category>[a-zA-Z\-_\ ]+)$/', array('action' => 'list_by_category'));
// 		$this->register_route('/blog\/(?P<url>[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/.*?)$/', array('action' => 'detail'));
// 		$this->register_route('/blog\/(?P<year>[0-9]{4})$/', array('action' => 'filter_list', 'month' => '-1', 'day' => '-1'));
// 		$this->register_route('/blog\/(?P<year>[0-9]{4})\/(?P<month>[0-9]{2})$/', array('action' => 'filter_list', 'day' => '-1'));
// 		$this->register_route('/blog\/(?P<year>[0-9]{4})\/(?P<month>[0-9]{2})\/(?P<day>[0-9]{2})$/', array('action' => 'filter_list'));
		
// 		$this->add_xmlrpc_method('getRecentPosts', 'metaWeblog');
// 		$this->add_xmlrpc_method('getCategories', 'metaWeblog');
// 		$this->add_xmlrpc_method('getPost', 'metaWeblog');
// 		$this->add_xmlrpc_method('newPost', 'metaWeblog');
// 		$this->add_xmlrpc_method('editPost', 'metaWeblog');
		
// 		$this->add_temp_event_handler('Core', 'HeaderTagRender', 'handle_header_callback');
	}	
}

# vim:ts=4 sw=4 noet
?>