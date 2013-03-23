<?php
# CMS - CMS Made Simple
# (c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA
#
#$Id: class.module.inc.php 7061 2011-05-28 18:48:04Z calguy1000 $

/**
 * @package             CMS
 */

/**
 * Base module class.
 *
 * All modules should inherit and extend this class with their functionality.
 *
 * @since		0.9
 * @version             1.8
 * @package		CMS
 */
abstract class CMSModule
{
	/**
	 * ------------------------------------------------------------------
	 * Initialization Functions and parameters
	 * ------------------------------------------------------------------
	 */

	/**
	 * A variable that indicates the current desired language
	 * this is effected by the (optional) lang parameter on module action
	 * calls. 
	 *
	 * @access private
	 */
	var $curlang;

	/**
	 * An array of loaded lang strings
	 *
	 * @access private
	 * @ignore
	 */
	var $langhash = array();

	/**
	 * A hash of the parameters passed in to the module action
	 *
	 * @access private
	 * @ignore
	 */
	var $params = array(array('name'=>'lang','default'=>'en_US','optional'=>true));

	/**
	 * @access private
	 * @ignore
	 */
	public $wysiwygactive = false;

	/**
	 * @access private
	 * @ignore
	 */
	public $syntaxactive = false;

	/**
	 * @access private
	 * @ignore
	 */
	var $error = '';

	/**
	 * @access private
	 * @ignore
	 */
	private $modinstall = false;

	/**
	 * @access private
	 * @ignore
	 */
	private $modtemplates = false;

	/**
	 * @access private
	 * @ignore
	 */
	private $modlang = false;

	/**
	 * @access private
	 * @ignore
	 */
	private $modform = false;

	/**
	 * @access private
	 * @ignore
	 */
	private $modredirect = false;

	/**
	 * @access private
	 * @ignore
	 */
	private $modmisc = false;

	/**
	 * @access private
	 * @ignore
	 */
	private $param_map = array();

	/**
	 * @access private
	 * @ignore
	 */
	private $restrict_unknown_params = false;

	/**
	 * @access private
	 * @ignore 
	 */
	private $__errors;

	/**
	 * @access private
	 * @ignore 
	 */
	private $__messages;

	/**
	 * @access private
	 * @ignore 
	 */
	private $__current_tab;

	/**
	 * Magic methods. This handles the deprecated. $this->db etc, syntax.
	 *
	 * @param string The key to get.  possible values are cms,db,smarty.config
	 * @deprecated
	 */
	public function &__get($key)
	{
	  switch( $key )
	    {
	    case 'cms':
	      return cmsms();

	    case 'smarty':
	      return cmsms()->GetSmarty();

	    case 'config':
	      return cmsms()->GetConfig();

	    case 'db':
	      return cmsms()->GetDb();
	    }

	  $tmp = null;
	  return $tmp;
	}

	/**
	 * Constructor
	 *
	 */
	public function CMSModule()
	{
		global $CMS_STYLESHEET;
		global $CMS_ADMIN_PAGE;
		global $CMS_MODULE_PAGE;
		global $CMS_INSTALL_PAGE;
		//$this->curlang = cms_current_language(); // current language for this request. <- Messes up frontend NLS stuff

		if( !isset($CMS_ADMIN_PAGE) && !isset($CMS_STYLESHEET) && !isset($CMS_INSTALL_PAGE))
		  {
		    $this->SetParameterType('assign',CLEAN_STRING);
		    $this->SetParameterType('module',CLEAN_STRING);
		    $this->SetParameterType('lang',CLEAN_STRING);
		    $this->SetParameterType('returnid',CLEAN_INT);
		    $this->SetParameterType('action',CLEAN_STRING);
		    $this->SetParameterType('showtemplate',CLEAN_STRING);
		    $this->SetParameterType('inline',CLEAN_INT);

		    $this->InitializeFrontend();
		  }
		else if( isset($CMS_ADMIN_PAGE) && !isset($CMS_STYLESHEET) && !isset($CMS_INSTALL_PAGE) )
		  {
		    $this->params[0]['help'] = lang('langparam');
		    $this->InitializeAdmin();
		  }
	}


	/**
	 * Private
	 *
	 * @ignore
	 */
	function LoadTemplateMethods()
	{
		if (!$this->modtemplates)
		{
			require_once(cms_join_path(dirname(__FILE__), 'module_support', 'modtemplates.inc.php'));
			$this->modtemplates = true;
		}
	}

	
	/**
	 * Private
	 *
	 * @ignore
	 */
	function LoadLangMethods()
	{
		if (!$this->modlang)
		{
			require_once(cms_join_path(dirname(__FILE__), 'module_support', 'modlang.inc.php'));
			$this->modlang = true;
		}
	}
	
	/**
	 * Private
	 *
	 * @ignore
	 */
	function LoadFormMethods()
	{
		if (!$this->modform)
		{
			require_once(cms_join_path(dirname(__FILE__), 'module_support', 'modform.inc.php'));
			$this->modform = true;
		}
	}
	
	/**
	 * Private
	 *
	 * @ignore
	 */
        function LoadRedirectMethods()
	{
		if (!$this->modredirect)
		{
			require_once(cms_join_path(dirname(__FILE__), 'module_support', 'modredirect.inc.php'));
			$this->modredirect = true;
		}
	}
	
	/**
	 * Private
	 *
	 * @ignore
	 */
	function LoadMiscMethods()
	{
		if (!$this->modmisc)
		{
			require_once(cms_join_path(dirname(__FILE__), 'module_support', 'modmisc.inc.php'));
			$this->modmisc = true;
		}
	}

	/**
	 * ------------------------------------------------------------------
	 * Plugin Functions.
	 * ------------------------------------------------------------------
	 */

	/**
	 * Callback function for module plugins.
	 * This method is used to call the module from
	 * within template co.
	 *
	 * This function should not be overridden
	 *
	 * @final
	 * @return mixed module call output.
	 * @ignore
	 */
	final static public function function_plugin($params,&$template)
	{
	  $class = get_called_class();
	  $params['module'] = $class;
	  return cms_module_plugin($params,$template);
	}


	/**
	 * Register a smarty plugin and attach it to this module.
	 * This method registers a static plugin to the plugins database table, and should be used only when a module
	 * is installed or upgraded.
	 *
	 * @see smarty documentation.
	 * @author calguy1000
	 * @since 1.11
	 * @param string The plugin name
	 * @param string The plugin type (function,compiler,block, etc)
	 * @param mixed  The function callback (must be a static function)
	 * @param boolean Wether this function is cachable.
	 * @param integer Indicates frontend (0), or frontend and backend (1) availability.
	 * @return void
	 */
	public function RegisterSmartyPlugin($name,$type,$callback,$cachable = TRUE,$usage = 0)
	{
	  if( !$name || !$type || !$callback )
	    {
	      throw new CmsException('Invalid data passed to RegisterSmartyPlugin');
	    }

	  // todo: check name, and type
	  if( $usage == 0 ) $usage = cms_module_smarty_plugin_manager::AVAIL_FRONTEND;
	  cms_module_smarty_plugin_manager::addStatic($this->GetName(),$name,$type,$callback,$cachable,$usage);
	}

	/**
	 * Unregister a smarty plugin from the system.
	 * This method removes any matching rows from the database, and should only be used in a modules uninstall or upgrade routine.
	 * 
	 * @author calguy1000
	 * @since 1.11
	 * @param string The smarty plugin name.  If no name is specified all smarty plugins registered to this module will be removed.
	 * @return void
	 */
	public function RemoveSmartyPlugin($name = '')
	{
	  if( $name == '' )
	    {
	      cms_module_smarty_plugin_manager::remove_by_module($this->GetName());
	    }
	  else
	    {
	      cms_module_smarty_plugin_manager::remove_by_name($name);
	    }
	}

	/**
	 * Register a plugin to smarty with the
	 * name of the module.  This method should be called
	 * from the module constructor, or from the SetParameters
	 * method.
	 *
	 * Note: 
	 * @final
	 * @return void
	 * @see SetParameters
	 * @see can_cache_output
	 * @param boolean Indicate wether this registration should be forced to be entered in the database. Default value is false (for compatibility)
	 * @param mixed Indicate wether this plugins output should be cachable.  If null, use the site preferences, and the can_cache_output method.  Otherwise a boolean is expected.
	 * @deprecated
	 */
	final public function RegisterModulePlugin($forcedb = FALSE,$cachable = null)
	{
	  global $CMS_ADMIN_PAGE;
	  global $CMS_INSTALL_PAGE;

	  if( is_null($cachable) )
	    {
	      $do_cache = get_site_preference('smarty_cachemodules','never');
	      $cachable = false;
	      if( $do_cache == 'always' )
		{
		  $cachable = true;
		}
	      else if( $do_cache == 'moduledecides' )
		{
		  $cachable = $this->can_cache_output();
		}
	    }

	  // frontend request.
	  $admin_req = (isset($CMS_ADMIN_PAGE) && !$this->LazyLoadAdmin())?1:0;
	  $fe_req = (!isset($CMS_ADMIN_PAGE) && !$this->LazyLoadFrontend())?1:0;
	  if( ($fe_req || $admin_req) && !$forcedb )
	    {
	      // no lazy loading.
	      $gCms = cmsms();
	      $smarty = $gCms->GetSmarty();
	      $smarty->register_function($this->GetName(),
					 array($this->GetName(),'function_plugin'),
					 $cachable
					 );
	      return TRUE;
	    }
	  else
	    {
	      return cms_module_smarty_plugin_manager::addStatic($this->GetName(),$this->GetName(),'function',
								 'function_plugin',$cachable);
	    }
	}

	/**
	 * Callback to determine if the output from a call to the module can be cached by smarty. 
	 *
	 * @since 1.11
	 * @author Robert Campbell
	 * @return boolean
	 */
	final public function can_cache_output()
	{
	  global $CMS_ADMIN_PAGE, $CMS_INSTALL_PAGE, $CMS_STYLESHEET;
	  if( isset($CMS_ADMIN_PAGE) || isset($CMS_INSTALL_PAGE) || isset($CMS_STYLESHEET) ) return FALSE;
	  return $this->AllowSmartyCaching();
	}

	/**
	 * Callback to determine if the output from a call to the module can be cached by smarty.
	 *
	 * @since 1.11
	 * @author Robert Campbell
	 * @return boolean
	 */
	public function AllowSmartyCaching()
	{
	  return FALSE;
	}

	/**
	 * ------------------------------------------------------------------
	 * Basic Functions.	 Name and Version MUST be overridden.
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns a sufficient about page for a module
	 *
	 * @abstract
	 * @return string The about page HTML text.
	 */
	function GetAbout()
	{
		$this->LoadMiscMethods();
		return cms_module_GetAbout($this);
	}

	/**
	 * Returns a sufficient help page for a module
	 * this function should not be overridden
	 *
	 * @return string The help page HTML text.
	 * @final
	 */
	final public function GetHelpPage()
	{
		$this->LoadMiscMethods();
		return cms_module_GetHelpPage($this);
	}

