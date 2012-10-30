<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * Global class for easy access to all important variables.
 *
 * @package CMS
 */

require_once(dirname(__FILE__).'/class.cms_variables.php');

/**
 * Simple global object to hold references to other objects
 *
 * Global object that holds references to various data structures
 * needed by classes/functions in CMS.  Initialized in include.php
 * as $gCms for use in every page.
 *
 * This class was named CmsObject before version 1.10
 *
 * @package CMS
 * @since 0.5
 */
final class CmsApp {

	const STATE_ADMIN_PAGE = 'admin_request';
	const STATE_INSTALL    = 'install_request';
	const STATE_STYLESHEET = 'stylesheet_request';
    const STATE_PARSE_TEMPLATE = 'parse_page_template';

	private static $_instance;

	/**
	 * List of currrent states.
	 * @ignore
	 */
	private $_states;
	private static $_statelist = array(self::STATE_ADMIN_PAGE,self::STATE_STYLESHEET,
									   self::STATE_INSTALL,self::STATE_PARSE_TEMPLATE);

	/**
	 * Database object - adodb reference to the current database
	 * @ignore
	 */
	private $db;

	/**
	 * Internal error array - So functions/modules can store up debug info and spit it all out at once
	 * @ignore
	 */
	var $errors = array();

	/**
	 * content types array - List of available content types
	 *
	 * @internal
	 * @ignore
	 */
	private $contenttypes = array();


