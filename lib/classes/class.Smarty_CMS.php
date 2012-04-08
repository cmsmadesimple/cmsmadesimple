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

    // Set template_c and canche dirs
    $this->setCompileDir(TMP_TEMPLATES_C_LOCATION);
    $this->setCacheDir(TMP_CACHE_LOCATION);
    $this->assign('app_name','CMS');

    if ($config["debug"] == true) {
      $this->force_compile = true;
      $this->debugging = false;
    }
		
    $this->registerResource('template',new CMSPageTemplateResource(''));
    $this->registerResource('module_db_tpl',new CMSModuleDbTemplateResource());
    $this->registerResource('module_file_tpl',new CMSModuleFileTemplateResource());

    // Set plugins dirs
    $this->addPluginsDir(cms_join_path($config['root_path'],'plugins'));

    // register default plugin handler
    $this->registerDefaultPluginHandler(array(&$this, 'defaultPluginHandler'));

    if( !isset($CMS_ADMIN_PAGE) )
      {
	$this->setTemplateDir(cms_join_path($config['root_path'],'tmp','templates'));
	$this->setConfigDir(cms_join_path($config['root_path'],'tmp','templates'));

	if (is_sitedown()) {
	  $this->setCaching(false);
	  $this->force_compile = true;
	}
		  
	// Check if we are at install page, don't register anything if so, cause nothing below is needed.
	if(isset($CMS_INSTALL_PAGE)) return;

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
	$this->registerResource('tpl_top',new CMSPageTemplateResource('top'));
	$this->registerResource('tpl_head',new CMSPageTemplateResource('head'));
	$this->registerResource('tpl_body',new CMSPageTemplateResource('body'));
	$this->registerResource('content',new CMSContentTemplateResource());
	$this->registerResource('htmlblob',new CMSGlobalContentTemplateResource());
	$this->registerResource('globalcontent',new CMSGlobalContentTemplateResource());
		
	// just for frontend actions.
	$this->registerPlugin('function','content','CMS_Content_Block::smarty_fetch_contentblock',false);
	$this->registerPlugin('function','content_image','CMS_Content_Block::smarty_fetch_imageblock',false);
	$this->registerPlugin('function','content_module','CMS_Content_Block::smarty_fetch_moduleblock',false);
	$this->registerPlugin('function','process_pagedata','CMS_Content_Block::smarty_fetch_pagedata',false);

	$this->autoload_filters = array('pre'=>'precompilefunc', 'post'=>'postcompilefunc');
	$this->merge_compiled_includes = TRUE;
		  
	if( get_site_preference('use_smartycache',0) )
	  {
	    // compile check can only be enabled, if using smarty cache... just for safety.
	    $this->setCompileCheck(get_site_preference('use_smartycompilecheck',1));
	  }
      }
    else if( isset($CMS_ADMIN_PAGE) && $CMS_ADMIN_PAGE == 1 ) 
      {
	$this->setCaching(false);
	$this->force_compile = true;

	$this->setTemplateDir(cms_join_path($config['root_path'],$config['admin_dir'],'templates'));
	$this->setConfigDir(cms_join_path($config['root_path'],$config['admin_dir'],'/configs'));;
      }

    // Enable security object
    $this->enableSecurity('CMSSmartySecurityPolicy');
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
	* loadPlugin method
	* NOTE: Overwrites parent
	*
	* @param string $plugin_name
	* @param boolean $check
	* @return mixed
	*/
    /*
	public function loadPlugin($plugin_name,$check = true)
	{
	  $res = parent::loadPlugin($plugin_name,$check);
	  if( $res ) 
	    {
	      if( !function_exists($plugin_name) )
		{
		  // see if it uses the old smarty_cms stuff.
		  $parts = explode('_',$plugin_name);
		  if( $parts[0] != 'smarty' || $parts[1] == 'internal' ) return $res;
		  return false;
		}
	    }
	  return $res;
	}
    */

    /**
     * _dflt_plugin
     *
     * @internal
     */
    public static function _dflt_plugin($params,&$smarty)
    {
      return '';
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
	  global $CMS_ADMIN_PAGE;
	  if( isset($CMS_ADMIN_PAGE) )
	    {
	      $callback = 'Smarty_CMS::_dflt_plugin';
	      $cachable = '';
	      return TRUE;
	    }

	  $cachable = TRUE;
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
		  debug_buffer('',"End Load Smarty Plugin $name/$type");
		  return TRUE;
		}
	    }

	  $row = cms_module_smarty_plugin_manager::load_plugin($name,$type);
	  if( is_array($row) && is_callable($row['callback']) )
	    {
	      $cachable = $row['cachable'];
	      $callback = $row['callback'];
	      debug_buffer('',"End Load Smarty Plugin $name/$type");
	      return TRUE;
	    }

	  debug_buffer('',"End Load Smarty Plugin $name/$type");
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
	  $name = $template; if( startswith($name,'string:') ) $name = 'string:';
	  debug_buffer('','Fetch '.$name.' start');
	  if( is_null($cache_id) || $cache_id === '' )
	    {
	      $cache_id = $this->_global_cache_id;
	    }
	  else if( $cache_id[0] == '|' )
	    {
	      $cache_id = $this->_global_cache_id . $cache_id;
	    }
	  $tmp = parent::fetch($template,$cache_id,$compile_id,$parent,$display,$merge_tpl_vars,$no_output_filter);
	  debug_buffer('','Fetch '.$name.' end');
	  return $tmp;
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
				
		if($config['debug']) {
		
			$this->assign('e_trace', $e->getTraceAsString());	
		}
		
		$output = $this->fetch('error-console.tpl');

		$this->force_compile = false;
		$this->debugging = false;		
		$this->template_dir = $odir;
		
		return $output;
	}	
	
} // end of class

?>
