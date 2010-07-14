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

class CmsModuleFunctionProxy extends CmsObject
{
	var $plugin_lookup = array();
	
	static private $instance = NULL;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Returns an instnace of the CmsModuleFunctionProxy singleton.  Most 
	 * people can generally use cmsms() instead of this, but they 
	 * both do the same thing.
	 *
	 * @return CmsModuleFunctionProxy The singleton CmsModuleFunctionProxy instance
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsModuleFunctionProxy();
		}
		return self::$instance;
	}
	
	public function register($module_name, $name, $callback = '')
	{
		cms_smarty()->register->templateFunction($name, array($this, $name));
		$this->plugin_lookup[$name] = array($module_name, $callback);
	}
	
	public function __call($function, $variables)
	{
		$params = $variables[0];
		$smarty = $variables[1];
		
		$lookup = $this->plugin_lookup[$function];
		
		$module = CmsModuleLoader::get_module_class($lookup[0]);
		if ($module)
		{
			if (is_callable(array($module, $lookup[1])))
			{
				echo call_user_func_array(array($module, $lookup[1]), array($params, &$smarty));
			}
		}
	}
}

# vim:ts=4 sw=4 noet
?>