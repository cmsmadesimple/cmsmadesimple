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
class CmsObject {

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
     * content cache array - If something's called GetAllContent, we keep a copy around
	 */
	var $ContentCache;

	/**
     * content id cache array - If something's called LoadContentByID, we keep a copy around
	 */
	var $ContentIdCache;

	/**
     * template cache array - If something's called LoadTemplateByID, we keep a copy around
	 */
	var $TemplateCache;

	/**
     * html blob cache array - If something's called LoadHtmlBlobByID, we keep a copy around
	 */
	var $HtmlBlobCache;

	/**
	 * Constructor
	 */
	function CmsObject()
	{
		$this->modules = array();
		$this->errors = array();
		$this->nls = array();
		$this->TemplateCache = array();
		$this->variables['content-type'] = 'text/html';
		$this->variables['modulenum'] = 1;
	}

	function & GetDb()
	{
        static $dbinstance;

		//Check to see if it hasn't been
		//instantiated yet.  If not, connect
		//and return it
        if (is_null($dbinstance))
		{
			$config =& $this->GetConfig();
			$dbinstance = &ADONewConnection($config['dbms'], 'pear:date');
			if (isset($config['persistent_db_conn']) && $config['persistent_db_conn'] == true)
			{
				$dbinstance->PConnect($config["db_hostname"],$config["db_username"],$config["db_password"],$config["db_name"]);
			}
			else
			{
				$dbinstance->Connect($config["db_hostname"],$config["db_username"],$config["db_password"],$config["db_name"]);
			}
			if (!$dbinstance) die("Connection failed");
			$dbinstance->SetFetchMode(ADODB_FETCH_ASSOC);
			$this->db = &$dbinstance;
		}

        return $dbinstance;
	}

	function & GetConfig()
	{
		static $configinstance;

        if (is_null($configinstance))
		{
			$configinstance = cms_config_load(true);
			$this->config = &$configinstance;
		}

		return $configinstance;
	}

	function & GetSmarty()
	{
        static $smartyinstance;

		//Check to see if it hasn't been
		//instantiated yet.  If not, connect
		//and return it
        if (is_null($smartyinstance))
		{
			$conf =& $this->GetConfig();
			if (!defined('SMARTY_DIR'))
			{
				define('SMARTY_DIR', dirname(__FILE__).'/lib/smarty/');
			}

			#Setup global smarty object
			$smartyinstance = new Smarty_CMS($conf);
			$this->smarty = &$smartyinstance;
		}

        return $smartyinstance;
	}

	function & GetHierarchyManager()
	{
		require_once(dirname(__FILE__).'/class.contenthierarchymanager.inc.php');

        static $hrinstance;

		//Check to see if it hasn't been
		//instantiated yet.  If not, connect
		//and return it
        if (is_null($hrinstance))
		{
			#Setup global smarty object
			$hrinstance = new ContentHierarchyManager();
		}

        return $hrinstance;
	}
}

# vim:ts=4 sw=4 noet
?>
