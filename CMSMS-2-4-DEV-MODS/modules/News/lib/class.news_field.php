<?php

// a class representing a field definition
final class news_field
{
  private $_data = array();
  private $_displayvalue;

  public function _get_data($key)
  {
    if( isset($this->_data[$key]) ) return $this->_data[$key];
  }

  public function __get($key)
  {
    $fielddefs = news_ops::get_fielddefs(FALSE);

    switch( $key ) {
    case 'alias':
      $alias = munge_string_to_url($this->name);
      return $alias;

    case 'id':
    case 'name':
    case 'type':
    case 'max_length':
    case 'create_date':
    case 'modified_date':
    case 'item_order':
    case 'public':
    case 'value':
      if( isset($this->_data[$key]) ) return $this->_data[$key];
      break;

    case 'extra':
      if( isset($this->_data['extra']) ) {
	if( !is_array($this->_data['extra']) ) $this->_data['extra'] = unserialize($this->_data['extra']);
	return $this->_data['extra'];
      }
      break;

    case 'options':
      $extra = $this->extra;
      if( is_array($extra) && isset($extra['options']) ) return $extra['options'];
      break;

    case 'displayvalue':
      if( !$this->_displayvalue ) {
	if( isset($this->_data['value']) ) {
	  $value = $this->_data['value'];
	  $this->_displayvalue = $value;
	  if( $this->type == 'dropdown' ) {
	    // dropdowns may have a different displayvalue than actual value.
	    if( is_array($this->options) && isset($this->options[$value]) ) $this->_displayvalue = $this->options[$value];
	  }
	}
      }
      return $this->_displayvalue;
      break;

    case 'fielddef_id':
      return $this->_data['id'];
    }
  }

  public function __isset($key)
  {
    switch( $key ) {
    case 'alias':
    case 'id':
    case 'name':
    case 'type':
    case 'max_length':
    case 'create_date':
    case 'modified_date':
    case 'item_order':
    case 'public':
      return TRUE;

    case 'value':
    case 'extra':
      return isset($this->_data[$key]);

    default:
      return FALSE;
    }
  }

  public function __set($key,$value)
  {
    switch( $key ) {
    case 'id':
    case 'name':
    case 'type':
    case 'max_length':
    case 'item_order':
    case 'public':
    case 'value':
    case 'extra':
      $this->_data[$key] = $value;
      break;

    case 'alias':
      throw new Exception('Attempt to set invalid data into field object: '.$key);
      break;

    case 'create_date':
    case 'modified_date':
      break;

    default:
      throw new Exception('Attempt to set invalid data into field object: '.$key);
    }
  }

  private function _validate()
  {
    if( $this->name == '' ) throw new CmsException('Invalid field definition name');
    if( $this->type == 'dropdown' && count($this->options) == 0 ) throw new CmsException('No options for dropdown field');
    if( $this->id > 0 && $this->item_order < 1 ) throw new CmsException('Invalid item order');
  }

  private function _insert()
  {
    $db = cmsms()->GetDb();
    if( $this->item_order < 1 ) {
      $query = 'SELECT MAX(item_order) FROM '.CMS_DB_PREFIX.'module_news_fielddefs';
      $num = (int)$db->GetOne($query);
      $this->item_order = $num+1;
    }
    $query = 'INSERT INTO '.CMS_DB_PREFIX."module_news_fielddefs 
              (name,type,max_length,create_date,modified_date,item_order,public,extra) 
              VALUES (?,?,?,NOW(),NOW(),?,?,?)";
    $dbr = $db->Execute($query,array($this->name,$this->type,$this->max_length,$this->item_order,$this->public,
				     serialize($this->extra)));
    $this->_data['id'] = $db->Insert_ID();
    $this->create_date = $this->modified_date = $db->DbTimeStamp(time());
  }

  private function _update()
  {
    $db = cmsms()->GetDb();
    $query = 'UPDATE '.CMS_DB_PREFIX.'module_news_fielddefs SET name = ?, type = ?, max_length = ?, modified_date = NOW(),
              item_orderr = ?, public = ?, extra = ? WHERE id = ?';
    $dbr = $db->Execute($query,array($this->name,$this->type,$this->max_length,$this->item_order,$this->public,
				     serialize($this->extra),$this->id));
    $this->modified_date = $db->DbTimeStamp(time());
  }

  public function save()
  {
    $this->_validate();
    if( $this->_data['id'] ) {
      $this->_insert();
    }
    else {
      $this->_update();
    }
  }

  public static function &load_by_id($id)
  {
    $id = (int)$id;
    if( $id < 1 ) return;

    $db = cmsms()->GetDb();
    $query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_news_fielddefs WHERE id = ?';
    $row = $db->GetRow($query,array($id));
    if( $row['extra'] ) $row['extra'] = unserialize($row['extra']);
    $obj = new news_field;
    $obj->_data = $row;
    return $obj;
  }

  public static function &load_by_name($name)
  {
    $name = trim($name);
    if( !$name ) return;

    $db = cmsms()->GetDb();
    $query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_news_fielddefs WHERE name = ?';
    $row = $db->GetRow($query,array($name));
    if( $row['extra'] ) $row['extra'] = unserialize($row['extra']);
    $obj = new news_field;
    $obj->_data = $row;
    return $obj;
  }
} // end of class

?>