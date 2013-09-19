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

/**
 * @package CMS
 */

require_once(dirname(dirname(__FILE__)).'/smarty/SmartyBC.class.php');

/**
 * Extends the Smarty class for content.
 *
 * @package CMS
 * @since 0.1
 */
class Smarty_CMS extends SmartyBC
{	
	public $id; // <- triggers error without | do search why this is needed
	public $params; // <- triggers error without | do search why this is needed
	protected $_global_cache_id;
	private static $_instance;

	/**
	* Constructor
	*
	* @param array The hash of CMSMS config settings
	*/
	public function __construct()
	{ 
		parent::__construct();

		global $CMS_ADMIN_PAGE; // <- Still needed?
		global $CMS_INSTALL_PAGE;
		
		$config = cmsms()->GetConfig();

		// Set template_c and cache dirs
		$this->setCompileDir(TMP_TEMPLATES_C_LOCATION);
		$this->setCacheDir(TMP_CACHE_LOCATION);
		$this->assign('app_name','CMS');

		if ($config["debug"] == true) {
		
			$this->force_compile = true;
			$this->debugging = false;
		}

		// Set plugins dirs
		$this->addPluginsDir(cms_join_path($config['root_path'],'plugins'));

		// common resources.
		$this->registerResource('module_db_tpl',new CMSModuleDbTemplateResource());
		$this->registerResource('module_file_tpl',new CMSModuleFileTemplateResource());
		$this->registerResource('template',new CMSPageTemplateResource()); // <- Should proably be global and removed from parser?		

		// Load User Defined Tags
		if( !cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
		  $utops = cmsms()->GetUserTagOperations();
		  $usertags = $utops->ListUserTags();
		  $caching = false;
		  if( get_site_preference('smarty_cacheudt','never') == 'always' && cmsms()->is_frontend_request() ) {
		    $caching = true;
		  }
		  foreach( $usertags as $id => $udt_name ) {
		    $function = $utops->CreateTagFunction($udt_name);
		    $this->registerPlugin('function',$udt_name,$function,$caching);
		  }
                }
	
		// register default plugin handler
		$this->registerDefaultPluginHandler(array(&$this, 'defaultPluginHandler'));

		if(cmsms()->is_frontend_request()) {
			$this->setTemplateDir(cms_join_path($config['root_path'],'tmp','templates'));
			$this->setConfigDir(cms_join_path($config['root_path'],'tmp','templates'));

			// Check if we are at install page, don't register anything if so, cause nothing below is needed.
			if(isset($CMS_INSTALL_PAGE)) return;

			if (is_sitedown()) {
				$this->setCaching(false);
				$this->force_compile = true;
			}

			// Load resources
			$this->registerResource('tpl_top',new CMSPageTemplateResource('top'));
			$this->registerResource('tpl_head',new CMSPageTemplateResource('head'));
			$this->registerResource('tpl_body',new CMSPageTemplateResource('body'));
			$this->registerResource('content',new CMSContentTemplateResource());
			//$this->registerResource('htmlblob',new CMSGlobalContentTemplateResource());
			$this->registerResource('globalcontent',new CMSGlobalContentTemplateResource());

			// just for frontend actions.
			$this->registerPlugin('compiler','content',array('CMS_Content_Block','smarty_compile_fecontentblock'),false);
			$this->registerPlugin('function','content_image','CMS_Content_Block::smarty_fetch_imageblock',false);
			$this->registerPlugin('function','content_module','CMS_Content_Block::smarty_fetch_moduleblock',false);
			$this->registerPlugin('function','process_pagedata','CMS_Content_Block::smarty_fetch_pagedata',false);

			// Autoload filters
			$this->autoloadFilters();

			// compile check can only be enabled, if using smarty cache... just for safety.
			if( get_site_preference('use_smartycache',0) ) {
			  if( version_compare(phpversion(),'5.3') >= 0 ) {
			    $this->setCompileCheck(get_site_preference('use_smartycompilecheck',1));
			  }
			}
			
		}
		else if(cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE)) {
		
			$this->setCaching(false);
			$this->force_compile = true;
			$this->setTemplateDir(cms_join_path($config['root_path'],$config['admin_dir'],'templates'));
			$this->setConfigDir(cms_join_path($config['root_path'],$config['admin_dir'],'configs'));;
			$this->registerResource('globalcontent',new CMSNullTemplateResource());
		}