	/**
	 * Returns the name of the module
	 *
	 * @abstract
	 * @return string The name of the module.
	 */
	function GetName()
	{
	  return get_class($this);
	}

	/**
	 * Returns the full path to the module directory.
	 *
	 * @final
	 * @return string The full path to the module directory.
	 */
	final public function GetModulePath()
	{
		if (is_subclass_of($this, 'CMSModule'))
		{
			return cms_join_path($this->config['root_path'], 'modules' , $this->GetName());
		}
		else
		{
			return dirname(__FILE__);
		}
	}

	/**
	 * Returns the URL path to the module directory.
	 *
	 * @final
	 * @param boolean Optional generate an URL using HTTPS path
	 * @return string The full path to the module directory.
	 */
	final public function GetModuleURLPath($use_ssl=false)
	{
	  return ($use_ssl?$this->config['ssl_url']:$this->config->smart_root_url()) . '/modules/' . $this->GetName();
	}


	/**
	 * Returns a translatable name of the module.  For modulues who's names can
	 * probably be translated into another language (like News)
	 *
	 * @abstract
	 * @return string
	 */
	function GetFriendlyName()
	{
		return $this->GetName();
	}

	/**
	 * Returns the version of the module
	 *
	 * @abstract
	 * @return string
	 */
	function GetVersion()
	{
		return '0.0.0.1';
	}

	/**
	 * Returns the minimum version necessary to run this version of the module.
	 * 
	 * @abstract
	 * @return string
	 */
	function MinimumCMSVersion()
	{
		global $CMS_VERSION;
		return $CMS_VERSION;
	}

	/**
	 * Returns the maximum version necessary to run this version of the module.
	 *
	 * @abstract
	 * @deprecated
	 * @return string
	 */
	function MaximumCMSVersion()
	{
		global $CMS_VERSION;
		return $CMS_VERSION;
	}

	/**
	 * Returns the help for the module
	 *
	 * @param string Optional language that the admin is using.	 If that language
	 * is not defined, use en_US.
	 *
	 * @abstract
	 * @return string Help HTML Text.
	 */
	function GetHelp()
	{
		return '';
	}

	/**
	 * Returns XHTML that needs to go between the <head> tags when this module is called from an admin side action.
	 *
	 * This method is called by the admin theme when executing an action for a specific module.
	 *
	 * @return string XHTML text
	 */
	function GetHeaderHTML()
	{
	  return '';
	}

	/**
	 * Use this method to prevent the admin interface from outputting header, footer,
	 * theme, etc, so your module can output files directly to the administrator.
	 * Do this by returning true.
	 *
	 * @param  array The input request.  This can be used to test conditions wether or not admin output should be suppressed.
	 * @return boolean
	 */
	function SuppressAdminOutput(&$request)
	{
		return false;
	}

	/**
	 * Register a route to use for pretty url parsing
	 *
	 * Note: This method is not compatible wih lazy loading in the front end.
	 *
	 * @final
	 * @see SetParameters
	 * @param string Regular Expression Route to register
	 * @param array Defaults for parameters that might not be included in the url
	 * @return void
	 */
	final public function RegisterRoute($routeregex, $defaults = array())
	{
		$route = new CmsRoute($routeregex,$this->GetName(),$defaults);
		cms_route_manager::register($route);
	}


	/**
	 * Register all static routes for this module.
	 *
	 * @since 1.11
	 * @author Robert Campbell
	 * @return void
	 */
	public function CreateStaticRoutes() {}


	/**
	 * Returns a list of parameters and their help strings in a hash.  This is generally
	 * used internally.
	 *
	 * @final
	 * @internal
	 * @access private
	 * @return array
	 */
	final public function GetParameters()
	{
	  if( count($this->params) == 1 && $this->params[0]['name'] == 'lang' )
	    {
	      // quick hack to load parameters if they are not already loaded.
	      $this->InitializeAdmin();
	    }
	  return $this->params;
	}

	/**
	 * Called from within the constructor,  This method should be overridden to call the CreaeteParameter
	 * method for each parameter that the module understands.
	 *
	 * Note: In past versions of CMSMS This method was used for both admin and frontend requests to
         * register routes, and create parameters, and register a module plugin, etc.  As of version 1.10
	 * this method is deprecated, and the appropriate functions are InitializeFrontend() and InitializeAdmin()
	 * This method is scheduled for removal in version 1.11
	 * 
	 * @abstract
	 * @see CreateParameter
	 * @see InitializeFrontend()
	 * @see InitializeAdmin()
	 * @deprecated
	 * @return void
	 */
	function SetParameters()
	{
	}

	/**
	 * Called from within the constructor, ONLY for frontend module 
         * actions.  This method should be overridden to create routes, and
	 * set handled parameters, and perform other initialization tasks
	 * that need to be setup for all frontend actions.
	 * 
	 * @abstract
	 * @see SetParameterType
	 * @see RegisterRoute
	 * @see RestrictUnknownParams
	 * @see RegisterModulePlugin
	 * @return void
	 */
	protected function InitializeFrontend()
	{
	  $this->SetParameters(); // for backwards compatibility purposes. may be removed.
	}


	/**
	 * Called from within the constructor, ONLY for admin module 
         * actions.  This method should be overridden to create routes, and
	 * set handled parameters, and perform other initialization tasks
	 * that need to be setup for all frontend actions.
	 * 
	 * @abstract
	 * @see CreateParameter
	 * @return void
	 */
	protected function InitializeAdmin()
	{
	  $this->SetParameters(); // for backwards compatibility purposes. may be removed.
	}

	
	/**
	 * A method to indicate that the system should drop and optionally
	 * generate an error about unknown parameters on frontend actions.
	 * 
	 * @see SetParameterType
	 * @see CreateParameter
	 * @final
	 * @param boolean Flag indicating wether unknown params should be restricted.
	 * @return void
	 */
	final public function RestrictUnknownParams($flag = true)
	{
	  $this->restrict_unknown_params = $flag;
	}


	/**
	 * Indicate the name of, and type of a parameter that is 
	 * acceptable for frontend actions.
	 *
	 * possible values for type are:
	 * CLEAN_INT,CLEAN_FLOAT,CLEAN_NONE,CLEAN_STRING
	 * 
	 * i.e:
	 * $this->SetParameterType('numarticles',CLEAN_INT);
	 *
	 * @see CreateParameter
	 * @see SetParameters
	 * @final
	 * @param string Parameter name;
	 * @param define Parameter type;
	 * @return void;
	 */
	final public function SetParameterType($param, $type)
	{
	  switch($type)
	    {
	    case CLEAN_INT:
	    case CLEAN_FLOAT:
	    case CLEAN_NONE:
	    case CLEAN_STRING:
	      $this->param_map[trim($param)] = $type;
	      break;
	    default:
	      trigger_error('Attempt to set invalid parameter type');
	      break;
	    }
	}


	/**
	 * Create a parameter and it's documentation for display in the
	 * module help.
	 *
	 * i.e:
	 * $this->CreateParameter('numarticles',100000,$this->Lang('help_numarticles'),true);
	 *
	 * @see SetParameters
	 * @see SetParameterType
	 * @final
	 * @param string Parameter name;
	 * @param string Default parameter value
	 * @param string Help String
	 * @param boolean Flag indicating wether this parameter is optional or required.
	 * @return void;
	 */
	final public function CreateParameter($param, $defaultval='', $helpstring='', $optional=true)
	{
		//was: array_unshift(
		array_push($this->params, array(
			'name' => $param,
			'default' => $defaultval,
			'help' => $helpstring,
			'optional' => $optional
		));
	}

	/**
	 * Returns a short description of the module
	 *
	 * @abstract
	 * @param string Optional language that the admin is using.	 If that language
	 * is not defined, use en_US.
	 * @return string
	 */
	function GetDescription($lang = 'en_US')
	{
		return '';
	}

	/**
	 * Returns a description of what the admin link does.
	 *
	 * @abstract
	 * @return string
	 */
	function GetAdminDescription()
	{
		return '';
	}

	/**
	 * Returns whether this module should only be loaded from the admin
	 *
	 * @abstract
	 * @return boolean
	 */
	function IsAdminOnly()
	{
		return false;
	}

	/**
	 * Returns the changelog for the module
	 *
	 * @return string HTML text of the changelog.
	 */
	function GetChangeLog()
	{
		return '';
	}

	/**
	 * Returns the name of the author
	 *
	 * @abstract
	 * @return string The name of the author.
	 */
	function GetAuthor()
	{
		return '';
	}

	/**
	 * Returns the email address of the author
	 *
	 * @abstract
	 * @return string The email address of the author.
	 */
	function GetAuthorEmail()
	{
		return '';
	}

	/**
	 * ------------------------------------------------------------------
	 * Reference functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns the cms->config object as a reference
	 * 
	 * @final
	 * @deprecated
	 * @return array The config hash.
	 */
	final public function GetConfig()
	{
	  return cmsms()->GetConfig();
	}

	/**
	 * Returns the cms->db object as a reference
	 *
	 * @final
	 * @deprecated
	 * @return object Adodb Database object.
	 */
	final public function & GetDb()
	{
	  return cmsms()->GetDb();
	}

	/**
	 * Returns CMSMS temporary and state variables.
	 *
	 * @final
	 * @deprecated Subject for removal in CMSMS 1.12
	 * @return array a hash of CMSMS temporary variables.
	 */
	final public function GetVariables()
	{
	  return cmsms()->variables;
	}

	/**
	 * ------------------------------------------------------------------
	 * Content Block Related Functions
	 * ------------------------------------------------------------------
	 */
	
	/**
	 * Get an input field for a module generated content block type.
	 *
	 * This method can be overridden if the module is providing content
         * block types to the CMSMS content objects.
	 *
	 * @abstract
	 * @param string Content block name
	 * @param mixed  Content block value
	 * @param array  Content block parameters
	 * @param boolean A flag indicating wether the content editor is in create mode (adding) vs. edit mod.
	 * @return mixed Either an array with two elements (prompt, and xhtml element) or a string containing only the xhtml input element.
	 */
	function GetContentBlockInput($blockName,$value,$params,$adding = false)
	{
	  return FALSE;
	}

	/**
	 * Return a value for a module generated content block type.
	 * Given input parameters (i.e: via _POST or _REQUEST), this method
	 * will extract a value for the given content block information.
	 *
	 * This method can be overridden if the module is providing content
         * block types to the CMSMS content objects.
	 *
	 * @abstract
	 * @param string Content block name
	 * @param array  Content block parameters
	 * @param array  input parameters
	 * @return mixed The content block value if possible.
	 */
	function GetContentBlockValue($blockName,$blockParams,$inputParams)
	{
	  return FALSE;
	}


	/**
	 * Validate the value for a module generated content block type.
	 *
	 * This method can be overridden if the module is providing content
         * block types to the CMSMS content objects.
	 *
	 * @abstract
	 * @param string Content block name
	 * @param mixed  Content block value
	 * @param arrray Content block parameters.
	 * @return string An error message if the value is invalid, empty otherwise.
	 */
	function ValidateContentBlockValue($blockName,$value,$blockparams)
	{
	  return '';
	}

