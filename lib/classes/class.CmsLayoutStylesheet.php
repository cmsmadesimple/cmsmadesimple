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
 * A class to represent a smarty template.
 *
 * @since 1.12
 * @author Robert Campbell <calguy1000@gmail.com>
 */
class CmsLayoutStylesheet
{
  const TABLENAME = 'layout_stylesheets';
  
  private $_dirty;
  private $_data = array();
  private $_design_assoc;

  public function get_id()
  {
    if( isset($this->_data['id']) ) return $this->_data['id'];
  }

  public function get_name()
  {
    if( isset($this->_data['name']) ) return $this->_data['name'];
  }

  public function set_name($str)
  {
    $str = trim($str);
    if( !$str ) throw new CmsInvalidDataException('Name cannot be empty');
    $this->_data['name'] = $str;
    $this->_dirty = TRUE;
  }

  public function get_content()
  {
    if( isset($this->_data['content']) ) return $this->_data['content'];
  }

  public function set_content($str)
  {
    $str = trim($str);
    if( !$str ) throw new CmsInvalidDataException('Template content cannot be empty');
    $this->_data['content'] = $str;
    $this->_dirty = TRUE;
  }

  public function get_description()
  {
    if( isset($this->_data['description']) ) return $this->_data['description'];
  }

  public function set_description($str)
  {
    $str = trim($str);
    $this->_data['description'] = $str;
    $this->_dirty = TRUE;
  }

  public function get_media_types()
  {
		// returns an array...
    if( isset($this->_data['media_type']) ) return $this->_data['media_type'];
  }

	public function has_media_type($str)
	{
    $str = trim($str);
		if( in_array($str,$this->_data['media_type']) ) return TRUE;
		return FALSE;
	}

	public function add_media_type($str)
	{
    $str = trim($str);
		if( !is_array($this->_data['media_type']) ) $this->_data['media_type'] = array();
		$this->_data['media_type'][] = $str;
		$this->_dirty = TRUE;
	}

  public function set_media_types($arr)
  {
		if( !is_array($arr) ) {
			if( (int)$arr == 0 && is_string($arr) ) {
				$arr = array($arr);
			}
			else {
				return;
			}
		}

    $this->_data['media_type'] = $arr;
    $this->_dirty = TRUE;
  }

  public function get_media_query()
  {
    if( isset($this->_data['media_query']) ) return $this->_data['media_query'];
  }

  public function set_media_query($str)
  {
    $str = trim($str);
    $this->_data['media_query'] = $str;
    $this->_dirty = TRUE;
  }

  public function get_created()
  {
    if( isset($this->_data['created']) ) return $this->_data['created'];
  }

  public function get_modified()
  {
    if( isset($this->_data['modified']) ) return $this->_data['modified'];
  }

  public function get_designs()
  {
    if( !$this->get_id() ) return;
    if( is_null($this->_design_assoc) ) {
      $this->_design_assoc = null;
      $db = cmsms()->GetDb();
      $query = 'SELECt design_id FROM '.cms_db_prefix().CmsLayoutCollection::CSSTABLE.'
                WHERE css_id = ?';
      $tmp = $db->GetCol($query,array($this->get_id()));
      if( is_array($tmp) && count($tmp) ) {
	$this->_design_assoc = $tmp;
      }
    }
    return $this->_design_assoc;
  }

  public function set_designs($x)
  {
    if( !is_array($x) ) return;

    foreach( $x as $y ) {
      if( !is_numeric($y) )
				throw new CmsInvalidDatException('Invalid data in design list.  Expect array of integers');
    }

    $this->_design_assoc = $x;
  }

  public function add_design($a) 
  {
    $n = null;
    if( is_object($a) && is_a($a,'CmsLayoutCollection') ) {
      $n = $a->get_id();
    }
    else if( (int)$a > 0 ) {
      $n = $a;
    }
    else if( (is_string($a) && strlen($a)) || (int)$a > 0 ) {
      $design = CmsLayoutCollection::load($a);
      $n = $design->get_id();
    }

    if( !is_array($this->_design_assoc) ) {
      $this->_design_assoc = array();
    }
    $this->_design_assoc[] = $n;
    $this->_dirty = TRUE;
  }

  public function remove_design($a)
  {
    if( !is_array($this->_design_assoc) || count($this->_design_assoc) == 0 ) return;

    $n = null;
    if( is_object($a) && is_a($a,'CmsLayoutCollection') ) {
      $n = $a->get_id();
    }
    else if( (int)$a > 0 ) {
      $n = $a;
    }
    else if( (is_string($a) && strlen($a)) || (int)$a > 0 ) {
      $design = CmsLayoutCollection::load($a);
      $n = $design->get_id();
    }

    if( in_array($n,$this->_design_assoc) ) {
      $t = array();
      foreach( $this->_design_assoc as $one ) {
				if( $n == $one ) continue;
				$t[] = $one;
      }
      $this->_design_assoc = $t;
      $this->_dirty = TRUE;
    }
  }

  protected function validate()
  {
    if( !$this->get_name() ) {
      throw new CmsInvalidDataException('Each template must have a name');
    }
    if( !$this->get_content() ) {
      throw new CmsInvalidDataException('Each template must have some content');
    }

    $db = cmsms()->GetDb();
    $tmp = null;
    if( $this->get_id() ) {
      // double check the name.
      $query = 'SELECT id FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE name = ? AND id != ?';
      $tmp = $db->GetOne($query,array($this->get_name(),$this->get_id()));
    } else {
      // double check the name.
      $query = 'SELECT id FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE name = ?';
      $tmp = $db->GetOne($query,array($this->get_name()));
    }
    if( $tmp ) {
      throw new CmsInvalidDataException('Stylesheet with the same name already exists.');
    }
  }

