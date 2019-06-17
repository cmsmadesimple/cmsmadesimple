<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * Global class representing the CMSMS application instance.
 *
 * This is a singleton class, that can only be instantiated once.  It also acts as a service provider.
 *
 * @package CMS
 * @license GPL
 */

use CMSMS\hook_manager;
use CMSMS\internal\hook_mapping_manager;
use CMSMS\internal\MactEncoder;
use CMSMS\internal\Smarty;
use CMSMS\internal\AdminThemeManager;
use CMSMS\internal\global_cache;
use CMSMS\internal\page_string_handler;
use CMSMS\internal\module_smarty_plugin_manager;
use CMSMS\LoginOperations;
use CMSMS\apc_cache_driver;
use CMSMS\LayoutTemplateManager;
use CMSMS\ScriptManager;
use CMSMS\StylesheetManager;
use CMSMS\ICookieManager;
use CMSMS\AutoCookieManager;
use CMSMS\simple_plugin_operations;
use CMSMS\Database\Connection as Database;
use CMSMS\Database\compatibility as DBCompatibility;

/**
 * Simple singleton class that contains various functions and states
 * representing the application.
 *
 * Note: This class was named CmsObject before version 1.10
 *
 * @package CMS
 * @license GPL
 * @since 0.5
 */
final class CmsApp
{

    /**
     * A constant indicating that the request is for a page in the CMSMS admin console
     */
    const STATE_ADMIN_PAGE = 'admin_request';

    /**
     * A constant indicating that the request is taking place during the installation process
     */
    const STATE_INSTALL    = 'install_request';

    /**
     * A constant indicating that the request is for a stylesheet
     */
    const STATE_STYLESHEET = 'stylesheet_request';

    /**
     * A constant indicating that we are currently parsing page templates
     */
    const STATE_PARSE_TEMPLATE = 'parse_page_template';

    /**
     * A constant indicating that the request is for an admin login
     */
    const STATE_LOGIN_PAGE = 'login_request';

    /**
     * @ignore
     */
    private static $_instance;

    /**
     * @ignore
     */
    private $_current_content_page;

    /**
     * @ignore
     */
    private $_content_type;

    /**
     * @ignore
     */
    private $_showtemplate = true;

    /**
     * List of currrent states.
     * @ignore
     */
    private $_states;

    /**
     * @ignore
     */
    private static $_statelist = array(self::STATE_ADMIN_PAGE,self::STATE_STYLESHEET, self::STATE_INSTALL,self::STATE_PARSE_TEMPLATE,self::STATE_LOGIN_PAGE);

    /**
     * Database object - adodb reference to the current database
     * @ignore
     */
    private $db;

    /**
     * Internal error array - So functions/modules can store up debug info and spit it all out at once
     * @ignore
     */
    private $errors = [];

    /**
     * @ignore
     */
    public function __get(string $key)
    {
        switch($key) {
        case 'db':
            return $this->GetDb();
        case 'config':
            return $this->GetConfig();
        }
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        if( self::$_instance ) throw new \LogicException("Only one instance of ".__CLASS__.' is permitted');
        self::$_instance = $this;

        register_shutdown_function([&$this, 'dbshutdown']);
    }

    /**
     * Retrieve the single app instancce.
     *
     * @since 1.10
     */
    public static function get_instance()
    {
        if( !isset(self::$_instance)  ) throw new \LogicException('An instance of '.__CLASS__.' has not been created');
        return self::$_instance;
    }


    /**
     * Retrieve the installed schema version.
     *
     * @since 2.0
     */
    public function get_installed_schema_version()
    {
        if( self::test_state(self::STATE_INSTALL) ) {
            $db = $this->GetDb();
            $query = 'SELECT version FROM '.CMS_DB_PREFIX.'version';
            return $db->GetOne($query);
        }
        return global_cache::get('schema_version');
    }

    /**
     * Retrieve the list of errors
     *
     * @ignore
     * @since 1.9
     * @internal
     * @access private.
     * return array
     */
    public function get_errors()
    {
        return $this->errors;
    }


