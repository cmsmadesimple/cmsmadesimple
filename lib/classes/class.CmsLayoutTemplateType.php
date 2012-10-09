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
class CmsLayoutTemplateType 
{
	const TABLENAME = 'layout_tpl_type';
  private $_dirty;
  private $_data = array();

  /**
   * Get the template type id
   *
   * @return integer type id, or null if this record has no id.
   */
  public function get_id()
  {
	  if( isset($this->_data['id']) ) return $this->_data['id'];
  }


  /**
   * Get the template originator (this is usually a module name)
   *
   * @return string
   */
  public function get_originator($viewable = FALSE)
  {
	  $out = '';
	  if( isset($this->_data['originator']) ) $out = $this->_data['originator'];
	  if( $out == '__CORE__' && $viewable ) $out = 'CMSMS';
	  return $out;
  }


  /**
   * Set the template originator string.
   *
   * @param string The originator string, usually a module name.
   */
  public function set_originator($str)
  {
	  $str = trim($str);
	  if( !$str ) throw new CmsInvalidDataException('Originator cannot be empty');
	  $this->_data['originator'] = $str;
	  $this->_dirty = TRUE;
  }


  /**
   * Return the template type name.
   *
   * @return String the template type
   */
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
	  $this->_data['name'] = $str;
	  $this->_dirty = TRUE;
  }

 
  /**
   * Get the flag indicating if this template type can have a 'default'
   *
   * @return boolean
   */
  public function get_dflt_flag()
  {
	  if( isset($this->_data['has_dflt']) ) return $this->_data['has_dflt'];
  }


  /**
   * Set the flag indicating if this template type can have a 'default'
   *
   * @param boolean
   */
  public function set_dflt_flag($val = TRUE)
  {
	  if( !is_bool($val) ) throw new CmsInvalidDataException('value is invalid for set_dflt_flag');
	  $this->_data['has_dflt'] = $val;
	  $this->_dirty = TRUE;
  }

  
  /**
   * Get the default contents used when creating a new template of this type.
   *
   * @return string
   */
  public function get_dflt_contents()
  {
	  if( isset($this->_data['dflt_contents']) ) return $this->_data['dflt_contents'];
  }


  /**
   * Set the default content used when creating a new templateof this  type.
   *
   * @param string The default template contents.
   */
  public function set_dflt_contents($str)
  {
	  $this->_data['dflt_contents'] = $str;
	  $this->_dirty = TRUE;
  }


  /**
   * Get the template type description.
   *
   * @return string
   */
  public function get_description()
  {
	  if( isset($this->_data['description']) ) return $this->_data['description'];
  }


  /**
   * Set the description for this template.
   *
   * @param string The default template contents.
   */
  public function set_description($str)
  {
	  $this->_data['description'] = $str;
	  $this->_dirty = TRUE;
  }


  /**
   * Get the owner of this template type.
   *
   * @return integer
   */
  public function get_owner()
  {
	  if( isset($this->_data['owner']) ) return $this->_data['owner'];
  }


  /**
   * Set the owner of this template type
   *
   * @param integer owner
   */
  public function set_owner($owner)
  {
	  if( (int)$owner == 0 ) throw new CmsInvalidDataException('value is invalid for owner');
	  $this->_data['owner'] = (int)$owner;
	  $this->_dirty = TRUE;
  }


  /**
   * Get the date that this object was created.
   *
   * @return integerUnix timestamp representing the creation date.  or null if this object has not been saved.
   */
  public function get_create_date()
  {
	  if( isset($this->_data['created']) ) return $this->_data['created'];
  }

  /**
   * Get the date that this object was last modified
   *
   * @return integerUnix timestamp representing the modification date.  or null if this object has not been saved.
   */
  public function get_modified_date()
  {
	  if( isset($this->_data['modified']) ) return $this->_data['modified'];
  }

  /**
   * Set a callback to be used to retrieve a translated version of the originator and name strings.
   * 
   * This callback must be a static string representing a static function name, or an array 
   * representing a class name and method name.  This callback (if set) will be used to translate
   * the originator string, and the name string to something suitable to users language.
   *
   * @param mixed A static function name string, or an array of class name and member name.
   */
  public function set_lang_callback($data)
  {
	  $this->_data['lang_callback'] = $data;
	  $this->_dirty = TRUE;
  }

  /**
   * Return the callback used to translate the originator and name strings.
   *
   * @return mixed
   */
  public function get_lang_callback()
  {
	  if( isset($this->_data['lang_callback']) ) return $this->_data['lang_callback'];
  }

  /**
   * Set a callback to be used when restoring the 'default content' to system default values.
   * 
   * Modules typically distribut sample templates.  This callback function is used when the 
   * user clicks on a button to reset the selected template type to it's factory default values.
   *
   * @param mixed A static function name string, or an array of class name and member name.
   */
  public function set_content_callback($data)
  {
	  $this->_data['content_callback'] = $data;
	  $this->_dirty = TRUE;
  }

  /**
   * Return the callback used to reset a template to its factory default values.
   *
   * @return mixed
   */
  public function get_content_callback()
  {
	  if( isset($this->_data['content_callback']) ) return $this->_data['content_callback'];
  }

  /**
   * Validate the integrity of a template type object.
   *
   * This method will check the contents of the object for validity, and ensure that
   * the originator/name combination are unique.
   *
   * This method throws an exception if an error is found in the integrity of the object.
   *
   * @param boolean Wether this is a new insert, or an update.
   */
  protected function validate($is_insert = TRUE)
  {
	  if( !$this->get_originator() ) {
		  throw new CmsInvalidDataException('Invalid Type Originator');
	  }
	  if( !$this->get_name() ) {
		  throw new CmsInvalidDataException('Invalid Type Name');
	  }
//     if( !$this->get_owner() || $this->get_owner() <= 0 ) {
//       throw new CmsInvalidDataException('Invalid Owner');
//     }
	  if( !$is_insert ) {
		  if( !isset($this->_data['id']) || (int)$this->_data['id'] <= 0 ) {
			  throw new CmsInvalidDataException('id is not set');
		  }

		  // check for item with the same name
		  $db = cmsms()->GetDb();
		  $query = 'SELECT id FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE originator = ? AND name = ? AND id != ?';
		  $dbr = $db->GetOne($query,array($this->get_originator(),$this->get_name(),$this->get_id()));
		  if( $dbr ) {
			  throw new CmsInvalidDataException('Template Type with the same name already exists.');
		  }
	  }
	  else {
		  // check for item with the same name
		  $db = cmsms()->GetDb();
		  $query = 'SELECT id FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE originator = ? AND name = ?';
		  $dbr = $db->GetOne($query,array($this->get_originator(),$this->get_name()));
		  if( $dbr ) {
			  throw new CmsInvalidDataException('Template Type with the same name already exists.');
		  }
	  }
  }

  /**
   * A function to insert the current type object into the database.
   *
   * This method will ensure that the current object is valid, generate an id, and
   * insert the record into the database.  An exception will be thrown if errors occur.
   */
  protected function _insert()
  {
	  if( !$this->_dirty ) return;
	  $this->validate();
	  $db = cmsms()->GetDb();
	  $now = time();
	  $query = 'INSERT INTO '.cms_db_prefix().self::TABLENAME.'
                (originator,name,has_dflt,dflt_contents,description,
                 lang_cb,dflt_content_cb,owner,created,modified)
                VALUES (?,?,?,?,?,?,?,?,?,?)';
	  $dbr = $db->Execute($query,array($this->get_originator(),
									   $this->get_name(),
									   $this->get_dflt_flag(),
									   $this->get_dflt_contents(),
									   $this->get_description(),
									   serialize($this->get_lang_callback()),
									   serialize($this->get_content_callback()),
									   $this->get_owner(),
									   $now,$now));
	  if( !$dbr ) {
		  throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
	  }
	  $this->_data['id'] = $db->Insert_ID();
	  $this->_dirty = null;
  }


  /**
   * A function to update the contents of the database to match th current record.
   *
   * This method will ensure that the current object is valid, generate an id, and
   * update the record in the database.  An exception will be thrown if errors occur.
   */
  protected function _update()
  {
	  if( !$this->_dirty ) return;
	  $this->validate(FALSE);
	  $db = cmsms()->GetDb();
	  $now = time();

	  $query = 'UPDATE '.cms_db_prefix().self::TABLENAME.'
                SET originator = ?, name = ?, has_dflt = ?, 
                    dflt_contents = ?, description = ?,
                    lang_cb = ?, dflt_content_cb = ?, owner = ?, modified = ?
                WHERE id = ?';
	  $dbr = $db->Execute($query,array($this->get_originator(),
									   $this->get_name(),
									   $this->get_dflt_flag(),
									   $this->get_dflt_contents(),
									   $this->get_description(),
									   serialize($this->get_lang_callback()),
									   serialize($this->get_content_callback()),
									   $this->get_owner(),
									   $now,
									   $this->get_id()));
	  if( !$dbr ) {
		  throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
	  }
	  $this->_dirty = null;
  }


  /**
   * Save the current record to the database.
   */
  public function save()
  {
	  if( !$this->get_id() ) {
		  return $this->_insert();
	  }
	  return $this->_update();
  }


  /** 
   * Get a list of templates for the current template type.
   *
   * @see CmsLayoutTemplate::list_by_type
   * @return Array of CmsLayoutTemplate objects.  or null.
   */
  public function get_template_list()
  {
	  return CmsLayoutTemplate::list_by_type($this);
  }

  /**
   * Delete the current object from the database (if it has been saved).
   *
   * This method will throw exceptions on error.
   */
  public function delete()
  {
	  if( !$this->getid() ) return;
    
	  if( is_array($tmp) && count($tmp) ) {
		  throw new CmsInvalidDataException('Cannot delete a template type with existing templates');
	  }
	  $db = cmsms()->GetDb();
	  $query = 'DELETE FROM '.cms_db_prefix().self::TABLENAME.'
              WHERE id = ?';
	  $dbr = $db->Execute($query,array($this->_data['id']));
	  if( !$dbr ) {
		  throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
	  }

	  $this->_dirty = TRUE;
	  unset($this->_data['id']);
  }

  /**
   * Create a new template of ths type
   *
   * This method will throw an exception if the template cannot be created.
   *
   * @param string The template name
   * @return CmsLayoutTemplate object, or null.
   */
  public function &create_new_template($name = '')
  {
	  $ob = self::create_by_type($this);
	  if( $name ) $ob->set_name($ob);
	  return $ob;
  }

  /**
   * Get the default template of ths type
   *
   * This method will throw an exception if the template cannot be created.
   *
   * @see CmsLayoutTemplate::load_dflt_by_type()
   * @return CmsLayoutTemplate object, or null.
   */
  public function &get_dflt_template()
  {
	  return CmsLayoutTemplate::load_dflt_by_type($this);
  }

  public function get_langified_display_value()
  {
	  $t = $this->get_lang_callback();
	  $to = $tn = null;
	  if( is_callable($t) ) {
		  $to = call_user_func($t,$this->get_originator());
		  $tn = call_user_func($t,$this->get_name());
	  }
	  if( !$to ) $to = $this->get_originator();
	  if( $to == '__CORE__' ) $to = 'CMSMS';
	  if( !$tn ) $tn = $this->get_name();
	  return $to.'::'.$tn;
  }

  public function reset_content_to_factory()
  {
	  if( !$this->get_dflt_flag() ) {
		  throw new CmsException('This template type does not have default contents');
	  }
	  $cb = $this->get_content_callback();
	  if( !$cb ) 
		  throw new CmsDataNotFoundException('No callback information to reset content');
	  if( !is_callable($cb) ) 
		  throw new CmsDataNotFoundException('No callback information to reset content');
	  
	  $content = call_user_func($cb);
	  $this->set_dflt_contents($content);
  }

  private static function &_load_from_data($row)
  {
	  $row['lang_callback'] = unserialize($row['lang_cb']);
	  unset($row['lang_cb']);
	  $row['content_callback'] = unserialize($row['dflt_content_cb']);
	  unset($row['dflt_content_cb']);

	  $ob = new CmsLayoutTemplateType;
	  $ob->_data = $row;
	  $ob->_dirty = FALSE;
	  return $ob;
  }


  /**
   * Load a CmsLayoutTemplateType object from the database.
   *
   * This method throws an exception when the requested object cannot be found.
   *  
   * @param mixed An integer template type id, or a string in the form of Originator::Name
   * @return CmsLayoutTemplateType object.
  */
  public static function &load($val)
  {
	  $db = cmsms()->GetDb();
	  $row = null;
	  if( (int)$val > 0 ) {
		  $query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE id = ?';
		  $row = $db->GetRow($query,array($val));
	  }
	  elseif( strlen($val) > 0 ) {
		  $tmp = explode('::',$val);
		  if( count($tmp) == 2 ) {
			  $query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.'
                        WHERE originator = ? AND name = ?';
			  $row = $db->GetRow($query,array(trim($tmp[0]),trim($tmp[1])));
		  }
	  }
	  if( !is_array($row) || count($row) == 0 ) {
		  throw new CmsDataNotFoundException('Could not find row identified by '.$val);
	  }

	  return self::_load_from_data($row);
  }

  /**
   * Load all of the template types for a certain originator.
   *
   * This method will throw exceptions if an error is encounted.
   *
   * @param string The origiator name
   * @return An array of CmsLayoutTemplateType objects, or null if no matches are found.
   */
  public static function load_all_by_originator($originator)
  {
	  if( !$originator ) {
		  throw new CmsInvalidDataException('Orignator is empty');
	  }

	  $db = cmsms()->GetDb();
	  $query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.'
                WHERE originator = ? ORDER BY modified DESC';
	  $list = $db->GetArray($query,array($originator));
	  if( !is_array($list) || count($list) == 0 ) {
		  return;
	  }

	  $out = array();
	  foreach( $list as $row ) {
		  $out[] = self::_load_from_data($row);
	  }
	  return $out;
  }


  public static function get_all()
  {
	  $db = cmsms()->GetDb();
	  $query = 'SELECT * FROM '.cms_db_prefix().self::TABLENAME.'
                ORDER BY modified DESC';
	  $list = $db->GetArray($query);
	  if( !is_array($list) || count($list) == 0 ) {
		  return;
	  }

	  $out = array();
	  foreach( $list as $row ) {
		  $out[] = self::_load_from_data($row);
	  }
	  return $out;
  }
} // end of class

#
# EOF
3
?>