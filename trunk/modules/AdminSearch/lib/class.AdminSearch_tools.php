<?php

final class AdminSearch_tools 
{
  private function __construct() {}

  public static function get_slave_classes()
  {
    $key = __CLASS__.'slaves'.get_userid(FALSE);
    $results = null;
    $data =  cms_cache_handler::get_instance()->get($key);
    if( !$data ) {
      // cache needs refreshing.    
      $results = array();

      // get module results.
      $mod = cms_utils::get_module('AdminSearch');
      $modulelist = $mod->GetModulesWithCapability('AdminSearch');
      if( is_array($modulelist) && count($modulelist) ) {
	foreach( $modulelist as $module_name ) {
	  $mod = cms_utils::get_module($module_name);
	  if( !is_object($mod) ) continue;
	  if( !method_exists($mod,'get_adminsearch_slaves') ) continue;

	  $classlist = $mod->get_adminsearch_slaves();
	  if( is_array($classlist) && count($classlist) ) {
	    foreach( $classlist as $class_name ) {
	      if( !class_exists($class_name) ) continue;
	      if( !is_subclass_of($class_name,'AdminSearch_slave') ) continue;
	      $obj = new $class_name;
	      if( !is_object($obj) ) continue;
        if( !$obj->check_permission()) continue;

	      $tmp = array();
	      $tmp['module'] = $module_name;
	      $tmp['class'] = $class_name;
	      $name = $tmp['name'] = $obj->get_name();
	      $tmp['description'] = $obj->get_description();
	      $tmp['section_description'] = $obj->get_section_description();
	      if( !$name ) continue;
	      if( isset($results[$name]) ) continue;

	      $results[$name] = $tmp;
	    }
	  }
	}
      }

      // store the results into the cache.
      cms_cache_handler::get_instance()->set($key,serialize($results));
    }
    else {
      $results = unserialize($data);
    }

    return $results;
  }

  public static function summarize($text,$len = 255)
  {
    $text = strip_tags($text);
    return substr($text,0,$len);
  }
}
?>
