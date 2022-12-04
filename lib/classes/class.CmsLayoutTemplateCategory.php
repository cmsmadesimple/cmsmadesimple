<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * Classes and utilities for managing template categories.
 * @package CMS
 * @license GPL
 */

/**
 * A class representing a template category.
 *
 * Templates can be optionally organized into categories, this class manages the category itself.
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell <calguy1000@gmail.com>
 */
class CmsLayoutTemplateCategory
{
	/**
	 * @ignore
	 */
    const TABLENAME = 'layout_tpl_categories';

	/**
	 * @ignore
	 */
    private $_dirty;

	/**
	 * @ignore
	 */
    private $_data = array();

	/**
	 * Get the category id
	 *
	 * @return int
	 */
    public function get_id()
    {
        if( isset($this->_data['id']) ) return $this->_data['id'];
    }

	/**
	 * Get the category name
	 *
	 * @return string
	 */
    public function get_name()
    {
        if( isset($this->_data['name']) ) return $this->_data['name'];
    }

    /**
     * Set the category name.
     *
     * The category name must be unique, and can only contain certain characters.
     *
     * @throws CmsInvalidDataException
     * @param sting $str The template type name.
     */
    public function set_name($str)
    {
        $str = trim($str);
        if( !$str ) throw new CmsInvalidDataException('Name cannot be empty');
        if( !CmsAdminUtils::is_valid_itemname($str) ) {
			throw new CmsInvalidDataException('Invalid characters in name');
		}
        $this->_data['name'] = $str;
        $this->_dirty = TRUE;
    }

	/**
	 * Get the category description
	 *
	 * @return string
	 */
    public function get_description()
    {
        if( isset($this->_data['description']) ) return $this->_data['description'];
    }

	/**
	 * Set the category description
	 *
	 * @param string $str The description
	 */
    public function set_description($str)
    {
		// description is allowed to be empty.
		$str = trim($str);
		$this->_data['description'] = $str;
		$this->_dirty = TRUE;
    }

	/**
	 * Get the category order
	 *
	 * @return int
	 */
    public function get_item_order()
    {
        if( isset($this->_data['item_order']) ) return $this->_data['item_order'];
    }

	/**
	 * Set the item order.
	 *
	 * The item order must be unique and incremental
	 * no validation is done on the item order in this method.
	 *
	 * @param int $idx
	 */
    public function set_item_order($idx)
    {
		// description is allowed to be empty.
		$idx = (int)$idx;
		if( $idx < 1 ) return;
		$this->_data['item_order'] = $idx;
		$this->_dirty = TRUE;
    }

	/**
	 * Get the date that this category was last saved to the database
	 *
	 * @return int The unix timestamp from the database
	 */
    public function get_modified()
    {
        if( isset($this->_data['modified']) ) return $this->_data['modified'];
    }

	/**
	 * Validate the correctness of this object
	 * @throws CmsInvalidDataException
	 */
    protected function validate()
    {
        if( !$this->get_name() ) throw new CmsInvalidDataException('A Template Categoy must have a name');
        if( !CmsAdminUtils::is_valid_itemname($this->get_name()) ) {
			throw new CmsInvalidDataException('Name must contain only letters, numbers and underscores.');
		}

        $db = cmsms()->GetDb();
        $tmp = null;
        if( !$this->get_id() ) {
            $query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ?';
            $tmp = $db->GetOne($query,array($this->get_name()));
        }
        else {
            $query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ? AND id != ?';
            $tmp = $db->GetOne($query,array($this->get_name(),$this->get_id()));
        }
        if( $tmp ) {
            throw new CmsInvalidDataException('A Template Categoy with the same name already exists');
        }
    }

