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
#$Id$

/**
 * Stylesheet related functions.
 *
 * @package CMS
 * @license GPL
 */

/**
 * Include stylesheet class definition
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.stylesheet.inc.php');

/**
 * Class for doing stylesheet related functions.  Maybe of the Group object functions are just wrappers around these.
 *
 * @since		0.11
 * @package		CMS
 * @license GPL
 */
final class StylesheetOperations
{
	private static $_instance;
	private $_cache = array();

	protected function __construct() {}
	public static function &get_instance()
	{
		if( !is_object(self::$_instance) )
		{
			self::$_instance = new StylesheetOperations();
		}
		return self::$_instance;
	}


	function & LoadStylesheets()
	{
		$gCms = cmsms();
		$db = $gCms->GetDb();

		$result = array();

		$query = "SELECT css_id, css_name, css_text, media_type, media_query, modified_date FROM ".cms_db_prefix()."css ORDER BY css_id";
		$dbresult = $db->Execute($query);

		while ($dbresult && $row = $dbresult->FetchRow())
		{
			$onestylesheet = new Stylesheet();
			$onestylesheet->id = $row['css_id'];
			$onestylesheet->name = $row['css_name'];
			$onestylesheet->value = $row['css_text'];
			$onestylesheet->media_type = $row['media_type'];
            $onestylesheet->media_query = $row['media_query'];
			$onestylesheet->modified_date = $db->UnixTimeStamp($row['modified_date']);
			$result[] = $onestylesheet;
		}
		return $result;
	}


	function AssociateStylesheetToTemplate( $stylesheetid, $templateid )
	{
		$gCms = cmsms();
		$db = $gCms->GetDb();

		$query = 'SELECT max(assoc_order) FROM '.cms_db_prefix().'css_assoc 
                           WHERE assoc_to_id = ?';
		$order = $db->GetOne($query,array($templateid));
		if( $order )
		  {
		    $order++;
		  }
		else
		  {
		    $order = 1;
		  }

		$time = $db->DBTimeStamp(time());
		$query = 'INSERT INTO '.cms_db_prefix().'css_assoc VALUES (?,?,?,'.$time.','.$time.',?)';
		$dbresult = $db->Execute( $query, 
					  array( $templateid, $stylesheetid,
						 'template', $order));
		return ($dbresult != false);
	}


	function GetTemplateAssociatedStylesheets($templateid)
	{
		$result = false;

		$gCms = cmsms();
		$db = $gCms->GetDb();

		$query = 'SELECT assoc_css_id FROM '.cms_db_prefix().'css_assoc WHERE
		           assoc_type = ? AND assoc_to_id = ? ORDER BY assoc_order';
		$dbresult = $db->Execute($query, array('template', $templateid));

		$result = array();
		while ($dbresult && $row = $dbresult->FetchRow())
		{
			$result[] = $row['assoc_css_id'];
		}

		return $result;
	}

	/**
	* NOTE: Static or not? -Stikki-
	*/
	function & LoadStylesheetByID($id)
	{
		$result = false;

		$gCms = cmsms();
		$db = $gCms->GetDb();

		if (isset($this->_cache[$id]))
		{
			return $this->_cache[$id];
		}

		$query = "SELECT css_id, css_name, css_text, media_type, media_query FROM ".cms_db_prefix()."css WHERE css_id = ?";
		$dbresult = $db->Execute($query, array($id));

		while ($dbresult && $row = $dbresult->FetchRow())
		{
			$onestylesheet = new Stylesheet();
			$onestylesheet->id = $row['css_id'];
			$onestylesheet->name = $row['css_name'];
			$onestylesheet->value = $row['css_text'];
			$onestylesheet->media_type = $row['media_type'];
            $onestylesheet->media_query = $row['media_query'];
			$result =& $onestylesheet;

			if (!isset($this->_cache[$onestylesheet->id]))
			{
				$this->_cache[$onestylesheet->id] =& $onestylesheet;
			}
		}

		return $result;
	}
	
	/**
	* Author: Stikki - stikki@cmsmadesimple.org
	*/
	function & LoadStylesheetByName($name)
	{
		$result = false;
		$name = trim($name);
	
		$db = cmsms()->GetDb();

		$query = "SELECT css_id FROM ".cms_db_prefix()."css WHERE css_name = ?";
		$id = $db->GetOne($query, array($name));
		
		if($id) {
		
			if (isset($this->_cache[$id])) {
			
				return $this->_cache[$id];
			}			
		
			$result = $this->LoadStylesheetByID($id);
		}
		
		return $result;
	}	

	function InsertStylesheet($stylesheet)
	{
		$result = -1; 

		$gCms = cmsms();
		$db = $gCms->GetDb();

		$new_stylesheet_id = $db->GenID(cms_db_prefix()."css_seq");
		$time = $db->DBTimeStamp(time());
		$query = "INSERT INTO ".cms_db_prefix()."css (css_id, css_name, css_text, media_type, media_query, create_date, modified_date) VALUES (?,?,?,?,?,".$time.",".$time.")";
		$dbresult = $db->Execute($query, array($new_stylesheet_id, $stylesheet->name, $stylesheet->value, $stylesheet->media_type, $stylesheet->media_query));
		if ($dbresult !== false)
		{
			$result = $new_stylesheet_id;
		}

		return $result;
	}

	function UpdateStylesheet($stylesheet)
	{
		$result = false; 

		$gCms = cmsms();
		$db = $gCms->GetDb();

		$time = $db->DBTimeStamp(time());
		$query = "UPDATE ".cms_db_prefix()."css SET css_name = ?,css_text = ?, media_type = ?, media_query = ?, modified_date = ".$time." WHERE css_id = ?";
		$dbresult = $db->Execute($query, array($stylesheet->name, $stylesheet->value, $stylesheet->media_type, $stylesheet->media_query, $stylesheet->id));
		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}
	
	/**
	* NOTE: Static or not? -Stikki-
	*/
	function DeleteStylesheetByID($id)
	{
		$result = false;

		$gCms = cmsms();
		$db = $gCms->GetDb();

		$query = "DELETE FROM ".cms_db_prefix()."css_assoc where assoc_css_id = ?";
		$dbresult = $db->Execute($query, array($id));

		$query = "DELETE FROM ".cms_db_prefix()."css where css_id = ?";
		$dbresult = $db->Execute($query, array($id));

		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	/**
	* Author: Stikki - stikki@cmsmadesimple.org
	*/
	function DeleteStylesheetByName($name)
	{
		$result = false;
		$name = trim($name);
	
		$db = cmsms()->GetDb();

		$query = "SELECT css_id FROM ".cms_db_prefix()."css WHERE css_name = ?";
		$id = $db->GetOne($query, array($name));

		if($id) {
		
			$result = $this->DeleteStylesheetByID($id);
		}
		
		return $result;
	}	

	function CheckExistingStylesheetName($name, $id = -1)
	{
		$result = false;

		$gCms = cmsms();
		$db = $gCms->GetDb();

		$query = "SELECT css_id from ".cms_db_prefix()."css WHERE css_name = ?";
		$attrs = array($name);
		
		if ($id > -1)
		{
			$query .= ' AND css_id != ?';
			$attrs[] = $id;
		}
		
		$dbresult = $db->Execute($query, $attrs);

		if ($dbresult && $dbresult->RecordCount() > 0)
		{
			$result = true; 
		}

		return $result;
	}
}

?>