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

define('FILTER_NONE', 999);

/**
 * Base class for modules.  This base class was renamed in 2.0
 * to allow for module compatibility with modules written for 
 * 1.x.
 *
 * @package cmsms
 * @author Ted Kulp
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
abstract class CmsModuleBase extends CmsObject
{   
	var $modinstall = false;
	var $modtemplates = false;
	var $modform = false;
	var $modredirect = false;
	var $modmisc = false;
	
	var $has_added_header_text = false;
	
	var $set_params;
	
	function __construct()
	{
		parent::__construct();
		
		$this->set_params = array();
		
		$this->create_parameter('module', FILTER_SANITIZE_STRING);
		$this->create_parameter('lang', FILTER_SANITIZE_STRING);
		$this->create_parameter('returnid', FILTER_SANITIZE_NUMBER_INT);
		$this->create_parameter('action', FILTER_SANITIZE_STRING);
		$this->create_parameter('showtemplate', FILTER_SANITIZE_STRING);
		
		$this->setup();
	}

	/**
	 * Returns the full path of the module directory.
	 */
	public function get_module_path()
	{
		if (is_subclass_of($this, 'CmsModuleBase'))
		{
			return cms_join_path(ROOT_DIR, 'modules' , $this->get_name());
		}
		else
		{
			return dirname(__FILE__);
		}
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Basic Methods. get_name and get_version MUST be overridden.
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns the name of the module
	 */
	abstract public function get_name();
	
	/**
	 * Returns a short description of the module
	 */
	public function get_description()
	{
		return '';
	}

	/**
	 * Returns a description of what the admin link does.
	 */
	public function get_admin_description()
	{
		return '';
	}

	/**
	 * Returns a translatable name of the module.  For modulues who's names can
	 * probably be translated into another language (like News)
	 */
	public function get_friendly_name()
	{
		return $this->get_name();
	}

	/**
	 * Returns the version of the module
	 */
	abstract public function get_version();

	/**
	 * Returns the minimum version necessary to run this version of the module.
	 */
	public function minimum_core_version()
	{
		return CMS_VERSION;
	}
	
	/**
	 * Checks to see whether this module will work on the currently installed
	 * version of CMSMS core.
	 *
	 * @return boolean Whether or not it meets the minimum core version requirement.
	 * @author Ted Kulp
	 **/
	final public function meets_minimum_requirements()
	{
		return version_compare($this->minimum_core_version(), CMS_VERSION) < 1;
	}
	
	/**
	 * Returns a sufficient about page for a module
	 */
	public function get_about()
	{
		$this->load_misc_methods();
		return cms_module_GetAbout($this);
	}

	/**
	 * Returns the help for the module
	 */
	public function get_help()
	{
		return '';
	}
	
	/**
	 * Returns a sufficient help page for a module
	 * this public function should not be overridden
	 */
	public function get_help_page()
	{
		$this->load_misc_methods();
		return cms_module_GetHelpPage($this);
	}

	/**
	 * Returns the changelog for the module
	 */
	public function get_change_log()
	{
		return '';
	}

	/**
	 * Returns the name of the author
	 */
	public function get_author()
	{
		return '';
	}

	/**
	 * Returns the email address of the author
	 */
	public function get_author_email()
	{
		return '';
	}
	
	/**
	 * Returns whether this module should only be loaded from the admin
	 */
	public function is_admin_only()
	{
		return false;
	}
	
	/**
	 * Return true if there is an admin for the module.	 Returns false by
	 * default.
	 */
	function has_admin()
	{
		return false;
	}
	
	/**
	 * Returns which admin section this module belongs to.
	 * this is used to place the module in the appropriate admin navigation
	 * section. Valid options are currently:
	 *
	 * content, layout, files, usersgroups, extensions, preferences, admin
	 *
	 */
	function get_admin_section()
	{
		return 'extensions';
	}
	
	/**
	 * Returns a list of the menu items and their respective actions.  By default,
	 * we return the module's friendly name, it's default admin description, points 
	 * it to "defaultadmin" and uses the admin section defined in get_admin_section.
	 */
	function get_admin_menu_items()
	{
		$default = array($this->get_friendly_name(), $this->get_admin_description(), 'defaultadmin', $this->get_admin_section());
		return array($default);
	}

	/**
	 * Should we use output buffering in the admin for this module?
	 */
	function has_admin_buffering()
	{
		return true;
	}
	
	/**
	 * Returns the name of the project from dev.cmsmadesimple.org, if it
	 * was developed there.  This allows the system to join up for handling
	 * automatic translations.
	 *
	 * @return string Name of the dev forge project
	 * @author Ted Kulp
	 * @since 2.0-svn
	 */
	function get_forge_project_name()
	{
		return '';
	}

	/**
	 * Returns true or false, depending on whether the user has the
	 * right permissions to see the module in their Admin menus.
	 *
	 * Defaults to true.
	 */
	function visible_to_admin_user()
	{
		return true;
	}

	/**
	 * Returns true if the module should be treated as a content module.
	 * Returns false by default.
	 */
	function is_content_module()
	{
		return false;
	}

	/**
	 * Returns true if the module should be treated as a plugin module (like
	 * {cms_module module='name'}.	Returns false by default.
	 */
	function is_plugin_module()
	{
		return false;
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Lazy Load methods to load offloaded code
	 * ------------------------------------------------------------------
	 */
	
	public function load_template_methods()
	{
		if (!$this->modtemplates)
		{
			require_once(cms_join_path(dirname(__FILE__), 'module_support', 'modtemplates.inc.php'));
			$this->modtemplates = true;
		}
	}
	
	public function load_form_methods()
	{
		if (!$this->modform)
		{
			require_once(cms_join_path(dirname(__FILE__), 'module_support', 'modform.inc.php'));
			$this->modform = true;
		}
	}
	
	public function load_redirect_methods()
	{
		if (!$this->modredirect)
		{
			require_once(cms_join_path(dirname(__FILE__), 'module_support', 'modredirect.inc.php'));
			$this->modredirect = true;
		}
	}
	
	public function load_misc_methods()
	{
		if (!$this->modmisc)
		{
			require_once(cms_join_path(dirname(__FILE__), 'module_support', 'modmisc.inc.php'));
			$this->modmisc = true;
		}
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Plugin Methods
	 * ------------------------------------------------------------------
	 */

	/**
	 * Register a plugin to smarty with the
	 * name of the module.  This method should be called
	 * from the module constructor, or from the setup()
	 * method.
	 */
	public function register_module_plugin($plugin_name = '', $method_name = 'function_plugin')
	{
		if ($plugin_name == '')
			$plugin_name = $this->get_name();

		cms_smarty()->register_function($plugin_name, array($this, $method_name));
	}

	/**
	 * Callback function for module plugins
	 * this function should not be overridden
	 */
	public function function_plugin($params, &$smarty)
	{
		$params['module'] = $this->get_name();
		return self::cms_module_plugin($params, $smarty);
	}

	/**
	 * Handling code for registered plugins.
	 */
	public static function cms_module_plugin($params, &$smarty)
	{
		$gCms = cmsms();
		$cmsmodules = cmsms()->modules;

		$id = 'm' . ++$gCms->variables["modulenum"];

		$returnid = '';
		if (isset($gCms->variables['pageinfo']) && isset($gCms->variables['pageinfo']->content_id))
		{
			$returnid = $gCms->variables['pageinfo']->content_id;
		}
		$params = array_merge($params, get_module_parameters($id));

		$modulename = '';
		$action = 'default';
		$inline = false;

		$checkid = '';

		if (isset($params['module'])) 
			$modulename = $params['module'];
		else
			return '<!-- ERROR: module name not specified -->';

		if (isset($params['idprefix'])) $id = trim($params['idprefix']);
		if (isset($params['action']) && $params['action'] != '')
		{
			// action was set in the module tag
			$action = $params['action'];
		}

		if (isset($_REQUEST['id'])) //Not really needed now...
		{
			$checkid = $_REQUEST['id'];
		}
		else if (isset($_REQUEST['mact']))
		{
			// we're handling an action
			$ary = explode(',', cms_htmlentities($_REQUEST['mact']), 4);
			$mactmodulename = (isset($ary[0])?$ary[0]:'');
			if (strtolower($mactmodulename) == strtolower($params['module']))
			{
				$checkid = (isset($ary[1])?$ary[1]:'');
				$mactaction = (isset($ary[2])?$ary[2]:'');
			}
			$mactinline = (isset($ary[3]) && $ary[3] == 1?true:false);

			if ($checkid == $id)
			{
				// the action is for this instance of the module
				$inline = $mactinline;
				if( $inline == true )
				{
					// and we're inline (the results are supposed to replace
					// the tag, not {content}
					$action = $mactaction;
				}
			}
		}

		if( $action == '' ) $action = 'default'; // probably not needed, but safe

		if (isset($cmsmodules))
		{
			$modulename = $params['module'];

			foreach ($cmsmodules as $key=>$value)
			{
				if (strtolower($modulename) == strtolower($key))
				{
					$modulename = $key;
				}
			}

			if (isset($modulename))
			{
				if (isset($cmsmodules[$modulename]))
				{
					if (isset($cmsmodules[$modulename]['object'])
						&& $cmsmodules[$modulename]['installed'] == true
						&& $cmsmodules[$modulename]['active'] == true
						&& $cmsmodules[$modulename]['object']->is_plugin_module())
					{
						@ob_start();

						$result = $cmsmodules[$modulename]['object']->do_action_base($action, $id, $params, $returnid);
						if ($result !== FALSE)
						{
							echo $result;
						}
						$modresult = @ob_get_contents();
						@ob_end_clean();
					
						return $modresult;
					}
					else
					{
						return "<!-- Not a tag module -->\n";
					}
				}
			}
		}
	}
	
	/**
	 * Returns XHTML that nees to go between the <head> tags
	 */
	public function get_header_html()
	{
	  return '';
	}

	/**
	 * Use this method to prevent the admin interface from outputting header, footer,
	 * theme, etc, so your module can output files directly to the administrator.
	 * Do this by returning true.
	 *
	 */
	public function suppress_admin_output($request)
	{
		return false;
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Setup Methods
	 * ------------------------------------------------------------------
	 */

	/**
	 * Register a route to use for pretty url parsing
	 *
	 * @param string Route to register
	 * @param array Defaults for parameters that might not be included in the url
	 */
	public function register_route($routeregex, $defaults = array())
	{
		$route = new CmsRoute();
		$route->module = $this->get_name();
		$route->defaults = $defaults;
		$route->regex = $routeregex;
		$routes =& cmsms()->variables['routes'];
		$routes[] =& $route;
	}
	
	/**
	 * Returns a list of parameters and their help strings in a hash.  This is generally
	 * used internally.
	 */
	public function get_parameters()
	{
		return $this->set_params;
	}
	
	public function setup()
	{
		
	}

	public function restrict_unknown_params($flag = true)
	{
		$this->restrict_unknown_params = $flag;
	}
	
	public function create_parameter($name, $filter = FILTER_NONE, $defaultval='', $helpstring='', $optional=true)
	{
		return $this->create_parameter_with_filter_options($name, $filter, array(), array(), $defaultval, $helpstring, $optional);
	}
	
	private function filter_id_list()
	{
		$result = array();
		foreach(filter_list() as $k=>$filter)
		{
			$result[] = filter_id($filter);
		}
		return $result;
	}

	public function create_parameter_with_filter_options($name, $filter = FILTER_NONE, $filter_flags = array(), $filter_params = array(), $defaultval='', $helpstring='', $optional=true)
	{
		if ($filter != FILTER_NONE && !in_array($filter, $this->filter_id_list()))
		{
			trigger_error('Attempt to set invalid parameter type');
		}

		array_unshift($this->set_params,
			array(
				'name' => $name,
				'filter' => $filter,
				'flags' => $filter_flags,
				'options' => $filter_params,
				'default' => $defaultval,
				'help' => $helpstring,
				'optional' => $optional
			)
		);
	}
	
	function clean_param_hash($data, $allow_unknown = false, $clean_keys = true)
	{
		$result = array();
		foreach ($data as $key => $value)
		{
			$mapped = false;
			
			foreach ($this->set_params as $set_param)
			{
				if ($key == $set_param['name'])
				{
					if (isset($set_param['filter']) && $set_param['filter'] != FILTER_NONE)
					{
						$filter_result = filter_var_array(array($key => $value), array($key => $set_param));
						if ($filter_result && ($set_param['filter'] == FILTER_VALIDATE_BOOLEAN || $filter_result[$key] !== FALSE))
							$value = $filter_result[$key];
						else
							$value = '';
					}
					
					$mapped = true;

					break;
				}
			}

			// we didn't clean this yet
			if ($allow_unknown && !$mapped)
			{
				// but we're allowing unknown stuff so we'll just clean it.
				$value = cms_htmlentities($value);
				$mapped = true;
			}

			if ($clean_keys)
			{
				$key = cms_htmlentities($key);
			}

			if (!$mapped && !$allow_unknown)
			{
				trigger_error('Parameter '.$key.' is not known... dropped', E_USER_WARNING);
				continue;
			}

			$result[$key]=$value;
		}

		return $result;
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Block Type Related Methods
	 * ------------------------------------------------------------------
	 */
	
	/**
	 * Registers a block type for use while editing content pages.
	 *
	 * @param string Name of the block type
	 * @param string File, relative to the module's path, the block type class resides in
	 * @param string Friendly name of the block type, if it's different from the actual block type's name
	 * @return void
	 * @author Ted Kulp
	 **/
	public function register_block_type($name, $file, $friendlyname = '')
	{
		$block_types =& cmsms()->blocktypes;
		if (!isset($block_types[strtolower($name)]))
		{
			$obj = new CmsBlockTypePlaceholder();
			$obj->type = strtolower($name);
			$obj->filename = cms_join_path(ROOT_DIR,'modules',$this->get_name(),$file);
			$obj->loaded = false;
			$obj->friendlyname = ($friendlyname != '' ? $friendlyname : $name);
			$block_types[strtolower($name)] =& $obj;
		}
	}
	
	/**
	 * ------------------------------------------------------------------
	 * ORM Related Methods
	 * ------------------------------------------------------------------
	 */
	
	/**
	 * Registers a class into the ORM system for use.
	 *
	 * @param string Name of the class
	 * @param string File, relative to the module's path, the class resides in
	 * @return void
	 * @author Ted Kulp
	 **/
	public function register_data_object($class_name, $file_name = '')
	{
		if ($file_name == '')
		{
			$file_name = cms_join_path($this->get_module_path(), 'class.' . underscore($class_name) . '.php');
		}

		if (include($file_name))
		{
			$orm =& cmsms()->orm;
			$orm[underscore($class_name)] = new $class_name;
			return true;
		}
		return false;
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Installation Related Methods
	 * ------------------------------------------------------------------
	 */
	
	/**
	 * Function that will get called as module is installed. This function should
	 * do any initialization functions including creating database tables. It
	 * should return a string message if there is a failure. Returning nothing (FALSE)
	 * will allow the install procedure to proceed.
	 */
	public function install()
	{
		$filename = cms_join_path($this->get_module_path(), 'method.install.php');
		if (@is_file($filename))
		{
			{				
				$gCms = cmsms(); //Backwards compatibility
				$db = cms_db();
				$config = cms_config();
				$smarty = cms_smarty();

				include($filename);
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Display a message after a successful installation of the module.
	 */
	public function install_post_message()
	{
		return FALSE;
	}

	final public function is_core()
	{
		return CmsModuleOperations::is_core($this->get_name());
	}

	final public function is_installable()
	{
		$gCms = cmsms();
		$broken_deps = 0;

		$dependencies = $this->get_dependencies();
		if (count($dependencies) > 0) #Check for any deps
		{
			#Now check to see if we can satisfy any deps
			foreach ($dependencies as $onedepkey => $onedepvalue)
			{
				if (!isset($gCms->modules[$onedepkey]) ||
				$gCms->modules[$onedepkey]['installed'] != true ||
				$gCms->modules[$onedepkey]['active'] != true ||
				version_compare($gCms->modules[$onedepkey]['object']->get_version(), $onedepvalue) < 0)
				{
					$broken_deps++;
				}
			}
		}
		
		return $broken_deps == 0;
	}
	
	public function create_table($table, $fields)
	{
		$dbdict = NewDataDictionary(cms_db());
		$taboptarray = array('mysql' => 'TYPE=MyISAM');

		$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.$table, $fields, $taboptarray);
		if (count($sqlarray))
		{
			$sqlarray[0] .= "\n/*!40100 DEFAULT CHARACTER SET UTF8 */";
		}
		$dbdict->ExecuteSQLArray($sqlarray);
	}
	
	public function create_index($table, $name, $field)
	{	
		$dbdict = NewDataDictionary(cms_db());

		$sqlarray = $dbdict->CreateIndexSQL($name, CMS_DB_PREFIX.$table, $field);
		$dbdict->ExecuteSQLArray($sqlarray);
	}
	
	/**
	 * Returns whether or not modules should be installed while installing
	 * CMS Made Simple.  Generally only useful for modules included with the CMS
	 * base install, but there could be a situation down the road where we have
	 * different distributions with different modules included in them.	 Defaults
	 * to TRUE, as there is not many reasons to not allow it.
	 */
	public function allow_auto_install()
	{
		return TRUE;
	}
	
	/**
	 * Function that will get called as module is uninstalled. This function should
	 * remove any database tables that it uses and perform any other cleanup duties.
	 * It should return a string message if there is a failure. Returning nothing
	 * (FALSE) will allow the uninstall procedure to proceed.
	 */
	public function uninstall()
	{
		$filename = cms_join_path($this->get_module_path(), 'method.uninstall.php');
		if (@is_file($filename))
		{
			{
				$gCms = cmsms(); //Backwards compatibility
				$db = cms_db();
				$config = cms_config();
				$smarty = cms_smarty();

				include($filename);
			}
		}
		/* //Maybe the module doesn't need any cleanup?
		else
		{
			return FALSE;
		}
		*/
	}

	/**
	 * Display a message and a Yes/No dialog before doing an uninstall.	 Returning noting
	 * (FALSE) will go right to the uninstall.
	 */
	public function uninstall_pre_message()
	{
		return FALSE;
	}

	/**
	 * Display a message after a successful uninstall of the module.
	 */
	public function uninstall_post_message()
	{
		return FALSE;
	}
	
	final public function is_uninstallable()
	{
		return !$this->check_for_dependents() && !$this->is_core();
	}
	
	public function drop_table($table)
	{
		$dbdict = NewDataDictionary(cms_db());

		$sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.$table);
		$dbdict->ExecuteSQLArray($sqlarray);
	}
	
	/**
	 * Function to perform any upgrade procedures. This is mostly used to for
	 * updating databsae tables, but can do other duties as well. It should
	 * return a string message if there is a failure. Returning nothing (FALSE)
	 * will allow the upgrade procedure to proceed. Upgrades should have a path
	 * so that they can be upgraded from more than one version back.  While not
	 * a requirement, it makes life easy for your users.
	 *
	 * @param string The version we are upgrading from
	 * @param string The version we are upgrading to
	 */
	public function upgrade($oldversion, $newversion)
	{
		$filename = cms_join_path($this->get_module_path(), 'method.upgrade.php');
		if (@is_file($filename))
		{
			{
				$gCms = cmsms(); //Backwards compatibility
				$db = cms_db();
				$config = cms_config();
				$smarty = cms_smarty();

				include($filename);
			}
		}
	}

	/**
	 * Returns whether or not modules should be autoupgraded while upgrading
	 * CMS versions.  Generally only useful for modules included with the CMS
	 * base install, but there could be a situation down the road where we have
	 * different distributions with different modules included in them.	 Defaults
	 * to TRUE, as there is not many reasons to not allow it.
	 */
	public function allow_auto_upgrade()
	{
		return TRUE;
	}
	
	final public function needs_upgrade()
	{
		return version_compare(CmsModuleOperations::get_installed_version($this->get_name()), $this->get_version());
	}
	
	/**
	 * Returns a list of dependencies and minimum versions that this module
	 * requires. It should return an hash, eg.
	 * return array('somemodule'=>'1.0', 'othermodule'=>'1.1');
	 */
	public function get_dependencies()
	{
		return array();
	}
	
	public function get_dependency_names()
	{
		return array_keys($this->get_dependencies());
	}

	/**
	 * Checks to see if currently installed modules depend on this module.	This is
	 * used by the plugins.php page to make sure that a module can't be uninstalled
	 * before any modules depending on it are uninstalled first.
	 */
	public function check_for_dependents()
	{
		$query = "SELECT * FROM " . cms_db_prefix() . "module_deps WHERE parent_module = ?";
		$row = cms_db()->GetRow($query, array($this->get_name()));

		if ($row)
			return true;

		return false;
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Navigation Related Methods
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
	 * @param string Name of the action to perform
	 * @param string The ID of the module
	 * @param string The parameters targeted for this module
	 */
	public function do_action($action_name, $id, $params, $returnid='')
	{
		$return_id = $returnid; //avoid confusion

		if ($action_name != '')
		{
			$filename = cms_join_path($this->get_module_path(), 'action.' . $action_name . '.php');

			if (@is_file($filename))
			{
				{
					$gCms = cmsms(); //Backwards compatibility
					$db = cms_db();
					$config = cms_config();
					$smarty = cms_smarty();

					$smarty->assign('module_action',$action_name);

					include($filename);

				}
			}
		}
	}

	public function do_action_base($name, $id, $incoming_params, $returnid = '')
	{
		if ($returnid != '')
		{
			if (!$this->restrict_unknown_params && CmsApplication::get_preference('allowparamcheckwarnings', 0))
			{
				trigger_error('WARNING: '.$this->get_name().' is not properly cleaning input params.', E_USER_WARNING);
			}
			// used to try to avert XSS flaws, this will
			// clean as many parameters as possible according
			// to a map specified with the SetParameterType metods.
			$incoming_params = $this->clean_param_hash($incoming_params, !$this->restrict_unknown_params);
		}

		if (isset($incoming_params['lang']))
		{
			$this->curlang = $incoming_params['lang'];
			$this->langhash = array();
		}

		if (!isset($incoming_params['action']))
		{
			$incoming_params['action'] = $name;
		}

		$incoming_params['action'] = cms_htmlentities($incoming_params['action']);
		$returnid = cms_htmlentities($returnid);
		$id = cms_htmlentities($id);
		$name = cms_htmlentities($name);
		return $this->do_action($name, $id, $incoming_params, $returnid);
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Web Service Methods
	 * ------------------------------------------------------------------
	 */
	function add_xmlrpc_method($method_name, $namespace = '')
	{
		CmsXmlrpc::add_method($method_name, array($this, $method_name), $namespace);
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Form Methods
	 * ------------------------------------------------------------------
	 */
	
    /**
	 * Returns the xhtml equivalent of an fieldset and legend.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution (not really used yet, but might be later)
	 * @param string The html name of the textbox (not really used yet, but might be later on)
	 * @param string The legend_text for this fieldset, if applicaple
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param string Any additional text that should be added into the legend tag when rendered
	 */
	public function create_fieldset_start($id, $name, $legend_text='', $addtext='', $addtext_legend='')
	{
		$this->load_form_methods();
		return cms_module_CreateFieldsetStart($this, $id, $name, $legend_text, $addtext, $addtext_legend);
	}

	/**
	* Returns the end of the fieldset in a  form.  This is basically just a wrapper around </form>, but
	* could be extended later on down the road.  It's here mainly for consistency.
	*/
	public function create_fieldset_end()
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
	 */
	public function create_frontend_form_start($id, $returnid, $action='default', $method='post', $enctype='', $inline=true, $idsuffix='', $params=array())
	{
		return $this->create_form_start($id,$action,$returnid,$method,$enctype,$inline,$idsuffix,$params);
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
	 * @param boolean A flag to determine if the action should just redirect back to this exact page
	 */
	public function create_form_start($id, $action='default', $returnid='', $method='post', $enctype='', $inline=false, $idsuffix='', $params = array(), $extra='', $html_id = '', $use_current_page_as_action = false)
	{
		$this->load_form_methods();
		return cms_module_CreateFormStart($this, $id, $action, $returnid, $method, $enctype, $inline, $idsuffix, $params, $extra, $html_id, $use_current_page_as_action);
	}

	/**
	 * Returns the end of the a module form.  This is basically just a wrapper around </form>, but
	 * could be extended later on down the road.  It's here mainly for consistency.
	 */
	public function create_form_end()
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
	 */
	public function create_input_text($id, $name, $value='', $size='10', $maxlength='255', $addttext='', $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateInputText($this, $id, $name, $value, $size, $maxlength, $addttext, $html_id);
	}

	/**
	 * Returns the xhtml equivalent of an label for input field.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input field this label is associated to
	 * @param string The text in the label
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	public function create_label_for_input($id, $name, $labeltext='', $addttext='', $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateLabelForInput($this, $id, $name, $labeltext, $addttext, $html_id);
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
	 */
	public function create_input_text_with_label($id, $name, $value='', $size='10', $maxlength='255', $addttext='', $label='', $labeladdtext='', $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateInputTextWithLabel($this, $id, $name, $value, $size, $maxlength, $addttext, $label, $labeladdtext, $html_id);
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
	 */
	public function create_input_file($id, $name, $accept='', $size='10',$addttext='',$html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateInputFile($this, $id, $name, $accept, $size, $addttext,$html_id);
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
	 */
	public function create_input_password($id, $name, $value='', $size='10', $maxlength='255', $addttext='', $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateInputPassword($this, $id, $name, $value, $size, $maxlength, $addttext, $html_id);
	}

	/**
	 * Returns the xhtml equivalent of a hidden field.	This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the hidden field
	 * @param string The predefined value of the field, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	public function create_input_hidden($id, $name, $value='', $addttext='', $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateInputHidden($this, $id, $name, $value, $addttext, $html_id);
	}

	/**
	 * Returns the xhtml equivalent of a checkbox.	This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the checkbox
	 * @param string The predefined value of the field, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	public function create_input_checkbox($id, $name, $selected = false, $addttext='', $html_id = '')
	{
		$id = cms_htmlentities($id);
		$name = cms_htmlentities($name);

		if ($html_id == '')
			$html_id = CmsResponse::make_dom_id($id . $name);

		$text = '<input type="hidden" name="'.$id.$name.'" value="0" />';
		$text .= '<input type="checkbox" name="'.$id.$name.'" id="'.$html_id.'" value="1"';
		if ($selected)
		{
			$text .= ' checked="checked"';
		}
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}
		$text .= " />\n";

		return $text;
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
	 */
	public function create_input_submit($id, $name, $value='', $addttext='', $image='', $confirmtext='', $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateInputSubmit($this, $id, $name, $value, $addttext, $image, $confirmtext, $html_id);
	}

	/**
	 * Returns the xhtml equivalent of a reset button.	This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the button
	 * @param string The predefined value of the button, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	public function create_input_reset($id, $name, $value='Reset', $addttext='', $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateInputReset($this, $id, $name, $value, $addttext, $html_id);
	 }

	/**
	 * Returns the xhtml equivalent of a file upload input.	 This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	public function create_file_upload_input($id, $name, $addttext='', $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateFileUploadInput($this, $id, $name, $addttext, $html_id);
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
	 * @param string Whether or not the array should be flipped (keys and values of has have opposite meanings)
	 */
	public function create_input_dropdown($id, $name, $items, $selected_index = -1, $selected_value = '', $additional_text = '', $flip_array = false, $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_create_input_dropdown($this, $id, $name, $items, $selected_index, $selected_value, $additional_text, $flip_array, $html_id);
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
	 */
	public function create_input_select_list($id, $name, $items, $selecteditems=array(), $size=3, $addttext='', $multiple = true, $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateInputSelectList($this, $id, $name, $items, $selecteditems, $size, $addttext, $multiple, $html_id);
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
	 */
	public function create_input_radio_group($id, $name, $items, $selectedvalue='', $addttext='', $delimiter='', $html_id = '')
	{
		$this->load_form_methods();
		return cms_module_CreateInputRadioGroup($this, $id, $name, $items, $selectedvalue, $addttext, $delimiter, $html_id);
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
	 */
	public function create_text_area($enablewysiwyg, $id, $text, $name, $classname='', $htmlid='', $encoding='', $stylesheet='', $cols='80', $rows='15',$forcewysiwyg="",$wantedsyntax="")
	{
		return create_textarea($enablewysiwyg, $text, $id.$name, $classname, $htmlid == '' ? CmsResponse::make_dom_id($id.$name) : '', $encoding, $stylesheet, $cols, $rows, $forcewysiwyg, $wantedsyntax);
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
	 */
	public function create_frontend_link( $id, $returnid, $action, $contents='', $params=array(), $warn_message='', $onlyhref=false, $inline=true, $addtext='', $targetcontentonly=false, $prettyurl='' )
	{
		return $this->create_link( $id, $action, $returnid, $contents, $params, $warn_message, $onlyhref, $inline, $addtext, $targetcontentonly, $prettyurl );
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
	 */
	public function create_link($id, $action, $returnid='', $contents='', $params=array(), $warn_message='', $onlyhref=false, $inline=false, $addttext='', $targetcontentonly=false, $prettyurl='')
	{
		$this->load_form_methods();
		return cms_module_CreateLink($this, $id, $action, $returnid, $contents, $params, $warn_message, $onlyhref, $inline, $addttext, $targetcontentonly, $prettyurl);
	}

	/**
	* Returns the xhtml equivalent of an href link for content links.	This is basically a nice
	* little wrapper to make sure that we go back to where we want and that it's xhtml complient
	*
	* @param string the page id of the page we want to direct to
	*/
	public function create_content_link($pageid, $contents='')
	{
		$this->load_form_methods();
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
	 */
	public function create_return_link($id, $returnid, $contents='', $params=array(), $onlyhref=false)
	{
		$this->load_form_methods();
		return '';
		return cms_module_CreateReturnLink($this, $id, $returnid, $contents, $params, $onlyhref);
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
	public function redirect_for_front_end($id, $returnid, $action, $params = array(), $inline = true )
	{
		return $this->redirect($id, $action, $returnid, $params, $inline);
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
	public function redirect($id, $action, $returnid='', $params=array(), $inline=false)
	{
		$this->load_redirect_methods();
		return cms_module_Redirect($this, $id, $action, $returnid, $params, $inline);
	}

	/**
	 * Redirects the user to a content page outside of the module.	The passed around returnid is
	 * frequently used for this so that the user will return back to the page from which they first
	 * entered the module.
	 *
	 * @param string Content id to redirect to.
	 */
	public function redirect_content($id)
	{
		CmsResponse::redirect_to_alias($id);
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Intermodule Functions
	 * ------------------------------------------------------------------
	 */
	public static function get_module_instance($module)
	{
		$gCms = cmsms();

		if (isset($gCms->modules[$module]) &&
			$gCms->modules[$module]['installed'] == true &&
			$gCms->modules[$module]['active'] == true)
		{
			return $gCms->modules[$module]['object'];
		}

		return FALSE;
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
	 */
	function default_language()
	{
		return 'en_US';
	}

	/**
	 * Returns the corresponding translated string for the id given.
	 *
	 * @param string Id of the string to lookup and return
	 * @param array Corresponding params for string that require replacement.
	 *		  These params use the vsprintf command and it's style of replacement.
	 */
	function lang()
	{
		$args = func_get_args();
		if (is_array($args) && count($args) == 1 && is_array($args[0]))
			$args = $args[0];
		return CmsLanguage::translate($args[0], array_slice($args, 1), $this->get_name(), '', $this->default_language());
	}
	
	/**
	 * Shortcut for lang()
	 *
	 * @param string Id of the string to lookup and return
	 * @param array Corresponding params for string that require replacement.
	 *		  These params use the vsprintf command and it's style of replacement.
	 */
	function __()
	{
		$args = func_get_args();
		if (is_array($args) && count($args) == 1 && is_array($args[0]))
			$args = $args[0];
		return CmsLanguage::translate($args[0], array_slice($args, 1), $this->get_name(), '', $this->default_language());
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Event Handler Related functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Add an event handler for a module event
	 *
	 * @param string $module_name  The name of the module sending the event
	 * @param string $event_name  The name of the event
	 * @param boolean $removable  Can this event be removed from the list?
	 *
	 * @return void
	 **/
	public function add_event_handler($module_name, $event_name, $removable = true)
	{
		CmsEvents::add_event_handler($module_name, $event_name, false, $this->get_name(), $removable);
	}
	
	/**
	 * Add a temporary event handler to the given module/event.  This only lasts for the duration
	 * of the request.
	 *
	 * @param string $module_name The name of the module sending the event
	 * @param string $event_name The name of the event
	 * @param string $callback_method The name of the method to callback to when this event fires.  Defaults to 'do_event'
	 * @param boolean $before_calls Should this callback before or after the stored events.  Defaults to false (after)
	 *
	 * @return void
	 **/
	public function add_temp_event_handler($module_name, $event_name, $callback_method = 'do_event', $before_calls = false)
	{
		CmsEvents::add_temp_event_handler($module_name, $event_name, array($this, $callback_method), $before_calls);
	}

	/**
	 * Inform the system about a new event that can be generated
	 *
	 * @param string The name of the event
	 *
	 * @returns nothing
	 */
	public function create_event( $eventname )
	{
		CmsEvents::create_event($this->get_name(), $eventname);
	}


	/**
	 * Handle an event triggered by another module
	 * This method must be over-ridden if this module is capable of handling events.
	 * of any type.
	 *
	 * @param string The name of the originating module
	 * @param string The name of the event
	 * @param array  Array of parameters provided with the event.
	 *
	 * @returns boolean
	 */
	public function do_event( $originator, $eventname, &$params )
	{
		if ($originator != '' && $eventname != '')
		{
			$filename = cms_join_path(ROOT_DIR, 'modules', $this->get_name(), 'event.' . $originator . '.' . $eventname . '.php');

			if (@is_file($filename))
			{
				{
					$gCms = cmsms(); //Backwards compatibility
					$db = cms_db();
					$config = cms_config();
					$smarty = cms_smarty();

					include($filename);

				}
			}
		}
	}

	/**
	 * Get a (langified) description for an event this module created.
	 * This method must be over-ridden if this module created any events.
	 *
	 * @param string The name of the event
	 *
	 * @returns text string
	 */
	public function get_event_description($eventname)
	{
		return '';
	}


	/**
	 * Get a (langified) descriptionof the details about when an event is
	 * created, and the parameters that are attached with it.
	 * This method must be over-ridden if this module created any events.
	 *
	 * @param string The name of the event
	 */
	public function get_event_help($eventname)
	{
		return '';
	}


	/**
	 * A callback indicating if this module has a DoEvent method to
	 * handle incoming events.
         */
	public function handles_events()
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
	 * @param string The name of the event
	 *
	 * @returns nothing
	 */
	public function remove_event($eventname)
	{
		CmsEvents::remove_event($this->get_name(), $eventname);
	}

	/**
	 * Remove an event from the CMS system
	 * This function removes all handlers to the event, and completely removes
	 * all references to this event from the database
	 *
	 * Note, only events created by this module can be removed.
	 *
	 * @param string The name of the event
	 *
	 * @returns nothing
	 */
	public function remove_event_handler($modulename, $eventname)
	{
		CmsEvents::remove_event_handler($modulename, $eventname, false, $this->get_name());
	}

	/**
	 * Trigger an event.
	 * This function will call all registered event handlers for the event
	 *
	 * @param string The name of the event
	 * @param array  The parameters associated with this event.
	 *
	 * @returns nothing
	 */
	public function send_event($eventname, $params)
	{
		CmsEvents::send_event($this->get_name(), $eventname, $params);
	}
	
	/**
	 * ------------------------------------------------------------------
	 * Other Functions
	 * ------------------------------------------------------------------
	 */

	 /**
	  *
	  * Module can spit out extra CSS for the admin side
	  *
	  */
	public function admin_style()
	{
		return '';
	}

	/**
	 * Set the content-type header.
	 *
	 * @param string Value to set the content-type header too
	 */
	public function set_content_type($contenttype)
	{
		$variables = &$this->cms->variables;
		$variables['content-type'] = $contenttype;
	}
	
	/**
	 * Adds text to be displayed by the {header} tag in the frontend rendering
	 * process.
	 *
	 * @param string The text to add to the {header} output
	 * @return void
	 * @author Ted Kulp
	 **/
	public function add_header_text($text)
	{
		$headers = cmsms()->get('header_additions');
		if ($headers == null)
			$headers = array();
		$headers[] = $text;
		cmsms()->set('header_additions', $headers);
		$this->has_added_header_text = true;
	}
	
	/**
	 * Checks to see if any add_header_text calls have been made yet.
	 *
	 * @return boolean Whether or not header text has been added
	 * @author Ted Kulp
	 **/
	public function has_added_header_text()
	{
		return $this->has_added_header_text;
	}

	/**
	 * Put an event into the audit (admin) log.	 This should be
	 * done on most admin events for consistency.
	 */
	public function audit($itemid, $itemname, $action)
	{
		#$userid = get_userid();
		#$username = $_SESSION["cms_admin_username"];
		audit($itemid, $itemname, $action);
	}

	/**
	 * Create's a new permission for use by the module.
	 *
	 * @param string Name of the permission to create
	 * @param string Description of the permission
	 */
	public function create_permission($permission_name, $permission_text)
	{
		global $gCms;
		$db = cms_db();

		try
		{
			$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name = ?";
			$count = $db->GetOne($query, array($permission_name));

			if (intval($count) == 0)
			{
				$new_id = $db->GenID(cms_db_prefix()."permissions_seq");
				$time = $db->DBTimeStamp(time());
				$query = "INSERT INTO ".cms_db_prefix()."permissions (id, permission_name, permission_text, create_date, modified_date) VALUES (?,?,?,".$time.",".$time.")";
				$db->Execute($query, array($new_id, $permission_name, $permission_text));
			}
		}
		catch (Exception $e)
		{
			//trigger_error("Could not run CreatePermission", E_WARNING);
		}
	}

	/**
	 * Checks a permission against the currently logged in user.
	 *
	 * @param string The name of the permission to check against the current user
	 */
	public function check_permission($permission_name)
	{
		$userid = get_userid();
		return check_permission($userid, $permission_name);
	}

	/**
	 * Removes a permission from the system.  If recreated, the
	 * permission would have to be set to all groups again.
	 *
	 * @param string The name of the permission to remove
	 */
	public function remove_permission($permission_name)
	{
		global $gCms;
		$db = cms_db();

		$query = "SELECT id FROM ".cms_db_prefix()."permissions WHERE permission_name = ?";
		$row = &$db->GetRow($query, array($permission_name));

		if ($row)
		{
			$id = $row["id"];

			$query = "DELETE FROM ".cms_db_prefix()."group_perms WHERE permission_id = ?";
			$db->Execute($query, array($id));

			$query = "DELETE FROM ".cms_db_prefix()."permissions WHERE id = ?";
			$db->Execute($query, array($id));
		}
	}

	/**
	 * Returns a module preference if it exists.
	 *
	 * @param string The name of the preference to check
	 * @param string The default value, just in case it doesn't exist
	 */
	public function get_preference($preference_name, $defaultvalue='')
	{
		return CmsApplication::get_preference($this->get_name() . "_mapi_pref_" . $preference_name, $defaultvalue);
	}

	/**
	 * Sets a module preference.
	 *
	 * @param string The name of the preference to set
	 * @param string The value to set it to
	 */
	public function set_preference($preference_name, $value)
	{
		return CmsApplication::set_preference($this->get_name() . "_mapi_pref_" . $preference_name, $value);
	}

	/**
	 * Removes a module preference.  If no preference name
	 * is specified, removes all module preferences.
	 *
	 * @param string The name of the preference to remove
	 */
	public function remove_preference($preference_name='')
	{
		if( $preference_name == '' )
		{
			return CmsApplication::remove_preference("^".$this->get_name()."_mapi_pref_",true);
		}
		return CmsApplication::remove_preference($this->GetName() . "_mapi_pref_" . $preference_name);
	}

    /**
     * ShowMessage
     * Returns a formatted page status message
     *
     * @param message - Message to be shown
     */
    public function show_message($message)
    {
		global $gCms;
		if (isset($gCms->variables['admintheme']))
		{
			$admintheme =& $gCms->variables['admintheme']; //php4 friendly
			return $admintheme->show_message($message);
		}
		return '';
	}

    /**
     * ShowErrors
     * Outputs errors in a nice error box with a troubleshooting link to the wiki
     *
     * @param errors - array or string of errors to be shown
     */
    public function show_errors($errors)
    {
        global $gCms;
        if (isset($gCms->variables['admintheme']))
        {
            $admintheme =& $gCms->variables['admintheme']; //php4 friendly
            return $admintheme->show_errors($errors);
        }
        return '';
    }

	public function is_wysiwyg()
	{
		return false;
	}

	/**
	 * ------------------------------------------------------------------
	 * Template/Smarty Functions
	 * ------------------------------------------------------------------
	 */

	public function list_templates($template_type = '')
	{
		if( empty($template_type) )
			{
				return cms_orm('CmsModuleTemplate')->find_all_by_module($this->get_name());
			}
		return cms_orm('CmsModuleTemplate')->find_all_by_module_and_template_type($this->get_name(), $template_type);
	}

	/**
	 * Returns a database saved template.  This should be used for admin functions only, as it doesn't
	 * follow any smarty caching rules.
	 */
	public function get_template($template_type, $template_name)
	{
		return cms_orm('CmsModuleTemplate')->find_by_module_and_template_type_and_name($this->get_name(), $template_type, $template_name);
	}

	/**
	 * Returns contents of the template that resides in modules/ModuleName/templates/{template_name}.tpl
	 * Code adapted from the Guestbook module
	 */
	public function get_template_from_file($template_name)
	{
		$ok = (strpos($tpl_name, '..') === false);
		if (!$ok) return;

		$template = cms_join_path(ROOT_DIR, 'modules', $this->get_name(), 'templates', $template_name.'.tpl');
		if (is_file($template)) {
			return file_get_contents($template);
		}
		else
		{
			return lang('errorinsertingtemplate');
		}
	}

	public function set_template($template_type, $template_name, $content, $default = false)
	{
		$template = cms_orm('CmsModuleTemplate')->find_by_module_and_template_type_and_name($this->get_name(), $template_type, $template_name);
		if ($template != null)
		{
			$template->content = $content;
			return $template->save();
		}
		else
		{
			$template = new CmsModuleTemplate();
			$template->module = $this->get_name();
			$template->template_type = $template_type;
			$template->template_name = $template_name;
			$template->content = $content;
			$template->default = $default;
			return $template->save();
		}
	}

	public function delete_template($template_type = '', $template_name = '')
	{
		if( $template_type != '' && $template_name != '' )
			{
				$template = cms_orm('CmsModuleTemplate')->find_by_module_and_template_type_and_name($this->get_name(), $template_type, $template_name);
				if ($template != null)
					{
						return $template->delete();
					}
			}
		else if( $template_type != '' && empty($template_name) )
			{
				$template = cms_orm('CmsModuleTemplate')->find_by_module_and_template_type($this->get_name(), $template_type);
				foreach( $templates as $one )
					{
						$templates->delete();
					}
				return true;
			}
		else if( empty($template_type) && empty($template_name) )
			{
				$templates = cms_orm('CmsModuleTemplate')->find_by_module($this->get_name());
				foreach( $templates as $one )
					{
						$templates->delete();
					}
				return true;
			}
		return false;
	}

	public function is_file_template_cached($template_name, $designation = '', $timestamp = '', $cache_id = '')
	{
		$ok = (strpos($template_name, '..') === false);
		if (!$ok) return;

		return $smarty->is_cached('module_file_tpl:' . $this->get_name() . ';' . $template_name, $cache_id, ($designation != '' ? $designation : $this->get_name()));
	}
	
	public function process_template($template_name, $id, $return_id, $designation = '', $cache_id = '')
	{
		$smarty = cms_smarty();

		$smarty->assign_by_ref('cms_mapi_module', $this);
		$smarty->assign('cms_mapi_id', $id);
		$smarty->assign('cms_mapi_return_id', $return_id);

		return $smarty->fetch('module_file_tpl:'.$this->get_name() . ';' . $template_name, $cache_id, ($designation != '' ? $designation : $this->get_name()));
	}

	public function is_database_template_cached($template_type, $template_name, $designation = '', $timestamp = '', $cache_id = '')
	{
		return $smarty->is_cached('module_db_tpl:' . $this->get_name() . ';' . $template_type . ';' . $template_name, $cache_id, ($designation != '' ? $designation : $this->get_name()));
	}
	
	public function process_template_from_database($id, $return_id, $template_type, $template_name = '', $designation = '', $cache_id = '')
	{
		$smarty = cms_smarty();

		$smarty->assign_by_ref('cms_mapi_module', $this);
		$smarty->assign('cms_mapi_id', $id);
		$smarty->assign('cms_mapi_return_id', $return_id);

		return $smarty->fetch('module_db_tpl:' . $this->get_name() . ';' . $template_type . ';' . $template_name, $cache_id, ($designation != '' ? $designation : $this->get_name()));
	}
	
	/**
	 * Given a template in a variable, this method processes it through smarty
	 * note, there is no caching involved.
	 */
	public function process_template_from_data( $data )
	{
		$smarty = cms_smarty();
		$smarty->_compile_source('temporary template', $data, $_compiled );
		@ob_start();
		$smarty->_eval('?>' . $_compiled);
		$_contents = @ob_get_contents();
		@ob_end_clean();
		return $_contents;
	}

	public function list_user_tags()
	{
		global $gCms;
		$usertagops =& $gCms->GetUserTagOperations();
		return $usertagops->ListUserTags();
	}

	public function call_user_tag($name, $params = array())
	{
		global $gCms;
		$usertagops =& $gCms->GetUserTagOperations();
		return $usertagops->CallUserTag($name, $params);
	}
	/** Returns the output the module wants displayed in the dashboard
	 * 
	 * @returns dashboard-content
	 */
	function GetDashboardOutput() {
		return '';
	}


	/**
	 * Returns the output the module wants displayed in the notification area
	 * 
	 * @returns a stdClass object with two properties.... priority (1->3)... and
	 * html, which indicates the text to display for the Notification.
	 */
	function GetNotificationOutput($priority=2) {
		return '';
	}
}

# vim:ts=4 sw=4 noet
?>
