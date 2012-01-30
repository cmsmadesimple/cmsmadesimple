<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
#$Id: content.functions.php 6863 2011-01-18 02:34:48Z calguy1000 $

require_once(dirname(dirname(__FILE__)).'/smarty/SmartyBC.class.php');

/**
 * Extends the Smarty class for content.
 *
 * Extends the Smarty class for checking timestamps and rendering content to the browser.
 *
 * @package CMS
 * @since 0.1
 */

class Smarty_CMS extends SmartyBC 
{	
	public $id; // <- triggers error without | do search why this is needed
	public $params; // <- triggers error without | do search why this is needed
	private $_global_cache_id;
	private static $_instance;

	/**
	* Constructor
	*
	* @param array The hash of CMSMS config settings
	*/
	public function __construct()
	{ 
		parent::__construct();

		global $CMS_ADMIN_PAGE;
		global $CMS_INSTALL_PAGE;  
		$config = cmsms()->GetConfig();

		// Set template and config dirs according witch instance we are at.
		if( isset($CMS_ADMIN_PAGE) && $CMS_ADMIN_PAGE == 1 ) {
		
			$this->setTemplateDir(cms_join_path($config['root_path'],$config['admin_dir'],'templates'));
			$this->setConfigDir(cms_join_path($config['root_path'],$config['admin_dir'],'/configs'));;
		} else {
		
		    $this->setTemplateDir(cms_join_path($config['root_path'],'tmp','templates'));
		    $this->setConfigDir(cms_join_path($config['root_path'],'tmp','templates'));
		}

		// Set template_c and canche dirs
		$this->setCompileDir(TMP_TEMPLATES_C_LOCATION);
		$this->setCacheDir(TMP_CACHE_LOCATION);

		// Set plugins dirs
		$this->addPluginsDir(cms_join_path($config['root_path'],'plugins'));

		// register default plugin handler
		$this->registerDefaultPluginHandler(array(&$this, 'defaultPluginHandler'));

		$this->assign('app_name','CMS');
		/* Disabling for now -Stikki- */
		if ($config["debug"] == true) {
		  $this->force_compile = true;
		  $this->debugging = true;
		}
		
		if (is_sitedown()) {
			$this->setCaching(false);
			$this->force_compile = true;
		}

		// Check if we are at install page, don't register anything if so, cause nothing below is needed.
		if(isset($CMS_INSTALL_PAGE)) return;

		if( isset($CMS_ADMIN_PAGE) && $CMS_ADMIN_PAGE == 1 ) {
		  $this->setCaching(false);
		  //$this->force_compile = true;
		}


		// Load User Defined Tags	
		$utops = cmsms()->GetUserTagOperations();
		$usertags = $utops->ListUserTags();
		$caching = (get_site_preference('smarty_cache_udt','never') == 'always')?true:false;
		foreach( $usertags as $id => $name )
		{
			$function = $utops->CreateTagFunction($name);
			$this->registerPlugin('function',$name,$function,$caching);
		}
		

		// Load resources
		$this->registerResource('template',new CMSPageTemplateResource(''));
		$this->registerResource('tpl_top',new CMSPageTemplateResource('top'));
		$this->registerResource('tpl_head',new CMSPageTemplateResource('head'));
		$this->registerResource('tpl_body',new CMSPageTemplateResource('body'));
		$this->registerResource('module_db_tpl',new CMSModuleDbTemplateResource());
		$this->registerResource('module_file_tpl',new CMSModuleFileTemplateResource());
		$this->registerResource('content',new CMSContentTemplateResource());
		$this->registerResource('htmlblob',new CMSGlobalContentTemplateResource());
		$this->registerResource('globalcontent',new CMSGlobalContentTemplateResource());
		
		if( !isset($CMS_ADMIN_PAGE) )
		{
		    // just for frontend actions.
		    $this->registerPlugin('function','content','CMS_Content_Block::smarty_fetch_contentblock',false);
		    $this->registerPlugin('function','process_pagedata','CMS_Content_Block::smarty_fetch_pagedata',false);
		    $this->registerPlugin('function','content_image','CMS_Content_Block::smarty_fetch_imageblock',false);
		    $this->registerPlugin('function','content_module','CMS_Content_Block::smarty_fetch_moduleblock',false);

		    $this->autoload_filters = array('pre'=>'precompilefunc', 'post'=>'postcompilefunc');

		    if( get_site_preference('use_smartycache',0) )
		    {
				// compile check can only be enabled, if using smarty cache... just for safety.
				$this->setCompileCheck(get_site_preference('use_smartycompilecheck',1));
		    }
		}

		// Enable security object
		$this->enableSecurity('CMSSmartySecurityPolicy');
	}