    /**
     * Add an error to the list
     *
     * @ignore
     * @since 1.9
     * @internal
     * @access private
     * @param string The error message.
     */
    public function add_error(string $str)
    {
        if( !is_array($this->errors) ) $this->errors = [];
        $this->errors[] = $str;
    }


    /**
     * Retrieve the request content type (for frontend requests)
     *
     * If no content type is explicity set, text/html is assumed.
     *
     * @since 2.0
     */
    public function get_content_type()
    {
        if( $this->_content_type ) return $this->_content_type;
        return 'text/html';
    }

    /**
     * Set the request content type to a valid mime type.
     *
     * @param string $mime_type
     * @since 2.0
     */
    public function set_content_type(string $mime_type = null)
    {
        $this->_content_type = null;
        if( $mime_type ) $this->_content_type = $mime_type;
    }

    /**
     * Disable the processing of the page template.
     * This function controls whether the page template will be processed at all.
     * It must be called early enough in the content generation process.
     *
     * Ideally this method can be called from within a module action that is called from within the default content block
     * when content_processing is set to 2 (the default) in the config.php
     *
     * @return void
     * @since 2.3
     */
    public function disable_template_processing()
    {
        $this->_showtemplate = false;
    }

    /**
     * Get the flag indicating whether or not template processing is allowed.
     *
     * @return bool
     * @since 2.3
     */
    public function template_processing_allowed()
    {
        return $this->_showtemplate;
    }

    /**
     * Set the current content page
     *
     * @since 2.0
     * @internal
     * @access private
     * @ignore
     */
    public function set_content_object(ContentBase &$content)
    {
        if( !$this->_current_content_page || $content instanceof ErrorPage ) $this->_current_content_page = $content;
    }

    /**
     * Get the current content page.
     * Will return the current content object for frontend requests.
     * For admin requests, this method returns null.
     *
     * @since 2.0
     * @return ContentBase|null
     */
    public function get_content_object()
    {
        return $this->_current_content_page;
    }

    /**
     * Get the ID of the current content page
     *
     * @since 2.0
     * @return int|null
     */
    public function get_content_id()
    {
        $obj = $this->get_content_object();
        if( is_object($obj) ) return $obj->Id();
    }


    /**
     * Get a list of all installed and available modules
     *
     * This method will return an array of module names that are installed, loaded and ready for use.
     * suotable for iteration with GetModuleInstance
     *
     * @see CmsApp::GetModuleInstance()
     * @since 1.9
     * @return string[]
     */
    public function GetAvailableModules()
    {
        return $this->GetModuleOperations()->get_available_modules();
    }


    /**
     * Get a reference to an installed module instance.
     *
     * This method will return a reference to the module object specified if it is installed, and available.
     * Optionally, a version check can be performed to test if the version of the requeted module matches
     * that specified.
     *
     * @since 1.9
     * @param string $module_name The module name.
     * @param string $version (optional) version number for a check.
     * @return CMSModule Reference to the module object, or null.
     */
    public function GetModuleInstance($module_name,$version = '')
    {
        return $this->GetModuleOperations()->get_module_instance($module_name,$version);
    }


    /**
     * Set the database connection object.
     *
     * @final
     * @internal
     * @ignore
     * @param ADOConnection $connection
     */
    final public function _setDb(Database $conn)
    {
        $this->db = $conn;
    }

    /**
    * Get a handle to the ADODB database object. You can then use this
    * to perform all kinds of database operations.
    *
    * @link http://phplens.com/lens/adodb/docs-adodb.htm
    * @final
    * @return ADOConnection a handle to the ADODB database object
    */
    final public function GetDb()
    {
        /* Check to see if we have a valid instance.
        * If not, build the connection
         */
        if (isset($this->db)) return $this->db;
        global $DONT_LOAD_DB;

        if( !isset($DONT_LOAD_DB) ) {
            $config = $this->GetConfig();
            $this->db = DBCompatibility::init($config);
        }

        return $this->db;
    }

