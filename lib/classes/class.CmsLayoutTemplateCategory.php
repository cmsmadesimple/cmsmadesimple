<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * @package CMS
 */

/**
 * A class to manage template types 
 *
 * @since 1.12
 * @author Robert Campbell <calguy1000@gmail.com>
 */
class CmsLayoutTemplateCategory
{
  const TABLENAME = 'layout_tpl_categories';
  private $_dirty;
  private $_data = array();

  public function get_id()
  {
    if( isset($this->_data['id']) ) return $this->_data['id'];
  }

  public function get_name()
  {
    if( isset($this->_data['name']) ) return $this->_data['name'];
  }

  /**
   * Set the template type name
   *
   * @param sting The template type name.
   */
  public function set_name($str)
  {
    $str = trim($str);
    if( !$str ) throw new CmsInvalidDataException('Name cannot be empty');
		if( preg_match('<^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$>', $str) ) {
			throw new CmsInvalidDataException('Invalid characters in name');
		}
    $this->_data['name'] = $str;
    $this->_dirty = TRUE;
  }

  public function get_description()
  {
    if( isset($this->_data['description']) ) return $this->_data['description'];
  }

  public function set_description($str)
  {
		// description is allowed to be empty.
		$str = trim($str);
		$this->_data['description'] = $str;
		$this->_dirty = TRUE;
  }

  public function get_item_order()
  {
    if( isset($this->_data['item_order']) ) return $this->_data['item_order'];
  }

  public function set_item_order($idx)
  {
		// description is allowed to be empty.
		$idx = (int)$idx;
		$this->_data['item_order'] = $idx;
		$this->_dirty = TRUE;
  }

  public function get_modified()
  {
    if( isset($this->_data['modified']) ) return $this->_data['modified'];
  }

  protected function validate()
  {
    if( !$this->get_name() ) {
      throw new CmsInvalidDataException('A Template Categoy must have a name');
    }

		if( !preg_match('/[A-Za-z0-9_\,\.\ ]/',$this->get_name()) ) {
			throw new CmsInvalidDataException('Name must contain only numbers letters, spaces and underscores.');
		}

    $db = cmsms()->GetDb();
    $tmp = null;
    if( !$this->get_id() ) {
      $query = 'SELECT id FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE name = ?';
      $tmp = $db->GetOne($query,array($this->get_name()));
    }
    else {
      $query = 'SELECT id FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE name = ? AND id != ?';
      $tmp = $db->GetOne($query,array($this->get_name(),$this->get_id()));
    }
    if( $tmp ) {
		throw new CmsInvalidDataException('A Template Categoy with the same name already exists');
    }
  }

  protected function _insert()
  {
    if( !$this->_dirty ) return;
    $this->validate();

    $db = cmsms()->GetDb();
		$query = 'SELECT max(item_order) FROM '.cms_db_prefix().self::TABLENAME;
    $item_order = $db->GetOne($query);
		if( !$item_order ) $item_order=0;
		$item_order++;
		$this->_data['item_order'] = $item_order;

    $query = 'INSERT INTO '.cms_db_prefix().self::TABLENAME.'
              (name,description,item_order,modified) VALUES (?,?,?,?)';
    $dbr = $db->Execute($query,array($this->get_name(),$this->get_description(),
									 $this->get_item_order(),time()));
    if( !$dbr ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }
    $this->_data['id'] = $db->Insert_ID();
    $this->_dirty = FALSE;
		audit($this->get_id(),'CMSMS','Template Category Created');
  }

  protected function _update()
  {
    if( !$this->_dirty ) return;
    $this->validate();

    $db = cmsms()->GetDb();
    $query = 'UPDATE '.cms_db_prefix().self::TABLENAME.'
              SET name = ?, description = ?, item_order = ?, modified = ? WHERE id = ?';
    $dbr = $db->Execute($query,array($this->get_name(),
																		 $this->get_description(),
																		 $this->get_item_order(),
																		 time(),$this->get_id()));
    if( !$dbr ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }
    $this->_dirty = FALSE;
		audit($this->get_id(),'CMSMS','Template Category Updated');
  }

  public function save()
  {
    if( !$this->get_id() ) {
      return $this->_insert();
    }
    return $this->_update();
  }

  public function delete()
  {
    if( !$this->get_id() ) return;

    $db = cmsms()->GetDb();
    $query = 'DELETE FROM '.cms_db_prefix().self::TABLENAME.'
              WHERE id = ?';
    $dbr = $db->Execute($query,array($this->get_id()));
    if( !$dbr ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }

		$query = 'UPDATE '.cms_db_prefix().self::TABLENAME.'
              SET item_order = item_order - 1
              WHERE item_order > ?';
		$dbr = $db->GetOne($query,array($this->_data['item_order']));

		audit($this->get_id(),'CMSMS','Template Category Deleted');
		unset($this->_data['item_order']);
    unset($this->_data['id']);
    $this->_dirty = TRUE;
  }

  private static function &_load_from_data($row)
  {
    $ob = new CmsLayoutTemplateCategory();
    $ob->_data = $row;
    return $ob;
  }

  public static function &load($val)
  {
    $db = cmsms()->GetDb();
    $row = null;
    if( (int)$val > 0 ) {
      $query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.' WHERE id = ?';
      $row = $db->GetRow($query,array((int)$val));
    }
    else {
      $query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.' WHERE name = ?';
      $row = $db->GetRow($query,array($val));
    }
    if( !is_array($row) || count($row) == 0 ) {
      throw new CmsDataNotFoundException('Could not find template category identified by '.$val);
    }

    return self::_load_from_data($row);
  }


  public static function get_all($prefix = '')
  {
    $db = cmsms()->GetDb();
    if( $prefix ) {
      $query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE name LIKE ?
                ORDER BY item_order ASC';
      $res = $db->GetArray($query,array($prefix.'%'));
    }
    else {
      $query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.' 
                ORDER BY item_order ASC';
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
