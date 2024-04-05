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
#
#$Id: class.bookmark.inc.php 2746 2006-05-09 01:18:15Z wishy $

/**
 * Usertag related functions.
 *
 * @package CMS
 * @license GPL
 */

/**
 * UserTags class for admin
 *
 * @package CMS
 * @license GPL
 */
final class UserTagOperations
{
	/**
	 * @ignore
	 */
	private static $_instance;

	/**
	 * @ignore
	 */
	private $_cache = array();

	/**
	 * @ignore
	 */
	protected function __construct() {}

	/**
	 * Get a reference to thie only allowed instance of this class
	 * @return UserTagOperations
	 */
	public static function &get_instance()
	{
		if( !isset(self::$_instance) ) self::$_instance = new UserTagOperations();
		return self::$_instance;
	}


	/**
	 * @ignore
	 */
	public function __call($name,$arguments)
	{
		$this->LoadUserTags();
		if( !isset($this->_cache[$name]) ) return;

		// it's a UDT alright
		$this->CallUserTag($name,$arguments);
	}

	/**
	 * @ignore
	 * @internal
	 */
	public static function setup()
	{
		$obj = new \CMSMS\internal\global_cachable(__CLASS__,function(){
			$db = CmsApp::get_instance()->GetDb();

			$query = 'SELECT * FROM '.CMS_DB_PREFIX.'userplugins'.' ORDER BY userplugin_name';
			$data = $db->GetArray($query);
			if( is_array($data) ) {
				$out = array();
				foreach( $data as $row ) {
					$out[$row['userplugin_name']] = $row;
				}
				return $out;
			}
		});
		\CMSMS\internal\global_cache::add_cachable($obj);
	}

	/**
	 * Load all the information about user tags
	 */
	public function LoadUserTags()
	{
		$this->_cache = \CMSMS\internal\global_cache::get(__CLASS__);
	}


	/**
	 * Get a user tag record (by name) from the cache
	 * @internal
	 */
	private function _get_from_cache($name)
	{
		$this->LoadUserTags();
		if( isset($this->_cache[$name]) ) return $this->_cache[$name];
		foreach( $this->_cache as $tagname => $row ) {
			if( $name == $row['userplugin_id'] ) return $row;
		}
	}

	/**
	 * Retrieve the body of a user defined tag
	 *
	 * @param string $name User defined tag name
	 * @return string|false
	 */
	function GetUserTag( $name )
	{
		$row = $this->_get_from_cache($name);
		return $row;
	}

	/**
	 * Test if a user defined tag with a specific name exists
	 *
	 * @param string $name User defined tag name
	 * @return string|false
	 * @since 1.10
	 */
	function UserTagExists($name)
	{
		$row = $this->_get_from_cache($name);
		if( is_array($row) ) return $name;
		return false;
	}


	/**
	 * Test if a plugin function by this name exists...
	 *
	 * @param string $name The name of the plugin to test
	 * @param bool   $check_functions Test if already registered to smarty.
	 */
	function SmartyTagExists($name,$check_functions = true)
	{
		// get the list of smarty plugins that are known.
		$config = \cms_config::get_instance();
		$phpfiles = glob(CMS_ROOT_PATH.'/plugins/function.*.php');
		if( is_array($phpfiles) && count($phpfiles) ) {
			for( $i = 0; $i < count($phpfiles); $i++ ) {
				$fn = basename($phpfiles[$i]);
				$parts = explode('.',$fn);
				if( count($parts) < 3 ) continue;
				$middle = array_slice($parts,1,count($parts)-2);
				$middle = implode('.',$middle);
				if( $name == $middle ) return TRUE;
			}
		}

		if( $check_functions ) {
			// registered by something else... maybe a module.
			$smarty = \Smarty_CMS::get_instance();
			if( $smarty->is_registered($name) ) return TRUE;
		}

		if( $this->UserTagExists($name) ) return TRUE;
		return FALSE;
	}

