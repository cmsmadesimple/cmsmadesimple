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
 * Class that represents the object passed to a callback
 * when the associated event is triggered.
 *
 * @package default
 * @author Ted Kulp
 **/
class CmsEvent extends CmsObjectRelationalMapping
{
	var $params = array('id' => -1, 'module_name' => 'Core');
	var $field_maps = array('originator' => 'module_name');
	var $table = 'events';

	function __construct()
	{
		parent::__construct();
	}
	
	function setup()
	{
		$this->create_has_many_association('event_handlers', 'CmsEventHandler', 'event_id', array('order' => 'order_num ASC'));
	}
	
	function before_delete()
	{
		if ($this->event_handlers != null)
		{
			foreach ($this->event_handlers as $handler)
			{
				$handler->delete();
			}
		}
	}
}

# vim:ts=4 sw=4 noet
?>