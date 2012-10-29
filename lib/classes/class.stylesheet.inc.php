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
 * Stylesheet class definition
 * @package CMS 
 * @license GPL
 */

die('remove this file');

/**
 * Generic stylesheet class. This can be used for any logged in stylesheet or stylesheet related function.
 *
 * @since		0.11
 * @package		CMS
 * @license	 GPL
 */
class Stylesheet
{
	/**
	 * ID
	 */
	var $id;
	
	/**
	 * Name
	 */
	var $name;
	
	/**
	 * Value
	 */
	var $value;
	
	/**
	 * CSS Media Type
	 */
	var $media_type;

    /**
     * CSS Media Type
     */
    var $media_query;    

	private $_theme_assoc;

	/**
	 * Sets some initial values
	 */
	public function __construct()
	{
		$this->SetInitialValues();
	}
	
	/**
	 * Sets object to some sane initial values
	 *
	 * @access private
	 */
	function SetInitialValues()
	{
		$this->id = -1;
		$this->name = '';
		$this->value = '';
		$this->media_type = '';
        $this->media_query = '';
	}
	
	/**
	 * Gets the Stylesheet id.
	 *
	 * @return integer The id of the Stylesheet.
	 */	
	function Id()
	{
		return $this->id;
	}
	
	/**
	 * Gets the Stylesheet name.
	 *
	 * @return string The name of the Stylesheet.
	 */
	function Name()
	{
		return $this->name;
	}

	/**
	 * Saves the Stylesheet to the database, creating a new record.
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 */
	function Save()
	{
		$result = false;
		
		$styleops = cmsms()->GetStylesheetOperations();
		
		if ($this->id > -1) {
			$result = $styleops->UpdateStylesheet($this);
		}
		else {
			$newid = $styleops->InsertStylesheet($this);
			if ($newid > -1) {
				$this->id = $newid;
				$result = true;
			}

		}

		return $result;
	}

	/**
	 * Deletes the Stylesheet from the database.
	 *
	 * @return mixed If successful, true.  If it fails, false.
	 */
	function Delete()
	{
		$result = false;

		if ($this->id > -1) {
			$styleops = cmsms()->GetStylesheetOperations();
			$result = $styleops->DeleteStylesheetByID($this->id);
			if ($result) {
				$this->SetInitialValues();
			}
		}

		return $result;
	}

	public function get_themes()
	{
		if( !$this->id ) return;
		if( is_null($this->_theme_assoc) ) {
			$this->_theme_assoc = null;
			$db = cmsms()->GetDb();
			$query = 'SELECt theme_id FROM '.cms_db_prefix().CmsLayoutCollection::CSSTABLE.'
                WHERE css_id = ?';
			$tmp = $db->GetCol($query,array($this->id));
			if( is_array($tmp) && count($tmp) ) {
				$this->_theme_assoc = $tmp;
			}
		}
		return $this->_theme_assoc;
	}

	public function set_themes($x)
	{
		if( !is_array($x) ) return;

		foreach( $x as $y ) {
			if( !is_numeric($y) )
				throw new CmsInvalidDatException('Invalid data in theme list.  Expect array of integers');
		}

		$this->_theme_assoc = $x;
		$this->_dirty = TRUE;
	}

	public function add_theme($a) 
	{
		$n = null;
		if( is_object($a) && is_a($a,'CmsLayoutCollection') ) {
			$n = $a->get_id();
		}
		else if( (int)$a > 0 ) {
			$n = $a;
		}
		else if( (is_string($a) && strlen($a)) || (int)$a > 0 ) {
			$theme = CmsLayoutCollection::load($a);
			$n = $theme->get_id();
		}

		if( !is_array($this->_theme_assoc) ) {
			$this->_theme_assoc = array();
		}
		$this->_theme_assoc[] = $n;
		$this->_dirty = TRUE;
	}

	public function remove_theme($a)
	{
		if( !is_array($this->_theme_assoc) || count($this->_theme_assoc) == 0 ) return;

		$n = null;
		if( is_object($a) && is_a($a,'CmsLayoutCollection') ) {
			$n = $a->get_id();
		}
		else if( (int)$a > 0 ) {
			$n = $a;
		}
		else if( (is_string($a) && strlen($a)) || (int)$a > 0 ) {
			$theme = CmsLayoutCollection::load($a);
			$n = $theme->get_id();
		}

		if( in_array($n,$this->_theme_assoc) ) {
			$t = array();
			foreach( $this->_theme_assoc as $one ) {
				if( $n == $one ) continue;
				$t[] = $one;
			}
			$this->_theme_assoc = $t;
			$this->_dirty = TRUE;
		}
	}
}

# vim:ts=4 sw=4 noet
?>
