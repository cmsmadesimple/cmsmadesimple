<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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

class CmsEventManager extends CmsObject
{
	static private $event_store = null;
	
	static public function init_store()
	{
		if (self::$event_store == null)
		{
			self::$event_store = array();
		}
	}

	static public function register_event_handler($name, $function)
	{
		self::init_store();

		//Create the store for this event if it doesn't exist
		if (!empty(self::$event_store[$name]))
		{
			self::$event_store[$name] = array();
		}

		//Make sure they passed a callback of some sort.
		//If so, add it to the queue.
		if (is_callable($function))
		{
			self::$event_store[$name][] = $function;
			return true;
		}

		return false;
	}

	static public function remove_event_handler($name, $function)
	{
		self::init_store();

		if (!empty(self::$event_store[$name]))
		{
			$found = null;
			foreach (self::$event_store[$name] as $k=>$callback)
			{
				if ($callback == $function)
				{
					$found = $k;
				}
			}

			if ($found !== null)
			{
				unset(self::$event_store[$name][$found]);
				return true;
			}
		}

		return false;
	}
	
	static public function remove_event($name)
	{
		self::init_store();
		
		if (!empty(self::$event_store[$name]))
		{
			unset(self::$event_store[$name]);
			return true;
		}
		
		return false;
	}

	static public function send_event($name, $arguments = array())
	{
		self::init_store();
		
		$send_params = array($name, &$arguments);
		if (isset(self::$event_store[$name]))
		{
			foreach (self::$event_store[$name] as &$one_method)
			{
				if (is_callable($one_method))
					call_user_func_array($one_method, $send_params);
			}
		}
	}
	
	static public function list_registered_events($with_counts = false)
	{
		self::init_store();
		
		if ($with_counts)
		{
			$res = array();
			foreach (self::$event_store as $k=>$v)
				$res[$k] = count(self::$event_store[$k]);
			return $res;
		}
		
		return array_keys(self::$event_store);
	}
	
	static public function is_registered_event($name)
	{
		self::init_store();
		
		return isset(self::$event_store[$name]);
	}
}

# vim:ts=4 sw=4 noet
?>