	/**
	 * Add or update a named user defined tag into the database
	 *
	 * @param string $name User defined tag name
	 * @param string $text Body of user defined tag
	 * @param string $description Description for the user defined tag.
	 * @param int    $id ID of existing user tag (for updates).
	 * @return bool
	 */
	function SetUserTag( $name, $text, $description, $id = null )
	{
		$db = CmsApp::get_instance()->GetDb();

		$existing = false;
		if( $id > 0 ) {
			// make sure we can find it.
			$usertag = $this->_get_from_cache( $id );
			if( !$usertag ) return false;
			$existing = true;
		}
		if (!$existing) {
			$this->_cache = array(); // reset the cache.
			$new_usertag_id = $db->GenID(CMS_DB_PREFIX."userplugins_seq");
			$query = "INSERT INTO ".CMS_DB_PREFIX."userplugins (userplugin_id, userplugin_name, code, description, create_date, modified_date) VALUES (?,?,?,?,".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
			$result = $db->Execute($query, array($new_usertag_id, $name, $text, $description));
			if ($result) {
				\CMSMS\internal\global_cache::clear(__CLASS__);
				return true;
			}
			return false;
		}
		else {
			$this->_cache = array(); // reset the cache.
			$query = 'UPDATE '.CMS_DB_PREFIX.'userplugins SET code = ?, userplugin_name = ?';
			$parms = array($text, $name);
			if( $description ) {
				$query .= ', description = ?';
				$parms[] = $description;
			}
			$query .= ', modified_date = NOW() WHERE userplugin_id = ?';
			$parms[] = $id;
			$result = $db->Execute($query, $parms);
			if ($result) {
				\CMSMS\internal\global_cache::clear(__CLASS__);
				return true;
			}

			return false;
		}
	}


	/**
	 * Remove a named user defined tag from the database
	 *
	 * @param string $name User defined tag name
	 * @return bool
	 */
	function RemoveUserTag( $name )
	{
		$gCms = CmsApp::get_instance();
		$db = $gCms->GetDb();

		$query = 'DELETE FROM '.CMS_DB_PREFIX.'userplugins WHERE userplugin_name = ?';
		$result = &$db->Execute($query, array($name));

		$this->_cache = array();
		if ($result) {
			\CMSMS\internal\global_cache::clear(__CLASS__);
			return true;
		}

		return false;
	}


 	/**
	 * Return a list (suitable for use in a pulldown) of user tags.
	 *
	 * @return array|false
	 */
	function ListUserTags()
	{
		$this->LoadUserTags();
		if( !$this->_cache || !count( $this->_cache  ) ) return;
		$plugins = array();
		foreach( $this->_cache as $key => $row ) {
			$plugins[$row['userplugin_id']] = $row['userplugin_name'];
		}
		asort($plugins);
		return $plugins;
	}


	/**
	 * Execute a user defined tag
	 *
	 * @param string $name The name of the user defined tag
	 * @param array  $params Optional parameters.
	 * @return mixed|false The returned data from the user defined tag, or FALSE if the UDT could not be found.
	 */
	function CallUserTag($name, &$params)
	{
		$row = $this->_get_from_cache($name);
		$result = FALSE;
		if( $row ) {
			$smarty = \Smarty_CMS::get_instance();
			$functionname = $this->CreateTagFunction($name);
			$result = call_user_func_array($functionname, array(&$params, $smarty));
		}
		return $result;
	}

	/**
	 * Given a UDT name create an executable function from it
	 *
	 * @internal
	 * @param string $name The name of the user defined tag to operate with.
	 */
	function CreateTagFunction($name)
	{
		$row = $this->_get_from_cache($name);
		
		if(!$row) { return ''; }
		
		$functionname = 'cms_user_tag_' . $name;
		
		if(!function_exists($functionname))
		{
			if(startswith($row['code'], '<?php'))
			{
				$row['code'] = substr($row['code'], 5);
			}
			
			if(endswith($row['code'], '?>'))
			{
				$row['code'] = substr($row['code'], 0, -2);
			}
			
			$code = 'function ' . $functionname . '($params,$smarty) {' . $row['code'] . "\n}";
			
			try
			{
				eval($code);
			}
			catch(ParseError $e)
			{
				$functionname = '';
			}
		
		}
		
		return $functionname;
	}

} // class

/**
 * @ignore
 * @package CMS
 * @license GPL
 */
//class_alias('UserTagOperations','UserTags');

?>