    /**
     * Get the database prefix.
     *
     * @return string
     */
    public function GetDbPrefix() : string
    {
        return CMS_DB_PREFIX;
    }

    /**
    * Get a handle to the global CMS config.
    *
    * This object contains global paths and settings that do not belong in the database.
    *
    * @final
    * @return cms_config The configuration object.
    */
    final public function GetConfig() : cms_config
    {
        static $_obj;
        if( !$_obj ) $_obj = new cms_config($this);
        return $_obj;
    }


    /**
    * Get a handle to the CMS ModuleOperations object.
    * If it does not yet exist, this method will instantiate it.
    *
    * @final
    * @see ModuleOperations
    * @return ModuleOperations handle to the ModuleOperations object
    */
    public function GetModuleOperations() : ModuleOperations
    {
        static $_obj;
        if( !$_obj ) $_obj = new ModuleOperations( $this, $this->GetConfig() );
        return $_obj;
    }


    /**
     * Get the simple plugin operations object.
     *
     * @return \CMSMS\simple_plugin_operations
     */
    public function GetSimplePluginOperations() : simple_plugin_operations
    {
        static $_obj;
        if( !$_obj ) $_obj = new simple_plugin_operations();
        return $_obj;
    }

    /**
    * Get a handle to the CMS UserOperations singleton object.
    * If it does not yet exist, this method will instantiate it.
    *
    * @final
    * @see UserOperations
    * @return UserOperations handle to the UserOperations object
    */
    public function GetUserOperations() : UserOperations
    {
        static $_obj;
        if( !$_obj ) $_obj = new UserOperations( $this->GetDb() );
        return $_obj;
    }

    /**
    * Get a handle to the CMS ContentOperations singleton object.
    * If it does not yet exist, this method will instantiate it.
    *
    * @final
    * @see ContentOperations::get_instance()
    * @return ContentOperations handle to the ContentOperations object
    */
    public function GetContentOperations() : ContentOperations
    {
        static $_obj;
        if( !$_obj ) $_obj = new ContentOperations( $this, $this->get_cache_driver() );
        return $_obj;
    }

    /**
    * Get a handle to the CMS Admin BookmarkOperations singleton object.
    * If it does not yet exist, this method will instantiate it.
    *
    * @final
    * @see BookmarkOperations
    * @return BookmarkOperations handle to the BookmarkOperations object, useful only in the admin
    */
    public function GetBookmarkOperations() : BookmarkOperations
    {
        static $_obj;
        if( !$_obj ) $_obj = new BookmarkOperations();
        return $_obj;
    }


    /**
    * Get a handle to the CMS GroupOperations object.
    * If it does not yet exist, this method will instantiate it.
    *
    * @final
    * @see GroupOperations
    * @return GroupOperations handle to the GroupOperations object
    */
    public function GetGroupOperations() : GroupOperations
    {
        static $_obj;
        if( !$_obj ) $_obj = new GroupOperations( $this->GetDb() );
        return $_obj;
    }

    /**
    * Get a handle to the CMS UserTagOperations object.
    * If it does not yet exist, this method will instantiate it.
    *
    * @final
    * @see UserTagOperations
    * @return UserTagOperations handle to the UserTagOperations object
    * @deprecated
    */
    public function GetUserTagOperations() : UserTagOperations
    {
        static $_obj;
        if( !$_obj ) $obj = new UserTagOperations( $this->GetSimplePluginOperations() );
        return $_obj;
    }


    /**
    * Get a handle to the CMS Smarty object.
    * If it does not yet exist, this method will instantiate it.
    *
    * @final
    * @see Smarty_CMS
    * @link http://www.smarty.net/manual/en/
    * @return mixed Smarty_CMS handle to the Smarty object.  Null if called from the phar installer
    */
    public function GetSmarty()
    {
        global $CMS_PHAR_INSTALLER;
        if( isset($CMS_PHAR_INSTALLER) ) {
            // we can't load the CMSMS version of smarty during the installation.
            return;
        }
        static $_obj;
        if( !$_obj ) $_obj = new Smarty($this);
        return $_obj;
    }