	/**
	* loadPlugin method
	* NOTE: Overwrites parent
	*
	* @param string $plugin_name
	* @param boolean $check
	* @return mixed
	*/
	public function loadPlugin($plugin_name,$check = true)
	{
	  $res = parent::loadPlugin($plugin_name,$check);
	  if( $res ) 
	    {
	      if( !function_exists($plugin_name) )
		{
		  $parts = explode('_',$plugin_name);
		  if( $parts[0] != 'smarty' || $parts[1] == 'internal' ) return $res;
		  return false;
		}
	    }
	  return $res;
	}

	/**
	* defaultPluginHandler
	* NOTE: Registered in constructor
	*
	* @param string $name
	* @param string $type
	* @param string $template
	* @param string $callback
	* @param string $script
	* @return boolean true on success, false on failure
	*/	
	public function defaultPluginHandler($name, $type, $template, &$callback, &$script)
	{
	  $config = cmsms()->GetConfig();
	  $fn = cms_join_path($config['root_path'],'plugins',$type.'.'.$name.'.php');
	  if( file_exists($fn) )
	    {
	      // plugins with the smarty_cms_function
	      $callback = 'smarty_cms_'.$type.'_'.$name;
	      $script = $fn;
	      require_once($fn);
	      if( function_exists('smarty_cms_'.$type.'_'.$name) )
		{
		  $callback = 'smarty_cms_'.$type.'_'.$name;
		  return TRUE;
		}
	    }

	  // maybe it was loaded by a module
	  $modulename = module_meta::get_instance()->find_module_by_plugin($name);
	  if( $modulename )
	    {
	      $obj = cms_utils::get_module($modulename);
	      if( is_object($obj) ) return TRUE;
	    }
	  return FALSE;
	}

	/**
	* get_instance method
	*
	* @return object $this
	*/
	public static function &get_instance()
	{
		if( !is_object(self::$_instance) ) {
			
			self::$_instance = new Smarty_CMS();
		}
		
		return self::$_instance;
	}

	/**
	* get_instance method
	*
	* @param int $id
	* @return void
	*/
	public function set_global_cacheid($id)
	{
	  if( is_null($id) || $id === '' )
	    {
	      $this->_global_cache_id = null;
	    }
	  else
	    {
	      $this->_global_cache_id = $id;
	    }
	}

	/**
	* fetch method
	* NOTE: Overwrites parent
	*
	* @param mixed $template
	* @param int $cache_id
	* @param mixed $parent
	* @param boolean $display
	* @param boolean $merge_tpl_vars
	* @param boolean $no_output_filter
	* @return mixed
	*/	
	public function fetch($template = null,$cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
	{
	  if( is_null($cache_id) || $cache_id === '' )
	    {
	      $cache_id = $this->_global_cache_id;
	    }
	  else if( $cache_id[0] == '|' )
	    {
	      $cache_id = $this->_global_cache_id . $cache_id;
	    }
	  return parent::fetch($template,$cache_id,$compile_id,$parent,$display,$merge_tpl_vars,$no_output_filter);
	}

	/**
	* clearCache method
	* NOTE: Overwrites parent
	*
	* @param mixed $template_name
	* @param int $cache_id
	* @param int $compile_id
	* @param mixed $exp_time
	* @param mixed $type
	* @return mixed
	*/	
	public function clearCache($template_name = null,$cache_id = null,$compile_id = null,$exp_time = null,$type = null)
	{
	  if( is_null($cache_id) || $cache_id === '' )
	    {
	      $cache_id = $this->_global_cache_id;
	    }
	  else if( $cache_id[0] == '|' )
	    {
	      $cache_id = $this->_global_cache_id . $cache_id;
	    }
	  return parent::clearCache($template_name,$cache_id,$compile_id,$exp_time,$type);
	}

	/**
	* isCached method
	* NOTE: Overwrites parent
	*
	* @param mixed $template_name
	* @param int $cache_id
	* @param int $compile_id
	* @param mixed $parent
	* @return mixed
	*/	
	public function isCached($template = null,$cache_id = null,$compile_id = null, $parent = null)
	{
	  if( is_null($cache_id) || $cache_id === '' )
	    {
	      $cache_id = $this->_global_cache_id;
	    }
	  else if( $cache_id[0] == '|' )
	    {
	      $cache_id = $this->_global_cache_id . $cache_id;
	    }
	  return parent::isCached($template,$cache_id,$compile_id,$parent);
	}
	
} // end of class

?>
