<?php

final class AdminSearch_tools 
{
  private function __construct() {}

  public static function get_slave_classes()
  {
    $cachefn = cms_join_path(TMP_CACHE_LOCATION,'c'.md5(get_class()));
    if( !file_exists($cachefn) || filemtime($cachefn) < time() - 3600 ) {
      // cache file needs refreshing.
      
      $results = array();

      // get module results.
      $mod = cms_utils::get_module('AdminSearch');
      $modulelist = $mod->GetModulesWithCapability('AdminSearch');
      debug_to_log($modulelist);
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

	      $tmp = array();
	      $tmp['module'] = $module_name;
	      $tmp['class'] = $class_name;
	      $name = $tmp['name'] = $obj->get_name();
	      $tmp['description'] = $obj->get_description();
	      if( !$name ) continue;
	      if( isset($results[$name]) ) continue;

	      $results[$name] = $tmp;
	    }
	  }
	}
      }

      // store the results into the cache.
      file_put_contents($cachefn,serialize($results));
    }

    $data = file_get_contents($cachefn);
    if( $data ) {
      return unserialize($data);
    }
  }
}
?>