    /**
    * Get a handle to the CMS HierarchyManager object.
    * If it does not yet exist, this method will instantiate it.
    *
    * @final
    * @see HierarchyManager
    * @return HierarchyManager handle to the HierarchyManager object
    */
    public function GetHierarchyManager()
    {
        /* Check to see if a HierarchyManager has been instantiated yet,
        and, if not, go ahead an create the instance. */
        static $_obj;
        if( !$_obj ) $_obj = global_cache::get('content_tree');
        return $_obj;
    }

    /**
     * Get a handle to the ScriptCombiner stuff
     *
     * @internal
     * @since 2.3
     */
    public function get_script_manager() : ScriptManager
    {
        static $_obj;
        if( !$_obj ) $_obj = new ScriptManager( $this );
        return $_obj;
    }

    /**
     * Get a handle to the Stylesheet Combiner stuff
     *
     * @internal
     * @since 2.3
     */
    public function get_stylesheet_manager() : StylesheetManager
    {
        static $_mgr;
        if( !$_mgr ) $_mgr = new StylesheetManager( $this );
        return $_mgr;
    }

    /**
     * Get the hook mapping manager
     *
     * @final
     * @since 2.3
     * @internal
     * @return \CMSMS\internal\hook_mapping_manager
     */
    public function GetHookMappingManager() : hook_mapping_manager
    {
        static $_mgr;
        if( !$_mgr ) $_mgr = new hook_mapping_manager($this->get_hook_manager(), $this->GetSimplePluginOperations(),
                                                      CMS_ASSETS_PATH.'/configs/hook_mapping.json');
        return $_mgr;
    }

    /**
     * Get the cache driver object.
     *
     * @final
     * @since 2.3
     * @return \cms_cache_driver
     */
    public function get_cache_driver() : cms_cache_driver
    {
        static $_driver;
        if( !$_driver ) {
            $config = $this->GetConfig();
            $ttl = (int) $config['cache_ttl'];
            if( !$ttl ) $ttl = 24 * 3600;
            $ttl = max(1,min($ttl,365 * 24 * 3600));
            if( $config['cache_driver'] == 'APC' ) {
                // get a TTL.
                $_driver = new apc_cache_driver( $ttl );
            } else {
                // todo: config options for the filecache driver.
                $opts = [ 'lifetime'=>$ttl ];
                $_driver = new cms_filecache_driver();
            }
        }
        return $_driver;
    }

    /**
     * Get the hook manager object
     *
     * @since 2.3
     */
    public function get_hook_manager() : hook_manager
    {
        static $_mgr;
        if( !$_mgr ) {
            $_mgr = new hook_manager();
        }
        return $_mgr;
    }

    /**
     * Get the template manager object (for DesignManager templates)
     *
     * @since 2.3
     */
    public function get_template_manager() : LayoutTemplateManager
    {
        static $mgr;
        if( !$mgr ) $mgr = new LayoutTemplateManager(
            $this->GetDB(),
            $this->get_cache_driver(),
            $this->get_hook_manager(),
            $this->config);
        return $mgr;
    }

    /**
     * Get the cookie manager.
     *
     * @since 2.3
     */
    public function get_cookie_manager() : ICookieManager
    {
        static $mgr;
        if( !$mgr ) $mgr = new AutoCookieManager($this);
        return $mgr;
    }

    /**
     * Get the mact encoder/decoder
     *
     * @internal
     * @since 2.3
     */
    public function get_mact_encoder() : MactEncoder
    {
        static $obj;
        if( !$obj ) $obj = new MactEncoder( $this );
        return $obj;
    }

    /**
     * If we have a secure MACT request, this will expand it into the old form inside $_REQUEST
     *
     * @internal
     * @since 2.3
     */
    public function expand_secure_mact()
    {
        $this->get_mact_encoder()->expand_secure_mact();
    }