	public function __get($key)
	{
		switch($key)
			{
			case 'config':
				return cms_config::get_instance();
				break;
			case 'variables':
				return cms_variables::get_instance();
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
		if( !self::$_instance  )
		{
			self::$_instance = new CmsApp();
		}
		return self::$_instance;
	}


	/**
	 * Retrieve the list of errors
	 *
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
	 * Retrieve the value of an internal variable.
	 *
	 * @internal
	 * @access private
	 * @since 1.9
	 * @param string The variable name to get
	 * @return mixed The value of the internal variable, or null.
	 */
	public function get_variable($key)
	{
		if( $key != '' && isset($this->variables[$key]) )
		{
			return $this->variables[$key];
		}
	}

	
	/**
	 * Set the value of an internal variable
	 *
	 * @internal
	 * @access private
	 * @since 1.9
	 * @param string The variable name to set
	 * @param mixed  The value
	 */
	public function set_variable($key,$value)
	{
		$this->variables[$key] = $value;
	}


	/**
	 * Get a list of all installed and available modules
	 *
	 * This method will return an array of module names that are installed, loaded and ready for use.
	 * suotable for iteration with GetModuleInstance
	 *
	 * @see CmsApp::GetModuleInstance()
	 * @since 1.9
	 * @return array
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
	 * @param string Module Name.
	 * @param string (optional) version number for a check.  
	 * @return object Reference to the module object, or null.
	 * @deprecated
	 */
	public function & GetModuleInstance($module_name,$version = '')
	{
		return ModuleOperations::get_instance()->get_module_instance($module_name,$version);
	}


	/**
	* Get a handle to the ADODB database object. You can then use this
	* to perform all kinds of database operations.
	*
	* @link http://phplens.com/lens/adodb/docs-adodb.htm
	* @final
	* @return ADOConnection a handle to the ADODB database object
	*/
	public function & GetDb()
	{
		global $DONT_LOAD_DB;
		/* Check to see if we have a valid instance.
		 * If not, build the connection */
		if (!isset($this->db) && (!isset($DONT_LOAD_DB) || $DONT_LOAD_DB == 'force'))
		{
			$this->db = adodb_connect();
		}
		
		return $this->db;
	}

	/**
	* Get a handle to the global CMS config. This is mostly values that are
	* defined in config.php
	*
	* @final
	* @return mixed an associative array of configuration values
	*/
	public function &GetConfig()
	{
		return cms_config::get_instance();
	}

	
	/**
	* Get a handle to the CMS ModuleOperations object. If it does not yet
	* exist, this method will instantiate it.
	*
	* @final
	* @see ModuleOperations
	* @return ModuleOperations handle to the ModuleOperations object
	*/
	public function & GetModuleOperations()
	{
		return ModuleOperations::get_instance();
	}

	
	/**
	* Get a handle to the CMS UserOperations object. If it does not yet
	* exist, this method will instantiate it.
	*
	* @final
	* @see UserOperations
	* @return UserOperations handle to the UserOperations object
	*/
	public function & GetUserOperations()
	{
		return UserOperations::get_instance();
	}
	
	/**
	* Get a handle to the CMS ContentOperations object. If it does not yet
	* exist, this method will instantiate it. To disambiguate, this is a globally-available
	* object with methods for dealing with Content -- it should not to be confused with
	* the GlobalContentOperations object.
	*
	* @final
	* @see ContentOperations
	* @return ContentOperations handle to the ContentOperations object
	*/
	public function & GetContentOperations()
	{
		return ContentOperations::get_instance();
	}

	/**
	* Get a handle to the CMS Admin BookmarkOperations object. If it does not yet
	* exist, this method will instantiate it.
	*
	* @final
	* @see BookmarkOperations
	* @return BookmarkOperations handle to the BookmarkOperations object, useful only in the admin
	*/	
	public function & GetBookmarkOperations()
	{
        if (!isset($this->bookmarkoperations))
		{
			$this->bookmarkoperations = new BookmarkOperations();
		}

		return $this->bookmarkoperations;
	}
	
	/**
	* Get a handle to the CMS TemplateOperations object. If it does not yet
	* exist, this method will instantiate it.
	*
	* @final
	* @see TemplateOperations
	* @return TemplateOperations handle to the TemplateOperations object
	*/
	public function & GetTemplateOperations()
	{
		return TemplateOperations::get_instance();
	}


	/**
	* Get a handle to the CMS StylesheetOperations object. If it does not yet
	* exist, this method will instantiate it.
	*
	* @final
	* @see StylesheetOperations
	* @return StylesheetOperations handle to the StylesheetOperations object
	*/	
	public function & GetStylesheetOperations()
	{
		return StylesheetOperations::get_instance();
	}
	
	/**
	* Get a handle to the CMS GroupOperations object. If it does not yet
	* exist, this method will instantiate it.
	*
	* @final
	* @see GroupOperations
	* @return GroupOperations handle to the GroupOperations object
	*/
	public function & GetGroupOperations()
	{
        if (!isset($this->groupoperations))
		{
			$this->groupoperations = new GroupOperations();
		}

		return $this->groupoperations;
	}
	
	/**
	* Get a handle to the CMS GlobalContentOperations object. If it does not yet
	* exist, this method will instantiate it. To disambiguate, this object has methods
	* for dealing with "Global Content Blocks," not to be confused with the ContentOperations
	* object available on a global basis from this class.
	*
	* @final
	* @see GlobalContentOperations
	* @return GlobalContentOperations handle to the GlobalContentOperations object
	*/
	public function & GetGlobalContentOperations()
	{
		return GlobalContentOperations::get_instance();
	}

	
	/**
	* Get a handle to the CMS UserTagOperations object. If it does not yet
	* exist, this method will instantiate it.
	*
	* @final
	* @see UserTagOperations
	* @return UserTagOperations handle to the UserTagOperations object
	*/
	public function & GetUserTagOperations()
	{
		return UserTagOperations::get_instance();
	}

	/**
	* Get a handle to the CMS Smarty object. If it does not yet
	* exist, this method will instantiate it. As of version 1.8,
	* CMS Made Simple uses version 2.6.25 of Smarty.
	*
	* @final
	* @see Smarty_CMS
	* @link http://www.smarty.net/manual/en/
	* @return Smarty_CMS handle to the Smarty object
	*/
	public function & GetSmarty()
	{	
		return Smarty_CMS::get_instance();	
	}

	/**
	* Get a handle to the CMS HierarchyManager object. If it does not yet
	* exist, this method will instantiate it.
	*
	* @final
	* @see HierarchyManager
	* @return HierarchyManager handle to the HierarchyManager object
	*/
	public function & GetHierarchyManager()
	{
		/* Check to see if a HierarchyManager has been instantiated yet,
		  and, if not, go ahead an create the instance. */
        if (!isset($this->hrinstance))
		{
			debug_buffer('', 'Start Loading Hierarchy Manager');
			$contentops =& $this->GetContentOperations();
			$this->hrinstance = $contentops->GetAllContentAsHierarchy(false);
			debug_buffer('', 'End Loading Hierarchy Manager');
		}

        return $this->hrinstance;
	}

	/**
	* Disconnect from the database.
	*
	* @final
	* @internal
	* @access private
	*/
	public function dbshutdown()
	{
		if (isset($this->db))
		{
			$db = $this->db;
			if ($db->IsConnected())
				$db->Close();
		}
	}


	/**
	* Clear out cached files from the CMS tmp/cache and tmp/templates_c
	* directories.
	*
	* @final
    * @internal
    * @ignore
	* @access private
	*/
	final public function clear_cached_files($age_days = -100)
	{
	  $the_time = time() - $age_days * 24*60*60;

	  $dirs = array(TMP_CACHE_LOCATION,TMP_TEMPLATES_C_LOCATION);
	  foreach( $dirs as $start_dir )
	    {
	      $dirIterator = new RecursiveDirectoryIterator($start_dir);
 	      $dirContents = new RecursiveIteratorIterator($dirIterator);
	      foreach( $dirContents as $one )
			  {
				  if( $one->isFile() && $one->getMTime() <= $the_time )
					  {
						  @unlink($one->getPathname());
					  }
			  }
	    }

		@touch(cms_join_path(TMP_CACHE_LOCATION,'index.html'));
		@touch(cms_join_path(TMP_TEMPLATES_C_LOCATION,'index.html'));
	}

	/**
	 * Get handle to Smarty parser object, ment for template parsing
	 *
	 * @final
	 * @internal
	 * @since 1.11.3
	 * @author Tapio Löytty
	 * @return Smarty_Parser handle to the Smarty object	 
	 * @ignore
	 */
	final public function & get_template_parser()
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
			
			if( isset($CMS_ADMIN_PAGE) ) 
				$this->_states[] = self::STATE_ADMIN_PAGE;
						
			if( isset($CMS_INSTALL_PAGE) ) 
				$this->_states[] = self::STATE_INSTALL;
			
			if( isset($CMS_STYLESHEET) )
				$this->_states[] = self::STATE_STYLESHEET;
			
		}
	}

    /** 
	 * Test if the current application state matches the requested value.

	 * This method will throw an exception if invalid data is passed in.
	 *
	 * @since 1.11.2
	 * @author Robert Campbell
	 * @param string A valid state name (see the state list above).  It is recommended that the class constants be used.
	 * @return boolean
	 */
	public function test_state($state)
	{
		if( !in_array($state,self::$_statelist) ) {
			throw new CmsInvalidDataException($state.' is an invalid CMSMS state');
		}
		$this->set_states();
		if( is_array($this->_states) && in_array($state,$this->_states) ) return TRUE;
		return FALSE;
	}

    /**
	 * Get a list of all current states.
	 *
	 * @since 1.11.2
	 * @author Robert Campbell
	 * @return Array of state strings, or null.
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
	 * @return void
	 */
    public function add_state($state)
    {
		if( !in_array($state,self::$_statelist) ) {
			throw new CmsInvalidDataException($state.' is an invalid CMSMS state');
		}
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
	 * @return void
	 */
    public function remove_state($state)
    {
		if( !in_array($state,self::$_statelist) ) {
			throw new CmsInvalidDataException($state.' is an invalid CMSMS state');
		}
		$this->set_states();
		if( !is_array($this->_states) || !in_array($state,$this->_states) ) {
			$key = array_search($state,$this->_states);
			if( $key !== FALSE ) unset($this->_states[$key]);
			return TRUE;
		}
		return FALSE;
    }

    /**
	 * A convenience method to test if the current request is a frontend request.
	 *
	 * @since 1.11.2
	 * @author Robert Campbell
	 * @return boolean
	 */
    public function is_frontend_request()
    {
		$tmp = $this->get_states();
		if( !is_array($tmp) || count($tmp) == 0 ) {
			return TRUE;
		}
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
	var $type;
	var $filename;
	var $friendlyname;
	var $loaded;
}

# vim:ts=4 sw=4 noet
?>
