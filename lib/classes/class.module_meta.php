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

/**
 * @package CMS 
 */

/**
 * A singleton class for managing meta data acquired from modules.
 * 
 * This class caches information from modules as needed.
 *
 * @package CMS
 * @since 1.10
 * @author  Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 */
final class module_meta
{
  static private $_instance = null;
  private $_cache_fn;
  private $_data = array();

  private function __construct() 
  {
	  $this->_cache_fn = TMP_CACHE_LOCATION.'/'.__CLASS__.'.cache';
  }


  /**
   * Get the instance of this object.  The object will be instantiated if necessary
   *
   * @return object
   */
  public static function &get_instance()
  {
    if( !isset(self::$_instance) )
    {
		$c = __CLASS__;
		self::$_instance = new $c;
	}
    return self::$_instance;
  }


  private function _load_cache()
  {
	  $config = cmsms()->GetConfig();
	  //if( count($this->_data) == 0 && (!isset($config['debug']) || !$config['debug']))
	  if( count($this->_data) == 0 )
	  {
		  $this->_data = array();
		  if( file_exists($this->_cache_fn) )
		  {
			  $this->_data = unserialize(file_get_contents($this->_cache_fn));
		  }
	  }
  }


  private function _save_cache()
  {
    file_put_contents($this->_cache_fn,serialize($this->_data));
  }


  /**
   * List modules by their capabilities
   *
   * @param string capability name
   * @param array optional capability parameters
   * @param boolean optional test value.
   * @return array of matching module names
   */
  public function module_list_by_capability($capability,$params = array(),$returnvalue = TRUE)
  {
    if( empty($capability) ) return;

    $this->_load_cache();
    $sig = md5($capability.serialize($params));
    if( !isset($this->_data['capability']) || !isset($this->_data['capability'][$sig]) )
      {
		  debug_buffer('start building module capability list');
		  if( !isset($this->_data['capability']) )
		  {
			  $this->_data['capability'] = array();
		  }

		  $installed_modules = ModuleOperations::get_instance()->GetInstalledModules();
		  $loaded_modules = ModuleOperations::get_instance()->GetLoadedModules();
		  $this->_data['capability'][$sig] = array();
		  foreach( $installed_modules as $onemodule )
		  {
			  $loaded_it = FALSE;
			  $object = null;
			  if( isset($loaded_modules[$onemodule]) )
			  {
				  $object = $loaded_modules[$onemodule];
			  }
			  else
			  {
				  $object = ModuleOperations::get_instance()->get_module_instance($onemodule);
				  $loaded_it = TRUE;
			  }
			  if( !$object ) continue;

			  // now do the test
			  $res = $object->HasCapability($capability,$params);
			  $this->_data['capability'][$sig][$onemodule] = $res;
			  
// 			  if( $loaded_it )
// 			  {
// 				  debug_display('unload '.$onemodule);
// 				  ModuleOperations::get_instance()->unload_module($onemodule);
// 			  }
		  }

		  debug_buffer('done building module capability list');
		  // store it.
		  $this->_save_cache();
      }

    $res = null;
    if( is_array($this->_data['capability'][$sig]) && count($this->_data['capability'][$sig]) )
    {
		$res = array();
		foreach( $this->_data['capability'][$sig] as $key => $value )
		{
			if( $value == $returnvalue )
			{
				$res[] = $key;
			}
		}
	}

    return $res;

  }


  /**
   * Return a list of modules that have the supplied method.
   *
   * This method will query all available modules, check if the method name exists for that module, and if so, call the method and trap the
   * return value.  
   *
   * @param string method name
   * @param mixed  optional return value.
   * @return array of matching module names
   */
  public function module_list_by_method($method,$returnvalue = TRUE)
  {
    if( empty($method) ) return;

    $this->_load_cache();
    if( !isset($this->_data['methods']) || !isset($this->_data['methods'][$method]) )
      {
		  debug_buffer('start building module method cache');
		  if( !isset($this->_data['methods']) )
			  {
				  $this->_data['methods'] = array();
			  }
		  
		  $installed_modules = ModuleOperations::get_instance()->GetInstalledModules();
		  $loaded_modules = ModuleOperations::get_instance()->GetLoadedModules();
		  $this->_data['methods'][$method] = array();
		  foreach( $installed_modules as $onemodule )
			  {
				  $loaded_it = FALSE;
				  $object = null;
				  if( isset($loaded_modules[$onemodule]) )
					  {
						  $object = $loaded_modules[$onemodule];
					  }
				  else
					  {
						  $object = ModuleOperations::get_instance()->get_module_instance($onemodule);
						  $loaded_it = TRUE;
					  }
				  if( !$object ) continue;
				  if( !method_exists($object,$method) ) continue;

				  // now do the test
				  $res = $object->$method();
				  $this->_data['methods'][$method][$onemodule] = $res;

				  // 	    if( $loaded_it )
				  // 	      {
				  // 			  debug_display('unload '.$onemodule);
				  // 			  ModuleOperations::get_instance()->unload_module($onemodule);
				  // 	      }
			  }

		  // store it.
		  debug_buffer('done building module method cache');
		  $this->_save_cache();
      }

    $res = null;
    if( is_array($this->_data['methods'][$method]) && count($this->_data['methods'][$method]) )
	{
		$res = array();
		foreach( $this->_data['methods'][$method] as $key => $value )
		{
			if( $value == $returnvalue )
			{
				$res[] = $key;
			}
		}
	}
    return $res;
  }
} // end of class

#
# EOF
#
# vim:ts=4 sw=4 noet
?>