    /**
     * Get a randmoized string to uniquely identify this site
     * can be used as a salt for other hashes.
     *
     * @since 2.3
     * @return string
     */
    public function get_site_identifier() : string
    {
        static $val;
        if( !$val ) $val = cms_siteprefs::get('site_signature');
        if( !$val ) cms_error('site_signature preference is empty... hopefully a development issue');
        return $val;
    }

    /**
     * Create a new instance of the mailer
     *
     * @since 2.3
     * @return cms_mailer
     */
    public function create_new_mailer(bool $exceptions = true) : cms_mailer
    {
        return new cms_mailer($exceptions);
    }

    /**
     * Get login operations object
     *
     * @internal
     * @since 2.3
     * @return LoginOperations
     */
    public function get_login_operations() : LoginOperations
    {
        static $_obj;
        if( !$_obj ) {
            $salt = cms_siteprefs::get(__METHOD__);
            if( !$salt ) {
                $salt = sha1( __FILE__.bin2hex(random_bytes(64)));
                cms_siteprefs::set(__METHOD__,$salt);
            }
            $flag = (bool) $this->GetConfig()['stupidly_ignore_xss_vulnerability'];
            $_obj = new LoginOperations( $this->GetUserOperations(), $this->get_cookie_manager(), $salt, $flag );
        }
        return $_obj;
    }

    /**
     * Get the mact encoder/decoder
     *
     * @internal
     * @since 2.3
     */
    public function get_page_string_handler() : page_string_handler
    {
        static $obj;
        if( !$obj ) $obj = new page_string_handler( $this->get_hook_manager() );
        return $obj;
    }

    /**
     * Get the module_smarty_plugin_manager
     *
     * @internal
     * @since 2.3
     * @return moddule_smarty_plugin_manager
     */
    public function get_module_smarty_plugin_manager()
    {
        static $obj;
        if( !$obj ) $obj = new module_smarty_plugin_manager( $this->GetDB(), $this->get_cache_driver() );
        return $obj;
    }

    /**
     * Get an admin theme manager object
     *
     * @since 2.3
     * @internal
     * @return AdminThemeManager
     */
    public function get_theme_manager() : AdminThemeManager
    {
        static $_obj;
        if( !$_obj ) {
            $path = $this->GetConfig()['admin_path'].'/themes';
            $_obj = new AdminThemeManager( $path );
        }
        return $_obj;
    }

    /**
     * Get the admin theme object
     *
     * @since 2.3
     * @return CmsAdminThemeBase
     */
    public function get_admin_theme() : CmsAdminThemeBase
    {
        global $CMS_ADMIN_PAGE;
        if( !isset($CMS_ADMIN_PAGE) ) throw new \LogicException(__METHOD__.' cannot be called from non admin pages');

        static $_obj;
        if( !$_obj ) {
            // get the defalt theme
            $uid = get_userid(FALSE);
            $theme_manager = $this->get_theme_manager();
            $theme_name = null;
            $usertheme = cms_userprefs::get_for_user($uid,'admintheme');
            if( $usertheme ) $_obj = $theme_manager->load_theme($usertheme, $this, $uid);
            if( !$_obj ) {
                $logintheme = cms_siteprefs::get('logintheme');
                if( $logintheme ) $_obj = $theme_manager->load_theme($logintheme, $this, $uid);
            }
            if( !$_obj ) {
                $dflttheme = $theme_manager->get_default_themename();
                if( $dflttheme ) $_obj = $theme_manager->load_theme($dflttheme, $this, $uid);
            }
            if( !$_obj ) throw new \RuntimeException("Could not find an admin theme to instantiate");
        }
        return $_obj;
    }

