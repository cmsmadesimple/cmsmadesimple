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
 * Class for doing stylesheet related functions.  Maybe of the Group object functions are just wrappers around these.
 *
 * @since		0.11
 * @package		CMS
 */

//require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.stylesheet.inc.php');

class StylesheetOperations
{
	function &LoadStylesheets()
	{
		global $gCms;
		$stylesheet =& $gCms->GetOrmClass('stylesheet');
		return $stylesheet->find_all(array('order' => 'css_id'));
	}


	function AssociateStylesheetToTemplate( $stylesheetid, $templateid )
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$time = $db->DBTimeStamp(time());
		$query = 'INSERT INTO '.cms_db_prefix().'css_assoc VALUES (?,?,?,'.$time.','.$time.')';
		$dbresult = $db->Execute( $query, array( $templateid, 
			$stylesheetid,
			'template'));
			return ($dbresult != false);
	}


	function GetTemplateAssociatedStylesheets($templateid)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = 'SELECT assoc_css_id FROM '.cms_db_prefix().'css_assoc WHERE
		           assoc_type = ? AND assoc_to_id = ?';
		$dbresult = $db->Execute($query, array('template', $templateid));

		$result = array();
		while ($dbresult && $row = $dbresult->FetchRow())
		{
			$result[] = $row['assoc_css_id'];
		}

		return $result;
	}


	function &LoadStylesheetByID($id)
	{
		global $gCms;
		$stylesheet =& $gCms->GetOrmClass('stylesheet');
		return $stylesheet->find_by_id($id);
	}

	function InsertStylesheet($stylesheet)
	{
		return $stylesheet->save();
	}

	function UpdateStylesheet($stylesheet)
	{
		return $stylesheet->save();
	}

	function DeleteStylesheetByID($id)
	{
		global $gCms;
		$stylesheet =& $gCms->GetOrmClass('stylesheet');
		return $stylesheet->delete($id);
	}

	function CheckExistingStylesheetName($name)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT css_id from ".cms_db_prefix()."css WHERE css_name = ?";
		$dbresult = $db->Execute($query,array($name));

		if ($dbresult && $dbresult->RecordCount() > 0)
		{
			$result = true; 
		}

		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>