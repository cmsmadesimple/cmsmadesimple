<?php

class microtiny_profile implements ArrayAccess
{
  private static $_keys = array('menubar','allowimages','showstatusbar','allowresize','formats','name','label','system',
                                'dfltstylesheet','allowcssoverride','allowtables');
  private static $_module;
  private $_data = array();

  public function __construct($data = null)
  {
      if( is_array($data) && count($data) ) {
          foreach( $data as $key => $value ) {
              $this[$key] = $value;
          }
      }
  }

  public function OffsetGet($key)
  {
    switch( $key ) {
    case 'menubar':
    case 'allowimages':
    case 'allowtables':
    case 'showstatusbar':
    case 'allowresize':
    case 'allowcssoverride':
    case 'system':
      if( isset($this->_data[$key]) ) return (bool)$this->_data[$key];
      break;

    case 'formats':
      if( isset($this->_data[$key]) ) return $this->_data[$key];
      break;

    case 'name':
    case 'dfltstylesheet':
      if( isset($this->_data[$key]) ) return trim($this->_data[$key]);
      break;

    case 'label':
      if( isset($this->_data[$key]) ) return $this->_data[$key];
      return $this['name'];

    default:
      throw new CmsInvalidDataException('invalid key '.$key.' for '.__CLASS__.' object');
    }
  }

  public function OffsetSet($key,$value)
  {
    switch( $key ) {
    case 'menubar':
    case 'allowtables':
    case 'allowimages':
    case 'showstatusbar':
    case 'allowresize':
    case 'allowcssoverride':
    case 'system':
      $this->_data[$key] = cms_to_bool($value);
      break;

    case 'formats':
      if( is_array($value) ) $this->_data[$key] = $value;
      break;

    case 'dfltstylesheet':
    case 'name':
    case 'label':
      $value = trim($value);
      if( $value ) $this->_data[$key] = $value;
      break;

    default:
      throw new CmsInvalidDataException('invalid key '.$key.' for '.__CLASS__.' object');
    }
  }

  public function OffsetExists($key)
  {
    switch( $key ) {
    case 'menubar':
    case 'allowtables':
    case 'allowimages':
    case 'showstatusbar':
    case 'allowresize':
    case 'allowcssoverride':
    case 'formats':
    case 'dfltstylesheet':
    case 'name':
    case 'label':
    case 'system':
      return (isset($this->_data[$key]))?TRUE:FALSE;

    default:
      throw new CmsInvalidDataException('invalid key '.$key.' for '.__CLASS__.' object');
    }
  }

  public function OffsetUnset($key)
  {
    switch( $key ) {
    case 'menubar':
    case 'allowtables':
    case 'allowimages':
    case 'showstatusbar':
    case 'allowresize':
    case 'allowcssoverride':
    case 'dfltstylesheet':
    case 'formats':
    case 'label':
      unset($this->_data[$key]);
      break;

    case 'system':
    case 'name':
      throw new CmsLogicException('Cannot unset '.$key.' for '.__CLASS__);

    default:
      throw new CmsInvalidDataException('invalid key '.$key.' for '.__CLASS__.' object');
    }
  }

  public function save()
  {
    if( !isset($this->_data['name']) || $this->_data['name'] == '' ) {
      throw new CmsInvalidDataException('Invalid microtiny profile name');
    }

    $data = serialize($this->_data);
    self::_get_module()->SetPreference('profile_'.$this->_data['name'],$data);
  }

  public function delete()
  {
      if( $this['name'] == '' ) return;
      self::_get_module()->RemovePreference('profile_'.$this['name']);
      unset($this->_data['name']);
  }

  private static function &_load_from_data($data)
  {
    if( !is_array($data) || !count($data) ) throw new CmsInvalidDataException('Invalid data passed to '.__CLASS__.'::'.__METHOD__);

    $obj = new microtiny_profile;
    foreach( $data as $key => $value ) {
      if( !in_array($key,self::$_keys) ) throw new CmsInvalidDataException('Invalid key '.$key.' for data in .'.__CLASS__);
      $obj->_data[$key] = trim($value);
    }
    return $obj;
  }

  public static function set_module(MicroTiny $module)
  {
    self::$_module = $module;
  }

  private static function &_get_module()
  {
    if( is_object(self::$_module) ) return self::$_module;
    return cms_utils::get_module('MicroTiny');
  }

  public static function &load($name)
  {
    if( $name == '' ) return;
    $data = self::_get_module()->GetPreference('profile_'.$name);
    if( !$data ) throw new CmsInvalidDataException('Unknown microtiny profile '.$name);

    $obj = new self();
    $obj->_data = unserialize($data);
    return $obj;
  }

  public static function list_all()
  {
    $prefix = 'profile_';
    return self::_get_module()->ListPreferencesByPrefix($prefix);
  }

} // end of class

?>