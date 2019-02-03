<?php
# CMS - CMS Made Simple
# (c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
# Visit our homepage at: http://cmsmadesimple.org
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
 * This file contains the base module class for all CMSMS modules.
 *
 * @package CMS
 * @license GPL
 */

/**
 * Base module class.
 *
 * All modules should inherit and extend this class with their functionality.
 *
 * @since		0.9
 * @version     2.0
 * @package		CMS
 * @property    CmsApp $cms A reference to the application object (deprecated)
 * @property    Smarty_CMS $smarty A reference to the global smarty object (deprecated)
 * @property    cms_config $config A reference to the global app configuration object (deprecated)
 * @property    ADOConnection $db  A reference to the global database configuration object (deprecated)
 */
abstract class CMSModule
{
    /**
     * ------------------------------------------------------------------
     * Initialization Functions and parameters
     * ------------------------------------------------------------------
     */

    /**
     * A hash of the parameters passed in to the module action
     *
     * @access private
     * @ignore
     */
    private $params = array();

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
    private $restrict_unknown_params = TRUE;
     */

    /**
     * @access private
     * @ignore
     */
    private $__current_tab;

    /**
     * @access private
     * @ignore
     */
    private $_action_tpl;

    /**
     * ------------------------------------------------------------------
     * Magic methods
     * ------------------------------------------------------------------
     */

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        global $CMS_FORCELOAD;
        global $CMS_STYLESHEET;
        global $CMS_ADMIN_PAGE;
        global $CMS_MODULE_PAGE;
        global $CMS_INSTALL_PAGE;

