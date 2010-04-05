<?php
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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
 * Global class for easy access to all important variables.
 *
 * @package CMS
 */

/**
 * Simple global object to hold references to other objects
 *
 * Global object that holds references to various data sctructures
 * needed by classes/functions in CMS.  Initialized in include.php
 * as $gCms for use in every page.
 *
 * @since 0.5
 */
class CmsApplication extends CmsObject
{
	/**
	 * Config object - hash containing variables from config.php
	 */
	var $config;

	/**
	 * Database object - adodb reference to the current database
	 */
	var $db;

	/**
	 * Variables object - various objects and strings needing to be passed 
	 */
	var $variables;

	/**
	 * Modules object - holds references to all registered modules
	 */
	var $cmsmodules;

	/**
	 * System Modules - a list (hardcoded) of all system modules
	 */
	var $cmssystemmodules;
	
	var $userpluginfunctions;

	/**
	 * Plugins object - holds list of all registered plugins 
	 */
	var $cmsplugins;

	/**
	 * User Plugins object - holds list and function pointers of all registered user plugins
	 */
	var $userplugins;

	/**
	 * BBCode object - for use in bbcode parsing
	 */
	var $bbcodeparser;

	/**
	 * Site Preferences object - holds all current site preferences so they're only loaded once
	 */
	public static $siteprefs;

	/**
	 * User Preferences object - holds user preferences as they're loaded so they're only loaded once
	 */
	var $userprefs;

	/**
	 * Smarty object - holds reference to the current smarty object -- will not be set in the admin
	 */
	var $smarty;

	/**
	 * Internal error array - So functions/modules can store up debug info and spit it all out at once
	 */
	var $errors;

	/**
     * nls array - This holds all of the nls information for different languages
	 */
	var $nls;

	/**
     * template cache array - If something's called LoadTemplateByID, we keep a copy around
	 */
	var $TemplateCache;

	/**
     * template cache array - If something's called LoadTemplateByID, we keep a copy around
	 */
	var $StylesheetCache;

	/**
     * html blob cache array - If something's called LoadHtmlBlobByID, we keep a copy around
	 */
	var $HtmlBlobCache;
	
	static private $instance = NULL;

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		
		CmsEventManager::send_event('Core:startup');
		
		$this->cmssystemmodules = 
		  array( 'FileManager','nuSOAP', 'MenuManager', 'ModuleManager', 'Search', 'CMSMailer', 'News', 'MicroTiny', 'SimplePrinting', 'ThemeManager' );
		$this->modules = array();
		$this->errors = array();
		$this->nls = array();
		$this->TemplateCache = array();
		$this->StylesheetCache = array();
		$this->variables['content-type'] = 'text/html';
		$this->variables['modulenum'] = 1;
		$this->variables['routes'] = array();
		
		#Setup hash for storing all modules and plugins
		$this->userplugins         = array();
		$this->userpluginfunctions = array();
		$this->cmsplugins          = array();
		self::$siteprefs           = array();
		
		//Config
		$this->config = CmsConfig::get_instance();
		
		//So our shutdown events are called right near the end of the page
		register_shutdown_function(array(&$this, 'shutdown'));
		