	/**
	 * @ignore
	 */
    protected function _insert()
    {
        if( !$this->_dirty ) return;
        $this->validate();

        $db = cmsms()->GetDb();
		$query = 'SELECT max(item_order) FROM '.CMS_DB_PREFIX.self::TABLENAME;
        $item_order = $db->GetOne($query);
		if( !$item_order ) $item_order=0;
		$item_order++;
		$this->_data['item_order'] = $item_order;

        $query = 'INSERT INTO '.CMS_DB_PREFIX.self::TABLENAME.' (name,description,item_order,modified) VALUES (?,?,?,?)';
        $dbr = $db->Execute($query,array($this->get_name(),$this->get_description(),
                                         $this->get_item_order(),time()));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        $this->_data['id'] = $db->Insert_ID();
        $this->_dirty = FALSE;
		audit($this->get_id(),'CMSMS','Template Category Created');
    }

	/**
	 * @ignore
	 */
    protected function _update()
    {
        if( !$this->_dirty ) return;
        $this->validate();

        $db = cmsms()->GetDb();
        $query = 'UPDATE '.CMS_DB_PREFIX.self::TABLENAME.' SET name = ?, description = ?, item_order = ?, modified = ? WHERE id = ?';
        $dbr = $db->Execute($query,array($this->get_name(),
                                         $this->get_description(),
                                         $this->get_item_order(),
                                         time(),(int)$this->get_id()));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        $this->_dirty = FALSE;
		audit($this->get_id(),'CMSMS','Template Category Updated');
    }

	/**
	 * Save this object to the database
	 * @throws CmsSQLErrorException
	 * @throws CmsInvalidDataException
	 */
    public function save()
    {
        if( !$this->get_id() ) return $this->_insert();
        return $this->_update();
    }

	/**
	 * Delete this object from the database
	 *
	 * This method will delete the object from the database, and erase the item order and id values
	 * from this object, suitable for re-saving
	 *
	 * @throw CmsSQLErrorException
	 */
    public function delete()
    {
        if( !$this->get_id() ) return;

        $db = cmsms()->GetDb();
        $query = 'DELETE FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id = ?';
        $dbr = $db->Execute($query,array($this->get_id()));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

		$query = 'UPDATE '.CMS_DB_PREFIX.self::TABLENAME.' SET item_order = item_order - 1 WHERE item_order > ?';
		$dbr = $db->GetOne($query,array($this->_data['item_order']));

		audit($this->get_id(),'CMSMS','Template Category Deleted');
		unset($this->_data['item_order']);
        unset($this->_data['id']);
        $this->_dirty = TRUE;
    }

	/**
	 * @ignore
	 */
    private static function &_load_from_data($row)
    {
        $ob = new CmsLayoutTemplateCategory();
        $ob->_data = $row;
        return $ob;
    }

	/**
	 * Load a category object from the database
	 *
     * @throws CmsDataNotFoundException
	 * @param int|string $val Either the integer category id, or the category name
	 * @return self
	 */
    public static function &load($val)
    {
        $db = cmsms()->GetDb();
        $row = null;
        if( is_numeric($val) && $val > 0 ) {
            $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id = ?';
            $row = $db->GetRow($query,array((int)$val));
        }
        else {
            $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ?';
            $row = $db->GetRow($query,array($val));
        }
        if( !is_array($row) || count($row) == 0 ) throw new CmsDataNotFoundException('Could not find template category identified by '.$val);

        return self::_load_from_data($row);
    }

	/**
	 * Load a set of categories from the database
	 *
	 * @param string $prefix An optional category name prefix.
	 * @return array Array of CmsLayoutTemplateCategory objects
	 */
    public static function get_all($prefix = '')
    {
        $db = cmsms()->GetDb();
        if( $prefix ) {
            $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name LIKE ? ORDER BY item_order ASC';
            $res = $db->GetArray($query,array($prefix.'%'));
        }
        else {
            $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' ORDER BY item_order ASC';
            $res = $db->GetArray($query);
        }
        if( is_array($res) && count($res) ) {
            $out = array();
            foreach( $res as $row ) {
				$out[] = self::_load_from_data($row);
            }
            return $out;
        }
    }
} // end of class,

#
# EOF
#
?>