  protected function _update()
  {
    if( !$this->_dirty ) return;
    $this->validate();

    $query = 'UPDATE '.cms_db_prefix().self::TABLENAME.'
              SET name = ?, content = ?, description = ?, media_type = ?, media_query = ?,
                  modified = ?
              WHERE id = ?';
		$tmp = '';
		if( isset($this->_data['media_type']) ) {
			$tmp = implode(',',$this->_data['media_type']);
		}
    $db = cmsms()->GetDb();
    $dbr = $db->Execute($query,
												array($this->get_name(),$this->get_content(),$this->get_description(),
															$tmp,$this->get_media_query(),time(),
															$this->get_id()));
    if( !$dbr ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }

    $query = 'DELETE FROM '.cms_db_prefix().CmsLayoutCollection::CSSTABLE.' WHERE css_id = ?';
    $dbr = $db->Execute($query,array($this->get_id()));
    if( !$dbr ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }
    $t = $this->get_designs();
    if( is_array($t) && count($t) ) {
      $query = 'INSERT INTO '.cms_db_prefix().CmsLayoutCollection::CSSTABLE.' (css_id,design_id)
                VALUES(?,?)';
      foreach( $t as $one ) {
				$dbr = $db->Execute($query,array($this->get_id(),(int)$one));
      }
    }

    CmsTemplateCache::clear_cache();
    audit($this->get_id(),'CMSMS','Stylesheet '.$this->get_name().' Updated');
    $this->_dirty = FALSE;
  }

  protected function _insert()
  {
    if( !$this->_dirty ) return;
    $this->validate();

    // insert the record
		$tmp = '';
		if( isset($this->_data['media_type']) ) {
			$tmp = implode(',',$this->_data['media_type']);
		}
    $query = 'INSERT INTO '.cms_db_prefix().self::TABLENAME.'
              (name,content,description,media_type,media_query,
               created,modified) VALUES (?,?,?,?,?,?,?)';
    $db = cmsms()->GetDb();
    $dbr = $db->Execute($query,
			array($this->get_name(),$this->get_content(),$this->get_description(),
			      $tmp,$this->get_media_query(),
			      time(),time()));
    if( !$dbr ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }
    $this->_data['id'] = $db->Insert_ID();

    $t = $this->get_designs();
    if( is_array($t) && count($t) ) {
      $query = 'INSERT INTO '.cms_db_prefix().CmsLayoutCollection::CSSTABLE.' 
                (css_id,design_id)
                VALUES(?,?)';
      foreach( $t as $one ) {
				$dbr = $db->Execute($query,array($this->get_id(),(int)$one));
				debug_display($db->sql.' -- '.$db->ErrorMsg());
      }
    }

    $this->_dirty = FALSE;
    CmsTemplateCache::clear_cache();
    audit($this->get_id(),'CMSMS','Stylesheet '.$this->get_name().' Created');
  }

  public function save()
  {
    if( $this->get_id() ) {
      return $this->_update();
    } else {
      return $this->_insert();
    }
  }

  public function delete()
  {
    if( !$this->get_id() ) return;

    $db = cmsms()->GetDb();
    $query = 'DELETE FROM '.cms_db_prefix().CmsLayoutCollection::CSSTABLE.'
              WHERE css_id = ?';
    $dbr = $db->Execute($query,array($this->get_id()));

    $query = 'DELETE FROM '.cms_db_prefix().self::TABLENAME.'
              WHERE id = ?';
    $dbr = $db->Execute($query,array($this->get_id()));

    CmsTemplateCache::clear_cache();
    audit($this->get_id(),'CMSMS','Stylesheet '.$this->get_name().' Deleted');
    unset($this->_data['id']);
    $this->_dirty = TRUE;
  }

  private static function &_load_from_data($row)
  {
    $ob = new CmsLayoutStylesheet();
		$row['media_type'] = explode(',',$row['media_type']);;
    $ob->_data = $row;
    return $ob;
  }

  public static function &load($a)
  {
    $db = cmsms()->GetDb();
    $row = null;
    if( (int)$a > 0 ) {
      $query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE id = ?';
      $row = $db->GetRow($query,array((int)$a));
    }
    else if( is_string($a) && strlen($a) > 0 ) {
      $query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE name = ?';
      $row = $db->GetRow($query,array($a));
    }
    if( !is_array($row) || count($row) == 0 ) {
      throw new CmsDataNotFoundException('Could not find template identified by '.$a);
    }

    return self::_load_from_data($row);
  }

	public static function load_bulk($ids)
	{
		for( $i = 0; $i < count($ids); $i++ ) {
			$ids[$i] = (int)$ids[$i];
		}
		$ids = array_unique($ids);

		$db = cmsms()->GetDb();
		$query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.' WHERE id IN ('
			.implode(',',$ids).')';
		$dbr = $db->GetArray($query);
		$out = array();
		if( is_array($dbr) && count($dbr) ) {
			foreach( $dbr as $row ) {
				$tmp = self::_load_from_data($row);
				if( is_object($tmp) ) {
					$out[] = self::_load_from_data($row);
				}
			}
		}

		if( count($out) ) return $out;
	}

  public static function get_all($brief = TRUE)
  {
    $db = cmsms()->GetDb();

    $query = 'SELECT id,name,media_type,media_query,media_type,created,modified FROM '.cms_db_prefix().self::TABLENAME.
             ' ORDER BY modified DESC';
    $dbr = $db->GetArray($query);

    if( is_array($dbr) && count($dbr) ) {
      $out = array();
      foreach( $dbr as $row ) {
				$out[] = self::_load_from_data($row);
      }
      return $out;
    }
  }
} // end of class

#
# EOF
#
?>