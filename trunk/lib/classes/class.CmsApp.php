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
 * Global class for easy access to all important variables.
 *
 * @package CMS
 * @license GPL
 */

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
final class CmsApp {

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
	 * List of currrent states.
	 * @ignore
	 */
	private $_states;

	/**
	 * @ignore
	 */
	private static $_statelist = array(self::STATE_ADMIN_PAGE,self::STATE_STYLESHEET, self::STATE_INSTALL,self::STATE_PARSE_TEMPLATE);

	/**
	 * Database object - adodb reference to the current database
	 * @ignore
	 */
	private $db;

	/**
	 * An override for the database prefix.  If specified, this will be used instead of config value.
	 * @ignore
	 */
	private $dbprefix;

    /**
     * @ignore
     */
    private $hrinstance;

	/**
	 * Internal error array - So functions/modules can store up debug info and spit it all out at once
	 * @ignore
	 */
	private $errors = array();

	/**
	 * @ignore
	 */
	public function __get($key)
	{
		switch($key) {
		case 'config':
			return cms_config::get_instance();
			break;
		}
	}

	/**
	 * Constructor
	 */
	protected function __construct()
	{
		register_shutdown_function(array(&$this, 'dbshutdown'));
	}

	/**
	 * Retrieve the single app instancce.
	 *
	 * @since 1.10
	 */
	public static function &get_instance()
	{
		if( !isset(self::$_instance)  ) self::$_instance = new CmsApp();
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
            $query = 'SELECT version FROM '.CmsApp::get_instance()->GetDbPrefix().'version';
            return $db->GetOne($query);
        }
        return \CMSMS\internal\global_cache::get('schema_version');
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
	public function add_error($str)
	{
		if( !is_array($this->errors) ) $this->errors = array();
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
	public function set_content_type($mime_type = '')
	{
		$this->_content_type = null;
		if( isset($mime_type) ) $this->_content_type = $mime_type;
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
	 * Get the current content page
	 *
	 * @since 2.0
	 */
	public function get_content_object()
	{
		return $this->_current_content_page;
	}

	/**
	 * Get the ID of the current content page
	 *
	 * @since 2.0
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
		return ModuleOperations::get_instance()->get_available_modules();
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
	 * @deprecated
	 */
	public function &GetModuleInstance($module_name,$version = '')
	{
		return ModuleOperations::get_instance()->get_module_instance($module_name,$version);
	}


	/**
	 * Set the database connection object.
	 *
	 * @final
	 * @internal
	 * @ignore
	 * @param ADOConnection $connection
	 */
	final public function _setDb(\CMSMS\Database\Connection $conn)
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
	final public function &GetDb()
	{
		/* Check to see if we have a valid instance.
		 * If not, build the connection
         */
		if (isset($this->db)) return $this->db;
		global $DONT_LOAD_DB;

		if( !isset($DONT_LOAD_DB) ) {
            $config = \cms_config::get_instance();
            $this->db = \CMSMS\Database\compatibility::init($config);
		}

		return $this->db;
	}

	/**
	 * Get the database prefix.
	 *
	 * @return string
	 */
	public function GetDbPrefix()
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
	public function &GetConfig()
	{
		return cms_config::get_instance();
	}


	/**
	* Get a handle to the CMS ModuleOperations object.
	* If it does not yet exist, this method will instantiate it.
	*
	* @final
	* @see ModuleOperations
	* @return ModuleOperations handle to the ModuleOperations object
    * @deprecated
	*/
	public function & GetModuleOperations()
	{
		return ModuleOperations::get_instance();
	}


	/**
	* Get a handle to the CMS UserOperations singleton object.
	* If it does not yet exist, this method will instantiate it.
	*
	* @final
	* @see UserOperations
	* @return UserOperations handle to the UserOperations object
    * @deprecated
	*/
	public function & GetUserOperations()
	{
		return UserOperations::get_instance();
	}

	/**
	* Get a handle to the CMS ContentOperations singleton object.
	* If it does not yet exist, this method will instantiate it.
	*
	* @final
	* @see ContentOperations::get_instance()
	* @return ContentOperations handle to the ContentOperations object
    * @deprecated
	*/
	public function & GetContentOperations()
	{
		return ContentOperations::get_instance();
	}

	/**
	* Get a handle to the CMS Admin BookmarkOperations singleton object.
	* If it does not yet exist, this method will instantiate it.
	*
	* @final
	* @see BookmarkOperations
	* @return BookmarkOperations handle to the BookmarkOperations object, useful only in the admin
    * @deprecated
	*/
	public function & GetBookmarkOperations()
	{
        if (!isset($this->bookmarkoperations)) $this->bookmarkoperations = new BookmarkOperations();
		return $this->bookmarkoperations;
	}


	/**
	* Get a handle to the CMS GroupOperations object.
	* If it does not yet exist, this method will instantiate it.
	*
	* @final
	* @see GroupOperations
	* @return GroupOperations handle to the GroupOperations object
    * @deprecated
	*/
	public function & GetGroupOperations()
	{
		return GroupOperations::get_instance();
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
	public function & GetUserTagOperations()
	{
		return UserTagOperations::get_instance();
	}

	/**
	* Get a handle to the CMS Smarty object.
	* If it does not yet exist, this method will instantiate it.
	*
	* @final
	* @see Smarty_CMS
	* @link http://www.smarty.net/manual/en/
	* @return Smarty_CMS handle to the Smarty object
	*/
	public function & GetSmarty()
	{
		global $CMS_PHAR_INSTALLER;
		if( isset($CMS_PHAR_INSTALLER) ) {
			// we can't load the CMSMS version of smarty during the installation.
			$out = null;
			return $out;
		}
		return Smarty_CMS::get_instance();
	}

	/**
	* Get a handle to the CMS HierarchyManager object.
	* If it does not yet exist, this method will instantiate it.
	*
	* @final
	* @see HierarchyManager
	* @return HierarchyManager handle to the HierarchyManager object
	*/
	public function & GetHierarchyManager()
	{
		/* Check to see if a HierarchyManager has been instantiated yet,
		  and, if not, go ahead an create the instance. */
        if( is_null($this->_hrinstance) ) $this->_hrinstance = \CMSMS\internal\global_cache::get('content_tree');
        return $this->_hrinstance;
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
	final public function clear_cached_files($age_days = 0)
	{
        $age_days = max(-1,(int) $age_days);
		global $CMS_LOGIN_PAGE, $CMS_INSTALL_PAGE;
		if( !defined('TMP_CACHE_LOCATION') ) return;
        	$age_days = max(0,(int)$age_days);
        	\CMSMS\HookManager::do_hook('clear_cached_files', [ 'older_than' => $age_days ]);
		$the_time = time() - $age_days * 24*60*60;

		$dirs = array(TMP_CACHE_LOCATION,PUBLIC_CACHE_LOCATION,TMP_TEMPLATES_C_LOCATION);
		foreach( $dirs as $start_dir ) {
			$dirIterator = new RecursiveDirectoryIterator($start_dir);
			$dirContents = new RecursiveIteratorIterator($dirIterator);
			foreach( $dirContents as $one ) {
				if( $one->isFile() && $one->getMTime() <= $the_time ) @unlink($one->getPathname());
			}
            		@touch(cms_join_path($start_dir,'index.html'));
		}
	}


	/**
	 * Get handle to the Smarty parser object, meant for template parsing.
	 *
	 * @internal
	 * @since 1.11.3
	 * @return Smarty_Parser handle to the Smarty object
         * @deprecated
	 */
	final public function &get_template_parser()
	{
		return Smarty_Parser::get_instance();
	}

	/**
	 * Set all known states from global variables.
	 *
	 * @since 1.11.2
	 * @deprecated
	 * @ignore
	 */
	private function set_states()
	{
		if( !isset($this->_states) ) {
			// build the array.
			global $CMS_ADMIN_PAGE;
			global $CMS_INSTALL_PAGE;
			global $CMS_STYLESHEET;

			$this->_states = array();

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
	public function test_state($state)
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
    public function remove_state($state)
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
    public function is_cli()
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
    public function is_frontend_request()
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
    public function is_https_request()
    {
        if( isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' ) return TRUE;
        return FALSE;
    }
}


/**
 * Simple global convenience object to hold CMS Content Type structure.
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
function &cmsms()
{
   return CmsApp::get_instance();
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

?>