		//Load database events when the database gets going
		CmsEventManager::register_event_handler('Core:database_opened', array('CmsDatabaseEventManager', 'load_events_into_manager'));
	}
	
	function shutdown()
	{
		CmsEventManager::send_event('Core:shutdown_soon');
		CmsEventManager::send_event('Core:shutdown_now');
	}
	
	/**
	 * Returns an instnace of the CmsApplication singleton.  Most 
	 * people can generally use cmsms() instead of this, but they 
	 * both do the same thing.
	 *
	 * @return CmsApplication The singleton CmsApplication instance
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsApplication();
			self::$instance->setup();
		}
		return self::$instance;
	}
	
	public function setup()
	{
		//Force setup
		CmsRequest::get_instance();
		CmsResponse::get_instance();
		
		//Setup the session
		CmsSession::setup();

		if (isset($starttime))
		{
		    cmsms()->starttime = $starttime;
		}

		#Load the config file (or defaults if it doesn't exist)
		//require(cms_join_path(ROOT_DIR,'version.php'));
		//require(cms_join_path(ROOT_DIR, 'lib', 'config.functions.php'));

		#Grab the current configuration
		$config = cmsms()->GetConfig();
		$GLOBALS['config'] = $config;

		#Attempt to override the php memory limit
		if( isset($config['php_memory_limit']) && !empty($config['php_memory_limit'])  )
		{
			ini_set('memory_limit',trim($config['php_memory_limit']));
		}

		#Hack for changed directory and no way to upgrade config.php
		$config['previews_path'] = str_replace('smarty/cms', 'tmp', $config['previews_path']);

		if ($config["debug"] == true)
		{
			@ini_set('display_errors',1);
			@error_reporting(E_STRICT);
		}

		/*
		debug_buffer('loading smarty');
		require(cms_join_path(ROOT_DIR,'lib','smarty','Smarty.class.php'));
		*/
		CmsProfiler::get_instance()->mark('loading pageinfo functions');
		require_once(cms_join_path(ROOT_DIR,'lib','classes','class.pageinfo.inc.php'));
		if (! isset($CMS_INSTALL_PAGE))
		{
			CmsProfiler::get_instance()->mark('loading translation functions');
			require_once(cms_join_path(ROOT_DIR,'lib','translation.functions.php'));
		}
		/*
		debug_buffer('loading events functions');
		require_once(cms_join_path(ROOT_DIR,'lib','classes','class.events.inc.php'));
		debug_buffer('loading php4 entity decode functions');
		require_once(ROOT_DIR.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'html_entity_decode_php4.php');
		*/

		CmsProfiler::get_instance()->mark('done loading files');

		#Load them into the usual variables.  This'll go away a little later on.
		global $DONT_LOAD_DB;
		if (!isset($DONT_LOAD_DB))
		{
		    $cmsdb = cms_db();
		}

		$smarty = cms_smarty();
		$GLOBALS['smarty'] = cms_smarty();

		#Setup the object sent to modules
		cmsms()->pluginnum = 1;

		#Set a umask
		$global_umask = CmsApplication::get_preference('global_umask','');
		if( $global_umask != '' )
		{
			@umask( octdec($global_umask) );
		}

		#Set the locale if it's set
		#either in the config, or as a site preference.
		$frontendlang = CmsApplication::get_preference('frontendlang','');
		if (isset($config['locale']) && $config['locale'] != '')
		{
			$frontendlang = $config['locale'];
		}
		if ($frontendlang != '')
		{
			@setlocale(LC_ALL, $frontendlang);
		}
		if (isset($config['timezone']) && ! empty($config['timezone']) && function_exists('date_default_timezone_set'))
		{
			date_default_timezone_set($config['timezone']);
		}

		$smarty->assign('sitename', CmsApplication::get_preference('sitename', 'CMSMS Site'));
		$smarty->assign('lang', $frontendlang);
		$smarty->assign('encoding', get_encoding());
		$gCms = cmsms();
		$smarty->assign_by_ref('gCms', $gCms);

		if ($config['debug'] == true)
		{
			$smarty->debugging = true;
			$smarty->error_reporting = 'E_ALL';
		}

		if (isset($CMS_ADMIN_PAGE) || isset($CMS_STYLESHEET))
		{
		    include_once(cms_join_path(ROOT_DIR,$config['admin_dir'],'lang.php'));
		}

		CmsProfiler::get_instance()->mark('Before module loader');

		CmsModuleLoader::load_module_data();
		#Load all installed module code
		//$modload = cmsms()->GetModuleLoader();
		//$modload->LoadModules(isset($LOAD_ALL_MODULES), !isset($CMS_ADMIN_PAGE));
	}
	
	public function get($name)
	{
		if (!isset($this->variables[$name])) {
			//throw new InvalidArgumentException("Cannot get($name), $name is not set.");
			return null;
		}
		return $this->variables[$name];
	}

	public function set($name, $value)
	{
		$this->variables[$name] = $value;
	}

    /**
     * Getter overload method.  Called when an $obj->field and field
     * does not exist in the object's variable list.  In this case,
     * it will get a db or smarty instance (for backwards 
     * compatibility), or call get on the given field name.
     *
     * @param string The field to look up
     * @return mixed The value for that field, if it exists
     * @author Ted Kulp
     **/
	public function __get($name)
	{
		if ($name == 'db')
			return CmsDatabase::get_instance();
		else if ($name == 'smarty')
			return CmsSmarty::get_instance();
		else if ($name == 'cmsmodules')
			return CmsModuleLoader::get_module_list();
		else if ($name == 'request')
			return CmsRequest::get_instance();
		else if ($name == 'response')
			return CmsResponse::get_instance();
		else
			return $this->get($name);
	}
	
    /**
     * Setter overload method.  Called when an $obj->field and field
     * does not exist in the object's variable list.  In this case,
     * it will get a db or smarty instance (for backwards 
     * compatibility), or call get on the given field name.
     *
     * @param string The field to set
     * @param string The value to set it to
     * @author Ted Kulp
     **/
	public function __set($name, $value)
	{
		if ($name != 'db' && $name != 'smarty')
			$this->set($name, $value);
	}
	
	public function __isset($name)
	{
		return isset($this->variables[$name]);
	}

	public function __unset($name)
	{
		unset($this->variables[$name]);
	}
	
	public static function run()
	{
		self::check_install();
		
		$dispatch = CmsDispatcher::get_instance();
		$dispatch->run();
	}
	
	public static function check_install()
	{
		//If we have a missing or empty config file, then we should think
		//about redirecting to the installer.  Also, check to see if the SITEDOWN
		//file is there.  That means we're probably in mid-upgrade.
		$config_location = CmsConfig::get_config_filename();
		if (!file_exists($config_location) || filesize($config_location) < 800)
		{
			if (FALSE == is_file(ROOT_DIR.'/install/index.php'))
			{
				die ('There is no config.php file or install/index.php please correct one these errors!');
			}
			else
			{
				//die('Do a redirect - ' . $config_location);
				CmsResponse::redirect('install/index.php');
			}
		}
		else if (file_exists(TMP_CACHE_LOCATION.'/SITEDOWN'))
		{
			echo "<html><head><title>Maintenance</title></head><body><p>Site down for maintenance.</p></body></html>";
			exit;
		}

		//Ok, one more check.  Make sure we can write to the following locations.  If not, then 
		//we won't even be able to push stuff through smarty.  Error out now while we have the 
		//chance.
		if (!is_writable(TMP_TEMPLATES_C_LOCATION) || !is_writable(TMP_CACHE_LOCATION))
		{
			echo '<html><title>Error</title></head><body>';
			echo '<p>The following directories must be writable by the web server:<br />';
			echo 'tmp/cache<br />';
			echo 'tmp/templates_c<br /></p>';
			echo '<p>Please correct by executing:<br /><em>chmod 777 tmp/cache<br />chmod 777 tmp/templates_c</em><br />or the equivilent for your platform before continuing.</p>';
			echo '</body></html>';
			exit;
		}
	}

	function GetDb()
	{
		return CmsDatabase::get_instance();
	}

	function GetConfig()
	{
        return CmsConfig::get_instance();
	}
	
	function GetModuleLoader()
	{
        if (!isset($this->moduleloader))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.moduleloader.inc.php'));
			$moduleloader = new ModuleLoader();
			$this->moduleloader = $moduleloader;
		}

		return $this->moduleloader;
	}
	
	function GetModuleOperations()
	{
        if (!isset($this->moduleoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.moduleoperations.inc.php'));
			$moduleoperations = new ModuleOperations();
			$this->moduleoperations = $moduleoperations;
		}

		return $this->moduleoperations;
	}
	
	function GetUserOperations()
	{
        if (!isset($this->useroperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.useroperations.inc.php'));
			$useroperations = new UserOperations();
			$this->useroperations = $useroperations;
		}

		return $this->useroperations;
	}
	
	function GetContentOperations()
	{
        if (!isset($this->contentoperations))
		{
			debug_buffer('', 'Load Content Operations');
			//require_once(cms_join_path(dirname(__FILE__), 'class.contentoperations.inc.php'));
			$contentoperations = new CmsContentOperations();
			$this->contentoperations = $contentoperations;
			debug_buffer('', 'End Load Content Operations');
		}

		return $this->contentoperations;
	}
	
	function GetBookmarkOperations()
	{
        if (!isset($this->bookmarkoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.bookmarkoperations.inc.php'));
			$bookmarkoperations = new BookmarkOperations();
			$this->bookmarkoperations = $bookmarkoperations;
		}

		return $this->bookmarkoperations;
	}
	
	function GetTemplateOperations()
	{
        if (!isset($this->templateoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.templateoperations.inc.php'));
			$templateoperations = new TemplateOperations();
			$this->templateoperations = $templateoperations;
		}

		return $this->templateoperations;
	}
	
	function GetStylesheetOperations()
	{
        if (!isset($this->stylesheetoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.stylesheetoperations.inc.php'));
			$stylesheetoperations = new StylesheetOperations();
			$this->stylesheetoperations = $stylesheetoperations;
		}

		return $this->stylesheetoperations;
	}
	
	function GetGroupOperations()
	{
        if (!isset($this->groupoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.groupoperations.inc.php'));
			$groupoperations = new GroupOperations();
			$this->groupoperations = $groupoperations;
		}

		return $this->groupoperations;
	}
	
	function GetGlobalContentOperations()
	{
        if (!isset($this->globalcontentoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.globalcontentoperations.inc.php'));
			$globalcontentoperations = new GlobalContentOperations();
			$this->globalcontentoperations = $globalcontentoperations;
		}

		return $this->globalcontentoperations;
	}
	
	function GetUserTagOperations()
	{
        if (!isset($this->usertagoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.usertagoperations.inc.php'));
			$usertagoperations = new UserTagOperations();
			$this->usertagoperations = $usertagoperations;
		}

		return $this->usertagoperations;
	}
	
	function GetPageInfoOperations()
	{
        if (!isset($this->pageinfooperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.pageinfo.inc.php'));
			$pageinfooperations = new PageInfoOperations();
			$this->pageinfooperations = &$pageinfooperations;
		}

		return $this->pageinfooperations;
	}

	public static function GetSmarty()
	{
		return CmsSmarty::get_instance();
	}

	public static function GetHierarchyManager()
	{
		return CmsPageTree::get_instance();
	}
	
	/**
	 * Loads a cache of site preferences so we only have to do it once.
	 *
	 * @since 0.6
	 */
	public static function load_site_preferences()
	{
		$db = cms_db();
		
		$result = array();
		
		if ($db->IsConnected())
		{
			$query = "SELECT sitepref_name, sitepref_value from {siteprefs}";
			$dbresult = $db->Execute($query);

			while ($dbresult && !$dbresult->EOF)
			{
				$result[$dbresult->fields['sitepref_name']] = $dbresult->fields['sitepref_value'];
				$dbresult->MoveNext();
			}

			if ($dbresult) $dbresult->Close();
		}

		return $result;
	}
	
	/**
	 * Gets the given site prefernce
	 *
	 * @since 0.6
	 */
	public static function get_preference($prefname, $defaultvalue = '')
	{
		$value = $defaultvalue;

		if (count(self::$siteprefs) == 0)
		{
			//self::$siteprefs = CmsCache::get_instance()->call('CmsApplication::load_site_preferences');
			self::$siteprefs = CmsApplication::load_site_preferences();
		}

		if (isset(self::$siteprefs[$prefname]))
		{
			$value = self::$siteprefs[$prefname];
		}

		return $value;
	}

	/**
	 * Removes the given site preference
	 *
	 * @param string Preference name to remove
	 */
	public static function remove_preference($prefname)
	{
		$db = cms_db();

		$query = "DELETE from ".cms_db_prefix()."siteprefs WHERE sitepref_name = ?";
		$result = $db->Execute($query, array($prefname));

		if (isset(self::$siteprefs[$prefname]))
		{
			unset(self::$siteprefs[$prefname]);
		}

		if ($result) $result->Close();
		CmsCache::clear();
	}

	/**
	 * Sets the given site perference with the given value.
	 *
	 * @since 0.6
	 */
	public static function set_preference($prefname, $value)
	{
		$doinsert = true;

		$db = cms_db();

		$query = "SELECT sitepref_value from ".cms_db_prefix()."siteprefs WHERE sitepref_name = ".$db->qstr($prefname);
		$result = $db->CacheExecute($query);

		if ($result && $result->RecordCount() > 0)
		{
			$doinsert = false;
		}

		if ($result) $result->Close();

		if ($doinsert)
		{
			$query = "INSERT INTO ".cms_db_prefix()."siteprefs (sitepref_name, sitepref_value) VALUES (".$db->qstr($prefname).", ".$db->qstr($value).")";
			$db->Execute($query);
		}
		else
		{
			$query = "UPDATE ".cms_db_prefix()."siteprefs SET sitepref_value = ".$db->qstr($value)." WHERE sitepref_name = ".$db->qstr($prefname);
			$db->Execute($query);
		}
		self::$siteprefs[$prefname] = $value;
		CmsCache::clear();
	}
	
	public static function is_sitedown()
	{
		if (self::get_preference('enablesitedownmessage', '0') !== '1')
			return FALSE;
		
		$excludes = self::get_preference('sitedownexcludes','');
		
		if (!isset($_SERVER['REMOTE_ADDR']))
			return TRUE;
		
		if (empty($excludes))
			return TRUE;

		$ret = cms_ipmatches($_SERVER['REMOTE_ADDR'], $excludes);
		
		if ($ret)
			return FALSE;
		
		return TRUE;
	}
}

# vim:ts=4 sw=4 noet
?>
