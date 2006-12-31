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

class CmsCache extends CmsObject
{
	static private $instance = null;
	static private $cache = null;
	
	function __construct($type = 'function')
	{
		parent::__construct();
		
		$dirname = dirname(dirname(dirname(__FILE__)));

		// Set a few options
		$options = array(
		    'cacheDir' => $dirname.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'cache/',
		    'lifeTime' => 300
		);

		if ($type == 'function')
		{
			//if (!CmsConfig::get('function_caching') || CmsConfig::get('debug'))
			if (!CmsConfig::get('function_caching'))
				$options['caching'] = false;

			require_once($dirname.DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'pear' . DIRECTORY_SEPARATOR . 'cache'. DIRECTORY_SEPARATOR . 'lite' . DIRECTORY_SEPARATOR . 'Function.php');
			$this->cache = new Cache_Lite_Function($options);
		}
		else
		{
			require_once($dirname.DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'pear' . DIRECTORY_SEPARATOR . 'cache'. DIRECTORY_SEPARATOR . 'lite' . DIRECTORY_SEPARATOR . 'Function.php');
			$this->cache = new Cache_Lite($options);
		}
	}
	
	public static function get_instance($type = 'function')
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (empty($instances[$type]))
		{
			$instances[$type] = new CmsCache($type);
		}

		return $instances[$type];
	}
	
	public function get($id, $group = 'default', $doNotTestCacheValidity = FALSE)
	{
		return $this->cache->get($id, $group, $doNotTestCacheValidity);
	}
	
	public function save($data, $id = NULL, $group = 'default')
	{
		return $this->cache->save($data, $id, $group);
	}
	
	public function call()
	{
		$args = func_get_args();
		return call_user_func_array(array(&$this->cache, 'call'), $args);
	}
	
	public function drop()
	{
		$args = func_get_args();
		return call_user_func_array(array(&$this->cache, 'drop'), $args);
	}
	
	public function clean($group = FALSE, $mode = 'ingroup')
	{
		return $this->cache->clean($group, $mode);
	}
	
	static public function clear($group = FALSE, $mode = 'ingroup')
	{
		return self::get_instance()->clean($group, $mode);
	}
}

# vim:ts=4 sw=4 noet
?>