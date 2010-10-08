<?php

class cms_variables implements ArrayAccess
{
  private $_allowed_variables = array('content_obj','content_id','page','page_id','page_name','position','friendly_position','starttime','user_id','username','pageinfo','pluginnum','convertclass','admintheme','user_in_group','routes','content-type','module-num','modulenum','error','cms_frontend_cur_language','admin_encoding','current_encoding','formcount','mid_cache','userperms','ownerpages','authorpages','bulkcontent','handlercache','default_content_id','contenttypes','authorblobs','module_template_cache','template','content-filename');

  private static $_instance;
  private $_data = array();

  // this is a singleton.
  private function __construct()  {}

  public static function get_instance()
  {
    if (!isset(self::$_instance)) {
      $c = __CLASS__;
      self::$_instance = new $c;
    }

    return self::$_instance;
  }

  public function offsetExists($key)
  {
    return isset($this->_data[$key]);
  }

  public function offsetGet($key)
  {
    if( !in_array($key,$this->_allowed_variables) )
      {
	trigger_error('Retrival of unauthorized internal variables is deprecated: '.$key,E_USER_NOTICE);
	return;
      }
    if( isset($this->_data[$key]) )
      {
	return $this->_data[$key];
      }
  }

  public function offsetSet($key,$value)
  {
    // could do a friend thing here... or other limiting things.
    if( !in_array($key,$this->_allowed_variables) )
      {
	trigger_error('Modification of internal data is deprecated: '.$key,E_USER_NOTICE);
      }
    $this->_data[$key] = $value;
  }

  public function offsetUnset($key)
  {
    if( !in_array($key,$this->_allowed_variables) )
      {
	trigger_error('Unsetting internal variable '.$key.' is invalid',E_USER_ERROR);
      }
    if( isset($this->_data[$key]) )
      {
	unset($this->_data[$key]);
      }
  }

} // end of class

?>