        if( cmsms()->is_cli() ) return;
        if( isset($CMS_FORCELOAD) && $CMS_FORCELOAD ) return;
        if( CmsApp::get_instance()->is_frontend_request() ) {
            $this->InitializeFrontend();
        }
        else if( isset($CMS_ADMIN_PAGE) && !isset($CMS_STYLESHEET) && !isset($CMS_INSTALL_PAGE) ) {
            $this->InitializeAdmin();
        }
    }

    /**
     * @ignore
     */
    public function __get($key)
    {
        switch( $key ) {
        case 'cms':
            return CmsApp::get_instance();

        case 'smarty':
            /* deprecated */
            $tpl = $this->GetActionTemplateObject();
            if( $tpl ) return $tpl;
            return \Smarty_CMS::get_instance();

        case 'config':
            return \cms_config::get_instance();

        case 'db':
            return CmsApp::get_instance()->GetDb();
        }

        return null;
    }

    /**
     * @since 2.0
     *
     * @ignore
     */
    public function __call($name, $args)
    {
        return FALSE;
    }

    /**
     * ------------------------------------------------------------------
     * Load internals.
     * ------------------------------------------------------------------
     */

    /**
     * Private
     *
     * @ignore
     */
    private function _loadTemplateMethods()
    {
        if (!$this->modtemplates) {
            require_once(cms_join_path(__DIR__,'internal','module_support','modtemplates.inc.php'));
            $this->modtemplates = true;
        }
    }

    /**
     * Private
     *
     * @ignore
     */
    private function _loadFormMethods()
    {
        if (!$this->modform) {
            require_once(cms_join_path(__DIR__, 'internal', 'module_support', 'modform.inc.php'));
            $this->modform = true;
        }
    }

    /**
     * Private
     *
     * @ignore
     */
    private function _loadRedirectMethods()
    {
        if (!$this->modredirect) {
            require_once(cms_join_path(__DIR__,'internal', 'module_support', 'modredirect.inc.php'));
            $this->modredirect = true;
        }
    }

    /**
     * Private
     *
     * @ignore
     */
    private function _loadMiscMethods()
    {
        if (!$this->modmisc) {
            require_once(cms_join_path(__DIR__,'internal', 'module_support', 'modmisc.inc.php'));
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
     * @internal
     * @return mixed module call output.
     */
    final static public function function_plugin($params,&$template)
    {
        $class = get_called_class();
        if( $class != 'CMSModule' && !isset($params['module']) ) $params['module'] = $class;
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
     * @param string  $name The plugin name
     * @param string  $type The plugin type (function,compiler,block, etc)
     * @param callback $callback The function callback (must be a static function)
     * @param bool $cachable Wether this function is cachable.
     * @param int $usage Indicates frontend (0), or frontend and backend (1) availability.
     */
    public function RegisterSmartyPlugin($name,$type,$callback,$cachable = TRUE,$usage = 0)
    {
        if( !$name || !$type || !$callback ) throw new CmsException('Invalid data passed to RegisterSmartyPlugin');

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
     * @param string $name The smarty plugin name.  If no name is specified all smarty plugins registered to this module will be removed.
     */
    public function RemoveSmartyPlugin($name = '')
    {
        if( $name == '' ) {
            cms_module_smarty_plugin_manager::remove_by_module($this->GetName());
            return;
        }
        cms_module_smarty_plugin_manager::remove_by_name($name);
    }

    /**
     * Register a plugin to smarty with the
     * name of the module.  This method should be called
     * from the module constructor, or from the SetParameters
     * method.
     *
     * Note:
     * @final
     * @see CMSModule::SetParameters
     * @see can_cache_output
     * @param bool $forcedb Indicate wether this registration should be forced to be entered in the database. Default value is false (for compatibility)
     * @param bool|null $cachable Indicate wether this plugins output should be cachable.  If null, use the site preferences, and the can_cache_output method.  Otherwise a bool is expected.
     */
    final public function RegisterModulePlugin($forcedb = FALSE,$cachable = false)
    {
        global $CMS_ADMIN_PAGE;
        global $CMS_INSTALL_PAGE;

        // frontend request.
        $admin_req = (isset($CMS_ADMIN_PAGE) && !$this->LazyLoadAdmin())?1:0;
        $fe_req = (!isset($CMS_ADMIN_PAGE) && !$this->LazyLoadFrontend())?1:0;
        if( ($fe_req || $admin_req) && !$forcedb ) {
            if( isset($CMS_INSTALL_PAGE) ) return TRUE;

            // no lazy loading.
            $gCms = CmsApp::get_instance();
            $smarty = $gCms->GetSmarty();
            $smarty->register_function($this->GetName(), array($this->GetName(),'function_plugin'), $cachable );
            return TRUE;
        }
        else {
            return cms_module_smarty_plugin_manager::addStatic($this->GetName(),$this->GetName(), 'function', 'function_plugin',$cachable);
            return TRUE;
        }
    }

    /**
     * Callback to determine if the output from a call to the module can be cached by smarty.
     *
     * @since 1.11
     * @author Robert Campbell
     * @return bool
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
     * @return bool
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
    public function GetAbout()
    {
        $this->_loadMiscMethods();
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
        $this->_loadMiscMethods();
        return cms_module_GetHelpPage($this);
    }

    /**
     * Returns the name of the module
     *
     * @abstract
     * @return string The name of the module.
     */
    public function GetName()
    {
        $tmp = get_class($this);
        return basename(str_replace('\\','/',$tmp));
    }

    /**
     * Returns the full path to the module directory.
     *
     * @final
     * @return string The full path to the module directory.
     */
    final public function GetModulePath()
    {
        if (is_subclass_of($this, 'CMSModule')) {
            return cms_join_path(CMS_ROOT_PATH, 'modules' , $this->GetName());
        }
        return __DIR__;
    }

    /**
     * Returns the URL path to the module directory.
     *
     * @final
     * @param bool $use_ssl Optional generate an URL using HTTPS path
     * @return string The full path to the module directory.
     */
    final public function GetModuleURLPath($use_ssl=false)
    {
        $config = \cms_config::get_instance();
        if( $use_ssl ) {
            return $this->config['ssl_url'].'/modules/'.$this->GetName();
        } else {
            return $config->smart_root_url().'/modules/'.$this->GetName();
        }
    }

    /**
     * Returns a translatable name of the module.  For modulues who's names can
     * probably be translated into another language (like News)
     *
     * @abstract
     * @return string
     */
    public function GetFriendlyName()
    {
        return $this->GetName();
    }

    /**
     * Returns the version of the module
     *
     * @abstract
     * @return string
     */
    abstract public function GetVersion();

    /**
     * Returns the minimum version necessary to run this version of the module.
     *
     * @abstract
     * @return string
     */
    public function MinimumCMSVersion()
    {
        global $CMS_VERSION;
        return $CMS_VERSION;
    }

    /**
     * Returns the help for the module
     *
     * Note: New for CMSMS 1.11.12 - If the global variable CMSMS_GENERATING_XML is set
     * it indicates that the help output will be stored in an XML file.  This variable
     * can be used to check wether advanced html output (like links to other documents)
     * should be generated.
	 *
     * @abstract
     * @return string Help HTML Text.
     */
    public function GetHelp()
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
    public function GetHeaderHTML()
    {
        return '';
    }

    /**
     * Use this method to prevent the admin interface from outputting header, footer,
     * theme, etc, so your module can output files directly to the administrator.
     * Do this by returning true.
     *
     * @param  array $request The input request.  This can be used to test conditions wether or not admin output should be suppressed.
     * @return bool
     */
    public function SuppressAdminOutput(&$request)
    {
        return false;
    }

    /**
     * Register a dynamic route to use for pretty url parsing
     *
     * Note: This method is not compatible wih lazy loading in the front end.
     *
     * @final
     * @see SetParameters
     * @param string $routeregex Regular Expression Route to register
     * @param array $defaults Associative array containing defaults for parameters that might not be included in the url
     */
    final public function RegisterRoute($routeregex, $defaults = array())
    {
        $route = new CmsRoute($routeregex,$this->GetName(),$defaults);
        cms_route_manager::register($route);
    }

    /**
     * Register all static routes for this module.
     *
     * @abstract
     * @since 1.11
     * @author Robert Campbell
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
        if( count($this->params) == 0 ) $this->InitializeAdmin(); // quick hack to load parameters if they are not already loaded.
        return $this->params;
    }

    /**
     * Method to sanitize all entries in a hash
     * This method is called by the module api to clean incomming parameters in the frontend.
     * It uses the map created with the SetParameterType() method in the module api.
     *
     * @internal
     * @deprecated
     * @param string Module Name
     * @param array  Hash data
     * @param array  A map of param names and type information
     * @param bool A flag indicating wether unknown keys in the input data should be allowed.
     * @param bool A flag indicating wether keys should be treated as strings and cleaned.
     */
    private function _cleanParamHash($modulename,$data,$map = false, $allow_unknown = false,$clean_keys = true)
    {
        $mappedcount = 0;
        $result = array();
        foreach( $data as $key => $value ) {
            $mapped = false;
            $paramtype = '';
            if( is_array($map) ) {
                if( isset($map[$key]) ) {
                    $paramtype = $map[$key];
                }
                else {
                    // Key not found in the map
                    // see if one matches via regular expressions
                    foreach( $map as $mk => $mv ) {
                        if(strstr($mk,CLEAN_REGEXP) === FALSE) continue;

                        // mk is a regular expression
                        $ss = substr($mk,strlen(CLEAN_REGEXP));
                        if( $ss !== FALSE ) {
                            if( preg_match($ss, $key) ) {
                                // it matches, we now know what type to use
                                $paramtype = $mv;
                                break;
                            }
                        }
                    }
                } // else

                if( $paramtype != '' ) {
                    switch( $paramtype ) {
                    case 'CLEAN_INT':
                        $mappedcount++;
                        $mapped = true;
                        $value = (int) $value;
                        break;
                    case 'CLEAN_FLOAT':
                        $mappedcount++;
                        $mapped = true;
                        $value = (float) $value;
                        break;
                    case 'CLEAN_NONE':
                        // pass through without cleaning.
                        $mappedcount++;
                        $mapped = true;
                        break;
                    case 'CLEAN_STRING':
                        $value = cms_htmlentities($value);
                        $mappedcount++;
                        $mapped = true;
                        break;
                    case 'CLEAN_FILE':
                        $value = realpath($value);
                        if( $realpath === FALSE ) {
                            $value = CLEANED_FILENAME;
                        }
                        else {
                            if( strpos($realpath, CMS_ROOT_PATH) !== 0 ) $value = CLEANED_FILENAME;
                        }
                        $mappedcount++;
                        $mapped = true;
                        break;
                    default:
                        $mappedcount++;
                        $mapped = true;
                        $value = cms_htmlentities($value);
                        break;
                    } // switch
                } // if $paramtype
            }

            // we didn't clean this yet
            if( $allow_unknown && !$mapped ) {
                // but we're allowing unknown stuff so we'll just clean it.
                $value = cms_htmlentities($value);
                $mappedcount++;
                $mapped = true;
            }

            if( $clean_keys ) $key = cms_htmlentities($key);

            if( !$mapped && !$allow_unknown ) {
                trigger_error('Parameter '.$key.' is not known by module '.$modulename.' dropped',E_USER_WARNING);
                continue;
            }
            $result[$key]=$value;
        }
        return $result;
    }

    /**
     * This method can be used to perform initialization functions that should be done both for frontend and admin requests
     * but not necessarily for lighweight module load requests (such as needed by ModuleManager).
     *
     * Note: This method is called from within the InitializeFrontend() and InitializeAdmin methods. so, if your module
     * overrides one or both of these methods be sure to call the appropriate parent method.
     *
     * @abstract
     * @see CreateParameter
     * @see InitializeFrontend()
     * @see InitializeAdmin()
     * @deprecated
     */
    function SetParameters() {}

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
     * @deprecated
     * @param bool $flag Indicaties wether unknown params should be restricted.
     */
    final public function RestrictUnknownParams($flag = true)
    {
        // do nothing (2.2.8)
    }

    /**
     * Indicate the name of, and type of a parameter that is
     * acceptable for frontend actions.
     *
     * possible values for type are:
     * CLEAN_INT,CLEAN_FLOAT,CLEAN_NONE,CLEAN_STRING,CLEAN_REGEXP,CLEAN_FILE
     *
     * i.e:
     * $this->SetParameterType('numarticles',CLEAN_INT);
     *
     * @see CreateParameter
     * @see SetParameters
     * @final
     * @deprecated
     * @param string $param Parameter name;
     * @param define $type  Parameter type;
     */
    final public function SetParameterType($param, $type)
    {
        switch($type) {
        case CLEAN_INT:
        case CLEAN_FLOAT:
        case CLEAN_NONE:
        case CLEAN_STRING:
        case CLEAN_FILE:
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
     * @deprecated
     * @param string $param Parameter name;
     * @param string $defaultval Default parameter value
     * @param string $helpstring Help String
     * @param bool $optional Flag indicating wether this parameter is optional or required.
     */
    final public function CreateParameter($param, $defaultval='', $helpstring='', $optional=true)
    {
        array_push($this->params, array('name' => $param,'default' => $defaultval,'help' => $helpstring,
                                        'optional' => $optional ));
    }

    /**
     * Returns a short description of the module
     *
     * @abstract
     * @return string
     */
    public function GetDescription()
    {
        return '';
    }

    /**
     * Returns a description of what the admin link does.
     *
     * @abstract
     * @return string
     */
    public function GetAdminDescription()
    {
        return '';
    }

    /**
     * Returns whether this module should only be loaded from the admin
     *
     * @abstract
     * @return bool
     */
    public function IsAdminOnly()
    {
        return false;
    }

    /**
     * Returns the changelog for the module
     *
     * @return string HTML text of the changelog.
     */
    public function GetChangeLog()
    {
        return '';
    }

    /**
     * Returns the name of the author
     *
     * @abstract
     * @return string The name of the author.
     */
    public function GetAuthor()
    {
        return '';
    }

    /**
     * Returns the email address of the author
     *
     * @abstract
     * @return string The email address of the author.
     */
    public function GetAuthorEmail()
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
    final public function &GetConfig()
    {
        return \cms_config::get_instance();
    }

    /**
     * Returns the cms->db object as a reference
     *
     * @final
     * @deprecated
     * @return ADOConnection Adodb Database object.
     */
    final public function &GetDb()
    {
        return CmsApp::get_instance()->GetDb();
    }

    /**
     * ------------------------------------------------------------------
     * Content Block Related Functions
     * ------------------------------------------------------------------
     */

    /**
     * Get an input field for a module generated content block type.
     *
     * This method is called from the content edit form when a {content_module} tag is encountered.
     *
     * This method can be overridden if the module is providing content
     * block types to the CMSMS content objects.
     *
     * @abstract
     * @since 2.0
     * @param string $blockName Content block name
     * @param mixed  $value     Content block value
     * @param array  $params    Associative array containing content block parameters
     * @param bool   $adding   A flag indicating wether the content editor is in create mode (adding) vs. edit mod.
     * @param ContentBase $content_obj The content object being edited.
     * @return mixed Either an array with two elements (prompt, and xhtml element) or a string containing only the xhtml input element.
     */
    function GetContentBlockFieldInput($blockName,$value,$params,$adding,ContentBase $content_obj)
    {
        return FALSE;
    }

    /**
     * Return a value for a module generated content block type.
     *
     * This mehod is called from a {content_module} tag, when the content edit form is being edited.
     *
     * Given input parameters (i.e: via _POST or _REQUEST), this method
     * will extract a value for the given content block information.
     *
     * This method can be overridden if the module is providing content
     * block types to the CMSMS content objects.
     *
     * @abstract
     * @since 2.0
     * @param string $blockName Content block name
     * @param array  $blockParams Content block parameters
     * @param array  $inputParams input parameters
     * @param ContentBase $content_obj The content object being edited.
     * @return mixed|false The content block value if possible.
     */
    function GetContentBlockFieldValue($blockName,$blockParams,$inputParams,ContentBase $content_obj)
    {
        return FALSE;
    }

    /**
     * Validate the value for a module generated content block type.
     *
     * This mehod is called from a {content_module} tag, when the content edit form is being validated.
     *
     * This method can be overridden if the module is providing content
     * block types to the CMSMS content objects.
     *
     * @abstract
     * @since 2.0
     * @param string $blockName Content block name
     * @param mixed  $value     Content block value
     * @param arrray $blockparams Content block parameters.
     * @param contentBase $content_obj The content object that is currently being edited.
     * @return string An error message if the value is invalid, empty otherwise.
     */
    function ValidateContentBlockFieldValue($blockName,$value,$blockparams,ContentBase $content_obj)
    {
        return '';
    }

    /**
     * Render the value of a module content block on the frontend of the website.
     * This gives modules the opportunity to render data stored in content blocks differently.
     *
     * @abstract
     * @since 2.2
     * @param string $blockName Content block name
     * @param string $value     Content block value as stored in the database
     * @param array  $blockparams Content block parameters
     * @param ContentBase $content_obj The content object that is currently being displayed
     * @return string
     */
    function RenderContentBlockField($blockName,$value,$blockparams,ContentBase $content_obj)
    {
        return $value;
    }

    /**
     * Register a bulk content action
     *
     * For use in the CMSMS content list this method allows a module to
     * register a bulk content action.
     *
     * @final
     * @param string $label A label for the action
     * @param string $action A module action name.
     */
    final public function RegisterBulkContentFunction($label,$action)
    {
        bulkcontentoperations::register_function($label,$action,$this->GetName());
    }

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
     * @return string|false A value of FALSE indicates no error.  Any other value will be used as an error message.
     */
    public function Install()
    {
        $filename = dirname(dirname(__DIR__)) . '/modules/'.$this->GetName().'/method.install.php';
        if (@is_file($filename)) {
            $gCms = CmsApp::get_instance();
            $db = $gCms->GetDb();
            $config = $gCms->GetConfig();
            global $CMS_INSTALL_PAGE;
            if( !isset($CMS_INSTALL_PAGE) ) $smarty = $gCms->GetSmarty();

            $res = include($filename);
            if( $res == 1 || $res == '' ) return FALSE;
            return $res;
        }
        return FALSE;
    }


    /**
     * Display a message after a successful installation of the module.
     *
     * @abstract
     * @return XHTML Text
     */
    public function InstallPostMessage()
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
     * @return string|false A result of FALSE indicates that the module uninstalled correctly, any other value indicates an error message.
     */
    public function Uninstall()
    {
        $filename = dirname(dirname(__DIR__)) . '/modules/'.$this->GetName().'/method.uninstall.php';
        if (@is_file($filename)) {
            $gCms = CmsApp::get_instance();
            $db = $gCms->GetDb();
            $config = $gCms->GetConfig();
            $smarty = $gCms->GetSmarty();

            $res = include($filename);
            if( $res == 1 || $res == '') return FALSE;
            if( is_string($res)) {
                $modops = $gCms->GetModuleOperations();
                $modops->SetError($res);
            }
            return $res;
        }
        else {
            return FALSE;
        }
    }

    /**
     * Function that gets called upon module uninstall, and returns a bool to indicate whether or
     * not the core should remove all module events, event handlers, module templates, and preferences.
     * The module must still remove its own database tables and permissions
     * @abstract
     * @return bool Whether the core may remove all module events, event handles, module templates, and preferences on uninstall (defaults to true)
     */
    public function AllowUninstallCleanup()
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
    public function UninstallPreMessage()
    {
        return FALSE;
    }

    /**
     * Display a message after a successful uninstall of the module.
     *
     * @abstract
     * @return XHTML Text, or FALSE
     */
    public function UninstallPostMessage()
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
     * @param string $oldversion The version we are upgrading from
     * @param string $newversion The version we are upgrading to
     * @return bool
     */
    public function Upgrade($oldversion, $newversion)
    {
        $filename = dirname(dirname(__DIR__)) . '/modules/'.$this->GetName().'/method.upgrade.php';
        if (@is_file($filename)) {
            $gCms = CmsApp::get_instance();
            $db = $gCms->GetDb();
            $config = $gCms->GetConfig();
            $smarty = $gCms->GetSmarty();

            $res = include($filename);
            if( $res == 1 || $res == '' ) return FALSE;
            return $res;
        }
        return FALSE;
    }

    /**
     * Returns a list of dependencies and minimum versions that this module
     * requires. It should return an hash, eg.
     * return array('somemodule'=>'1.0', 'othermodule'=>'1.1');
     *
     * @abstract
     * @return array
     */
    public function GetDependencies()
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
     * @return bool
     */
    final public function CheckForDependents()
    {
        $gCms = CmsApp::get_instance();
        $db = $gCms->GetDb();
        $result = false;

        $query = "SELECT child_module FROM ".CMS_DB_PREFIX."module_deps WHERE parent_module = ? LIMIT 1";
        $tmp = $db->GetOne($query,array($this->GetName()));
        if( $tmp ) $result = true;
        return $result;
    }

    /**
     * Creates an xml data package from the module directory.
     *
     * @final
     * @return string XML Text
     * @param string $message reference to returned message.
     * @param int $filecount reference to returned file count.
     */
    final public function CreateXMLPackage( &$message, &$filecount )
    {
        $modops = ModuleOperations::get_instance();
        return $modops->CreateXmlPackage($this, $message, $filecount);
    }

    /**
     * Return true if there is an admin for the module.	 Returns false by
     * default.
     *
     * @abstract
     * @return bool
     */
    public function HasAdmin()
    {
        return false;
    }

    /**
     * Returns which admin section this module belongs to.
     * this is used to place the module in the appropriate admin navigation
     * section. Valid options are currently:
     *
     * main, content, layout, files, usersgroups, extensions, preferences, siteadmin, myprefs, ecommerce
     *
     * @abstract
     * @return string
     */
    public function GetAdminSection()
    {
        return 'extensions';
    }

    /**
     * Return a array of CmsAdminMenuItem objects representing menu items for the admin nav for this module.
     *
     * This method should do all permissions checking when building the array of objects.
     *
     * @since 2.0
     * @abstract
     * @return array of CmsAdminMenuItem objects
     */
    public function GetAdminMenuItems()
    {
        if( !$this->VisibleToAdminUser() ) return;

        $out = array();
        $out[] = CmsAdminMenuItem::from_module($this);

        return $out;
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
     * @return bool
     */
    public function VisibleToAdminUser()
    {
        return true;
    }

    /**
     * Returns true if the module should be treated as a plugin module (like
     * {cms_module module='name'}.	Returns false by default.
     *
     * @abstract
     * @return bool
     */
    public function IsPluginModule()
    {
        return false;
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
     * @return bool
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
     * @return bool
     */
    public function LazyLoadAdmin()
    {
        return FALSE;
    }

    /**
     * ------------------------------------------------------------------
     * Module capabilities, a new way of checking what a module can do
     * ------------------------------------------------------------------
     */

    /**
     * Returns true if the modules thinks it has the capability specified
     *
     * @abstract
     * @param string $capability an id specifying which capability to check for, could be "wysiwyg" etc.
     * @param array  $params An associative array further params to get more detailed info about the capabilities. Should be syncronized with other modules of same type
     * @return bool
     */
    public function HasCapability($capability, $params = array())
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
    public function get_tasks()
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
     * Returns header code specific to this SyntaxHighlighter
     *
     *
     * @abstract
     * @return string
     */
    public function SyntaxGenerateHeader()
    {
        return '';
    }

    /**
     * ------------------------------------------------------------------
     * WYSIWYG Related Functions
     *
     * These methods are only useful for creating wysiwyg editor modules.
     * ------------------------------------------------------------------
     */

    /**
     * Returns header code specific to this WYSIWYG
     *
     * @abstract
     * @param string $selector (optional) The id of the element that is being initialized, if null the WYSIWYG module should assume the selector
     *   to be textarea.<ModuleName>.
     * @param string $cssname (optional) The name of the CMSMS stylesheet to associate with the wysiwyg editor for additional styling.
     *   if elementid is not null then the cssname is only used for the specific element.  WYSIWYG modules may not obey the cssname paramter
     *   depending on their settings and capabilities.
     * @return string
     */
    public function WYSIWYGGenerateHeader($selector = null,$cssname = null)
    {
        return '';
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
     * @param string $name The Name of the action to perform
     * @param string $id The ID of the module
     * @param string $params The parameters targeted for this module
     * @param int $returnid The current page id that is being displayed.
     * @return string output XHTML.
     */
    public function DoAction($name, $id, $params, $returnid='')
    {
        if( $returnid == '' ) {
            $errors = $messages = null;
            if( isset($params['__activetab']) ) $this->SetCurrentTab(trim(cleanValue($params['__activetab'])));
            $e_key = $this->GetName().'_errors';
            $m_key = $this->GetName().'_messages';
            if( isset( $_SESSION[$e_key]) ) $errors = $_SESSION[$e_key];
            if( isset( $_SESSION[$m_key]) ) $messages = $_SESSION[$m_key];
            unset( $_SESSION[$e_key], $_SESSION[$m_key] );
            if( is_array($errors) && count($errors) ) echo $this->ShowErrors($errors);
            if( is_array($messages) && count($messages) ) echo $this->ShowMessage($messages[0]);
        }

        if ($name != '') {
            //Just in case DoAction is called directly and it's not overridden.
            //See: http://0x6a616d6573.blogspot.com/2010/02/cms-made-simple-166-file-inclusion.html
            $name = preg_replace('/[^A-Za-z0-9\-_+]/', '', $name);

            $filename = $this->GetModulePath().'/action.' . $name . '.php';
            if( !is_file($filename) ) {
                @trigger_error("$name is an unknown acton of module ".$this->GetName());
                throw new \CmsError404Exception("Module action not found");
            }

            // these are included in scope in the included file for convenience.
            $gCms = CmsApp::get_instance();
            $db = $gCms->GetDb();
            $config = $gCms->GetConfig();
            $smarty = ( $this->_action_tpl ) ? $this->_action_tpl : $smarty = $gCms->GetSmarty()->get_template_parent();
            include($filename);
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
     * @param string $name The action name
     * @param string $id The action identifier
     * @param array  $params The action params
     * @param int $returnid The current page id.  Empty for admin requests.
     * @param Smarty_Internal_Template &$smarty The curernt smarty template object.
     * @return string The action output.
     */
    final public function DoActionBase($name, $id, $params, $returnid='', &$smarty )
    {
        $name = preg_replace('/[^A-Za-z0-9\-_+]/', '', $name);
        if( $returnid != '' ) {
            /*
            if( !$this->restrict_unknown_params ) {
                // put mention into the admin log
                audit('',$this->GetName(),'Module is not properly cleaning input params');
            }
            */

            // merge in params from module hints.
            $hints = cms_utils::get_app_data('__CMS_MODULE_HINT__'.$this->GetName());
            if( is_array($hints) ) {
                foreach( $hints as $key => $value ) {
                    if( isset($params[$key]) ) continue;
                    $params[$key] = $value;
                }
                unset($hints);
            }

            // used to try to avert XSS flaws, this will
            // clean as many parameters as possible according
            // to a map specified with the SetParameterType metods.
            $params = $this->_cleanParamHash($this->GetName(),$params,$this->param_map, true);
        }

        // handle the stupid input type='image' problem.
        foreach( $params as $key => $value ) {
            if( endswith($key,'_x') ) {
                $base = substr($key,0,strlen($key)-2);
                if( isset($params[$base.'_y']) && !isset($params[$base]) ) $params[$base] = $base;
            }
        }

        if( !isset($params['action']) ) $params['action'] = $name;
        $params['action'] = cms_htmlentities($params['action']);
        $returnid = cms_htmlentities($returnid);
        $id = cms_htmlentities($id);
        $name = cms_htmlentities($name);

        if( $returnid != '' ) {
            $tmp = $params;
            $tmp['module'] = $this->GetName();
            \CMSMS\HookManager::do_hook('module_action', $tmp);
        }

        $gCms = CmsApp::get_instance(); // in scope for compatibility reasons.
        //$smarty = $gCms->GetSmarty(); // use the passed in template.
        $smarty->assign('actionid',$id);
        $smarty->assign('actionparams',$params);
        $smarty->assign('returnid',$returnid);
        $smarty->assign('actionmodule',$this->GetName());
        $smarty->assign('mod',$this);

        $saved_action_tpl = $this->_action_tpl;
        $this->_action_tpl = $smarty;
        $output = $this->DoAction($name, $id, $params, $returnid);
        $this->_action_tpl = $saved_action_tpl;

        if( isset($params['assign']) ) {
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
     * @param string $helptext The help text to be shown on mouse over
     * @param string $linktext The text to be shown as the link, default to a simple question mark
     * @param string $forcewidth Forces another width of the popupbox than the one set in admin css
     * @param string $classname An alternative classname for the a-link of the tooltip
     * @param string $href The URL or url portion to use in the href portion of the generated link.
     * @deprecated
     * @return string
     */
    final function CreateTooltip($helptext, $linktext="?", $forcewidth="", $classname="admin-tooltip admin-tooltip-box", $href="")
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
     * @param string $id The id given to the module on execution
     * @param string $action The action that this form should do when the link is clicked
     * @param string $returnid The id to eventually return to when the module is finished it's task
     * @param string $contents The text that will have to be clicked to follow the link
     * @param string $tooltiptext The helptext to be shown as tooltip-popup
     * @param string $params An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
     * @deprecated
     * @return string
     */
    final function CreateTooltipLink($id, $action, $returnid, $contents, $tooltiptext, $params=array())
    {
        return $this->CreateTooltip($tooltiptext,$contents,"","admin-tooltip",$this->CreateLink($id,$action,$returnid,"admin-tooltip",$params,"",true) );
    }

    /**
     * Returns the xhtml equivalent of an fieldset and legend.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @final
     * @param string $id The id given to the module on execution (not really used yet, but might be later)
     * @param string $name The html name of the textbox (not really used yet, but might be later on)
     * @param string $legend_text The legend_text for this fieldset, if applicaple
     * @param string $addtext Any additional text that should be added into the tag when rendered
     * @param string $addtext_legend Any additional text that should be added into the legend tag when rendered
     * @deprecated
     * @return string
     */
    final function CreateFieldsetStart( $id, $name, $legend_text='', $addtext='', $addtext_legend='' )
    {
        $this->_loadFormMethods();
        return cms_module_CreateFieldsetStart($this, $id, $name, $legend_text, $addtext, $addtext_legend);
    }

    /**
     * Returns the end of the fieldset in a  form.  This is basically just a wrapper around </form>, but
     * could be extended later on down the road.  It's here mainly for consistency.
     *
     * @final
     * @deprecated
     * @return string
     */
    final function CreateFieldsetEnd()
    {
        return '</fieldset>'."\n";
    }

    /**
     * Returns the start of a module form, optimized for frontend use
     *
     * @param string $id The id given to the module on execution
     * @param string $returnid The page id to eventually return to when the module is finished it's task
     * @param string $action The name of the action that this form should do when the form is submitted
     * @param string $method Method to use for the form tag.  Defaults to 'post'
     * @param string $enctype Optional enctype to use, Good for situations where files are being uploaded
     * @param bool $inline A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
     * @param string $idsuffix Text to append to the end of the id and name of the form
     * @param array $params Extra parameters to pass along when the form is submitted
     * @deprecated
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
     * @param string $id The id given to the module on execution
     * @param string $action The action that this form should do when the form is submitted
     * @param string $returnid The page id to eventually return to when the module is finished it's task
     * @param string $method Method to use for the form tag.  Defaults to 'post'
     * @param string $enctype Optional enctype to use, Good for situations where files are being uploaded
     * @param bool $inline A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
     * @param string $idsuffix Text to append to the end of the id and name of the form
     * @param array $params Extra parameters to pass along when the form is submitted
     * @param string $extra Text to append to the <form>-statement, for instanse for javascript-validation code
     * @return string
     */
    function CreateFormStart($id, $action='default', $returnid='', $method='post', $enctype='', $inline=false, $idsuffix='', $params = array(), $extra='')
    {
        $this->_loadFormMethods();
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
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the textbox
     * @param string $value The predefined value of the textbox, if any
     * @param string $size The number of columns wide the textbox should be displayed
     * @param string $maxlength The maximum number of characters that should be allowed to be entered
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputText($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputText($this, $id, $name, $value, $size, $maxlength, $addttext);
    }

    /**
     * Returns the xhtml equivalent of an label for input field.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input field this label is associated to
     * @param string $labeltext The text in the label
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateLabelForInput($id, $name, $labeltext='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateLabelForInput($this, $id, $name, $labeltext, $addttext);
    }

    /**
     * Returns the xhtml equivalent of an input textbox with label.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the textbox
     * @param string $value The predefined value of the textbox, if any
     * @param string $size The number of columns wide the textbox should be displayed
     * @param string $maxlength The maximum number of characters that should be allowed to be entered
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @param string $label The text for label
     * @param string $labeladdtext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputTextWithLabel($id, $name, $value='', $size='10', $maxlength='255', $addttext='', $label='', $labeladdtext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputTextWithLabel($this, $id, $name, $value, $size, $maxlength, $addttext, $label, $labeladdtext);
    }

    /**
     * Returns the html5 equivalent of an input of type color.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input field
     * @param string $value The predefined value of the textbox, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @return string
     */
    function CreateInputColor($id, $name, $value='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputColor($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input of type date.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input field
     * @param string $value The predefined value of the textbox, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputDate($id, $name, $value='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputDate($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input of type datetime.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input field
     * @param string $value The predefined value of the textbox, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputDatetime($id, $name, $value='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputDatetime($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input of type datetime-local.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input field
     * @param string $value The predefined value of the textbox, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputDatetimeLocal($id, $name, $value='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputDatetimeLocal($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input of type month.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input field
     * @param string $value The predefined value of the textbox, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputMonth($id, $name, $value='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputMonth($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input of type week.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input field
     * @param string $value The predefined value of the textbox, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputWeek($id, $name, $value='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputWeek($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input of type time.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input field
     * @param string $value The predefined value of the textbox, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputTime($id, $name, $value='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputTime($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input of type number.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input field
     * @param string $value The predefined value of the textbox, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @return string
     */
    function CreateInputNumber($id, $name, $value='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputNumber($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input of type range.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input field
     * @param string $value The predefined value of the textbox, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputRange($id, $name, $value='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputRange($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input textbox of type email.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the textbox
     * @param string $value The predefined value of the textbox, if any
     * @param string $size The number of columns wide the textbox should be displayed
     * @param string $maxlength The maximum number of characters that should be allowed to be entered
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputEmail($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputEmail($this, $id, $name, $value, $size, $maxlength, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input textbox of type tel.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the textbox
     * @param string $value The predefined value of the textbox, if any
     * @param string $size The number of columns wide the textbox should be displayed
     * @param string $maxlength The maximum number of characters that should be allowed to be entered
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputTel($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputTel($this, $id, $name, $value, $size, $maxlength, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input of type search.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the textbox
     * @param string $value The predefined value of the textbox, if any
     * @param string $size The number of columns wide the textbox should be displayed
     * @param string $maxlength The maximum number of characters that should be allowed to be entered
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputSearch($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputSearch($this, $id, $name, $value, $size, $maxlength, $addttext);
    }

    /**
     * Returns the html5 equivalent of an input of type url.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the textbox
     * @param string $value The predefined value of the textbox, if any
     * @param string $size The number of columns wide the textbox should be displayed
     * @param string $maxlength The maximum number of characters that should be allowed to be entered
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputUrl($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputUrl($this, $id, $name, $value, $size, $maxlength, $addttext);
    }


    /**
     * Returns the xhtml equivalent of a file-selector field.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the textbox
     * @param string $accept The MIME-type to be accepted, default is all
     * @param string $size The number of columns wide the textbox should be displayed
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputFile($id, $name, $accept='', $size='10',$addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputFile($this, $id, $name, $accept, $size, $addttext);
    }

    /**
     * Returns the xhtml equivalent of an input password-box.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the textbox
     * @param string $value The predefined value of the textbox, if any
     * @param string $size The number of columns wide the textbox should be displayed
     * @param string $maxlength The maximum number of characters that should be allowed to be entered
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputPassword($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputPassword($this, $id, $name, $value, $size, $maxlength, $addttext);
    }

    /**
     * Returns the xhtml equivalent of a hidden field.	This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the hidden field
     * @param string $value The predefined value of the field, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputHidden($id, $name, $value='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputHidden($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the xhtml equivalent of a checkbox.	This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the checkbox
     * @param string $value The value returned from the input if selected
     * @param string $selectedvalue The current value. If equal to $value the checkbox is selected
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputCheckbox($id, $name, $value='', $selectedvalue='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputCheckbox($this, $id, $name, $value, $selectedvalue, $addttext);
    }

    /**
     * Returns the xhtml equivalent of a submit button.	 This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the button
     * @param string $value The predefined value of the button, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @param string $image Use an image instead of a regular button
     * @param string $confirmtext Optional text to display in a confirmation message.
     * @deprecated
     * @return string
     */
    function CreateInputSubmit($id, $name, $value='', $addttext='', $image='', $confirmtext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputSubmit($this, $id, $name, $value, $addttext, $image, $confirmtext);
    }

    /**
     * Returns the xhtml equivalent of a reset button.	This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the button
     * @param string $value The predefined value of the button, if any
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputReset($id, $name, $value='Reset', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputReset($this, $id, $name, $value, $addttext);
    }

    /**
     * Returns the xhtml equivalent of a file upload input.	 This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the input
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @param int $size The size of the text field associated with the file upload field.  Some browsers may not respect this value.
     * @param int $maxlength The maximim length of the content of the text field associated with the file upload field.  Some browsers may not respect this value.
     * @deprecated
     * @return string
     */
    function CreateFileUploadInput($id, $name, $addttext='',$size='10', $maxlength='255')
    {
        $this->_loadFormMethods();
        return cms_module_CreateFileUploadInput($this, $id, $name, $addttext, $size, $maxlength);
    }

    /**
     * Returns the xhtml equivalent of a dropdown list.	 This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it is xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the dropdown list
     * @param string $items An array of items to put into the dropdown list... they should be $key=>$value pairs
     * @param string $selectedindex The default selected index of the dropdown list.  Setting to -1 will result in the first choice being selected
     * @param string $selectedvalue The default selected value of the dropdown list.  Setting to '' will result in the first choice being selected
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputDropdown($id, $name, $items, $selectedindex=-1, $selectedvalue='', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputDropdown($this, $id, $name, $items, $selectedindex, $selectedvalue, $addttext);
    }

    /**
     * Returns the html5 equivalent input field with datalist options.  This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's html5 compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the textbox
     * @param string $value The predefined value of the textbox, if any
     * @param string $items An array of items to put into the list... they should be $key=>$value pairs
     * @param string $size The number of columns wide the textbox should be displayed
     * @param string $maxlength The maximum number of characters that should be allowed to be entered
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @deprecated
     * @return string
     */
    function CreateInputDataList($id, $name, $value='', $items, $size='10', $maxlength='255', $addttext='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputDataList($this, $id, $name, $value, $items, $size, $maxlength, $addttext);
    }

    /**
     * Returns the xhtml equivalent of a multi-select list.	 This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it is xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the select list
     * @param string $items An array of items to put into the list... they should be $key=>$value pairs
     * @param string $selecteditems An array of items in the list that should default to selected.
     * @param string $size The number of rows to be visible in the list (before scrolling).
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @param bool $multiple indicates wether multiple selections are allowed (defaults to true)
     * @return string
     * @deprecated
     */
    function CreateInputSelectList($id, $name, $items, $selecteditems=array(), $size=3, $addttext='', $multiple = true)
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputSelectList($this, $id, $name, $items, $selecteditems, $size, $addttext, $multiple);
    }

    /**
     * Returns the xhtml equivalent of a set of radio buttons.	This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it is xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $name The html name of the radio group
     * @param string $items An array of items to create as radio buttons... they should be $key=>$value pairs
     * @param string $selectedvalue The default selected index of the radio group.	 Setting to -1 will result in the first choice being selected
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @param string $delimiter A delimiter to throw between each radio button, e.g., a <br /> tag or something for formatting
     * @return string
     */
    function CreateInputRadioGroup($id, $name, $items, $selectedvalue='', $addttext='', $delimiter='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateInputRadioGroup($this, $id, $name, $items, $selectedvalue, $addttext, $delimiter);
    }

    /**
     * Returns the xhtml equivalent of a textarea.	Also takes WYSIWYG preference into consideration if it's called from the admin side.
     *
     * @param bool   $enablewysiwyg Should we try to create a WYSIWYG for this textarea?
     * @param string $id The id given to the module on execution
     * @param string $text The text to display in the textarea's content
     * @param string $name The html name of the textarea
     * @param string $classname The CSS class to associate this textarea to
     * @param string $htmlid The html id to give to this textarea
     * @param string $encoding The encoding to use for the content
     * @param string $stylesheet The text of the stylesheet associated to this content.	 Only used for certain WYSIWYGs
     * @param string $cols The number of characters wide (columns) the resulting textarea should be
     * @param string $rows The number of characters high (rows) the resulting textarea should be
     * @param string $forcewysiwyg The wysiwyg-system to be forced even if the user has chosen another one
     * @param string $wantedsyntax The language the content should be syntaxhightlighted as
     * @param string $addtext Any additional text to include with the textarea field.
     * @return string
     * @deprecated
     * @see CmsFormUtils::create_textarea
     */
    function CreateTextArea($enablewysiwyg, $id, $text, $name, $classname='', $htmlid='', $encoding='', $stylesheet='', $cols='', $rows='',$forcewysiwyg='',$wantedsyntax='',$addtext='')
    {
        $parms = array();
        $parms['enablewysiwyg'] = $enablewysiwyg;
        $parms['name'] = $id.$name;
        if( $classname ) $parms['class'] = $classname;
        if( $htmlid ) $parms['id'] = $htmlid;
        if( $encoding ) $parms['encoding'] = $encoding;
        if( $stylesheet ) $parms['stylesheet'] = $stylesheet;
        if( $cols ) $parms['cols'] = $cols;
        if( $rows ) $parms['rows'] = $rows;
        if( $text ) $parms['text'] = $text;
        if( $forcewysiwyg ) $parms['forcemodule'] = $forcewysiwyg;
        if( $wantedsyntax ) $parms['wantedsyntax'] = $wantedsyntax;
        if( $addtext ) $parms['addtext'] = $addtext;

        try {
            return CmsFormUtils::create_textarea($parms);
        }
        catch( CmsException $e ) {
            return '';
        }
    }


    /**
     * Returns the xhtml equivalent of a textarea.	Also takes Syntax hilighter preference
     * into consideration if it's called from the admin side.
     *
     * @deprecated
     * @param string $id The id given to the module on execution
     * @param string $text The text to display in the textarea's content
     * @param string $name The html name of the textarea
     * @param string $classname The CSS class to associate this textarea to
     * @param string $htmlid The html id to give to this textarea
     * @param string $encoding The encoding to use for the content
     * @param string $stylesheet The text of the stylesheet associated to this content.	 Only used for certain WYSIWYGs
     * @param string $cols The number of characters wide (columns) the resulting textarea should be
     * @param string $rows The number of characters high (rows) the resulting textarea should be
     * @param string $addtext Additional text for the text area tag.
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
     * @param string $id The id given to the module on execution
     * @param string $returnid The id to eventually return to when the module is finished it's task
     * @param string $action The action that this form should do when the link is clicked
     * @param string $contents The text that will have to be clicked to follow the link
     * @param string $params An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
     * @param string $warn_message Text to display in a javascript warning box.  If they click no, the link is not followed by the browser.
     * @param bool $onlyhref A flag to determine if only the href section should be returned
     * @param bool $inline A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
     * @param string $addtext Any additional text that should be added into the tag when rendered
     * @param bool $targetcontentonly A flag indicating that the output of this link should target the content area of the destination page.
     * @param string $prettyurl An optional pretty url segment (relative to the root of the site) to use when generating the link.
     * @return string
     */
    function CreateFrontendLink( $id, $returnid, $action, $contents='', $params=array(),
                                 $warn_message='', $onlyhref=false, $inline=true, $addtext='',
                                 $targetcontentonly=false, $prettyurl='' )
    {
        return $this->CreateLink( $id, $action, $returnid, $contents, $params, $warn_message, $onlyhref,
                                  $inline, $addtext, $targetcontentonly, $prettyurl );
    }

    /**
     * Returns the xhtml equivalent of an href link	 This is basically a nice little wrapper
     * to make sure that id's are placed in names and also that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $action The action that this form should do when the link is clicked
     * @param string $returnid The id to eventually return to when the module is finished it's task
     * @param string $contents The text that will have to be clicked to follow the link
     * @param string $params An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
     * @param string $warn_message Text to display in a javascript warning box.  If they click no, the link is not followed by the browser.
     * @param bool $onlyhref A flag to determine if only the href section should be returned
     * @param bool $inline A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
     * @param string $addttext Any additional text that should be added into the tag when rendered
     * @param bool $targetcontentonly A flag to determine if the link should target the default content are of the destination page.
     * @param string $prettyurl An optional pretty url segment (related to the root of the website) for a pretty url.
     * @return string
     */
    function CreateLink($id, $action, $returnid='', $contents='', $params=array(),
                        $warn_message='', $onlyhref=false, $inline=false, $addttext='',
                        $targetcontentonly=false, $prettyurl='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateLink($this, $id, $action, $returnid, $contents, $params, $warn_message, $onlyhref, $inline, $addttext, $targetcontentonly, $prettyurl);
    }


    /**
     * Returns a URL to a module action
     * This method is called by the CreateLink methods when creating a link to a module action.
     *
     * @since 1.10
     * @param string  $id The module action id (cntnt01 indicates that the defaul content block of the destination page should be used).
     * @param string  $action The module action name
     * @param int $returnid The destination page.
     * @param hash    $params Areay of parameters for the URL.  These will be ignored if the prettyurl argument is specified.
     * @param bool $inline Wether the target of the output link is the same tag on the same page.
     * @param bool $targetcontentonly Wether the target of the output link targets the content area of the destination page.
     * @param string  $prettyurl An optional url segment related to the root of the page for pretty url purposes.
     * @return string.
     */
    public function create_url($id,$action,$returnid='',$params=array(),
                               $inline=false,$targetcontentonly=false,$prettyurl='')
    {
        $this->_loadFormMethods();
        return cms_module_create_url($this,$id,$action,$returnid,$params,$inline,$targetcontentonly,$prettyurl);
    }

    /**
     * Return a pretty url string for a module action
     * This method is called by the create_url and the CreateLink methods if the pretty url argument is not specified in order
     * to attempt automating a pretty url for an action.
     *
     * @since 1.10
     * @abstract
     * @param string $id The module action id (cntnt01 indicates that the defaul content block of the destination page should be used).
     * @param string $action The module action name
     * @param int $returnid The destination page.
     * @param array   $params Areay of parameters for the URL.  These will be ignored if the prettyurl argument is specified.
     * @param bool $inline Wether the target of the output link is the same tag on the same page.
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
     * @param int $pageid the page id of the page we want to direct to
     * @param string $contents The optional text or XHTML contents of the generated link
     * @return string
     * @deprecated
     */
    public function CreateContentLink($pageid, $contents='')
    {
        $this->_loadFormMethods();
        return cms_module_CreateContentLink($this, $pageid, $contents);
    }


    /**
     * Returns the xhtml equivalent of an href link for Content links.	This is basically a nice little wrapper
     * to make sure that we go back to where we want to and that it's xhtml compliant.
     *
     * @param string $id The id given to the module on execution
     * @param string $returnid The id to return to when the module is finished it's task
     * @param string $contents The text that will have to be clicked to follow the link
     * @param string $params An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
     * @param bool $onlyhref A flag to determine if only the href section should be returned
     * @return string
     */
    public function CreateReturnLink($id, $returnid, $contents='', $params=array(), $onlyhref=false)
    {
        $this->_loadFormMethods();
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
     * @param string $tab The tab name.  If empty, the current tab is used.
     * @param mixed|null  $params An assoiciative array of params, or null
     * @param string $action The action name (if not specified, defaultadmin is assumed)
     * @see CMSModule::SetCurrentTab
     */
    public function RedirectToAdminTab($tab = '',$params = '',$action = '')
    {
        if( $tab == '' ) if( $this->__current_tab ) $tab = $this->__current_tab;
        if( $params == '' ) $params = array();
        if( $tab != '' ) $params['__activetab'] = $tab;
        if( empty($action) ) $action = 'defaultadmin';
        $this->Redirect('m1_',$action,'',$params,FALSE);
    }

    /**
     * Redirects the user to another action of the module.
     * This function is optimized for frontend use.
     *
     * @param string $id The id given to the module on execution
     * @param string $returnid The action that this form should do when the form is submitted
     * @param string $action The id to eventually return to when the module is finished it's task
     * @param string $params An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
     * @param bool $inline A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
     */
    public function RedirectForFrontEnd($id, $returnid, $action, $params = array(), $inline = true )
    {
        return $this->Redirect($id, $action, $returnid, $params, $inline );
    }

    /**
     * Redirects the user to another action of the module.
     *
     * @param string $id The id given to the module on execution
     * @param string $action The action that this form should do when the form is submitted
     * @param string $returnid The id to eventually return to when the module is finished it's task
     * @param string $params An array of params that should be inlucded in the URL of the link.	 These should be in a $key=>$value format.
     * @param bool $inline A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
     */
    public function Redirect($id, $action, $returnid='', $params=array(), $inline=false)
    {
        $this->_loadRedirectMethods();
        return cms_module_Redirect($this, $id, $action, $returnid, $params, $inline);
    }

    /**
     * Redirects to an admin page
     * @param string $page php script to redirect to
     * @param array  $params optional array of url parameters
     * @deprecated
     */
    public function RedirectToAdmin($page,$params = array())
    {
        $this->_loadRedirectMethods();
        return cms_module_RedirectToAdmin($this,$page,$params);
    }

    /**
     * Redirects the user to a content page outside of the module.	The passed around returnid is
     * frequently used for this so that the user will return back to the page from which they first
     * entered the module.
     *
     * @param int $id Content id to redirect to.
     */
    public function RedirectContent($id)
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
     * @param string $module The required module name.
     * @return CMSModule The module object, or FALSE
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
     * @param string $capability name of the capability we are checking for. could be "wysiwyg" etc.
     * @param array  $params further params to get more detailed info about the capabilities. Should be syncronized with other modules of same type
     * @return array
     */
    final public function GetModulesWithCapability($capability, $params=array())
    {
        $result=array();
        $tmp = ModuleOperations::get_modules_with_capability($capability,$params);
        if( is_array($tmp) && count($tmp) ) {
            for( $i = 0, $n = count($tmp); $i < $n; $i++ ) {
                if( is_object($tmp[$i]) ) {
                    $result[] = get_class($tmp[$i]);
                }
                else {
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
     * Returns the corresponding translated string for the id given.
     * This method accepts variable arguments.  The first argument (required) is the language string key (a string)
     * Further arguments may be sprintf arguments matching the specified key.
     *
     * @return string
     */
    public function Lang()
    {
        //Push module name onto front of array
        $args = func_get_args();
        array_unshift($args,'');
        $args[0] = $this->GetName();

        return CmsLangOperations::lang_from_realm($args);
    }

    /**
     * ------------------------------------------------------------------
     * Template/Smarty Functions
     * ------------------------------------------------------------------
     */

    /**
     * Get a reference to the smarty template object that was passed in to the the action.
     * This method is only valid within a module action.
     *
     * @final
     * @since 2.0.1
     * @author calguy1000
     * @return Smarty_Internal_Template
     */
    final public function GetActionTemplateObject()
    {
        if( $this->_action_tpl ) return $this->_action_tpl;
    }

    /**
     * Build a resource string for an old module templates resource.
     * If the template name provided ends with .tpl a module file template is assumed.
     *
     * @final
     * @since 1.11
     * @author calguy1000
     * @param string $template The template name.
     * @return string
     */
    final public function GetDatabaseResource($template)
    {
        if( endswith($template,'.tpl') ) return 'module_file_tpl:'.$this->GetName().';'.$template;
        return 'module_db_tpl:'.$this->GetName().';'.$template;
    }

    /**
     * A function to return a resource identifier to a module specific template
     * if the template specified ends in .tpl then a file template is assumed.
     *
     * Note: Since 2.2.1 This function will throw a logic exception if a string or eval resource is supplied.
     *
     * @since 2.0
     * @author calguy1000
     * @param string $template The template name.
     * @return string
     */
    final public function GetTemplateResource($template)
    {
        if( strpos($template,':') !== FALSE ) {
            if( startswith($template,'string:') || startswith($template,'eval:') || startswith($template,'extends:') ) {
                throw new \LogicException('Invalid smarty resource specified for a module template.');
            }
            return $template;
        }
        if( endswith($template,'.tpl') ) return 'module_file_tpl:'.$this->GetName().';'.$template;
        return 'cms_template:'.$template;
    }


    /**
     * A function to return a resource identifier to a module specific file template
     *
     * @since 1.11
     * @author calguy1000
     * @param string $template The template name.
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
     * @deprecated
     * @param string $modulename If empty the current module name is used.
     * @return array
     */
    final public function ListTemplates($modulename = '')
    {
        $this->_loadTemplateMethods();
        return cms_module_ListTemplates($this, $modulename);
    }

    /**
     * Returns a database saved template.  This should be used for admin functions only, as it doesn't
     * follow any smarty caching rules.
     *
     * @final
     * @deprecated
     * @param string $tpl_name the template name.
     * @param string $modulename  If empty the current module name is used.
     * @return string
     */
    final public function GetTemplate($tpl_name, $modulename = '')
    {
        $this->_loadTemplateMethods();
        return cms_module_GetTemplate($this, $tpl_name, $modulename);
    }

    /**
     * Returns contents of the template that resides in modules/ModuleName/templates/{template_name}.tpl
     * Code adapted from the Guestbook module
     *
     * @final
     * @param string $template_name
     * @return string
     */
    final public function GetTemplateFromFile($template_name)
    {
        $this->_loadTemplateMethods();
        return cms_module_GetTemplateFromFile($this, $template_name);
    }


    /**
     * Sets a smarty template into the database and associates it with a module.
     *
     * @final
     * @deprecated
     * @param string $tpl_name The template name
     * @param string $content The template content
     * @param string $modulename The module name, if empty the current module name is used.
     * @return bool
     */
    final public function SetTemplate($tpl_name, $content, $modulename = '')
    {
        $this->_loadTemplateMethods();
        return cms_module_SetTemplate($this, $tpl_name, $content, $modulename);
    }

    /**
     * Delete a module template from the database
     *
     * @final
     * @deprecated
     * @param string $tpl_name The Template name, if empty all templates associated with the module are deleted.
     * @param string $modulename The module name, if empty the current module name is used.
     * @return bool
     */
    final public function DeleteTemplate($tpl_name = '', $modulename = '')
    {
        $this->_loadTemplateMethods();
        return cms_module_DeleteTemplate($this, $tpl_name, $modulename);
    }

    /**
     * Process A File template through smarty
     *
     * @final
     * @deprecated
     * @param string  $tpl_name    Template name
     * @param string  $designation Cache Designation
     * @param bool $cache       Cache flag
     * @param string  $cacheid     Unique cache flag
     * @return string
     */
    final public function ProcessTemplate($tpl_name, $designation = '', $cache = false, $cacheid = '')
    {
        $this->_loadTemplateMethods();
        return cms_module_ProcessTemplate($this, $tpl_name, $designation, $cache = false, $cacheid);
    }

    /**
     * Given a template in a variable, this method processes it through smarty
     * note, there is no caching involved.
     *
     * Note: this function is deprecated and scheduled for removal.
     *
     * @final
     * @param data $data Input template
     * @return string
     * @deprecated
     */
    final public function ProcessTemplateFromData( $data )
    {
        $this->_loadTemplateMethods();
        return cms_module_ProcessTemplateFromData($this, $data);
    }

    /**
     * Process a smarty template associated with a module through smarty and return the results
     *
     * @final
     * @deprecated
     * @param string $tpl_name Template name
     * @param string $designation (optional) Designation
     * @param bool $cache (optional) Cachable flag
     * @param string $modulename (optional) module name, if empty the current module is used.
     * @return string
     */
    final public function ProcessTemplateFromDatabase($tpl_name, $designation = '', $cache = false, $modulename = '')
    {
        $this->_loadTemplateMethods();
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
     * @deprecated
     */
    final public function ListUserTags()
    {
        $gCms = CmsApp::get_instance();
        $usertagops = $gCms->GetUserTagOperations();
        return $usertagops->ListUserTags();
    }

    /**
     * Call a specific user defined tag
     *
     * @final
     * @deprecated
     * @param string $name   Name of the user defined tag
     * @param array  $params Parameters for the user defined tag.
     * @return array
     */
    final public function CallUserTag($name, $params = array())
    {
        $gCms = CmsApp::get_instance();
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
     *
     * Used for the various template forms, this method can be used to control the tab that is displayed by default
     * when redirecting to an admin action that displays multiple tabs.
     *
     * @final
     * @since 1.11
     * @author calguy1000
     * @param string $tab The tab name
     * @see CMSModule::RedirectToAdminTab();)
     */
    public function SetCurrentTab($tab)
    {
        $this->__current_tab = $tab;
        cms_admin_tabs::set_current_tab($tab);
    }


    /**
     * Output a string suitable for staring tab headers.
     *
     * i.e:  echo $this->StartTabHeaders();
     *
     * @final
     * @return string
     */
    public function StartTabHeaders()
    {
        return cms_admin_tabs::start_tab_headers();
    }

    /**
     * Set a specific tab header.
     *
     * i.e:  echo $this->SetTabHeader('preferences',$this->Lang('preferences'));
     *
     * @final
     * @param string $tabid The tab id
     * @param string $title The tab title
     * @param bool $active wether the tab is active or not.
     * @param booleban A flag indicating wether this tab is active.
     * @return string
     */
    public function SetTabHeader($tabid,$title,$active=false)
    {
        return cms_admin_tabs::set_tab_header($tabid,$title,$active);
    }

    /**
     * Output a string to stop the output of headers and close the necessary XHTML div.
     *
     * @final
     * @return string
     */
    public function EndTabHeaders()
    {
        return cms_admin_tabs::end_tab_headers();
    }

    /**
     * Output a string to indicate the start of XHTML areas for tabs.
     *
     * @final
     * @return string
     */
    public function StartTabContent()
    {
        return cms_admin_tabs::start_tab_content();
    }

    /**
     * Output a string to indicate the end of XHTML areas for tabs.
     *
     * @final
     * @return string
     */
    public function EndTabContent()
    {
        return cms_admin_tabs::end_tab_content();
    }

    /**
     * Output a string to indicate the start of the output for a specific tab
     *
     * @final
     * @param string $tabid the tab id
     * @param arrray $params Parameters
     * @see CMSModule::SetTabHeaders()
     * @return string
     */
    public function StartTab($tabid, $params = array())
    {
        return cms_admin_tabs::start_tab($tabid,$params);
    }

    /**
     * Output a string to indicate the end of the output for a specific tab.
     *
     * @final
     * @return string
     */
    public function EndTab()
    {
        return cms_admin_tabs::end_tab();
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
    public function AdminStyle()
    {
        return '';
    }

    /**
     * Set the content-type header.
     *
     * @abstract
     * @param string $contenttype Value to set the content-type header too
     */
    public function SetContentType($contenttype)
    {
        CmsApp::get_instance()->set_content_type($contenttype);
    }

    /**
     * Put an event into the audit (admin) log.	 This should be
     * done on most admin events for consistency.
     *
     * @final
     * @param string $itemid   useful for working on a specific record (i.e article or user)
     * @param string $itemname item name
     * @param string $action   action name
     */
    final public function Audit($itemid = '', $itemname, $action)
    {
        audit($itemid,$itemname,$action);
    }

    /**
     * ShowMessage
     * Returns a formatted page status message
     *
     * @final
     * @param string $message Message to be shown
     * @return string
     */
    public function ShowMessage($message)
    {
        $theme = cms_utils::get_theme_object();
        if( is_object($theme) ) return $theme->ShowMessage($message);
        return '';
    }

    /**
     * Set a display  message.
     *
     * @since 1.11
     * @author Robert Campbell
     * @param string|string[] $str The message.  Accepts either an array of messages or a single string.
     */
    public function SetMessage($str)
    {
        $key = $this->GetName().'_messages';
        if( !isset( $_SESSION[$key] ) ) $_SESSION[$key] = [];
        if( !is_array($str) ) $str = [ $str ];
        $_SESSION[$key] = array_merge( $_SESSION[$key], $str );
    }

    /**
     * ShowErrors
     * Outputs errors in a nice error box with a troubleshooting link to the wiki
     *
     * @final
     * @param string|string[] $errors array or string of errors to be shown
     * @return string
     */
    public function ShowErrors($errors)
    {
        $theme = cms_utils::get_theme_object();
        if( is_object($theme) ) return $theme->ShowErrors($errors);
        return '';
    }

    /**
     * Set an error  message.
     *
     * @since 1.11
     * @author Robert Campbell
     * @param string|string[] $str The message.  Accepts either an array of messages or a single string.
     */
    public function SetError($str)
    {
        $key = $this->GetName().'_errors';
        if( !isset( $_SESSION[$key]) ) $_SESSION[$key] = [];
        if( !is_array($str) ) $str = array($str);
        $_SESSION[$key] = array_merge( $_SESSION[$key], $str );
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
     * @param string $permission_name Name of the permission to create
     * @param string $permission_text Description of the permission
     */
    final public function CreatePermission($permission_name, $permission_text = null)
    {
        try {
            if( !$permission_text ) $permission_text = $permission_name;
            $perm = new CmsPermission();
            $perm->source = $this->GetName();
            $perm->name = $permission_name;
            $perm->text = $permission_text;
            $perm->save();
        }
        catch( Exception $e ) {
			// ignored.
        }
    }

    /**
     * Checks a permission against the currently logged in user.
     *
     * @final
     * @param string $permission_name The name of the permission to check against the current user
     * @return bool
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
     * @param string $permission_name The name of the permission to remove
     */
    final public function RemovePermission($permission_name)
    {
        try {
            $perm = CmsPermission::load($permission_name);
            $perm->delete();
        }
        catch( Exception $e ) {
            // ignored.
        }
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
     * @param string $preference_name The name of the preference to check
     * @param string $defaultvalue    The default value, just in case it doesn't exist
     * @return string
     */
    final public function GetPreference($preference_name, $defaultvalue='')
    {
        return cms_siteprefs::get($this->GetName().'_mapi_pref_'.$preference_name, $defaultvalue);
    }

    /**
     * Sets a module preference.
     *
     * @final
     * @param string $preference_name The name of the preference to set
     * @param string $value The value to set it to
     */
    final public function SetPreference($preference_name, $value)
    {
        return cms_siteprefs::set($this->GetName().'_mapi_pref_'.$preference_name, $value);
    }

    /**
     * Removes a module preference.  If no preference name
     * is specified, removes all module preferences.
     *
     * @final
     * @param string $preference_name The name of the preference to remove.  If empty all preferences associated with the module are removed.
     * @return bool
     */
    final public function RemovePreference($preference_name='')
    {
        if( $preference_name == '' ) return cms_siteprefs::remove($this->GetName().'_mapi_pref_',true);
        return cms_siteprefs::remove($this->GetName().'_mapi_pref_'.$preference_name);
    }

    /**
     * List all preferences for a specific module by prefix.
     *
     * @final
     * @param string $prefix
     * @return string[]|null An array of preference names, or null.
     * @since 2.0
     */
    final public function ListPreferencesByPrefix($prefix)
    {
        if( !$prefix ) return;
        $prefix = $this->GetName().'_mapi_pref_'.$prefix;
        $tmp = cms_siteprefs::list_by_prefix($prefix);
        if( is_array($tmp) && count($tmp) ) {
            for( $i = 0, $n = count($tmp); $i < $n; $i++ ) {
                if( !startswith($tmp[$i],$prefix) ) {
                    throw new CmsInvalidDataException(__CLASS__.'::'.__METHOD__.' invalid prefix for preference');
                }
                $tmp[$i] = substr($tmp[$i],strlen($prefix));
            }
            return $tmp;
        }
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
     * @param string $modulename       The name of the module sending the event, or 'Core'
     * @param string $eventname       The name of the event
     * @param bool $removable      Can this event be removed from the list?
     * @returns bool
     */
    final public function AddEventHandler( $modulename, $eventname, $removable = true )
    {
        Events::AddEventHandler( $modulename, $eventname, false, $this->GetName(), $removable );
    }


    /**
     * Inform the system about a new event that can be generated
     *
     * @final
     * @param string $eventname The name of the event
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
     * @param string $originator The name of the originating module
     * @param string $eventname The name of the event
     * @param array  $params Array of parameters provided with the event.
     * @return bool
     */
    public function DoEvent( $originator, $eventname, &$params )
    {
        if ($originator != '' && $eventname != '') {
            $filename = dirname(dirname(__DIR__)) . '/modules/'.$this->GetName().'/event.'
                . $originator . "." . $eventname . '.php';

            if (@is_file($filename)) {
                $gCms = CmsApp::get_instance();
                $db = $gCms->GetDb();
                $config = $gCms->GetConfig();
                $smarty = $gCms->GetSmarty();

                include($filename);
            }
        }
    }


    /**
     * Get a (langified) description for an event this module created.
     * This method must be over-ridden if this module created any events.
     *
     * @abstract
     * @param string $eventname The name of the event
     * @return string
     */
    public function GetEventDescription( $eventname )
    {
        return "";
    }


    /**
     * Get a (langified) descriptionof the details about when an event is
     * created, and the parameters that are attached with it.
     * This method must be over-ridden if this module created any events.
     *
     * @abstract
     * @param string $eventname The name of the event
     * @return string
     */
    public function GetEventHelp( $eventname )
    {
        return "";
    }


    /**
     * A callback indicating if this module has a DoEvent method to
     * handle incoming events.
     *
     * @abstract
     * @return bool
     */
    public function HandlesEvents()
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
     * @param string $eventname The name of the event
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
     * @param string $modulename The module name (or Core)
     * @param string $eventname  The name of the event
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
     * @param string $eventname The name of the event
     * @param array  $params The parameters associated with this event.
     */
    final public function SendEvent( $eventname, $params )
    {
        Events::SendEvent($this->GetName(), $eventname, $params);
    }

} // end of class


/**
 * Indicates that the incoming parameter is expected to be an integer.
 * This is used when cleaning input parameters for a module action or module call.
 */
define('CLEAN_INT','CLEAN_INT');

/**
 * Indicates that the incoming parameter is expected to be a float
 * This is used when cleaning input parameters for a module action or module call.
 */
define('CLEAN_FLOAT','CLEAN_FLOAT');

/**
 * Indicates that the incoming parameter is not to be cleaned.
 * This is used when cleaning input parameters for a module action or module call.
 */
define('CLEAN_NONE','CLEAN_NONE');

/**
 * Indicates that the incoming parameter is a string.
 * This is used when cleaning input parameters for a module action or module call.
 */
define('CLEAN_STRING','CLEAN_STRING');

/**
 * Indicates that the incoming parameter is a regular expression.
 * This is used when cleaning input parameters for a module action or module call.
 */
define('CLEAN_REGEXP','regexp:');

/**
 * Indicates that the incoming parameter is an uploaded file.
 * This is used when cleaning input parameters for a module action or module call.
 */
define('CLEAN_FILE','CLEAN_FILE');

/**
 * @ignore
 */
define('CLEANED_FILENAME','BAD_FILE');
