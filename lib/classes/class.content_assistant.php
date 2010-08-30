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
    if( isset($gCms->config['auto_create_url']) && $gCms->config['auto_create_url'] == true )
      {
	return TRUE;
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
    // first check for invalid chars.
    $translated = munge_string_to_url($url,false,true);
    if( strtolower($translated) != strtolower($url) )
      {
	return FALSE;
      }

    // now check to see if this URL is used.
    global $gCms;
    $db = $gCms->GetDb();
    $query = '';
    $parms = array($url);
    if( $content_id > 0 )
      {
	$query = 'SELECT content_id FROM '.cms_db_prefix().'content
                   WHERE url = ? AND content_id != ?';
	$parms[] = $content_id;
      }
    else
      {
	$query = 'SELECT content_id FROM '.cms_db_prefix().'content
                   WHERE url = ?';
      }
    $tmp = $db->GetOne($query,$parms);
    if( $tmp )
      {
	return FALSE;
      }

    // not used by content... check modules.
    foreach( $gCms->modules as $name => $data )
      {
	if( $name  && isset($data['object'])  )
	  {
	    $module =& $data['object'];
	    if( $module->IsValidRoute($url) )
	      {
		return FALSE;
	      }
	  }
      }
    return TRUE;
  }
} // end of class

?>