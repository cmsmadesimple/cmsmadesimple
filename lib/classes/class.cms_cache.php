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
 * Wrapper class around Cache:Lite for caching full pages and function output.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsCache extends CmsObject
{
	static private $instances = null;
	private $cache = null;
	
	function __construct($type = 'Function')
	{
		parent::__construct();
		
		ini_set('include_path', cms_join_path(ROOT_DIR, 'lib/'));
		require_once cms_join_path(ROOT_DIR, 'lib', 'Zend', 'Cache.php');

		// Set a few options
		$frontend_options = array(
			'automatic_serialization' => true,
		    'lifeTime' => 300
		);
		
		$backend_options = array('cache_dir' => cms_join_path(ROOT_DIR, 'tmp', 'cache/'));
		
		if ($type == 'Function')
		{
			$frontend_options['caching'] = CmsConfig::get('function_caching');
		}

		$this->cache = Zend_Cache::factory($type, 'File', $frontend_options, $backend_options);
	}
	
	public static function get_instance($type = 'Function')
	{
		if (self::$instances == null)
		{
			self::$instances = array();
		}

		if (empty(self::$instances[$type]))
		{
			self::$instances[$type] = new CmsCache($type);
		}

		return self::$instances[$type];
	}
	
	public function get($id)
	{
		return $this->cache->load($id);
	}
	
	public function save($data, $id = NULL)
	{
		return $this->cache->save($data, $id);
	}
	
	public function test($id = NULL)
	{
		return $this->cache->test($id);
	}
	
	public function call()
	{
		$args = func_get_args();
		if (count($args) < 2 || !is_array($args[1]))
			$args[1] = array($args[1]);
		return call_user_func_array(array($this->cache, 'call'), $args);
	}
	
	public function drop()
	{
		$args = func_get_args();
		if (count($args) < 2 || !is_array($args[1]))
			$args[1] = array($args[1]);
		return call_user_func_array(array($this->cache, 'drop'), $args);
	}
	
	public function clean($group = FALSE)
	{
		CmsContentOperations::clear_cache();
		$this->cache->setOption('caching', false);
		return $this->cache->clean(Zend_Cache::CLEANING_MODE_ALL);
	}
	
	static public function clear($group = FALSE)
	{
		return self::get_instance()->clean($group);
	}
}

# vim:ts=4 sw=4 noet
?>