    /**
     * Get a list of page templates that are displayable to editors.
     *
     * @return array An array of hashes, each entry is a hash with 'label' and 'value' properties.
     * @internal
     * @ignore
     * @access private
     */
    public function get_page_template_list() : array
    {
        $list = null;
        $page_template_list = $this->GetConfig()['page_template_list'];
        if( !empty($page_template_list) ) {
            if( is_string($page_template_list) ) $page_template_list = [$page_template_list];
            foreach( $page_template_list as $lbl => $val ) {
                if( (int) $lbl > 0 && trim($lbl) == $lbl ) $lbl = $val;
                $list[] = ['value'=>$val, 'label'=>$lbl ];
            }
            return $list;
        }

        $_tpl = CmsLayoutTemplate::template_query( ['as_list'=>1] );
        if( is_array($_tpl) && count($_tpl) > 0 ) {
            foreach( $_tpl as $tpl_id => $tpl_name ) {
                $list[] = [ 'value'=>$tpl_id,'label'=>$tpl_name ];
            }
        }
        // read from theme directories if they exit.
        $themes = glob(CMS_ASSETS_PATH.'/themes/*/theme.json');
        if( !empty($themes) ) {
            foreach( $themes as $theme_json ) {
                $theme = basename(dirname($theme_json));
                $json = json_decode(file_get_contents($theme_json));
                if( !$json || !isset($json->page_templates)  ) continue;
                if( !is_array($json->page_templates) || !isset($json->page_templates[0]) ) continue;
                foreach( $json->page_templates as $one ) {
                    if( !isset($one->label) || !isset($one->template) || !$one->label || !$one->template ) continue;
                    $one->label = $theme.' : '.$one->label;
                    $one->value = "cms_theme:$theme;".$one->template;
                    $list[] = json_decode(json_encode($one), TRUE);
                }
            }
        }
        if( empty($list) ) throw new \LogicException('Could not determine a template list');
        return $list;
    }

    /**
     * Disconnect from the database.
     *
     * @final
     * @internal
     * @ignore
     * @access private
     */
    public function dbshutdown()
    {
        if (isset($this->db)) {
            $db = $this->db;
            if ($db->IsConnected())	$db->Close();
        }
    }


    /**
     * Clear out cached files from the CMS tmp/cache and tmp/templates_c directories.
     *
     * NOTE: This function is for use by CMSMS only.  No third party application, UDT or code
     *   can use this method and still exist in the CMSMS forge or be supported in any way.
     *
     * @final
     * @internal
     * @ignore
     * @access private
     */
    final public function clear_cached_files()
    {
        $this->get_hook_manager()->emit('Core::BeforeClearCache');

        // clear APC, or file cache separately and completely
        $config = $this->GetConfig();
        global_cache::clear_all();
        $this->get_cache_driver()->clear();

        $the_time = time();
        $dirs = array(TMP_CACHE_LOCATION, PUBLIC_CACHE_LOCATION, TMP_TEMPLATES_C_LOCATION);
        foreach( $dirs as $start_dir ) {
            $dirIterator = new RecursiveDirectoryIterator($start_dir);
            $dirContents = new RecursiveIteratorIterator($dirIterator);
            foreach( $dirContents as $one ) {
                if( $one->isFile() && $one->getMTime() <= $the_time ) @unlink($one->getPathname());
            }
            @touch(cms_join_path($start_dir,'index.html'));
        }

        file_put_contents(TMP_CACHE_LOCATION.'/.root_url', $config['root_url']);
        $this->get_hook_manager()->emit('Core::AfterClearCache');
    }

    /**
     * Set all known states from global variables.
     *
     * @since 1.11.2
     * @ignore
     */
    private function set_states()
    {
        if( !isset($this->_states) ) {
            // build the array.
            global $CMS_ADMIN_PAGE;
            global $CMS_INSTALL_PAGE;
            global $CMS_STYLESHEET;
            global $CMS_LOGIN_PAGE;

            $this->_states = array();

            if( isset($CMS_LOGIN_PAGE) ) $this->_states[] = self::STATE_LOGIN_PAGE;
            if( isset($CMS_ADMIN_PAGE) ) $this->_states[] = self::STATE_ADMIN_PAGE;
            if( isset($CMS_INSTALL_PAGE) ) $this->_states[] = self::STATE_INSTALL;
            if( isset($CMS_STYLESHEET) ) $this->_states[] = self::STATE_STYLESHEET;
        }
    }

