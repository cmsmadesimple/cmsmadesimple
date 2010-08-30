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
    return get_site_preference('content_autocreate_urls',0);
  }


  /**
   * A utility function to test if the supplied url path is valid for the supplied content id
   *
   * @param string The partial url path to test
   * @return boolean
   */ 
  public static function is_valid_url($url,$content_id = '')
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

    cms_route_manager::load_routes();
    $route = cms_route_manager::find_match($url);
    if( !$route ) return TRUE;
    if( $route->is_content() )

      {
	if($content_id == '' || ($route->get_content() == $content_id))
	  {
	    return TRUE;
	  }
      }
    return FALSE;
  }
} // end of class

?>