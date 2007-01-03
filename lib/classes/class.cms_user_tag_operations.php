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
	function GetUserTag( $name )
	{
		$code = false;

		global $gCms;
		$db =& $gCms->GetDb();
		
		$query = 'SELECT userplugin_id, code FROM '.cms_db_prefix().'userplugins WHERE userplugin_name = ?';
		$result = &$db->Execute($query, array($name));

		while ($result && !$result->EOF)
		{
			$code = $result->fields['code'];
			$result->MoveNext();
		}
		
		return $code;
	}


	/**
	 * Add or update a named user defined tag into the database
	 *
	 * @params string $name User defined tag name
	 * @params string $text Body of user defined tag
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function SetUserTag( $name, $text )
	{
		global $gCms;
		$db =& $gCms->GetDb();
		
		$existing = UserTagOperations::GetUserTag($name);
		if (!$existing)
		{
			$query = "INSERT INTO userplugins (userplugin_name, code, create_date, modified_date) VALUES (?,?,".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
			$result = $db->Execute($query, array($name, $text));
			if ($result)
				return true;
			else
				return false;
		}
		else
		{
			$query = 'UPDATE userplugins SET code = ?, modified_date = '.$db->DBTimeStamp(time()).' WHERE userplugin_name = ?';
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
	 * @params string $name User defined tag name
	 *
	 * @returns mixed If successful, true.  If it fails, false.
	 */
	function RemoveUserTag( $name )
	{
		global $gCms;
		$db =& $gCms->GetDb();
		
		$query = 'DELETE FROM '.cms_db_prefix().'userplugins WHERE userplugin_name = ?';
		$result = &$db->Execute($query, array($name));

		if ($result)
		{
			return true;
		}
		
		return false;
	}


 	/**
	 * Return a list (suitable for use in a pulldown) of user tags.
	 *
	 * @returns mixed If successful, an array.  If it fails, false.
	 */
	function ListUserTags()
	{
		global $gCms;
		$db =& $gCms->GetDb();

		$plugins = array();
		
		//var_dump($db);

		$query = 'SELECT userplugin_name FROM '.cms_db_prefix().'userplugins ORDER BY userplugin_name';
		//var_dump($db->Execute($query));
		$result = &$db->Execute($query);
		
		//var_dump($result);

		while ($result && !$result->EOF)
		{
			$plugins[$result->fields['userplugin_name']] =& $result->fields['userplugin_name'];
			$result->MoveNext();
		}
		
		if (count($plugins) == 0)
			$plugins = false;

		return $plugins;
	}
	
	function CallUserTag($name, &$params)
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

} // class

class UserTags extends CmsUserTagOperations
{}

# vim:ts=4 sw=4 noet
?>
