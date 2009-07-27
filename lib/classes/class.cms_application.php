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
	var $siteprefs;

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
	
	/**
	 * content types array - List of available content types
	 */
	var $contenttypes;
	
	static private $instance = NULL;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->cmssystemmodules = 
		  array( 'FileManager','nuSOAP', 'MenuManager', 'ModuleManager', 'Search', 'CMSMailer', 'News', 'TinyMCE', 'Printing', 'ThemeManager' );
		$this->modules = array();
		$this->errors = array();
		$this->nls = array();
		$this->contenttypes = array();
		$this->TemplateCache = array();
		$this->StylesheetCache = array();
		$this->variables['content-type'] = 'text/html';
		$this->variables['modulenum'] = 1;
		$this->variables['routes'] = array();
		
		#Setup hash for storing all modules and plugins
		$this->cmsmodules          = array();
		$this->userplugins         = array();
		$this->userpluginfunctions = array();
		$this->cmsplugins          = array();
		$this->siteprefs           = array();

		register_shutdown_function(array(&$this, 'dbshutdown'));
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
		}
		return self::$instance;
	}

	function & GetDb()
	{
		#static $dbinstance;

		//Check to see if it hasn't been
		//instantiated yet.  If not, connect
		//and return it
		#if (!isset($dbinstance) && !isset($this->db))
		global $DONT_LOAD_DB;
		if (!isset($this->db) && !isset($DONT_LOAD_DB))
		{
			$this->db =& adodb_connect();
		}

		#return $dbinstance;
		$db =& $this->db;
		return ($db);
	}

	function & GetConfig()
	{
        if (!isset($this->config))
		{
			$configinstance = cms_config_load(true);
			$this->config = &$configinstance;
		}

		return $this->config;
	}
	
	function & GetModuleLoader()
	{
        if (!isset($this->moduleloader))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.moduleloader.inc.php'));
			$moduleloader = new ModuleLoader();
			$this->moduleloader = &$moduleloader;
		}

		return $this->moduleloader;
	}
	
	function & GetModuleOperations()
	{
        if (!isset($this->moduleoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.moduleoperations.inc.php'));
			$moduleoperations = new ModuleOperations();
			$this->moduleoperations = &$moduleoperations;
		}

		return $this->moduleoperations;
	}
	
	function & GetUserOperations()
	{
        if (!isset($this->useroperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.useroperations.inc.php'));
			$useroperations = new UserOperations();
			$this->useroperations = &$useroperations;
		}

		return $this->useroperations;
	}
	
	function & GetContentOperations()
	{
        if (!isset($this->contentoperations))
		{
			debug_buffer('', 'Load Content Operations');
			//require_once(cms_join_path(dirname(__FILE__), 'class.contentoperations.inc.php'));
			$contentoperations = new CmsContentOperations();
			$this->contentoperations = &$contentoperations;
			debug_buffer('', 'End Load Content Operations');
		}

		return $this->contentoperations;
	}
	
	function & GetBookmarkOperations()
	{
        if (!isset($this->bookmarkoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.bookmarkoperations.inc.php'));
			$bookmarkoperations = new BookmarkOperations();
			$this->bookmarkoperations = &$bookmarkoperations;
		}

		return $this->bookmarkoperations;
	}
	
	function & GetTemplateOperations()
	{
        if (!isset($this->templateoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.templateoperations.inc.php'));
			$templateoperations = new TemplateOperations();
			$this->templateoperations = &$templateoperations;
		}

		return $this->templateoperations;
	}
	
	function & GetStylesheetOperations()
	{
        if (!isset($this->stylesheetoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.stylesheetoperations.inc.php'));
			$stylesheetoperations = new StylesheetOperations();
			$this->stylesheetoperations = &$stylesheetoperations;
		}

		return $this->stylesheetoperations;
	}
	
	function & GetGroupOperations()
	{
        if (!isset($this->groupoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.groupoperations.inc.php'));
			$groupoperations = new GroupOperations();
			$this->groupoperations = &$groupoperations;
		}

		return $this->groupoperations;
	}
	
	function & GetGlobalContentOperations()
	{
        if (!isset($this->globalcontentoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.globalcontentoperations.inc.php'));
			$globalcontentoperations = new GlobalContentOperations();
			$this->globalcontentoperations = &$globalcontentoperations;
		}

		return $this->globalcontentoperations;
	}
	
	function & GetUserTagOperations()
	{
        if (!isset($this->usertagoperations))
		{
			require_once(cms_join_path(dirname(__FILE__), 'class.usertagoperations.inc.php'));
			$usertagoperations = new UserTagOperations();
			$this->usertagoperations = &$usertagoperations;
		}

		return $this->usertagoperations;
	}

	function & GetSmarty()
	{
		//Check to see if it hasn't been
		//instantiated yet.  If not, connect
		//and return it
		if (!isset($this->smarty))
		{
			$conf =& $this->GetConfig();

			if (!defined('SMARTY_DIR'))
			{
				define('SMARTY_DIR', cms_join_path($dirname,'lib','smarty') . DIRECTORY_SEPARATOR);
			}

			#Setup global smarty object
			$this->smarty = new Smarty_CMS($conf);
		}

        return $this->smarty;
	}

	function & GetHierarchyManager()
	{
		//Check to see if it hasn't been
		//instantiated yet.  If not, connect
		//and return it
        if (!isset($this->hrinstance))
		{
			debug_buffer('', 'Start Loading Hierarchy Manager');
			#require_once(dirname(__FILE__).'/class.contenthierarchymanager.inc.php');

			#Setup global smarty object
			$contentops =& $this->GetContentOperations();
			$this->hrinstance =& $contentops->GetAllContentAsHierarchy(false, array());
			debug_buffer('', 'End Loading Hierarchy Manager');
		}

        return $this->hrinstance;
	}

	function dbshutdown()
	{
		if (isset($this->db))
		{
			$db =& $this->db;
			if ($db->IsConnected())
				$db->Close();
		}
	}
}

class CmsRoute
{
	var $module;
	var $regex;
	var $defaults;
}

class CmsContentTypePlaceholder
{
	var $type;
	var $filename;
	var $friendlyname;
	var $loaded;
}

# vim:ts=4 sw=4 noet
?>