		// Enable security object
		// Note: Buggy, disabled prior to release of CMSMS 1.11
		//$this->enableSecurity('CMSSmartySecurityPolicy');
	}
	
	/**
	* get_instance method
	*
	* @return object $this
	*/
	public static function &get_instance()
	{
		if( !is_object(self::$_instance) ) {
			self::$_instance = new self;
		}
		
		return self::$_instance;
	}	

	/**
	* Load filters from CMSMS plugins folder
	*
	* @return void
	*/	
	private function autoloadFilters()
	{
		$pre = array();
		$post = array();
		$output = array();
		
		foreach( $this->plugins_dir as $onedir ) {
		
			if( !is_dir($onedir) ) 
				continue;

			$files = glob($onedir.'/*php');
			if( !is_array($files) || count($files) == 0 ) 
				continue;

			foreach( $files as $onefile ) {
			
				$onefile = basename($onefile);
				$parts = explode('.',$onefile);
				if( !is_array($parts) || count($parts) != 3 )
					continue;
				
				switch( $parts[0] ) {
				
					case 'output':
						$output[] = $parts[1];
						break;
						
					case 'prefilter':
						$pre[] = $parts[1];
						break;
						
					case 'postfilter':
						$post[] = $parts[1];
						break;
				}
			}
		}

		$this->autoload_filters = array('pre'=>$pre,'post'=>$post,'output'=>$output);
	}
	
    /**
     * Registers plugin to be used in templates
     *
     * @param string   $type       plugin type
     * @param string   $tag        name of template tag
     * @param callback $callback   PHP callback to register
     * @param boolean  $cacheable  if true (default) this fuction is cachable
     * @param array    $cache_attr caching attributes if any
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or Smarty_Internal_Template) instance for chaining
     * @throws SmartyException when the plugin tag is invalid
     */
    public function registerPlugin($type, $tag, $callback, $cacheable = true, $cache_attr = null)
    {
        if (!isset($this->smarty->registered_plugins[$type][$tag])) {
            $this->smarty->registered_plugins[$type][$tag] = array($callback, (bool) $cacheable, (array) $cache_attr);
        }
        
        return $this;
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
    public function defaultPluginHandler($name, $type, $template, &$callback, &$script, &$cachable)
    {
		debug_buffer('',"Start Load Smarty Plugin $name/$type");

		$cachable = TRUE;
		$config = cmsms()->GetConfig();
		$fn = cms_join_path($config['root_path'],'plugins',$type.'.'.$name.'.php');
		if( file_exists($fn) ) {
		
			// plugins with the smarty_cms_function
			require_once($fn);
			$func = 'smarty_cms_'.$type.'_'.$name;
			$script = $fn;
			
			if( function_exists($func) ) {
				$callback = $func;
				$cachable = FALSE;
				debug_buffer('',"End Load Smarty Plugin $name/$type");
				return TRUE;
			}
		}

      if( cmsms()->is_frontend_request() ) {
	$row = cms_module_smarty_plugin_manager::load_plugin($name,$type);
	if( is_array($row) && is_array($row['callback']) && count($row['callback']) == 2 && 
	    is_string($row['callback'][0]) && is_string($row['callback'][1]) ) {
	  $cachable = $row['cachable'];
	  $callback = $row['callback'][0].'::'.$row['callback'][1];
	  return TRUE;
	}
      }
		return FALSE;
    }

	/**
	 * Test if a smarty plugin with the specified name already exists.
	 *
	 * @param string the plugin name
	 * @return boolean
	 */
	public function is_registered($name)
	{
		return isset($this->registered_plugins['function'][$name]);
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
	  $name = $template; if( startswith($name,'string:') ) $name = 'string:';
	  debug_buffer('','Fetch '.$name.' start');
	  if( is_null($cache_id) || $cache_id === '' ) {
	    $cache_id = $this->_global_cache_id;
	  }
	  else if( $cache_id[0] == '|' ) {
	    $cache_id = $this->_global_cache_id . $cache_id;
	  }

	  // send an event before fetching...this allows us to change template stuff.
	  if( cmsms()->is_frontend_request() ) {
	    $parms = array('template'=>&$template,'cache_id'=>&$cache_id,'compile_id'=>&$compile_id,
			   'display'=>&$display,'no_output_filter'=>&$no_output_filter);
	    Events::SendEvent('Core','TemplatePreFetch',$parms);
	  }

	  $tmp = parent::fetch($template,$cache_id,$compile_id,$parent,$display,false,$no_output_filter);
	  debug_buffer('','Fetch '.$name.' end');
	  return $tmp;
	}

	public function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $do_clone = true)
	{
	  if ($parent instanceof Smarty) {
	    $saved_tpl_vars = $parent->tpl_vars;
            $saved_config_vars = $parent->config_vars;
	  }
	  $tpl = parent::createTemplate($template, $cache_id, $compile_id, $parent, $do_clone);
	  if ($parent instanceof Smarty) {
            $parent->tpl_vars = $saved_tpl_vars;
            $parent->config_vars = $saved_config_vars;
            $tpl->tpl_vars = &$parent->tpl_vars;
            $tpl->config_vars = &$parent->config_vars;
	  }
	  return $tpl;
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
	public function clearCache($template_name,$cache_id = null,$compile_id = null,$exp_time = null,$type = null)
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
	
	/**
	* Error console
	*
	* @param object Exception $e
	* @return html
	* @author Stikki
	*/	
	public function errorConsole(Exception $e)
	{
		$config = cmsms()->GetConfig();
		$odir = $this->template_dir;

		$this->force_compile = true;
		$this->debugging = true;
		$this->template_dir = cms_join_path($config['root_path'], 'lib', 'smarty');
			
		$this->assign('e_line', $e->getLine());
		$this->assign('e_file', $e->getFile());
		$this->assign('e_message', $e->getMessage());
		$this->assign('e_trace', htmlentities($e->getTraceAsString()));
		
		// put mention into the admin log
		audit('', 'Error: '.substr( $e->getMessage(),0 ,50 ), 'has occured');

		$output = $this->fetch('error-console.tpl');

		$this->force_compile = false;
		$this->debugging = false;		
		$this->template_dir = $odir;
		
		return $output;
	}	


    /**
     * Takes unknown classes and loads plugin files for them
     * class name format: Smarty_PluginType_PluginName
     * plugin filename format: plugintype.pluginname.php
     *
     * Note: this method overrides the one in the smarty base class and provides more testing.
     *
     * @param string $plugin_name    class plugin name to load
     * @param bool   $check          check if already loaded
     * @return string |boolean filepath of loaded file or false
     */
    public function loadPlugin($plugin_name, $check = true)
    {
		// if function or class exists, exit silently (already loaded)
		if ($check && (is_callable($plugin_name) || class_exists($plugin_name, false))) {
			return true;
		}
		
		// Plugin name is expected to be: Smarty_[Type]_[Name]
		$_name_parts = explode('_', $plugin_name, 3);
		
		// class name must have three parts to be valid plugin
		// count($_name_parts) < 3 === !isset($_name_parts[2])
		if (!isset($_name_parts[2]) || strtolower($_name_parts[0]) !== 'smarty') {
			throw new SmartyException("plugin {$plugin_name} is not a valid name format");
			return false;
		}
		
		// if type is "internal", get plugin from sysplugins
		if (strtolower($_name_parts[1]) == 'internal') {
		
			$file = SMARTY_SYSPLUGINS_DIR . strtolower($plugin_name) . '.php';
			if (file_exists($file)) {
			
				require_once($file);
				return $file;
			} else {
			
				return false;
			}
		}
		// plugin filename is expected to be: [type].[name].php
		$_plugin_filename = "{$_name_parts[1]}.{$_name_parts[2]}.php";

		$_stream_resolve_include_path = function_exists('stream_resolve_include_path');

		// loop through plugin dirs and find the plugin
		foreach($this->getPluginsDir() as $_plugin_dir) {
		
			$names = array(
				$_plugin_dir . $_plugin_filename,
				$_plugin_dir . strtolower($_plugin_filename)
			);
			
			foreach ($names as $file) {
			
				if (file_exists($file)) {
				
					require_once($file);
					if( is_callable($plugin_name) || class_exists($plugin_name, false) )
						return $file;
				}
				
				if ($this->use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_plugin_dir)) {
				
					// try PHP include_path
					if ($_stream_resolve_include_path) {
					
						$file = stream_resolve_include_path($file);
					} else {
					
						$file = Smarty_Internal_Get_Include_Path::getIncludePath($file);
					}

					if ($file !== false) {
						require_once($file);
						if( is_callable($plugin_name) || class_exists($plugin_name, false) )
							return $file;
					}
				}
			}
		}
		// no plugin loaded
		return false;
    }	

} // end of class

?>