	/**
	 * Register a bulk content action
	 *
	 * For use in the CMSMS content list this method allows a module to 
         * register a bulk content action.
	 *
	 * @final
	 * @param string A label for the action
	 * @param string A module action name.
	 * @return void
	 */
	final public function RegisterBulkContentFunction($label,$action)
	{
	  bulkcontentoperations::register_function($label,$action,$this->GetName());
	}


	/**
	 * ------------------------------------------------------------------
	 * Content Type Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Does this module support a custom content type?
	 *
	 * This method can be overridden if the module is defining one or more
	 * custom content types (note this is different than content block 
         * types)
	 *
	 * @abstract
	 * @return boolean
	 */
	function HasContentType()
	{
		return FALSE;
	}
	
	/**
	 * Register a new content type
	 *
	 * @deprecated
	 * @final
	 * @param string A name for the new content type
	 * @param string A filename containing the content type definition.
	 * @param string A friendly name for this content type.
	 * @return void
	 */
	final public function RegisterContentType($name, $file, $friendlyname = '')
	{
	  $contentops = cmsms()->GetContentOperations();

	  $obj = new CmsContentTypePlaceholder();
	  $obj->class = $name;
	  $obj->type  = strtolower($name);
	  $obj->filename = $file;
	  $obj->loaded = false;
	  $obj->friendlyname = ($friendlyname != '' ? $friendlyname : $name);
	  $contentops->register_content_type($obj);
	}

	/**
	 * Return an instance of the new content type
	 *
	 * This method must be overridden if this object is providing
	 * a content type.
	 *
	 * @abstract
	 * @deprecated
	 * @return object
	 */
	function GetContentTypeInstance()
	{
		return FALSE;
	}

// 	// what is this?
// 	function IsExclusive()
// 	{
// 		return FALSE;
// 	}

	/**
	 * ------------------------------------------------------------------
	 * Installation Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Function that will get called as module is installed. This function should
	 * do any initialization functions including creating database tables. It
	 * should return a string message if there is a failure. Returning nothing (FALSE)
	 * will allow the install procedure to proceed.
	 *
	 * The default behavior of this method is to include a file named method.install.php
	 * in the module directory, if one can be found.  This provides a way of splitting
	 * secondary functions into other files.
	 *
	 * @abstract
	 * @return mixed FALSE indicates no error.  Any other value will be used as an error message.
	 */
	function Install()
	{
		$filename = dirname(dirname(dirname(__FILE__))) . '/modules/'.$this->GetName().'/method.install.php';
		if (@is_file($filename))
		{
			{
			  $gCms = cmsms();
				$db = $gCms->GetDb();
				$config = $gCms->GetConfig();
				$smarty = $gCms->GetSmarty();

				$res = include($filename);
				if( $res == 1 || $res == '' ) return FALSE;
				return $res;
			}
		}
		else
		{
			return FALSE;
		}
	}


	/**
	 * Display a message after a successful installation of the module.
	 *
	 * @abstract
	 * @return XHTML Text
	 */
	function InstallPostMessage()
	{
		return FALSE;
	}


