<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
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
class CmsSmartyTemplate
{
  private $_dirty;
  private $_data = array();
  private $_addt_editors;
  private static $_obj_cache;
  private static $_name_cache;

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

  public function get_type_id()
  {
    if( isset($this->_data['type_id']) ) return $this->_data['type_id'];
  }

  public function set_type($a)
  {
    $n = null;
    if( is_object($a) && is_a($a,'CmsTemplateType') ) {
      $n = $a->get_id();
    }
    else if( (is_string($a) && strlen($a)) || (int)$a > 0 ) {
      $cat = CmsTemplateType::load($a);
      $n = $cat->get_id();
    }
    $this->_data['type_id'] = (int) $n;
    $this->_dirty = TRUE;
  }

  public function get_type_dflt()
  {
    if( isset($this->_data['type_dflt']) ) return $this->_data['type_dflt'];
  }

  public function set_type_dflt($n)
  {
    $n = (bool)$n;
    $this->_data['type_dflt'] = $n;
    $this->_dirty = TRUE;
  }

  public function get_category_id()
  {
    if( isset($this->_data['category_id']) ) return $this->_data['category_id'];
  }

  public function &get_category()
  {
    $n = $this->get_category_id();
    if( $n > 0 ) {
      return CmsTemplateCategory::load($n);
    }
  }

  public function set_category($a)
  {
    $n = null;
    if( is_object($a) && is_a($a,'CmsTemplateCategory') ) {
      $n = $a->get_id();
    }
    else if( (is_string($a) && strlen($a)) || (int)$a > 0 ) {
      $cat = CmsTemplateCategory::load($a);
      $n = $cat->get_id();
    }
    $this->_data['category_id'] = (int) $n;
    $this->_dirty = TRUE;
  }

  public function get_theme_id()
  {
    if( isset($this->_data['theme_id']) ) return $this->_data['theme_id'];
  }

  public function set_theme($a)
  {
    $n = null;
    if( is_object($a) && is_a($a,'CmsLayoutTheme') ) {
      $n = $a->get_id();
    }
    else if( (is_string($a) && strlen($a)) || (int)$a > 0 ) {
      $theme = CmsLayoutTheme::load($a);
      $n = $theme->get_id();
    }
    $this->_data['theme_id'] = (int) $n;
    $this->_dirty = TRUE;
  }

  public function get_owner_id()
  {
    if( isset($this->_data['owner_id']) ) return $this->_data['owner_id'];
  }

