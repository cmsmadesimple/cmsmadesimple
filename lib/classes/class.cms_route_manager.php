<?php

class cms_route_manager
{
  private static $_routes;

  /*
  static public function load($force = false)
  {
    if( (!is_array(self::$_routes) || count(self::$_routes) == 0) && $force == false )
      {
	return TRUE;
      }

    $fn = self::get_filename();
    if(! file_exists($fn) )
      {
	return FALSE;
      }

    $data = @file($self);
    self::$_routes = array();
    foreach( $data as $line )
      {
	$obj = unserialize($line);
	if( is_object($obj) ) self::$_routes[] = $obj;
      }

    if( !count(self::$_routes) ) return FALSE;
    return TRUE;
  }

  static public function save()
  {
    if( !is_array(self::$_routes) || count(self::$_routes) == 0 )
      {
	return FALSE;
      }

    $fn = self::get_filename();
    $fp = fopen($fn,'w');
    if( !$fp ) return FALSE;
    foreach( self::$_routes as &$route )
      {
	fwrite($fp,serialize($route)."\n");
      }
    fclose($fp);
  }
  */

  static public function route_exists(CmsRoute $route)
  {
    if( !is_array(self::$_routes) ) return FALSE;

    foreach( self::$_routes as $test )
      {
	if( $test == $route ) return TRUE;
      }
    return FALSE;
  }


  static public function register(CmsRoute $route)
  {
    if( self::route_exists($route) ) return TRUE;

    if( !is_array(self::$_routes) )
      {
	self::$_routes = array();
      }
    self::$_routes[] = $route;
    return TRUE;
  }


  static public function match_str($str)
  {
    if( !is_array(self::$_routes) )
      {
	return FALSE;
      }
    
    foreach( self::$_routes as $route )
      {
	if( $route->matches($str) )
	  {
	    return $route;
	  }
      }

    return FALSE;
  }

  /*
  private static function get_filename()
  {
    return TMP_CACHE_LOCATION.'/routes.dat';
  }
  */
} // end of class


# vim:ts=4 sw=4 noet
?>