	/**
	 * Function that will get called as module is uninstalled. This function should
	 * remove any database tables that it uses and perform any other cleanup duties.
	 * It should return a string message if there is a failure. Returning nothing
	 * (FALSE) will allow the uninstall procedure to proceed.
	 *
	 * The default behaviour of this function is to include a file called method.uninstall.php
	 * in your module directory to do uninstall operations.
	 *
	 * @abstract
	 * @return mixed FALSE indicates that the module uninstalled correctly, any other value indicates an error message.
	 */
	function Uninstall()
	{
		$filename = dirname(dirname(dirname(__FILE__))) . '/modules/'.$this->GetName().'/method.uninstall.php';
		if (@is_file($filename))
		{
		  $gCms = cmsms();
		  $db = $gCms->GetDb();
		  $config = $gCms->GetConfig();
		  $smarty = $gCms->GetSmarty();
		  
		  $res = include($filename);
		  if( $res == 1 || $res == '') return FALSE;
		  if( is_string($res)) 
		    {
		      $modops = $gCms->GetModuleOperations();
		      $modops->SetError($res);
		    }
		  return $res;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Function that gets called upon module uninstall, and returns a boolean to indicate whether or
	 * not the core should remove all module events, event handlers, module templates, and preferences.
	 * The module must still remove its own database tables and permissions
	 * @abstract
	 * @return boolean whether the core may remove all module events, event handles, module templates, and preferences on uninstall (defaults to true)
	 */
	function AllowUninstallCleanup()
	{
		return true;
	}

	/**
	 * Display a message and a Yes/No dialog before doing an uninstall.	 Returning noting
	 * (FALSE) will go right to the uninstall.
	 *
	 * @abstract
	 * @return XHTML Text, or FALSE.
	 */
	function UninstallPreMessage()
	{
		return FALSE;
	}

	/**
	 * Display a message after a successful uninstall of the module.
	 *
	 * @abstract
	 * @return XHTML Text, or FALSE
	 */
	function UninstallPostMessage()
	{
		return FALSE;
	}

	/**
	 * Function to perform any upgrade procedures. This is mostly used to for
	 * updating databsae tables, but can do other duties as well. It should
	 * return a string message if there is a failure. Returning nothing (FALSE)
	 * will allow the upgrade procedure to proceed. Upgrades should have a path
	 * so that they can be upgraded from more than one version back.  While not
	 * a requirement, it makes life easy for your users.
	 *
	 * The default behavior of this method is to call a function called method.upgrade.php
	 * in your module directory, if it exists.
	 *
	 * @param string The version we are upgrading from
	 * @param string The version we are upgrading to
	 * @return boolean
	 */
	function Upgrade($oldversion, $newversion)
	{
		$filename = dirname(dirname(dirname(__FILE__))) . '/modules/'.$this->GetName().'/method.upgrade.php';
		if (@is_file($filename))
		{
			{
			  $gCms = cmsms();
				$db = $gCms->GetDb();
				$config = $gCms->GetConfig();
				$smarty = $gCms->GetSmarty();

				$res = include($filename);
				if( $res == 1 || $res == '' ) return TRUE;
				$modops = $gCms->GetModuleOperations();
				//$modops->SetError($res);
				return FALSE;
			}
		}
	}

	/**
	 * Returns whether or not modules should be autoupgraded while upgrading
	 * CMS versions.  Generally only useful for modules included with the CMS
	 * base install, but there could be a situation down the road where we have
	 * different distributions with different modules included in them.	 Defaults
	 * to TRUE, as there is not many reasons to not allow it.
	 *
	 * @abstract
	 * @return boolean
	 */
	function AllowAutoInstall()
	{
		return TRUE;
	}

	/**
	 * Returns whether or not modules should be autoupgraded while upgrading
	 * CMS versions.  Generally only useful for modules included with the CMS
	 * base install, but there could be a situation down the road where we have
	 * different distributions with different modules included in them.	 Defaults
	 * to TRUE, as there is not many reasons to not allow it.
	 *
	 * @abstract
	 * @return boolean
	 */
	function AllowAutoUpgrade()
	{
		return TRUE;
	}

	/**
	 * Returns a list of dependencies and minimum versions that this module
	 * requires. It should return an hash, eg.
	 * return array('somemodule'=>'1.0', 'othermodule'=>'1.1');
	 *
	 * @abstract
	 * @return array
	 */
	function GetDependencies()
	{
		return array();
	}

	/**
	 * Checks to see if currently installed modules depend on this module.	This is
	 * used by the plugins.php page to make sure that a module can't be uninstalled
	 * before any modules depending on it are uninstalled first.
	 *
	 * @internal
	 * @access private
	 * @final
	 * @return boolean
	 */
	final public function CheckForDependents()
	{
	  $gCms = cmsms();
		$db = $gCms->GetDb();

		$result = false;

		$query = "SELECT child_module FROM ".cms_db_prefix()."module_deps WHERE parent_module = ? LIMIT 1";
		$tmp = $db->GetOne($query,array($this->GetName()));
		if( $tmp ) 
		  {
		    $result = true;
		  }

		return $result;
	}


	/**
	 * Creates an xml data package from the module directory.
	 *
	 * @final
	 * @return string XML Text
	 * @param string reference to returned message.
	 * @param integer reference to returned file count.
	 */
	final public function CreateXMLPackage( &$message, &$filecount )
	{
	  $gCms = cmsms();
		$modops = $gCms->GetModuleOperations();
		return $modops->CreateXmlPackage($this, $message, $filecount);
	}


	/**
	 * Return true if there is an admin for the module.	 Returns false by
	 * default.
	 *
	 * @abstract
	 * @return boolean
	 */
	function HasAdmin()
	{
		return false;
	}

	/**
	 * Returns which admin section this module belongs to.
	 * this is used to place the module in the appropriate admin navigation
	 * section. Valid options are currently:
	 *
	 * content, layout, files, usersgroups, extensions, preferences, siteadmin, ecommerce
	 *
	 * @abstract
	 * @return string
	 */
	function GetAdminSection()
	{
		return 'extensions';
	}

	/**
	 * Returns true or false, depending on whether the user has the
	 * right permissions to see the module in their Admin menus.
	 *
	 * Typically permission checks are done in the overriden version of
	 * this method.
	 *
	 * Defaults to true.
	 *
	 * @abstract
	 * @return boolean
	 */
	function VisibleToAdminUser()
	{
	  return true;
	}

	/**
	 * Returns true if the module should be treated as a content module.
	 * Returns false by default.
	 *
	 * @abstract
	 * @return boolean
	 */
	function IsContentModule()
	{
		return false;
	}

	/**
	 * Returns true if the module should be treated as a plugin module (like
	 * {cms_module module='name'}.	Returns false by default.
	 *
	 * @abstract
	 * @return boolean
	 */
	function IsPluginModule()
	{
		return false;
	}


	/**
	 * Returns true if the module acts as a soap server
	 *
	 * @abstract
	 * @return boolean
	 */
	function IsSoapModule()
	{
		return false;
	}

	/**
	 * Returns true if the module may support lazy loading in the front end
	 *
	 * @since 1.8
	 * @abstract
	 * @return boolean
	 * @deprecated
	 */
	public function SupportsLazyLoading()
	{
	  return LazyLoadFrontend();
	}

	/**
	 * Returns true if the module may support lazy loading in the front end
	 *
	 * Note: The results of this function are not read on each request, only during install and upgrade
	 * therefore if the return value of this function changes the version number of the module should be
	 * increased to force a re-load
	 *
	 * In CMSMS 1.10 routes are loaded upon each request, if a module registers routes it cannot be lazy loaded.
	 *
	 * @since 1.10
	 * @abstract
	 * @return boolean
	 */
	public function LazyLoadFrontend()
	{
	  return FALSE;
	}

	/**
	 * Returns true if the module may support lazy loading in the admin interface.
	 *
	 * Note: The results of this function are not read on each request, only during install and upgrade
	 * therefore if the return value of this function changes the version number of the module should be
	 * increased to force a re-load
	 *
	 * In CMSMS 1.10 routes are loaded upon each request, if a module registers routes it cannot be lazy loaded.
	 *
	 * @since 1.10
	 * @abstract
	 * @return boolean
	 */
	public function LazyLoadAdmin()
	{
	  return FALSE;
	}


	/**
	 * ------------------------------------------------------------------
	 * HTML Blob / GCB Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * ------------------------------------------------------------------
	 * Module capabilities, a new way of checking what a module can do
	 * ------------------------------------------------------------------
	 */
	
	/**
	 * Returns true if the modules thinks it has the capability specified
	 *
	 * @abstract
	 * @param an id specifying which capability to check for, could be "wysiwyg" etc.
	 * @param associative array further params to get more detailed info about the capabilities. Should be syncronized with other modules of same type
	 * @return boolean
	 */
	function HasCapability($capability, $params=array())
	{
	  return false;
	}


	/**
	 * Returns a list of the tasks that this module manages
	 *
	 * @since 1.8
	 * @abstract
	 * @return array of CmsRegularTask objects, or one object.  NULL if not handled.
	 */
	function get_tasks()
	{
	  return FALSE;
	}

	
	/**
	 * ------------------------------------------------------------------
	 * Syntax Highlighter Related Functions
	 *
	 * These functions are only used if creating a syntax highlighter module.
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns true if this module should be treated as a Syntax Highlighting module. It
	 * returns false be default.
	 *
	 * @abstract
	 * @return boolean
	 */
	function IsSyntaxHighlighter()
	{
		return false;
	}
	
	/**
	 * Returns true if this SyntaxHighlighter should be considered active, eventhough it's
	 * not the choice of the user. Used for forcing a wysiwyg.
	 * returns false be default.
	 *
	 * @abstract
	 * @return boolean
	 */
	function SyntaxActive()
	{
		return $this->syntaxactive;
	}

	/**
	 * Returns content destined for the <form> tag.	 It's useful if javascript is
	 * needed for the onsubmit of the form.
	 *
	 * @abstract
	 * @return string
	 */
	function SyntaxPageForm()
	{
		return '';
	}

	/**
	 * This is a function that would be called before a form is submitted.
	 * Generally, a dropdown box or something similar that would force a submit
	 * of the form via javascript should put this in their onchange line as well
	 * so that the SyntaxHighlighter can do any cleanups before the actual form submission
	 * takes place.
	 *
	 * @abstract
	 * @return string
	 */
	 function SyntaxPageFormSubmit()
	 {
		return '';
	 }

	 /**
	  * Returns header code specific to this SyntaxHighlighter
	  *
	  *
	  * @abstract
	  * @param string The html-code of the page before replacing SyntaxHighlighter-stuff
	  * @return string
	  */
	  function SyntaxGenerateHeader($htmlresult='')
	  {
		return '';
	  }

	 /**
	  * Returns body code specific to this SyntaxHighlighter
	  *
	  * @abstract
	  * @return string
	  */
	  function SyntaxGenerateBody()
	  {
		return '';
	  }

	/**
	 * Returns the textarea specific for this WYSIWYG.
	 *
	 * @abstract
	 * @param string HTML name of the textarea
	 * @param string the language which the content should be highlighted as
	 * @param int Number of columns wide that the textarea should be
	 * @param int Number of rows long that the textarea should be
	 * @param string Encoding of the content
	 * @param string Content to show in the textarea
	 * @param string Stylesheet for content, if available
	 * @param string additional attributes to include in textarea tag.
	 * @return string
	 */
	  function SyntaxTextarea($name='textarea',$syntax='html',$columns='80',$rows='15',$encoding='',$content='',$stylesheet='',$addtext='')
	{
		$this->syntaxactive=true;
		return '<textarea name="'.$name.'" cols="'.$columns.'" rows="'.$rows.'" '.$addtext.' >'.$content.'</textarea>';
	}

	/**
	 * Returns whether or not this module should show in any module lists generated by a WYSIWYG.
	 *
	 * @abstract
	 * @return boolean
	 */
	function ShowInSyntaxList()
	{
		return true;
	}
	
	
	
	/**
	 * ------------------------------------------------------------------
	 * WYSIWYG Related Functions
	 *
	 * These methods are only useful for creating wysiwyg editor modules.
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns true if this module should be treated as a WYSIWYG module. It
	 * returns false be default.
	 *
	 * @abstract
	 * @return boolean
	 */
	function IsWYSIWYG()
	{
		return false;
	}

	/**
	 * Returns true if this wysiwyg should be considered active, eventhough it's
	 * not the choice of the user. Used for forcing a wysiwyg.
	 * returns false be default.
	 *
	 * @abstract
	 * @return boolean
	 */
	function WYSIWYGActive()
	{
		return $this->wysiwygactive;
	}

	/**
	 * Returns content destined for the <form> tag.	 It's useful if javascript is
	 * needed for the onsubmit of the form.
	 *
	 * @abstract
	 * @return string
	 */
	function WYSIWYGPageForm()
	{
		return '';
	}

	/**
	 * This is a function that would be called before a form is submitted.
	 * Generally, a dropdown box or something similar that would force a submit
	 * of the form via javascript should put this in their onchange line as well
	 * so that the WYSIWYG can do any cleanups before the actual form submission
	 * takes place.
	 *
	 * @abstract
	 * @return string
	 */
	 function WYSIWYGPageFormSubmit()
	 {
		return '';
	 }

	 /**
	  * Returns header code specific to this WYSIWYG
	  *
	  * @abstract
	  * @param string The html-code of the page before replacing WYSIWYG-stuff
	  * @return string
	  */
	  function WYSIWYGGenerateHeader($htmlresult='')
	  {
		return '';
	  }

	 /**
	  * Returns body code specific to this WYSIWYG
	  *
	  * @abstract
	  * @return string
	  */
	  function WYSIWYGGenerateBody()
	  {
		return '';
	  }

	  /**
	   * Returns the textarea specific for this WYSIWYG.
	   *
	   * @abstract
	   * @param string HTML name of the textarea
	   * @param int Number of columns wide that the textarea should be
	   * @param int Number of rows long that the textarea should be
	   * @param string Encoding of the content
	   * @param string Content to show in the textarea
	   * @param string Stylesheet for content, if available
	   * @param string Additional attributes to include in textarea tag.
	   * @return string
	   */
	  function WYSIWYGTextarea($name='textarea',$columns='80',$rows='15',$encoding='',$content='',$stylesheet='',$addtext='')
	  {
	    $this->wysiwygactive=true;
	    return '<textarea name="'.$name.'" cols="'.$columns.'" rows="'.$rows.'" '.$addtext.' >'.$content.'</textarea>';
	  }

	/**
	 * Returns whether or not this module should show in any module lists generated by a WYSIWYG.
	 *
	 * @abstract
	 * @return boolean
	 */
	function ShowInWYSIWYG()
	{
		return true;
	}

	/**
	 * ------------------------------------------------------------------
	 * Navigation Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Used for navigation between "pages" of a module.	 Forms and links should
	 * pass an action with them so that the module will know what to do next.
	 * By default, DoAction will be passed 'default' and 'defaultadmin',
	 * depending on where the module was called from.  If being used as a module
	 * or content type, 'default' will be passed.  If the module was selected
	 * from the list on the admin menu, then 'defaultadmin' will be passed.
	 *
	 * In order to allow splitting up functionality into multiple PHP files the default
	 * behaviour of this method is to look for a file named action.<action name>.php
	 * in the modules directory, and if it exists include it.
	 *
	 * @abstract
	 * @param string Name of the action to perform
	 * @param string The ID of the module
	 * @param string The parameters targeted for this module
	 * @param integer The current page id that is being displayed.
	 * @return string output XHTML.
	 */
	function DoAction($name, $id, $params, $returnid='')
	{
	  if( $returnid == '' ) {
	    if( isset($params['__activetab']) ) {
	      $this->__current_tab = trim($params['__activetab']);
	    }
	    if( isset($params['__errors']) ) {
	      $this->__errors = explode('::err::',$params['__errors']);
	    }
	    if( isset($params['__messages']) ) {
	      $this->__messages = explode('::msg::',$params['__messages']);
	    }

	    if( is_array($this->__errors) && count($this->__errors) ) {
	      echo $this->ShowErrors($this->__errors);
	    }
	    if( is_array($this->__messages) && count($this->__messages) ) {
	      echo $this->ShowMessage($this->__messages[0]);
	    }
	  }

	  if ($name != '') {
	    //Just in case DoAction is called directly and it's not overridden.
	    //See: http://0x6a616d6573.blogspot.com/2010/02/cms-made-simple-166-file-inclusion.html
	    $name = preg_replace('/[^A-Za-z0-9\-_+]/', '', $name);

	    $filename = dirname(dirname(dirname(__FILE__))) . '/modules/'.$this->GetName().'/action.' . $name . '.php';
	    if (@is_file($filename)) {
	      $gCms = cmsms();
	      $db = $gCms->GetDb();
	      $config = $gCms->GetConfig();
	      $smarty = $gCms->GetSmarty();
	      
	      include($filename);
	    }
	  }
	}

	/**
	 * This method prepares the data and does appropriate checks before
	 * calling a module action.
	 *
	 * @internal
	 * @ignore
	 * @final
	 * @access private
	 * @param string The action name
	 * @param string The action identifier
	 * @param array  The action params
	 * @param integer The current page id.
	 * @return string The action output.
	 */
	final public function DoActionBase($name, $id, $params, $returnid='')
	{
          $name = preg_replace('/[^A-Za-z0-9\-_+]/', '', $name);
	  if( $returnid != '' )
	    {
	      if( !$this->restrict_unknown_params )
		{
		  // put mention into the admin log
		  audit('',$this->GetName(),'Module is not properly cleaning input params');
		}
	      // used to try to avert XSS flaws, this will
	      // clean as many parameters as possible according
	      // to a map specified with the SetParameterType metods.
	      $params = cleanParamHash($this->GetName(),$params,$this->param_map,
				       !$this->restrict_unknown_params);
	    }

	  // handle the stupid input type='image' problem.
	  foreach( $params as $key => $value )
	    {
	      if( endswith($key,'_x') ) {
		$base = substr($key,0,strlen($key)-2);
		if( isset($params[$base.'_y']) && !isset($params[$base]) )
		  {
		    $params[$base] = $base;
		  }
	      }
	    }

	  if (isset($params['lang']))
	    {
	      $this->curlang = $params['lang'];
	      $this->langhash = array();
	    }
	  if( !isset($params['action']) )
	    {
	      $params['action'] = $name;
	    }
	  $params['action'] = cms_htmlentities($params['action']);
	  $returnid = cms_htmlentities($returnid);
	  $id = cms_htmlentities($id);
	  $name = cms_htmlentities($name);

	  $smarty = cmsms()->GetSmarty();
	  $smarty->assign('actionid',$id);
	  $smarty->assign('actionparams',$params);
	  $smarty->assign('returnid',$returnid);
	  $smarty->assign('actionmodule',$this->GetName());

	  $output = $this->DoAction($name, $id, $params, $returnid);

	  if( isset($params['assign']) )
	    {
	      $gCms = cmsms();
	      $smarty = $gCms->GetSmarty();
	      $smarty->assign(cms_htmlentities($params['assign']),$output);
	      return;
	    }
	  return $output;
	}



  /**
   * ------------------------------------------------------------------
   * Form and XHTML Related Methods
   * ------------------------------------------------------------------
   */

  /**
   * Returns the xhtml equivalent of a tooltip help link.
   *
   * @final
   * @param string The help text to be shown on mouse over
   * @param string The text to be shown as the link, default to a simple question mark
   * @param string Forces another width of the popupbox than the one set in admin css
   * @param string An alternative classname for the a-link of the tooltip
   * @param string The URL or url portion to use in the href portion of the generated link.
   * @deprecated
   * @return string
   */
  function CreateTooltip($helptext, $linktext="?", $forcewidth="", $classname="admin-tooltip admin-tooltip-box", $href="")
  {
    $result='<a class="'.$classname.'"';
    if ($href!='') $result.=' href="'.$href.'"';
    $result.='>'.$linktext.'<span';
    if ($forcewidth!="" && is_numeric($forcewidth)) $result.=' style="width:'.$forcewidth.'px"';
    $result.='>'.htmlentities($helptext)."</span></a>\n";
    return $result;
  }

  /**
   * Returns the xhtml equivalent of a tooltip-enabled href link	 This is basically a nice little wrapper
   * to make sure that id's are placed in names and also that it's xhtml compliant.
   *
   * @final
   * @param string The id given to the module on execution
   * @param string The action that this form should do when the link is clicked
   * @param string The id to eventually return to when the module is finished it's task
   * @param string The text that will have to be clicked to follow the link
   * @param string The helptext to be shown as tooltip-popup
   * @param string An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
   * @deprecated
   * @return string
   */
  function CreateTooltipLink($id, $action, $returnid, $contents, $tooltiptext, $params=array())
  {
    return $this->CreateTooltip($tooltiptext,$contents,"","admin-tooltip",$this->CreateLink($id,$action,$returnid,"admin-tooltip",$params,"",true) );
  }

  /**
   * Returns the xhtml equivalent of an fieldset and legend.  This is basically a nice little wrapper
   * to make sure that id's are placed in names and also that it's xhtml compliant.
   *
   * @final
   * @param string The id given to the module on execution (not really used yet, but might be later)
   * @param string The html name of the textbox (not really used yet, but might be later on)
   * @param string The legend_text for this fieldset, if applicaple
   * @param string Any additional text that should be added into the tag when rendered
   * @param string Any additional text that should be added into the legend tag when rendered
   * @return string
   */
  function CreateFieldsetStart( $id, $name, $legend_text='', $addtext='', $addtext_legend='' )
  {
    $this->LoadFormMethods();
    return cms_module_CreateFieldsetStart($this, $id, $name, $legend_text, $addtext, $addtext_legend);
  }

  /**
   * Returns the end of the fieldset in a  form.  This is basically just a wrapper around </form>, but
   * could be extended later on down the road.  It's here mainly for consistency.
   * 
   * @final
   * @return string
   */
  function CreateFieldsetEnd()
  {
    return '</fieldset>'."\n";
  }


  /**
   * Returns the start of a module form, optimized for frontend use
   *
   * @param string The id given to the module on execution
   * @param string The id to eventually return to when the module is finished it's task
   * @param string The action that this form should do when the form is submitted
   * @param string Method to use for the form tag.  Defaults to 'post'
   * @param string Optional enctype to use, Good for situations where files are being uploaded
   * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
   * @param string Text to append to the end of the id and name of the form
   * @param array Extra parameters to pass along when the form is submitted
   * @return string
   */
  function CreateFrontendFormStart($id,$returnid,$action='default',$method='post',
				   $enctype='',$inline=true,$idsuffix='',$params=array())
  {
    return $this->CreateFormStart($id,$action,$returnid,$method,$enctype,$inline,$idsuffix,$params);
  }


  /**
   * Returns the start of a module form
   *
   * @param string The id given to the module on execution
   * @param string The action that this form should do when the form is submitted
   * @param string The id to eventually return to when the module is finished it's task
   * @param string Method to use for the form tag.  Defaults to 'post'
   * @param string Optional enctype to use, Good for situations where files are being uploaded
   * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
   * @param string Text to append to the end of the id and name of the form
   * @param array Extra parameters to pass along when the form is submitted
   * @param string Text to append to the <form>-statement, for instanse for javascript-validation code
   * @return string
   */
  function CreateFormStart($id, $action='default', $returnid='', $method='post', $enctype='', $inline=false, $idsuffix='', $params = array(), $extra='')
  {
    $this->LoadFormMethods();
    return cms_module_CreateFormStart($this, $id, $action, $returnid, $method, $enctype, $inline, $idsuffix, $params, $extra);
  }

  /**
   * Returns the end of the a module form.  This is basically just a wrapper around </form>, but
   * could be extended later on down the road.  It's here mainly for consistency.
   *
   * @return string
   */
  function CreateFormEnd()
  {
    return '</form>'."\n";
  }

	/**
	 * Returns the xhtml equivalent of an input textbox.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputText($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputText($this, $id, $name, $value, $size, $maxlength, $addttext);
	}

        /**
         * Returns the xhtml equivalent of an label for input field.  This is basically a nice little wrapper
         * to make sure that id's are placed in names and also that it's xhtml compliant.
         *
         * @param string The id given to the module on execution
         * @param string The html name of the input field this label is associated to
         * @param string The text in the label
         * @param string Any additional text that should be added into the tag when rendered
	 * @return string
         */
        function CreateLabelForInput($id, $name, $labeltext='', $addttext='')
        {
	        $this->LoadFormMethods();
	        return cms_module_CreateLabelForInput($this, $id, $name, $labeltext, $addttext);
        }

	/**
	 * Returns the xhtml equivalent of an input textbox with label.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param string The text for label 
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputTextWithLabel($id, $name, $value='', $size='10', $maxlength='255', $addttext='', $label='', $labeladdtext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputTextWithLabel($this, $id, $name, $value, $size, $maxlength, $addttext, $label, $labeladdtext);
	}

	/**
	 * Returns the html5 equivalent of an input of type color.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input field
	 * @param string The predefined value of the textbox, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	
	function CreateInputColor($id, $name, $value='', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputColor($this, $id, $name, $value, $addttext);
	}	

	/**
	 * Returns the html5 equivalent of an input of type date.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input field
	 * @param string The predefined value of the textbox, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	
	function CreateInputDate($id, $name, $value='', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputDate($this, $id, $name, $value, $addttext);
	}
	
	/**
	 * Returns the html5 equivalent of an input of type datetime.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input field
	 * @param string The predefined value of the textbox, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	
	function CreateInputDatetime($id, $name, $value='', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputDatetime($this, $id, $name, $value, $addttext);
	}
	
	/**
	 * Returns the html5 equivalent of an input of type datetime-local.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input field
	 * @param string The predefined value of the textbox, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	
	function CreateInputDatetimeLocal($id, $name, $value='', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputDatetimeLocal($this, $id, $name, $value, $addttext);
	}

	/**
	 * Returns the html5 equivalent of an input of type month.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input field
	 * @param string The predefined value of the textbox, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	
	function CreateInputMonth($id, $name, $value='', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputMonth($this, $id, $name, $value, $addttext);
	}		

	/**
	 * Returns the html5 equivalent of an input of type week.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input field
	 * @param string The predefined value of the textbox, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	
	function CreateInputWeek($id, $name, $value='', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputWeek($this, $id, $name, $value, $addttext);
	}
	
	/**
	 * Returns the html5 equivalent of an input of type time.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input field
	 * @param string The predefined value of the textbox, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	
	function CreateInputTime($id, $name, $value='', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputTime($this, $id, $name, $value, $addttext);
	}	
	
	/**
	 * Returns the html5 equivalent of an input of type number.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input field
	 * @param string The predefined value of the textbox, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	
	function CreateInputNumber($id, $name, $value='', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputNumber($this, $id, $name, $value, $addttext);
	}	
	
	/**
	 * Returns the html5 equivalent of an input of type range.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input field
	 * @param string The predefined value of the textbox, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	
	function CreateInputRange($id, $name, $value='', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputRange($this, $id, $name, $value, $addttext);
	}				

	/**
	 * Returns the html5 equivalent of an input textbox of type email.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */	
	function CreateInputEmail($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputEmail($this, $id, $name, $value, $size, $maxlength, $addttext);
	}
	
	/**
	 * Returns the html5 equivalent of an input textbox of type tel.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */	
	function CreateInputTel($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputTel($this, $id, $name, $value, $size, $maxlength, $addttext);
	}	
	
	/**
	 * Returns the html5 equivalent of an input of type search.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputSearch($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputSearch($this, $id, $name, $value, $size, $maxlength, $addttext);
	}
	
	/**
	 * Returns the html5 equivalent of an input of type url.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputUrl($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputUrl($this, $id, $name, $value, $size, $maxlength, $addttext);
	}							
			

	/**
	 * Returns the xhtml equivalent of a file-selector field.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The MIME-type to be accepted, default is all
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputFile($id, $name, $accept='', $size='10',$addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputFile($this, $id, $name, $accept, $size, $addttext);
	}

	/**
	 * Returns the xhtml equivalent of an input password-box.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputPassword($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputPassword($this, $id, $name, $value, $size, $maxlength, $addttext);
	}

	/**
	 * Returns the xhtml equivalent of a hidden field.	This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the hidden field
	 * @param string The predefined value of the field, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputHidden($id, $name, $value='', $addttext='')
	{
	  $this->LoadFormMethods();
	  return cms_module_CreateInputHidden($this, $id, $name, $value, $addttext);
	}

	/**
	 * Returns the xhtml equivalent of a checkbox.	This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the checkbox
	 * @param string The value returned from the input if selected
	 * @param string The current value. If equal to $value the checkbox is selected 
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputCheckbox($id, $name, $value='', $selectedvalue='', $addttext='')
	{
	  $this->LoadFormMethods();
	  return cms_module_CreateInputCheckbox($this, $id, $name, $value, $selectedvalue, $addttext);
	}
	

	/**
	 * Returns the xhtml equivalent of a submit button.	 This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the button
	 * @param string The predefined value of the button, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param string Use an image instead of a regular button
	 * @param string Optional text to display in a confirmation message.
	 * @return string
	 */
	function CreateInputSubmit($id, $name, $value='', $addttext='', $image='', $confirmtext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputSubmit($this, $id, $name, $value, $addttext, $image, $confirmtext);
	}

	/**
	 * Returns the xhtml equivalent of a reset button.	This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the button
	 * @param string The predefined value of the button, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputReset($id, $name, $value='Reset', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputReset($this, $id, $name, $value, $addttext);
	 }

	/**
	 * Returns the xhtml equivalent of a file upload input.	 This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param integer The size of the text field associated with the file upload field.  Some browsers may not respect this value.
	 * @param integer The maximim length of the content of the text field associated with the file upload field.  Some browsers may not respect this value.
	 * @return string
	 */
	function CreateFileUploadInput($id, $name, $addttext='',$size='10', $maxlength='255')
	{
		$this->LoadFormMethods();
		return cms_module_CreateFileUploadInput($this, $id, $name, $addttext, $size, $maxlength);
	}


	/**
	 * Returns the xhtml equivalent of a dropdown list.	 This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it is xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the dropdown list
	 * @param string An array of items to put into the dropdown list... they should be $key=>$value pairs
	 * @param string The default selected index of the dropdown list.  Setting to -1 will result in the first choice being selected
	 * @param string The default selected value of the dropdown list.  Setting to '' will result in the first choice being selected
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputDropdown($id, $name, $items, $selectedindex=-1, $selectedvalue='', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputDropdown($this, $id, $name, $items, $selectedindex, $selectedvalue, $addttext);
	}
	
	/**
	 * Returns the html5 equivalent input field with datalist options.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's html5 compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string An array of items to put into the list... they should be $key=>$value pairs
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 * @return string
	 */
	function CreateInputDataList($id, $name, $value='', $items, $size='10', $maxlength='255', $addttext='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputDataList($this, $id, $name, $value, $items, $size, $maxlength, $addttext);
	}	

	/**
	 * Returns the xhtml equivalent of a multi-select list.	 This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it is xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the select list
	 * @param string An array of items to put into the list... they should be $key=>$value pairs
	 * @param string An array of items in the list that should default to selected.
	 * @param string The number of rows to be visible in the list (before scrolling).
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param boolean indicates wether multiple selections are allowed (defaults to true)
	 * @return string
	 */
	function CreateInputSelectList($id, $name, $items, $selecteditems=array(), $size=3, $addttext='', $multiple = true)
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputSelectList($this, $id, $name, $items, $selecteditems, $size, $addttext, $multiple);
	}

	/**
	 * Returns the xhtml equivalent of a set of radio buttons.	This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it is xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the radio group
	 * @param string An array of items to create as radio buttons... they should be $key=>$value pairs
	 * @param string The default selected index of the radio group.	 Setting to -1 will result in the first choice being selected
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param string A delimiter to throw between each radio button, e.g., a <br /> tag or something for formatting
	 * @return string
	 */
	function CreateInputRadioGroup($id, $name, $items, $selectedvalue='', $addttext='', $delimiter='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateInputRadioGroup($this, $id, $name, $items, $selectedvalue, $addttext, $delimiter);
	}

	/**
	 * Returns the xhtml equivalent of a textarea.	Also takes WYSIWYG preference into consideration if it's called from the admin side.
	 *
	 * @param bool Should we try to create a WYSIWYG for this textarea?
	 * @param string The id given to the module on execution
	 * @param string The text to display in the textarea's content
	 * @param string The html name of the textarea
	 * @param string The CSS class to associate this textarea to
	 * @param string The html id to give to this textarea
	 * @param string The encoding to use for the content
	 * @param string The text of the stylesheet associated to this content.	 Only used for certain WYSIWYGs
	 * @param string The number of characters wide (columns) the resulting textarea should be
	 * @param string The number of characters high (rows) the resulting textarea should be
	 * @param string The wysiwyg-system to be forced even if the user has chosen another one
	 * @param string The language the content should be syntaxhightlighted as
	 * @param string Any additional text to include with the textarea field.
	 * @return string
	 */
	function CreateTextArea($enablewysiwyg, $id, $text, $name, $classname='', $htmlid='', $encoding='', $stylesheet='', $cols='80', $rows='15',$forcewysiwyg='',$wantedsyntax='',$addtext='')
	{
	  return create_textarea($enablewysiwyg, $text, $id.$name, $classname, $htmlid, $encoding, $stylesheet, $cols, $rows,$forcewysiwyg,$wantedsyntax,$addtext);
	}


	/**
	 * Returns the xhtml equivalent of a textarea.	Also takes Syntax hilighter preference 
         * into consideration if it's called from the admin side.
	 *
	 * @param string The id given to the module on execution
	 * @param string The text to display in the textarea's content
	 * @param string The html name of the textarea
	 * @param string The CSS class to associate this textarea to
	 * @param string The html id to give to this textarea
	 * @param string The encoding to use for the content
	 * @param string The text of the stylesheet associated to this content.	 Only used for certain WYSIWYGs
	 * @param string The number of characters wide (columns) the resulting textarea should be
	 * @param string The number of characters high (rows) the resulting textarea should be
	 * @param string Additional text for the text area tag.
	 * @return string
	 */
	function CreateSyntaxArea($id,$text,$name,$classname='',$htmlid='',$encoding='',
				  $stylesheet='',$cols='80',$rows='15',$addtext='')
	{
	  return create_textarea(false,$text,$id.$name,$classname,$htmlid, $encoding, $stylesheet,
				 $cols,$rows,'','html',$addtext);
	}

	/**
	 * Returns the xhtml equivalent of an href link	 This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string The action that this form should do when the link is clicked
	 * @param string The text that will have to be clicked to follow the link
	 * @param string An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
	 * @param string Text to display in a javascript warning box.  If they click no, the link is not followed by the browser.
	 * @param boolean A flag to determine if only the href section should be returned
	 * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param boolean A flag indicating that the output of this link should target the content area of the destination page.
	 * @param string An optional pretty url segment (relative to the root of the site) to use when generating the link.
	 * @return string
	 */
	function CreateFrontendLink( $id, $returnid, $action, $contents='', $params=array(), $warn_message='',
					 $onlyhref=false, $inline=true, $addtext='', $targetcontentonly=false, $prettyurl='' )
	{
	  return $this->CreateLink( $id, $action, $returnid, $contents, $params, $warn_message, $onlyhref,
				    $inline, $addtext, $targetcontentonly, $prettyurl );
	}

	/**
	 * Returns the xhtml equivalent of an href link	 This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the link is clicked
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string The text that will have to be clicked to follow the link
	 * @param string An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
	 * @param string Text to display in a javascript warning box.  If they click no, the link is not followed by the browser.
	 * @param boolean A flag to determine if only the href section should be returned
	 * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param boolean A flag to determine if the link should target the default content are of the destination page.
	 * @param string An optional pretty url segment (related to the root of the website) for a pretty url.
	 * @return string
	 */
	function CreateLink($id, $action, $returnid='', $contents='', $params=array(), $warn_message='', $onlyhref=false, $inline=false, $addttext='', $targetcontentonly=false, $prettyurl='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateLink($this, $id, $action, $returnid, $contents, $params, $warn_message, $onlyhref, $inline, $addttext, $targetcontentonly, $prettyurl);
	}


	/**
	 * Returns a URL to a module action
	 * This method is called by the CreateLink methods when creating a link to a module action.
	 *
	 * @since 1.10
	 * @param string The module action id (cntnt01 indicates that the defaul content block of the destination page should be used).
	 * @param string The module action name
	 * @param integer The destination page.
	 * @param hash   Areay of parameters for the URL.  These will be ignored if the prettyurl argument is specified.
	 * @param boolean Wether the target of the output link is the same tag on the same page.
	 * @param boolean Wether the target of the output link targets the content area of the destination page.
	 * @param string  An optional url segment related to the root of the page for pretty url purposes.
	 * @return string.
	 */
	public function create_url($id,$action,$returnid='',$params=array(),
				   $inline=false,$targetcontentonly=false,$prettyurl='')
	{
	  $this->LoadFormMethods();
	  return cms_module_create_url($this,$id,$action,$returnid,$params,
				       $inline,$targetcontentonly,$prettyurl);
	}

	/**
	 * Return a pretty url string for a module action
	 * This method is called by the create_url and the CreateLink methods if the pretty url argument is not specified in order
	 * to attempt automating a pretty url for an action.
	 * 
	 * @since 1.10
	 * @abstract
	 * @param string The module action id (cntnt01 indicates that the defaul content block of the destination page should be used).
	 * @param string The module action name
	 * @param integer The destination page.
	 * @param hash   Areay of parameters for the URL.  These will be ignored if the prettyurl argument is specified.
	 * @param boolean Wether the target of the output link is the same tag on the same page.
	 * @return string
	 */
	public function get_pretty_url($id,$action,$returnid='',$params=array(),$inline=false)
	{
	  return '';
	}

	/**
	* Returns the xhtml equivalent of an href link for content links.	This is basically a nice
	* little wrapper to make sure that we go back to where we want and that it's xhtml complient
	*
	* @param string the page id of the page we want to direct to
	* @param string The optional text or XHTML contents of the generated link
	* @return string
	*/
	function CreateContentLink($pageid, $contents='')
	{
		$this->LoadFormMethods();
		return cms_module_CreateContentLink($this, $pageid, $contents);
	}


	/**
	 * Returns the xhtml equivalent of an href link for Content links.	This is basically a nice little wrapper
	 * to make sure that we go back to where we want to and that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The id to return to when the module is finished it's task
	 * @param string The text that will have to be clicked to follow the link
	 * @param string An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
	 * @param boolean A flag to determine if only the href section should be returned
	 * @return string
	 */
	function CreateReturnLink($id, $returnid, $contents='', $params=array(), $onlyhref=false)
	{
		$this->LoadFormMethods();
		return cms_module_CreateReturnLink($this, $id, $returnid, $contents, $params, $onlyhref);
	}


  /**
   * ------------------------------------------------------------------
   * Redirection Methods
   * ------------------------------------------------------------------
   */

	/**
	 * Redirect to the specified tab.
	 * Applicable only to admin actions.
	 *
	 * @since 1.11
	 * @author Robert Campbell
	 * @param string The tab name.  If empty, the current tab is used.
	 * @param mixed  An assoiciative array of params, or null
	 * @param string The action name (if not specified, defaultadmin is assumed)
	 */
	function RedirectToAdminTab($tab = '',$params = '',$action = '')
	{
	  if( $tab == '' ) {
	    if( $this->__current_tab ) {
	      $tab = $this->__current_tab;
	    }
	  }
          if( $params == '' ) {
            $params = array();
          }
	  if( $tab != '' )
	    {
	      $params['__activetab'] = $tab;
	    }
	  if( empty($action) ) $action = 'defaultadmin';
	  $this->Redirect('m1_',$action,'',$params,TRUE);
	}

	/**
	 * Redirects the user to another action of the module.
	 * This function is optimized for frontend use.
	 *
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the form is submitted
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
	 * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
	 */
	function RedirectForFrontEnd($id, $returnid, $action, $params = array(), $inline = true )
	{
	  return $this->Redirect($id, $action, $returnid, $params, $inline );
	}

	/**
	 * Redirects the user to another action of the module.
	 *
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the form is submitted
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
	 * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
	 */
	function Redirect($id, $action, $returnid='', $params=array(), $inline=false)
	{
	  if( $returnid == '' ) {
	    if( is_array($this->__errors) && count($this->__errors) ) {
	      $params['__errors'] = implode('::err::',$this->__errors);
	    }
	    if( is_array($this->__messages) && count($this->__messages) ) {
	      $params['__messages'] = implode('::msg::',$this->__messages);
	    }
	  }

	  $this->LoadRedirectMethods();
	  return cms_module_Redirect($this, $id, $action, $returnid, $params, $inline);
	}
	
  /**
   * Redirects to an admin page
   * @param string php script to redirect to
   * @param array  optional array of url parameters
   */
  function RedirectToAdmin($page,$params = array())
  {
    $this->LoadRedirectMethods();
    return cms_module_RedirectToAdmin($this,$page,$params);
  }

	/**
	 * Redirects the user to a content page outside of the module.	The passed around returnid is
	 * frequently used for this so that the user will return back to the page from which they first
	 * entered the module.
	 *
	 * @param string Content id to redirect to.
	 * @return void
	 */
	function RedirectContent($id)
	{
	  redirect_to_alias($id);
	}

	/**
	 * ------------------------------------------------------------------
	 * Intermodule Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Get a reference to another module object
	 *
	 * @final
	 * @param string The required module name.
	 * @return object The module object, or FALSE
	 */
	static public function &GetModuleInstance($module)
	{
	  return cms_utils::get_module($module);
	}

	/**
	 * Returns an array of modulenames with the specified capability
	 * and which are installed and enabled, of course
	 *
	 * @final
	 * @param an id specifying which capability to check for, could be "wysiwyg" etc.
	 * @param associative array further params to get more detailed info about the capabilities. Should be syncronized with other modules of same type
	 * @return array
	 */
	final public function GetModulesWithCapability($capability, $params=array())
	{
	  $result=array();
	  $tmp = ModuleOperations::get_modules_with_capability($capability,$params);
	  if( is_array($tmp) && count($tmp) )
	    {
	      for( $i = 0; $i < count($tmp); $i++ )
		{
		  if( is_object($tmp[$i]) )
		    {
		      $result[] = get_class($tmp[$i]);
		    }
		  else
		    {
		      $result[] = $tmp[$i];
		    }
		}
	    }
	  return $result;
	}


	/**
	 * ------------------------------------------------------------------
	 * Language Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Sets the default language (usually en_US) for the module.  There
	 * should be at least a language file for this language if the Lang()
	 * function is used at all.
	 *
	 * @abstract
	 * @deprecated Modules should use en_US as their default language.
	 */
	function DefaultLanguage()
	{
		return 'en_US';
	}

	/**
	 * Returns the corresponding translated string for the id given.
	 * This method accepts variable arguments.  The first argument (required) is the language string key (a string)
	 * Further arguments may be sprintf arguments matching the specified key.
	 *
	 * @final
	 * @return string
	 */
	public function Lang()
	{
		$this->LoadLangMethods();

		//Push $this onto front of array
		$args = func_get_args();
		array_unshift($args,'');
		$args[0] = &$this;

		return call_user_func_array('cms_module_Lang', $args);
	}

	/**
	 * ------------------------------------------------------------------
	 * Template/Smarty Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * A function to return a resource identifier to a module specific database template
	 *
	 * @since 1.11
	 * @author calguy1000
	 * @param string The template name.
	 * @return string
	 */
	final public function GetDatabaseResource($template)
	{
	  return 'module_db_tpl:'.$this->GetName().';'.$template;
	}


	/**
	 * A function to return a resource identifier to a module specific file template
	 *
	 * @since 1.11
	 * @author calguy1000
	 * @param string The template name.
	 * @return string
	 */
	final public function GetFileResource($template)
	{
	  return 'module_file_tpl:'.$this->GetName().';'.$template;
	}

	/**
	 * List Templates associated with a module
	 *
	 * @final
	 * @param string module name.  If empty the current module name is used.
	 * @return array
	 */
	final public function ListTemplates($modulename = '')
	{
		$this->LoadTemplateMethods();
		return cms_module_ListTemplates($this, $modulename);
	}

	/**
	 * Returns a database saved template.  This should be used for admin functions only, as it doesn't
	 * follow any smarty caching rules.
	 *
	 * @final
	 * @param string template name
	 * @param string module name.  If empty the current module name is used.
	 * @return string
	 */
	final public function GetTemplate($tpl_name, $modulename = '')
	{
		$this->LoadTemplateMethods();
		return cms_module_GetTemplate($this, $tpl_name, $modulename);
	}

	/**
	 * Returns contents of the template that resides in modules/ModuleName/templates/{template_name}.tpl
	 * Code adapted from the Guestbook module
	 *
	 * @final
	 * @param string template name
	 * @return string
	 */
	final public function GetTemplateFromFile($template_name)
	{
		$this->LoadTemplateMethods();
		return cms_module_GetTemplateFromFile($this, $template_name);
	}


	/**
	 * Sets a smarty template into the database and associates it with a module.
	 *
	 * @final
	 * @param string The template name
	 * @param string The template content
	 * @param string The module name, if empty the current module name is used.
	 * @return boolean
	 */
	final public function SetTemplate($tpl_name, $content, $modulename = '')
	{
		$this->LoadTemplateMethods();
		return cms_module_SetTemplate($this, $tpl_name, $content, $modulename);
	}

	/** 
	 * Delete a module template from the database
	 *
	 * @final
	 * @param string the Template name, if empty all templates associated with the module are deleted.
	 * @param string The module name, if empty the current module name is used.
	 * @return boolean
	 */
	final public function DeleteTemplate($tpl_name = '', $modulename = '')
	{
		$this->LoadTemplateMethods();
		return cms_module_DeleteTemplate($this, $tpl_name, $modulename);
	}

	/**
	 * Test if a file template has been compiled and is cached by smarty.
	 *
	 * @final
	 * @internal
	 * @access private
	 * @deprecated
	 * @param string The template name
	 * @param string The cache designation
	 * @param string The cache timestamp
	 * @param string The cache id (for caching).
	 */
	final public function IsFileTemplateCached($tpl_name, $designation = '', $timestamp = '', $cacheid = '')
	{
		$this->LoadTemplateMethods();
		return cms_module_IsFileTemplateCached($this, $tpl_name, $designation, $timestamp, $cacheid);
	}

	/**
	 * Process A File template through smarty
	 *
	 * @final
	 * @deprecated
	 * @param string Template name
	 * @param string Cache Designation
	 * @param boolean Cache flag
	 * @param string  Unique cache flag
	 * @return string
	 */
	final public function ProcessTemplate($tpl_name, $designation = '', $cache = false, $cacheid = '')
	{
		$this->LoadTemplateMethods();
		return cms_module_ProcessTemplate($this, $tpl_name, $designation, $cache = false, $cacheid);
	}

	/**
	 * Test if a database template has been compiled and is cached by smarty.
	 *
	 * @final
	 * @internal
	 * @access private
	 * @deprecated
	 * @param string The template name
	 * @param string The cache designation
	 * @param string The cache timestamp
	 */
	final public function IsDatabaseTemplateCached($tpl_name, $designation = '', $timestamp = '')
	{
		$this->LoadTemplateMethods();
		return cms_module_IsDatabaseTemplateCached($this, $tpl_name, $designation, $timestamp);
	}

	/**
	 * Given a template in a variable, this method processes it through smarty
	 * note, there is no caching involved.
	 *
	 * Note: this function is deprecated and scheduled for removal.
	 *
	 * @final
	 * @param data Input template
	 * @return string
	 * @deprecated
	 */
	final public function ProcessTemplateFromData( $data )
	{
		$this->LoadTemplateMethods();
		return cms_module_ProcessTemplateFromData($this, $data);
	}

	/**
	 * Process a smarty template associated with a module through smarty and return the results
	 *
	 * @final
	 * @param string Template name
	 * @param string (optional) Designation
	 * @param boolean (optional) Cachable flag
	 * @param string (optional) module name, if empty the current module is used.
	 * @return string
	 */ 
	final public function ProcessTemplateFromDatabase($tpl_name, $designation = '', $cache = false, $modulename = '')
	{
		$this->LoadTemplateMethods();
		return cms_module_ProcessTemplateFromDatabase($this, $tpl_name, $designation, $cache, $modulename);
	}



	/**
	 * ------------------------------------------------------------------
	 * User defined tag methods.
	 * ------------------------------------------------------------------
	 */

	/**
	 * Return a list of user defined tags
	 *
	 * @final
	 * @return array
	 */
	final public function ListUserTags()
	{
	  $gCms = cmsms();
	  $usertagops = $gCms->GetUserTagOperations();
	  return $usertagops->ListUserTags();
	}

	/**
	 * Call a specific user defined tag
	 *
	 * @final
	 * @param string Name of the user defined tag
	 * @param array  Parameters for the user defined tag.
	 * @return array
	 */
	final public function CallUserTag($name, $params = array())
	{
	  $gCms = cmsms();
	  $usertagops = $gCms->GetUserTagOperations();
	  return $usertagops->CallUserTag($name, $params);
	}


	/**
	 * ------------------------------------------------------------------
	 * Tab Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Set the current tab for the action.
	 * Used for the various template forms, this method can be used to control the tab that is displayed by default
	 * when redirecting to an action that displays multiple tabs.
	 *
	 * @since 1.11
	 * @author calguy1000
	 * @param string The tab name
	 * @return void
	 */
	function SetCurrentTab($tab)
	{
	  $this->__current_tab = $tab;
	}


	/**
	 * Output a string suitable for staring tab headers
	 * i.e:  echo $this->StartTabHeaders();
	 *
	 * @final
	 * @return string
	 */ 
	function StartTabHeaders()
	{
		return '<div id="page_tabs">';
	}

	/**
	 * Set a specific tab header.
	 * i.e:  echo $this->SetTabHeader('preferences',$this->Lang('preferences'));
	 *
	 * @final

	 * @param string The tab id
	 * @param string The tab title
	 * @param booleban A flag indicating wether this tab is active.
	 * @return string
	 */ 
	function SetTabHeader($tabid,$title,$active=false)
	{
	  if( $active == FALSE )
	    {
	      $active = ($tabid == $this->__current_tab);
	    }

	  $a="";
	  if (TRUE == $active)
	    {
	      $a=" class='active'";
	      $this->mActiveTab = $tabid;
	    }
	  $tabid = strtolower(str_replace(' ','_',$tabid));
	  return '<div id="'.$tabid.'"'.$a.'>'.$title.'</div>';
	}

	/**
	 * Output a string to stop the output of headers and close the necessary XHTML div.
	 *
	 * @final
	 * @return string
	 */
	function EndTabHeaders()
	{
		return "</div><!-- EndTabHeaders -->";
	}

	/**
	 * Output a string to indicate the start of XHTML areas for tabs.
	 *
	 * @final
	 * @return string
	 */
	function StartTabContent()
	{
		return '<div class="clearb"></div><div id="page_content">';
	}

	/**
	 * Output a string to indicate the end of XHTML areas for tabs.
	 *
	 * @final
	 * @return string
	 */
	function EndTabContent()
	{
		return '</div> <!-- EndTabContent -->';
	}

	/**
	 * Output a string to indicate the start of the output for a specific tab
	 *
	 * @final
	 * @param string tabid (see SetTabHeader)
	 * @param arrray Parameters
	 * @return string
	 */
	function StartTab($tabid, $params = array())
	{
		if (FALSE == empty($this->mActiveTab) && $tabid == $this->mActiveTab && FALSE == empty($params['tab_message'])) {
			$message = $this->ShowMessage($this->Lang($params['tab_message']));
		} else {
			$message = '';
		}
		return '<div id="' . strtolower(str_replace(' ', '_', $tabid)) . '_c">'.$message;
	}

	/**
	 * Output a string to indicate the end of the output for a specific tab.
	 *
	 * @final
	 * @return string
	 */
	function EndTab()
	{
		return '</div> <!-- EndTab -->';
	}

	/**
	 * ------------------------------------------------------------------
	 * Other Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 *
	 * Called in the admin theme for every installed module, this method allows
	 * the module to output style information for use in the admin theme.
	 *
	 * @abstract
	 * @returns string css text.
	 */
	function AdminStyle()
	{
	  return '';
	}

	/**
	 * Set the content-type header.
	 *
	 * @abstract
	 * @param string Value to set the content-type header too
	 * @return string valid content type string.
	 */
	function SetContentType($contenttype)
	{
	  $variables = cmsms()->variables;
	  $variables['content-type'] = $contenttype;
	}

	/**
	 * Put an event into the audit (admin) log.	 This should be
	 * done on most admin events for consistency.
	 *
	 * @final
	 * @param string item id, useful for working on a specific record (i.e article or user)
	 * @param string item name
	 * @param string action name
	 * @return void
	 */
	final public function Audit($itemid, $itemname, $action)
	{
		audit($itemid,$itemname,$action);
	}

	/**
	 * Creates a string containing links to all the pages.
	 *
	 * @deprecated
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the form is submitted
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string the current page to display
	 * @param string the amount of items being listed
	 * @param string the amount of items to list per page
	 * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
	 * @return string
	 */
	function CreatePagination($id, $action, $returnid, $page, $totalrows, $limit, $inline=false)
	{
		$this->LoadMiscMethods();
		return cms_module_CreatePagination($this, $id, $action, $returnid, $page, $totalrows, $limit, $inline);
	}

	/**
	 * ShowMessage
	 * Returns a formatted page status message
	 *
	 * @final
	 * @param message - Message to be shown
	 * @return string
	 */
	function ShowMessage($message)
	{
	  $gCms = cmsms();
	  if (isset($gCms->variables['admintheme'])) {
	    $admintheme =& $gCms->variables['admintheme']; //php4 friendly
	    return $admintheme->ShowMessage($message);
	  }
	  return '';
	}

	/**
	 * Set a display  message.
	 *
	 * @since 1.11
	 * @author Robert Campbell
	 * @param mixed The message.  Accepts either an array of messages or a single string.
	 */
	public function SetMessage($str)
	{
	  if( !is_array($this->__messages) ) $this->__messages = array();
          if( !is_array($str) ) $str = array($str);
	  $this->__messages = array_merge($this->__messages,$str);
	}

	/**
	 * ShowErrors
	 * Outputs errors in a nice error box with a troubleshooting link to the wiki
	 *
	 * @final
	 * @param errors - array or string of errors to be shown
	 * @return string
	 */
	function ShowErrors($errors)
	{
	  $theme = cms_utils::get_theme_object();
	  if( is_object($theme) ) {
	    return $theme->ShowErrors($errors);
	  }
	  return '';
	}

	/**
	 * Set an error  message.
	 *
	 * @since 1.11
	 * @author Robert Campbell
	 * @param mixed The message.  Accepts either an array of messages or a single string.
	 */
	public function SetError($str)
	{
	  if( !is_array($this->__errors) ) $this->__errors = array();
          if( !is_array($str) ) $str = array($str);
	  $this->__errors = array_merge($this->__errors,$str);
	}


	/**
	 * ------------------------------------------------------------------
	 * Permission Functions
	 * ------------------------------------------------------------------
	 */


	/**
	 * Create's a new permission for use by the module.
	 *
	 * @final
	 * @param string Name of the permission to create
	 * @param string Description of the permission
	 * @return void
	 */
	final public function CreatePermission($permission_name, $permission_text)
	{
	  $gCms = cmsms();
		$db = $gCms->GetDB();

		$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name = ?";
		$count = $db->GetOne($query, array($permission_name));

		if (intval($count) == 0)
		{
			$new_id = $db->GenID(cms_db_prefix()."permissions_seq");
			$time = $db->DBTimeStamp(time());
			$query = "INSERT INTO ".cms_db_prefix()."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (?,?,?,".$time.",".$time.")";
			$db->Execute($query, array($new_id, $permission_name, $permission_text));
		}
	}

	/**
	 * Checks a permission against the currently logged in user.
	 *
	 * @final
	 * @param string The name of the permission to check against the current user
	 * @return boolean
	 */
	final public function CheckPermission($permission_name)
	{
		$userid = get_userid(false);
		return check_permission($userid, $permission_name);
	}

	/**
	 * Removes a permission from the system.  If recreated, the
	 * permission would have to be set to all groups again.
	 *
	 * @final
	 * @param string The name of the permission to remove
	 * @return void
	 */
	final public function RemovePermission($permission_name)
	{
	  cms_mapi_remove_permission($permission_name);
	}

	/**
	 * ------------------------------------------------------------------
	 * Preference Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns a module preference if it exists.
	 *
	 * @final
	 * @param string The name of the preference to check
	 * @param string The default value, just in case it doesn't exist
	 * @return string
	 */
	final public function GetPreference($preference_name, $defaultvalue='')
	{
		return get_site_preference($this->GetName() . "_mapi_pref_" . $preference_name, $defaultvalue);
	}

	/**
	 * Sets a module preference.
	 *
	 * @final
	 * @param string The name of the preference to set
	 * @param string The value to set it to
	 * @return void
	 */
	final public function SetPreference($preference_name, $value)
	{
	  return set_site_preference($this->GetName() . "_mapi_pref_" . $preference_name, $value);
	}

	/**
	 * Removes a module preference.  If no preference name
	 * is specified, removes all module preferences.
	 *
	 * @final
	 * @param string The name of the preference to remove.  If empty all preferences associated with the module are removed.
	 * @return boolean
	 */
	final public function RemovePreference($preference_name='')
	{
	  if( $preference_name == '' )
	    {
	      return remove_site_preference($this->GetName()."_mapi_pref_",true);
	    }
	  return remove_site_preference($this->GetName() . "_mapi_pref_" . $preference_name);
	}

	/**
	 * ------------------------------------------------------------------
	 * Event Handler Related functions
	 * ------------------------------------------------------------------
	 */


	/**
	* Add an event handler for an existing eg event.
	*
	* @final
	* @param string modulename       The name of the module sending the event, or 'Core'
	* @param string $eventname       The name of the event
	* @param boolean $removable      Can this event be removed from the list?
	* @returns mixed If successful, true.  If it fails, false.
	*/
	final public function AddEventHandler( $modulename, $eventname, $removable = true )
	{
		Events::AddEventHandler( $modulename, $eventname, false, $this->GetName(), $removable );
	}


	/**
	 * Inform the system about a new event that can be generated
	 *
	 * @final
	 * @param string The name of the event
	 * @returns nothing
	 */
	final public function CreateEvent( $eventname )
	{
		Events::CreateEvent($this->GetName(), $eventname);
	}


	/**
	 * An event that this module is listening to has occurred, and should be handled.
	 * This method must be over-ridden if this module is capable of handling events.
	 * of any type.
	 *
	 * The default behavior of this method is to check for a function called event.<originator>.<eventname>.php
	 * in the module directory, and if this file exists it, include it to handle the event.
	 *
	 * @abstract
	 * @param string The name of the originating module
	 * @param string The name of the event
	 * @param array  Array of parameters provided with the event.
	 * @return boolean
	 */
	function DoEvent( $originator, $eventname, &$params )
	{
		if ($originator != '' && $eventname != '')
		{
			$filename = dirname(dirname(dirname(__FILE__))) . '/modules/'.$this->GetName().'/event.' 
			. $originator . "." . $eventname . '.php';

			if (@is_file($filename))
			{
				{
				  $gCms = cmsms();
					$db = $gCms->GetDb();
					$config = $gCms->GetConfig();
					$smarty = $gCms->GetSmarty();

					include($filename);

				}
			}
		}
	}


	/**
	 * Get a (langified) description for an event this module created.
	 * This method must be over-ridden if this module created any events.
	 *
	 * @abstract
	 * @param string The name of the event
	 * @return string
	 */
	function GetEventDescription( $eventname )
	{
		return "";
	}


	/**
	 * Get a (langified) descriptionof the details about when an event is
	 * created, and the parameters that are attached with it.
	 * This method must be over-ridden if this module created any events.
	 *
	 * @abstract
	 * @param string The name of the event
	 * @return string 
	 */
	function GetEventHelp( $eventname )
	{
		return "";
	}


	/**
	 * A callback indicating if this module has a DoEvent method to
	 * handle incoming events.
	 *
	 * @abstract
	 * @return boolean
         */
	function HandlesEvents()
	{
		return false;
	}

	/**
	 * Remove an event from the CMS system
	 * This function removes all handlers to the event, and completely removes
	 * all references to this event from the database
	 *
	 * Note, only events created by this module can be removed.
	 *
	 * @final
	 * @param string The name of the event
	 * @return void
	 */
	final public function RemoveEvent( $eventname )
	{
		Events::RemoveEvent($this->GetName(), $eventname);
	}

	/**
	 * Remove an event handler from the CMS system
	 * This function removes all handlers to the event, and completely removes
	 * all references to this event from the database
	 *
	 * Note, only events created by this module can be removed.
	 *
	 * @final
	 * @param string The module name (or Core)
	 * @param string The name of the event
	 * @return void
	 */
	final public function RemoveEventHandler( $modulename, $eventname )
	{
		Events::RemoveEventHandler($modulename, $eventname, false, $this->GetName());
	}


	/**
	 * Trigger an event.
	 * This function will call all registered event handlers for the event
	 *
	 * @final
	 * @param string The name of the event
	 * @param array  The parameters associated with this event.
	 * @return void
	 */
	final public function SendEvent( $eventname, $params )
	{
		Events::SendEvent($this->GetName(), $eventname, $params);
	}
	
	/**
	 * Returns the output the module wants displayed in the dashboard
	 * 
	 * @abstract
	 * @return string dashboard-content
	 */
	function GetDashboardOutput() 
        {
	  return '';
	}


	/**
	 * Returns the output the module wants displayed in the notification area
	 * 
	 * @abstract
	 * @param int Notification priority between 1 and 3
	 * @return object A stdClass object with two properties.... priority (1->3)... and
	 * html, which indicates the text to display for the Notification.
	 */
	function GetNotificationOutput($priority=2) 
        {
	  return '';
	}

}


# vim:ts=4 sw=4 noet
?>
