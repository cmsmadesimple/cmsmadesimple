<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (tedkulp@users.sf.net)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Static methods for handling user defined tags.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsUserTagOperations extends CmsObject
{
	static private $instance = NULL;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get an instance of this object, though most people should be using
	 * the static methods instead.  This is more for compatibility than
	 * anything else.
	 *
	 * @return CmsUserTagOperations The instance of the singleton object.
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsUserTagOperations();
		}
		return self::$instance;
	}
	
	/**
	 * Retrieve the body of a user defined tag
	 *
	 * @params string $name User defined tag name
	 *
	 * @returns mixed If successfull, the body of the user tag (string).  If it fails, false
	 */
	function get_user_tag($name)
	{
		$user_tag = cms_orm('CmsUserTag')->find_by_name($name);
		if ($user_tag != null)
		{
			return $user_tag->code;
		}

		return false;
	}

	function GetUserTag( $name )
	{
		return self::get_user_tag($name);
	}


	/**
	 * Add or update a named user defined tag into the database
	 *
	 * @params string $name User defined tag name
	 * @params string $text Body of user defined tag
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function set_user_tag( $name, $text )
	{
		$user_tag = cms_orm('CmsUserTag')->find_by_name($name);
		if ($user_tag == null)
		{
			$user_tag = new CmsUserTag();
			$user_tag->name = $name;
		}
		
		$user_tag->code = $text;
		
		return $user_tag->save();
	}
	
	function SetUserTag( $name, $text )
	{
		return self::set_user_tag($name, $text);
	}


	/**
	 * Remove a named user defined tag from the database
	 *
	 * @params string $name User defined tag name
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function remove_user_tag( $name )
	{
		$user_tag = cms_orm('CmsUserTag')->find_by_name($name);
		if ($user_tag != null)
		{
			return $user_tag->delete();
		}
		
		return false;
	}
	
	function RemoveUserTag( $name )
	{
		return self::remove_user_tag($name);
	}


 	/**
	 * Return a list (suitable for use in a pulldown) of user tags.
	 *
	 * @returns mixed If successful, an array.  If it fails, false.
	 */
	function list_user_tags_for_dropdown()
	{
		cms_orm('CmsUserTag')->find_all(array('order' => 'name asc'));
	}
	
	function ListUserTags()
	{
		return self::list_user_tags_for_dropdown();
	}
	
	function call_user_tag($name, &$params)
	{
		$smarty = cms_smarty();
		
		$code = self::GetUserTag($name);
		
		$result = FALSE;
		
		$functionname = "tmpcallusertag_".$name."_userplugin_function";
		
		if (function_exists($functionname) || !(@eval('function '.$functionname.'(&$params, &$smarty) {'.$code.'}') === FALSE))
		{
			$result = call_user_func_array($functionname, array(&$params, &$smarty));
		}
		
		return $result;
	}
	
	function CallUserTag($name, &$params)
	{
		return self::call_user_tag($name, $params);
	}

} // class

/**
 * @deprecated Deprecated.  Use CmsUserTagOperations instead.
 **/
class UserTags extends CmsUserTagOperations
{}

# vim:ts=4 sw=4 noet
?>
