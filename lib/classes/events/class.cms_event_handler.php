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

class CmsEventHandler extends CmsObjectRelationalMapping
{
	var $params = array('id' => -1, 'order_num' => -1);
	var $field_maps = array('handler_order' => 'order_num');
	var $table = 'event_handlers';
	
	var $list_filter_fields = array('event_id');

	function __construct()
	{
		parent::__construct();
	}
	
	function setup()
	{
		$this->create_belongs_to_association('event', 'CmsEvent', 'event_id');
		$this->assign_acts_as('list');
	}
	
	function after_save()
	{
		CmsCache::clear();
	}
	
	function after_delete()
	{
		CmsCache::clear();
	}
}

# vim:ts=4 sw=4 noet
?>