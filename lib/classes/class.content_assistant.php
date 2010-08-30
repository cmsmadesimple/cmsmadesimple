<?php

class content_assistant
{
  public static function auto_create_url()
  {
    return 1; // todo... fix me.
    return get_site_preference('content_auto_create_url',0);
  }

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

    return TRUE;
  }
} // end of class

?>