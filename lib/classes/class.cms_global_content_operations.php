<?php
#CMS - CMS Made Simple
#(c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
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
 * Class for doing html blob related functions.  Maybe of the HtmlBlob object functions are just wrappers around these.
 *
 * @since		0.6
 * @package		CMS
 */

class CmsGlobalContentOperations extends CmsObject
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
	 * @return CmsGlobalContentOperations The instance of the singleton object.
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsGlobalContentOperations();
		}
		return self::$instance;
	}

	/**
	 * Prepares an array with the list of the html blobs $userid is an author of 
	 *
	 * @return array An array in whose elements are the IDs of the blobs  
	 * @since 0.11
	 */
	public static function get_global_content_by_author_id($userid)
	{
		$gCms = cmsms();
		$db = cms_db();
	    $variables = &$gCms->variables;
		if (!isset($variables['authorblobs']))
		{
			$variables['authorblobs'] = array();

			$query = "SELECT htmlblob_id FROM ".cms_db_prefix()."additional_htmlblob_users WHERE user_id = ?";
			$result = &$db->Execute($query, array($userid));

			while ($result && !$result->EOF)
			{
				$variables['authorblobs'][] = $result->fields['htmlblob_id'];
				$result->MoveNext();
			}
		}

		return $variables['authorblobs'];
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsGlobalContentOperations::get_global_content_by_author_id instead.
	 **/
	function AuthorBlobs($userid)
	{
		return CmsGlobalContentOperations::get_global_content_by_author_id($userid);
	}

	/**
	 * @deprecated Deprecated.  Use cmsms()->global_content->find_all() instead.
	 **/
	function LoadHtmlBlobs()
	{
		return cmsms()->global_content->find_all(array('order' => 'htmlblob_name ASC'));
	}

	/**
	 * @deprecated Deprecated.  Use cmsms()->global_content->find_by_id($id) instead.
	 **/
	function LoadHtmlBlobByID($id)
	{
		return cmsms()->global_content->find_by_id($id);
	}

	/**
	 * @deprecated Deprecated.  Use cmsms()->global_content->find_by_name($name) instead.
	 **/
	function &LoadHtmlBlobByName($name)
	{
		return cmsms()->global_content->find_by_name($name);
	}

	/**
	 * @deprecated Deprecated.  Use $obj->save() instead.
	 **/
	function InsertHtmlBlob($htmlblob)
	{
		return $htmlblob->save();
	}

	/**
	 * @deprecated Deprecated.  Use $obj->save() instead.
	 **/
	function UpdateHtmlBlob($htmlblob)
	{
		return $htmlblob->save();
	}

	/**
	 * @deprecated Deprecated.  Use cmsms->global_content->delete($id) instead.
	 **/
	function DeleteHtmlBlobByID($id)
	{
		return cmsms->global_content->delete($id);
	}

	/**
	 * Checks to see if an global content block already has the given name.
	 *
	 * @param string Name of the block to look for
	 * @return bool Whether or not the block name exists
	 * @author Ted Kulp
	 **/
	public static function check_existing_global_content_name($name)
	{
		return cmsms()->global_content->find_count_by_name($name) > 0;
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsGlobalContentOperations::check_existing_global_content_name($name) instead.
	 **/
	function CheckExistingHtmlBlobName($name)
	{
		return CmsGlobalContentOperations::check_existing_global_content_name($name);
	}

	function CheckOwnership($id, $user_id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT htmlblob_id FROM ".cms_db_prefix()."htmlblobs WHERE htmlblob_id = ? AND owner = ?";
		$row = &$db->GetRow($query, array($id, $user_id));

		if ($row)
		{
			$result = true;
		}

		return $result;
	}

	function CheckAuthorship($id, $user_id)
	{		
		global $gCms;
		$db = &$gCms->GetDb();
		$result = false;

		$query = "SELECT additional_htmlblob_users_id FROM ".cms_db_prefix()."additional_htmlblob_users WHERE htmlblob_id = ? AND user_id = ?";
		$row = &$db->GetRow($query, array($id, $user_id));

		if ($row)
		{
			$result = true;
		}

		return $result;
	}

	function ClearAdditionalEditors($id)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$query = "DELETE FROM ".cms_db_prefix()."additional_htmlblob_users WHERE htmlblob_id = ?";

		$dbresult = $db->Execute($query, array($id));
	}

	function InsertAdditionalEditors($id, $user_id)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$new_id = $db->GenID(cms_db_prefix()."additional_htmlblob_users_seq");
		$query = "INSERT INTO ".cms_db_prefix()."additional_htmlblob_users (additional_htmlblob_users_id, htmlblob_id, user_id) VALUES (?,?,?)";
		$dbresult = $db->Execute($query, array($new_id,$id,$user_id));
	}
}

class GlobalContentOperations extends CmsGlobalContentOperations
{
}

class HtmlBlobOperations extends CmsGlobalContentOperations
{
}

?>