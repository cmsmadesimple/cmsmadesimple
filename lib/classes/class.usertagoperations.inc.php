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
	private static $_instance;
	private $_cache = array();

	protected function __construct() {}

	public static function &get_instance()
	{
		if( !isset(self::$_instance) )
		{
			self::$_instance = new UserTagOperations();
		}
		return self::$_instance;
	}


	public function __call($name,$arguments)
	{
		$this->LoadUserTags();
		if( !isset($this->_cache[$name]) ) return;

		// it's a UDT alright
		$this->CallUserTag($name,$arguments);
	}

	/*
	 * Load all the information about user tags
	 */
	public function LoadUserTags()
	{
		if( count($this->_cache) == 0 )
		{
			$db = cmsms()->GetDb();
				
			$query = 'SELECT * FROM '.cms_db_prefix().'userplugins'.' ORDER BY userplugin_name';
			$data = $db->GetArray($query);
			if( is_array($data) )
			{
				foreach( $data as $row )
				{
					$this->_cache[$row['userplugin_name']] = $row;
				}
			}
		}
	}

	
	/**
	 * Get a user tag record (by name) from the cache 
	 */
	private function _get_from_cache($name)
	{
		$this->LoadUserTags();
		if( isset($this->_cache[$name]) )
		{
			return $this->_cache[$name];
		}
		foreach( $this->_cache as $tagname => $row )
		{
			if( $name == $row['userplugin_id'] ) return $row;
		}
	}

	/**
	 * Retrieve the body of a user defined tag
	 *
	 * @param string $name User defined tag name
	 *
	 * @return mixed If successfull, the body of the user tag (string).  If it fails, false
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
	 *
	 * @return mixed If successfull the name of the user defined tag.  false otherwise
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
	 */
	function SmartyTagExists($name,$check_functions = true)
	{
		// get the list of smarty plugins that are known.
		$config = cmsms()->GetConfig();
		$phpfiles = glob($config['root_path'].'/plugins/function.*.php');
		if( is_array($phpfiles) && count($phpfiles) )
		{
			for( $i = 0; $i < count($phpfiles); $i++ )
			{
				$fn = basename($phpfiles[$i]);
				$parts = explode('.',$fn);
				if( count($parts) < 3 ) continue;
				$middle = array_slice($parts,1,count($parts)-2);
				$middle = implode('.',$middle);
				if( $name == $middle ) return TRUE;
			}
		}
		
		if( $check_functions )
		{
			// registered by something else... maybe a module.
			$smarty = cmsms()->GetSmarty();
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
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 */
	function SetUserTag( $name, $text )
	{
		$db = cmsms()->GetDb();

		$existing = $this->GetUserTag($name);
		if (!$existing)
		{
			$this->_cache = array(); // reset the cache.
			$new_usertag_id = $db->GenID(cms_db_prefix()."userplugins_seq");
			$query = "INSERT INTO ".cms_db_prefix()."userplugins (userplugin_id, userplugin_name, code, create_date, modified_date) VALUES (?,?,?,".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
			$result = $db->Execute($query, array($new_usertag_id, $name, $text));
			if ($result)
				return true;
			else
				return false;
		}
		else
		{
			$this->_cache = array(); // reset the cache.
			$query = 'UPDATE '.cms_db_prefix().'userplugins SET code = ?, modified_date = '.$db->DBTimeStamp(time()).' WHERE userplugin_name = ?';
			$result = $db->Execute($query, array($text, $name));
			if ($result)
				return true;
			else
				return false;
		}
	}


	/**
	 * Remove a named user defined tag from the database
	 *
	 * @param string $name User defined tag name
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 */
	function RemoveUserTag( $name )
	{
		$gCms = cmsms();
		$db = $gCms->GetDb();
		
		$query = 'DELETE FROM '.cms_db_prefix().'userplugins WHERE userplugin_name = ?';
		$result = &$db->Execute($query, array($name));

		$this->_cache = array();
		if ($result)
		{
			return true;
		}
		
		return false;
	}


 	/**
	 * Return a list (suitable for use in a pulldown) of user tags.
	 *
	 * @return mixed If successful, an array.  If it fails, false.
	 */
	function ListUserTags()
	{
		$this->LoadUserTags();
		$plugins = array();
		foreach( $this->_cache as $key => $row )
		{
			$plugins[$row['userplugin_id']] = $row['userplugin_name'];
		}
		asort($plugins);
		return $plugins;
	}
	

	function CallUserTag($name, &$params)
	{
		$row = $this->_get_from_cache($name);
		$result = FALSE;
		if( $row )
		{
			$smarty = cmsms()->GetSmarty();
			$functionname = $this->CreateTagFunction($name);
			$result = call_user_func_array($functionname, array(&$params, &$smarty));
		}
		return $result;
	}


	function CreateTagFunction($name)
	{
		$row = $this->_get_from_cache($name);
		if( !$row ) return;
		$functionname = 'cms_user_tag_'.$name;
		if( !function_exists($functionname) )
		{
			$code = 'function '.$functionname.'($params,&$smarty) {'.$row['code']."\n}";
			@eval($code);
		}
		return $functionname;
	}

} // class

/**
 * @ignore
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
//class_alias('UserTagOperations','UserTags');

# vim:ts=4 sw=4 noet
?>
