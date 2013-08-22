<?php

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
	if( !is_array($this->_data['extra']) ) {
	  $this->_data['extra'] = unserialize($this->_data['extra']);
	}
	return $this->_data['extra'];
      }
      break;

    case 'options':
      $extra = $this->extra;
      if( is_array($extra) && isset($extra['options']) ) {
	return $extra['options'];
      }
      break;

    case 'displayvalue':
      if( !$this->_displayvalue ) {
	if( isset($this->_data['value']) ) {
	  $value = $this->_data['value'];
	  $this->_displayvalue = $value;
	  if( $this->type == 'dropdown' ) {
	    // dropdowns may have a different displayvalue than actual value.
	    if( is_array($this->options) && isset($this->options[$value]) ) {
	      $this->_displayvalue = $this->options[$value];
	    }
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
} // end of class

?>