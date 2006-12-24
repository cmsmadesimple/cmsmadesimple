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

class CmsStylesheetOperations extends CmsObject
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
	 * @return CmsStylesheetOperations The instance of the singleton object.
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsStylesheetOperations();
		}
		return self::$instance;
	}

	/**
	 * Returns all of the stylesheets in the system, ordered by name.
	 *
	 * @return array All of the stylesheets in the system
	 * @author Ted Kulp
	 **/
	public static function load_stylesheets()
	{
		return cmsms()->stylesheet->find_all(array('order' => 'name'));
	}
	
	/**
	 * @deprecated Deprecated.  Use load_stylesheets instead.
	 **/
	function LoadStylesheets()
	{
		return CmsStylesheetOperations::load_stylesheets();
	}

	/**
	 * Assocates a stylesheet to the a template.
	 *
	 * @param integer The stylesheet to attach
	 * @param integer The template to attach it to
	 * @return bool Whether or not the attachment was successful
	 * @author Ted Kulp
	 **/
	function associate_stylesheet_to_template($stylesheet_id, $template_id)
	{
		$db = cms_db();

		$time = $db->DBTimeStamp(time());
		$query = 'INSERT INTO '.cms_db_prefix().'css_assoc VALUES (?,?,?,'.$time.','.$time.')';
		$dbresult = $db->Execute( $query, array( $template_id, $stylesheet_id, 'template'));

		return ($dbresult != false);
	}
	
	/**
	 * @deprecated Deprecated.  Use associate_stylesheet_to_template instead.
	 **/
	function AssociateStylesheetToTemplate($stylesheetid, $templateid)
	{
		return CmsStylesheetOperations::AssociateStylesheetToTemplate($stylesheetid, $templateid);
	}

	function get_stylesheets_associated_to_template($template_id)
	{
		$result = false;

		$db = cms_db();

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
	
	/**
	 * @deprecated Deprecated.  Use get_stylesheets_associated_to_template instead.
	 **/
	function GetTemplateAssociatedStylesheets($templateid)
	{
		return CmsStylesheetOperations::get_stylesheets_associated_to_template($templateid)
	}

	/**
	 * Loads a stylesheet by stylesheet id.
	 *
	 * @param integer The stylesheet id to load
	 * @return mixed If successful, the filled Stylesheet object.  If it fails, it returns null.
	 * @author Ted Kulp
	 **/
	function load_stylesheet_by_id($id)
	{
		return cmsms()->stylesheet->find_by_id($id);
	}
	
	/**
	 * @deprecated Deprecated.  Use load_stylesheet_by_id instead.
	 **/
	function LoadStylesheetByID($id)
	{
		return CmsStylesheetOperations::load_stylesheet_by_id($id);
	}

	/**
	 * Saves a new stylesheet to the database.
	 *
	 * @deprecated Deprecated.  User $stylesheet->save() instead.
	 *
	 * @param mixed Stylesheet object to save
	 *
	 * @return mixed The new stylesheet id.  If it fails, it returns -1.
	 * @since 0.6.1
	 */
	function InsertStylesheet($stylesheet)
	{
		return $stylesheet->save();
	}

	/**
	 * Updates an existing stylesheet in the database.
	 *
	 * @deprecated Deprecated.  User $stylesheet->save() instead.
	 *
	 * @param mixed Stylesheet object to save
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 * @since 0.6.1
	 */
	function UpdateStylesheet($stylesheet)
	{
		return $stylesheet->save();
	}

	/**
	 * Deletes an existing user from the database.
	 *
	 * @deprecated Deprecated.  Use cmsms()->stylesheet->delete($id) instead.
	 *
	 * @param mixed Id of the stylesheet to delete
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 * @since 0.6.1
	 */
	function DeleteStylesheetByID($id)
	{
		return cmsms()->stylesheet->delete($id);
	}

	/**
	 * Checks to see if a stylesheet name is in use already.
	 *
	 * @param string The name of the stylesheet to look up
	 * @return bool Whether or not that name is in use already
	 * @author Ted Kulp
	 **/
	function check_existing_stylesheet_name($name)
	{
		return cmsms()->stylesheet->find_count_by_name($name) > 0 ? true : false;
	}
	
	/**
	 * @deprecated Deprecated.  Use check_existing_stylesheet_name instead.
	 **/
	function CheckExistingStylesheetName($name)
	{
		return CmsStylesheetOperations::check_existing_stylesheet_name($name);
	}
}

# vim:ts=4 sw=4 noet
?>