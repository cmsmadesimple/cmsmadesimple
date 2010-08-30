<?php

class content_assistant
{
  /**
   * A utility function to test if we are allowed to auto create url paths
   *
   * @return boolean
   */
  public static function auto_create_url()
  {
    global $gCms;
    if( isset($gCms->config['auto_create_url']) && $gCms->config['auto_create_url'] != 'none' )
      {
	return strtolower($gCms->config['auto_create_url']);
      }
    return FALSE;
  }


  /**
   * A utility function to test if the supplied url path is valid for the supplied content id
   *
   * @param string The partial url path to test
   * @param integer The associated content id.  Use a negative value for new content objects.
   * @return boolean
   */ 
  public static function is_valid_url($url,$content_id)
  {
    // check for starting or ending slashes
    if( startswith($url,'/') || endswith($url,'/') )
      {
	return FALSE;
      }

    // first check for invalid chars.
    $translated = munge_string_to_url($url,false,true);
    if( strtolower($translated) != strtolower($url) )
      {
	return FALSE;
      }

    // force modules to register their routes.
    global $gCms;
    global $CMS_ADMIN_PAGE;
    $flag = false;
    if( isset($CMS_ADMIN_PAGE) )
      {
	// hack to force modules to register their routes.
	$flag = $CMS_ADMIN_PAGE;
	unset($CMS_ADMIN_PAGE);
      }

    foreach( $gCms->modules as $name => $data )
      {
	if( $name && isset($data['object'])  )
	  {
	    $module =& $data['object'];
	    $module->SetParameters();
	  }
      }

    if( $flag )
      {
	$CMS_ADMIN_PAGE = $flag;
      }

    // force content to register routes.
    $contentops = $gCms->GetContentOperations();
    $contentops->register_routes();

    $route = cms_route_manager::match_str($url);
    if( is_object($route) ) return FALSE;

    return TRUE;
  }
} // end of class

?>