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
class CmsApplication extends CmsObject {

	/**
	 * Config object - hash containing variables from config.php
	 */
	#var $config;

	/**
	 * Database object - adodb reference to the current database
	 */
	//var $db;

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
	static private $siteprefs = array();

	/**
	 * User Preferences object - holds user preferences as they're loaded so they're only loaded once
	 */
	var $userprefs;

	/**
	 * Smarty object - holds reference to the current smarty object -- will not be set in the admin
	 */
	//public $smarty = NULL;

	/**
	 * Internal error array - So functions/modules can store up debug info and spit it all out at once
	 */
	var $errors;

	/**
     * nls array - This holds all of the nls information for different languages
	 */
	//var $nls;

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
	
	/**
	 * block types array - List of available block types
	 */
	var $blocktypes;
	
	var $ormclasses;
	
	//var $globalcontentoperations;
	
	var $bookmarkoperations;
	
	//var $templateoperations;
	
	//var $contentoperations;
	
	var $usertagoperations;
	
	//var $useroperations;
	
	var $groupoperations;
	
	var $moduleoperations;
	
	var $hrinstance;
	
	var $params = array();
	
	//var $desccache = array();
	
	var $modules;
	
	var $StylesheeteCache;
	
	var $userpluginfunctions;
	
	var $orm;
	
	static private $instance = NULL;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->cmssystemmodules = array( 'nuSOAP', 'MenuManager', 'ModuleManager' );
		$this->modules = array();
		$this->errors = array();
		$this->nls = array();
		$this->contenttypes = array();
		$this->TemplateCache = array();
		$this->StylesheetCache = array();
		$this->variables['content-type'] = 'text/html';
		$this->variables['modulenum'] = 1;
		$this->variables['routes'] = array();
		$this->variables['pluginnum'] = 1;
		
		#Setup hash for storing all modules and plugins
		$this->cmsmodules          = array();
		$this->userplugins         = array();
		$this->userpluginfunctions = array();
		$this->cmsplugins          = array();
		$this->siteprefs           = array();
		$this->orm                 = array();
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

	/**
	 * @deprecated Deprecated.  Use cms_db() instead.
	 **/
	function get_db()
	{
		return CmsDatabase::get_instance();
	}
	
	/**
	 * @deprecated Deprecated.  Use cms_db() instead.
	 **/
	function GetDb()
	{
		return CmsDatabase::get_instance();
	}
	
	/**
	 * Getter overload method.  Called when an $obj->field and field
	 * does not exist in the object's variable list.  In this case,
	 * it will get a config, db or smarty instance (for backwards 
	 * compatibility), or call get_orm_class on the given field name.
	 *
	 * @param string The field to look up
	 * @return mixed The value for that field, if it exists
	 * @author Ted Kulp
	 **/
	function __get($name)
	{
		if ($name == 'config')
			return CmsConfig::get_instance();
		else if ($name == 'db')
			return CmsDatabase::get_instance();
		else if ($name == 'smarty')
			return CmsSmarty::get_instance();
		else
			return $this->get_orm_class($name);
	}
	
	/**
	 * Retrieves an instance of an ORM'd object for doing
	 * find_by_* methods.
	 *
	 * @return mixed The object with the given name, or null if it's not registered
	 * @author Ted Kulp
	 **/
	function get_orm_class($name, $try_prefix = true)
	{
		if (isset($this->orm[$name]))
			return $this->orm[$name];
		elseif (isset($this->orm[underscore($name)]))
			return $this->orm[underscore($name)];
		else
		{
			// Let's try to load the thing dynamically
			$name = camelize($name);
			if (class_exists($name))
			{
				$this->orm[underscore($name)] = new $name;
				return $this->orm[underscore($name)];	
			}
			else
			{
				if ($try_prefix)
				{
					return $this->get_orm_class('cms_' . $name, false);
				}
				else
				{
					var_dump('Class not found! -- ' . $name);
					return null;
				}
			}
		}
	}
	
	/**
	 * @deprecated Deprecated.  Use cms_config() instead.
	 **/
	function get_config()
	{
		return CmsConfig::get_instance();
	}

	/**
	 * @deprecated Deprecated.  Use cms_config() instead.
	 **/
	function GetConfig()
	{
		return CmsConfig::get_instance();
	}
		
	function &GetModuleOperations()
	{
        if (!isset($this->moduleoperations))
		{
			//require_once(cms_join_path(dirname(__FILE__), 'class.moduleoperations.inc.php'));
			$moduleoperations = new CmsModuleOperations();
			$this->moduleoperations = $moduleoperations;
		}

		return $this->moduleoperations;
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsUserOperations::some_method instead.
	 **/
	function GetUserOperations()
	{
        return CmsUserOperations::get_instance();
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::some_method instead.
	 **/
	function GetContentOperations()
	{
		return CmsContentOperations::get_instance();
	}
	
	function &GetBookmarkOperations()
	{
        if (!isset($this->bookmarkoperations))
		{
			//require_once(cms_join_path(dirname(__FILE__), 'class.bookmarkoperations.inc.php'));
			$bookmarkoperations = new BookmarkOperations();
			$this->bookmarkoperations = $bookmarkoperations;
		}

		return $this->bookmarkoperations;
	}
	
	function GetTemplateOperations()
	{
        return CmsTemplateOperations::get_instance();
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsStylesheetOperations::some_method instead.
	 **/
	function GetStylesheetOperations()
	{
		return CmsStylesheetOperations::get_instance();
	}
	
	function &GetGroupOperations()
	{
        if (!isset($this->groupoperations))
		{
			//require_once(cms_join_path(dirname(__FILE__), 'class.groupoperations.inc.php'));
			$groupoperations = new GroupOperations();
			$this->groupoperations = $groupoperations;
		}

		return $this->groupoperations;
	}
	
	function GetGlobalContentOperations()
	{
		return CmsGlobalContentOperations::get_instance();
	}
	
	function &GetUserTagOperations()
	{
        if (!isset($this->usertagoperations))
		{
			//require_once(cms_join_path(dirname(__FILE__), 'class.usertagoperations.inc.php'));
			$usertagoperations = new UserTagOperations();
			$this->usertagoperations = &$usertagoperations;
		}

		return $this->usertagoperations;
	}

	function GetSmarty()
	{
		return CmsSmarty::get_instance();
	}
	
	function get_smarty()
	{
		return CmsSmarty::get_instance();
	}

	function &GetHierarchyManager()
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
	
	/**
	 * Loads a cache of site preferences so we only have to do it once.
	 *
	 * @since 0.6
	 */
	public static function load_site_preferences()
	{
		$db = cms_db();
		
		$result = array();

		$query = "SELECT sitepref_name, sitepref_value from ".cms_db_prefix()."siteprefs";
		$dbresult = &$db->Execute($query);

		while ($dbresult && !$dbresult->EOF)
		{
			$result[$dbresult->fields['sitepref_name']] = $dbresult->fields['sitepref_value'];
			$dbresult->MoveNext();
		}

		if ($dbresult) $dbresult->Close();

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
			self::$siteprefs = CmsCache::get_instance()->call('CmsApplication::load_site_preferences');
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
		$result = $db->Execute($query);

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
	}

	function __destruct()
	{
		//*cough* Hack
		CmsDatabase::close();
	}
}

class CmsContentTypePlaceholder
{
	var $type;
	var $filename;
	var $friendlyname;
	var $loaded;
}

/*
class CmsBlockTypePlaceholder extends CmsContentTypePlaceholder
{
}
*/

# vim:ts=4 sw=4 noet
?>