    /**
	 * Test if the current application state matches the requested value.
	 * This method will throw an exception if invalid data is passed in.
	 *
	 * @since 1.11.2
	 * @author Robert Campbell
	 * @param string $state A valid state name (see the state list above).  It is recommended that the class constants be used.
	 * @return bool
	 */
    public function test_state($state) : bool
    {
        if( !in_array($state,self::$_statelist) ) throw new CmsInvalidDataException($state.' is an invalid CMSMS state');
        $this->set_states();
        if( is_array($this->_states) && in_array($state,$this->_states) ) return TRUE;
        return FALSE;
    }

    /**
	 * Get a list of all current states.
	 *
	 * @since 1.11.2
	 * @author Robert Campbell
	 * @return stringp[] Array of state strings, or null.
	 */
    public function get_states()
    {
        $this->set_states();
        if( isset($this->_states) ) return $this->_states;
    }

    /**
	 * Add a state to the list of states.
	 *
	 * This method will throw an exception if an invalid state is passed in.
	 *
	 * @ignore
	 * @internal
	 * @since 1.11.2
	 * @author Robert Campbell
	 * @param string The state.  We recommend you use the class constants for this.
	 */
    public function add_state($state)
    {
        if( !in_array($state,self::$_statelist) ) throw new CmsInvalidDataException($state.' is an invalid CMSMS state');
        $this->set_states();
        $this->_states[] = $state;
    }

    /**
	 * Remove a state to the list of states.
	 *
	 * This method will throw an exception if an invalid state is passed in.
	 *
	 * @ignore
	 * @internal
	 * @since 1.11.2
	 * @author Robert Campbell
	 * @param string The state.  We recommend you use the class constants for this.
	 */
    public function remove_state(string $state)
    {
        if( !in_array($state,self::$_statelist) ) throw new CmsInvalidDataException($state.' is an invalid CMSMS state');
        $this->set_states();
        if( !is_array($this->_states) || !in_array($state,$this->_states) ) {
            $key = array_search($state,$this->_states);
            if( $key !== FALSE ) unset($this->_states[$key]);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * A convenience method to test if the current request was executed via the CLI.
     *
     * @since 2.2.9
     * @author Robert Campbell
     * @return bool
     */
    public function is_cli() : bool
    {
        return (php_sapi_name() == 'cli');
    }

    /**
     * A convenience method to test if the current request is a frontend request.
     *
     * @since 1.11.2
     * @author Robert Campbell
     * @return bool
     */
    public function is_frontend_request() : bool
    {
        $tmp = $this->get_states();
        if( !is_array($tmp) || count($tmp) == 0 ) return TRUE;
        return FALSE;
    }

    /** A convenience method to test if the current request was over HTTPS.
     *
	 * @since 1.11.12
	 * @author Robert Campbell
	 * @return bool
	 */
    public function is_https_request() : bool
    {
        if( isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' ) return TRUE;
        return FALSE;
    }
}


/**
 * Simple global convenience object to hold CMS Content Type structure.
 *
 * This class is subject to a refactoring at some point.
 *
 * @package CMS
 */
class CmsContentTypePlaceholder
{

    /**
     * @var string The type name
     */
    public $type;

    /**
     * @var string The filename containing the type class
     */
    public $filename;

    /**
     * @var string A friendly name for the type
     */
    public $friendlyname;

    /**
     * @var Wether the type has been loaded
     */
    public $loaded;
}


/**
 * Return the global cmsms() object.
 *
 * @since 1.7
 * @return CmsApp
 * @see CmsApp::get_instance()
 */
function cmsms() : CmsApp
{
    static $_obj;
    if( !$_obj ) $_obj = CmsApp::get_instance();
    return $_obj;
}


/**
 * Returns the currently configured database prefix.
 *
 * @since 0.4
 * @return string
 * @see CmsApp::GetDbPrefix();
 */
function cms_db_prefix() {
    return CMS_DB_PREFIX;
}
