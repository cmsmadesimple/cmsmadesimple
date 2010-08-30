<?php

class cms_content_cache
{
  private static $_alias_map;
  private static $_id_map;
  private static $_content_cache;


  private static function &get_content_obj($hash)
  {
    $res = null;
    if( self::$_content_cache ) 
      {
	if( isset(self::$_content_cache[$hash]) )
	  {
	    $res = self::$_content_cache[$hash];
	  }
      }
    return $res;
  }


  public static function &get_content($identifier)
  {
    $res = null;
    $hash = self::content_exists($identifier);
    if( $hash === FALSE )
      {
	return $res;
      }

    return self::get_content_obj($hash);
  }


  public static function content_exists($identifier)
  {
    if( !self::$_content_cache ) return FALSE;

    if( is_numeric($identifier) )
      {
	if( !self::$_id_map ) return FALSE;
	if( !isset(self::$_id_map[$identifier]) ) return FALSE;
	return self::$_id_map[$identifier];
      }
    else if( is_string($identifier) )
      {
	if( !self::$_alias_map ) return FALSE;
	if( !isset(self::$_alias_map[$identifier]) ) return FALSE;
	return self::$_alias_map[$identifier];
      }
    return FALSE;
  }

  
  public static function add_content($id,$alias,ContentBase& $obj)
  {
    if( !$id || !$alias ) return FALSE;
    if( !self::$_alias_map ) self::$_alias_map = array();
    if( !self::$_id_map ) self::$_id_map = array();
    if( !self::$_content_cache ) self::$_content_cache = array();
    
    $hash = md5($id.$alias);
    self::$_content_cache[$hash] = $obj;
    self::$_alias_map[$alias] = $hash;
    self::$_id_map[$id] = $hash;
    return TRUE;
  }


  public static function clear()
  {
    self::$_content_cache = null;
    self::$_alias_map = null;
    self::$_id_map = null;
  }


  public static function get_loaded_page_ids()
  {
    return array_keys(self::$_id_map);
  }


  public static function get_id_from_alias($alias)
  {
    if( !isset(self::$_alias_map) ) return FALSE;
    if( !isset(self::$_alias_map[$alias]) ) return FALSE;
    $hash = self::$_alias_map[$alias];
    return array_search($hash,self::$_id_map);
  }

  public static function get_alias_from_id($id)
  {
    if( !isset(self::$_id_map) ) return FALSE;
    if( !isset(self::$_id_map[$id]) ) return FALSE;
    $hash = self::$_id_map[$id];
    return array_search($hash,self::$_alias_map);
  }
}

?>