  public function set_owner($a)
  {
    $n = null;
    if( (int)$a > 0 ) {
      $n = (int)$a;
    }
    else if( is_string($a) ) {
      // load the user by name.
      $ops = cmsms()->GetUserOperations();
      $ob = $ops->LoadUserByUsername($a);
      if( is_object($a) && is_a($a,'User') ) {
	$n = $a->id;
      }
    }
    else if( is_object($a) && is_a($a,'User') ) {
      $n = $a->id;
    }
    if( $n < 0 ) throw new CmsInvalidDataException('Owner id must be valid.');
    $this->_data['owner_id'] = $n;
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

  public function get_additional_editors()
  {
    if( is_null($this->_addt_editors) ) {
      if( $this->get_id() ) {
	$db = cmsms()->GetDb();
	$query = 'SELECT user_id FROM '.cms_db_prefix().'template_addt_users
                WHERE tpl_id = ?';
	$col = $db->GetCol($query,array($this->get_id()));
	$this->_addt_editors = array();
	if( count($col) ) {
	  $this->_addt_editors = $col;
	}
      }
    }
    if( count($this->_addt_editors) ) {
      return $this->_addt_editors;
    }
  }

  private static function _resolve_user($a)
  {
    if( (int)$a > 0 ) return $a;
    if( is_string($a) ) {
      $ops = cmsms()->GetUserOperations();
      $ob = $ops->LoadUserByUsername($a);
      if( is_object($a) && is_a($a,'User') ) {
	return $a->id;
      }
    }
    if( is_object($a) && is_a($a,'User') ) {
      return $a->id;
    }
    throw new CmsInvalidDataException('Could not resolve '.$a.' to a user id');
  }

  public function set_additional_editors($a)
  {
    if( !is_array($a) ) {
      // maybe a single value...
      $res = self::_resolve_user($a);
      $this->_addt_editors = array($res);
      $this->_dirty = TRUE;
    }
    else {
      $tmp = array();
      for( $i = 0; $i < count($a); $i++ ) {
	$tmp[] = self::_resolve_user($a[$i]);
      }
      $this->_addt_editors = $tmp;
      $this->_dirty = TRUE;
    }
  }

  public function can_edit($a)
  {
    $res = self::_resolve_user($a);
    if( $res == $this->get_owner_id() ) return TRUE;
    if( is_array($this->_addt_editors) && count($this->_addt_editors) &&
	!in_array($res,$this->_addt_editors) ) return TRUE;
    return FALSE;
  }

  public function process()
  {
    $smarty = cmsms()->GetSmarty();
    return $smarty->fetch('cms_smarty_template:id='.$this->get_id());
  }

  protected function validate()
  {
    if( !$this->get_name() ) {
      throw new CmsInvalidDataException('Each template must have a name');
    }
    if( !$this->get_content() ) {
      throw new CmsInvalidDataException('Each template must have some content');
    }
    if( $this->get_type_id() <= 0 ) {
      throw new CmsInvalidDataException('Each template must be associated with a type');
    }
	if( $this->get_theme_id() <= 0 ) {
      throw new CmsInvalidDataException('Each template must be associated with a type');
	}

    $db = cmsms()->GetDb();
    $tmp = null;
    if( $this->get_id() ) {
      // double check the name.
      $query = 'SELECT id FROM '.cms_db_prefix().'templates_list
                WHERE name = ? AND id != ?';
      $tmp = $db->GetOne($query,array($this->get_name(),$this->get_id()));
    } else {
      // double check the name.
      $query = 'SELECT id FROM '.cms_db_prefix().'templates_list
                WHERE name = ?';
      $tmp = $db->GetOne($query,array($this->get_name()));
    }
    if( $tmp ) {
      throw new CmsInvalidDataException('Template with the same name already exists.');
    }
  }

  protected function _update()
  {
    if( !$this->_dirty ) return;
    $this->validate();

    $query = 'UPDATE '.cms_db_prefix().'template_list
              SET name = ?, content = ?, description = ?, type_id = ?, type_dflt = ?,
                  category_id = ?, theme_id = ?, owner_id = ?, modified = ?
              WHERE id = ?';
    $dbr = $db->Execute($query,
			array($this->get_name(),$this->get_content(),$this->get_description(),
			      $this->get_type_id(),$this->get_type_dflt(),$this->get_category_id(),
			      $this->get_theme_id(),$this->get_owner_id(),time));
    if( !$dbr ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }
    if( $this->get_type_dflt() ) {
      // if it's default for a type, unset default flag for all other records with this type
      $query = 'UPDATE '.cms_db_prefix().'template_list SET type_dflt = 0
                WHERE type_id = ? AND type_dflt = 1 AND id != ?';
      $dbr = $db->Execute($query,array($this->get_type_id(),$this->get_id()));
      if( !$dbr ) {
	throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
      }
    }

    $query = 'DELETE FROM '.cms_db_prefix().'templates_addt_users WHERE tpl_id = ?';
    $dbr = $db->Execute($query,array($tpl->get_id()));
    if( !$dbr ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }
    
    $t = $this->get_additional_editors();
    if( is_array($t) && count($t) ) {
      $query = 'INSERT INTO '.cms_db_prefix().'template_addt_users (tpl_id,user_id)
                VALUES(?,?)';
      foreach( $t as $one ) {
	$dbr = $db->Execute($query,array($this->get_id(),(int)$one));
      }
    }
    $this->_dirty = FALSE;
  }

  protected function _insert()
  {
    if( !$this->_dirty ) return;
    $this->validate();

    // insert the record
    $query = 'INSERT INTO '.cms_db_prefix().'template_list
              (name,content,description,type_id,type_dflt,category_id,theme_id,owner_id,
               created,modified) VALUES (?,?,?,?,?,?,?,?,?,?)';
    $db = cmsms()->GetDb();
    $dbr = $db->Execute($query,
			array($this->get_name(),$this->get_content(),$this->get_description(),
			      $this->get_type_id(),$this->get_type_dflt(),$this->get_category_id(),
			      $this->get_theme_id(),$this->get_owner_id(),time(),time()));
    if( !$dbr ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }
    $this->_data['id'] = $db>Insert_ID();
    if( $this->get_type_dflt() ) {
      // if it's default for a type, unset default flag for all other records with this type
      $query = 'UPDATE '.cms_db_prefix().'template_list SET type_dflt = 0
                WHERE type_id = ? AND type_dflt = 1 AND id != ?';
      $dbr = $db->Execute($query,array($this->get_type_id(),$this->get_id()));
      if( !$dbr ) {
	throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
      }
    }
    $t = $this->get_additional_editors();
    if( is_array($t) && count($t) ) {
      $query = 'INSERT INTO '.cms_db_prefix().'template_addt_users (tpl_id,user_id)
                VALUES(?,?)';
      foreach( $t as $one ) {
	$dbr = $db->Execute($query,array($this->get_id(),(int)$one));
      }
    }
    $this->_dirty = FALSE;
  }

  public function save()
  {
    if( $this->get_id() ) {
      return $this->_update();
    } else {
      return $this->_insert();
    }
  }

  private static function _load_from_data($row)
  {
    $ob = new CmsSmartyTemplate();
    $ob->_data = $row;
    
    self::$_obj_cache[$ob->get_id()] = $ob;
    self::$_name_cache[$ob->get_name()] = $ob->get_id();
    
    return $ob;
  }

  protected static function _load_bulk($list)
  {
    if( !is_array($list) || count($list) == 0 ) return;

    $list2 = array();
    foreach( $list as $one ) {
      $one = (int)$one;
      if( $one <= 0 ) continue;
      if( isset(self::$_obj_cache[$one]) ) continue;
      $list2[] = $one;
    }

    // get the data and populate the cache.
    $query = 'SELECT * FROM '.cms_db_prefix().'template_list WHERE id IN ('.
      implode(',',$list2).')';
    $db = cmsms()->GetDb();
    $dbr = $db->GetArray($query);
    if( is_array($dbr) && count($dbr) ) {
      foreach( $dbr as $row ) {
	$ob = self::_load_from_data($row);
      }
    }

    // pull what we can from the cache
    $out = array();
    foreach( $list as $one ) {
      if( isset(self::$_cache[$one]) ) {
	$out[] = self::$_cache[$one];
      }
    }
    return $out;
  }

  public static function &load($a)
  {
	  $db = cmsms()->GetDb();
	  $row = null;
	  if( (int)$a > 0 ) {
		  if( isset(self::$_obj_cache[$a]) ) {
			  return self::$_obj_cache[$a];
		  }
		  $query = 'SELECT * FROM '.cms_db_prefix().'template_list
                    WHERE id = ?';
		  $row = $db->GetRow($query,array((int)$a));
	  }
	  else if( is_string($a) && strlen($a) == 0 ) {
		  if( isset(self::$_name_cache[$a]) ) {
			  $n = self::$_name_cache[$a];
			  return self::$_obj_cache[$n];
		  }

		  $query = 'SELECT * FROM '.cms_db_prefix().'template_list
                    WHERE name = ?';
		  $row = $db->GetRow($query,array($a));
	  }
	  if( !is_array($row) || count($row) == 0 ) {
		  throw new CmsDataNotFoundException('Could not find row identified by '.$val);
	  }

	  return self::_load_from_data($row);
  }


  public static function get_owned_templates($a)
  {
    $n = self::_resolve_user($a);
    if( $n <= 0 ) {
      throw new CmsInvalidDataException('Invalid user specified to get_owned_templates');
    }
    
    $db = cmsms()->GetDb();
    $query = 'SELECT id FROM '.cms_db_prefix().'template_list WHERE owner_id = ?';
    $tmp = $db->GetCol($query,array($n));
    if( !is_array($tmp) || count($tmp) == 0 ) return;

    return self::_load_bulk($tmp);
  }

  public static function get_editable_templates($a)
  {
    $n = self::_resolve_user($a);
    if( $n <= 0 ) {
      throw new CmsInvalidDataException('Invalid user specified to get_owned_templates');
    }
    
    $db = cmsms()->GetDb();
    $query = 'SELECT id FROM '.cms_db_prefix().'template_list WHERE owner_id = ?';
    $tmp1 = $db->GetCol($query,array($n));

    $query = 'SELECT tpl_id FROM '.cms_db_prefix().'template_addt_users WHERE user_id = ?';
    $tmp2 = $db->GetCol($query,array($n));

    if( is_array($tmp1) && is_array($tmp2) ) {
      $tmp = array_merge($tmp1,$tmp2);
    } else if( is_array($tmp1) ) {
      $tmp = $tmp1;
    } else if( is_array($tmp2) ) {
      $tmp = $tmp2;
    }

    if( is_array($tmp) && count($tmp) ) {
      $tmp = array_unique($tmp);
      if( is_array($tmp) && count($tmp) ) {
	return self::_load_bulk($tmp);
      }
    }
  }

  public static function &create_by_type($t)
  {
    $t2 = null;
    if( is_int($t) || is_string($t) ) {
      $t2 = CmsTemplateType::load($t);
    }
    else if( is_object($t) && is_a($t,'CmsTemplateType') ) {
      $t2 = t;
    }

    if( !$t2 ) {
      throw new CmsInvalidDataException('Invalid data passed to CmsSmartyTemplate::create_by_type()');
    }
    
    $tpl = new CmsSmartyTemplate;
    $tpl->set_name('New Template');
    $tpl->set_type($t2);
    $tpl->set_content($t2->get_dflt_contents());
    return $tpl;
  }

  public static function load_dflt_by_type($t)
  {
    $t2 = null;
    if( is_int($t) || is_string($t) ) {
      $t2 = CmsTemplateType::load($t);
    }
    else if( is_object($t) && is_a($t,'CmsTemplateType') ) {
      $t2 = t;
    }

    if( !$t2 ) {
      throw new CmsInvalidDataException('Invalid data passed to CmsSmartyTemplate::;load_dflt_by_type()');
    }

    $db = cmsms()->GetDb();
    $query = 'SELECT * FROM '.cms_db_prefix().'template_list WHERE type_id = ? AND type_dflt = ?';
    $tmp = $db->GetRow($query,array($t2->get_id(),1));
    if( !is_array($tmp) ) {
      throw new CmsDataNotFoundException('Could not find row identified by '.$val);
    }

    return self::_load_from_data($row);
  }

  public static function process_by_name($name)
  {
    $smarty = cmsms()->GetSmarty();
    return $smarty->fetch('cms_smarty_template:name='.$this->get_name());
  }

  public static function process_dflt($t)
  {
    $tpl = self::load_dflt_by_type($t);
    return $smarty->fetch('cms_smarty_template:id='.$tpl->get_id());
  }
} // end of class

#
# EOF
#
?>