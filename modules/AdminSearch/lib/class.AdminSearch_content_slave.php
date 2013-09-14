<?php

final class AdminSearch_content_slave extends AdminSearch_slave
{
  public function get_name() 
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('lbl_content_search');
  }

  public function get_description()
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('desc_content_search');
  }

  public function check_permission()
  {
    return TRUE;
  }

  public function get_matches()
  {
    $userid = get_userid();

    $db = cmsms()->GetDb();
    $query = 'SELECT DISTINCT content_id FROM '.cms_db_prefix().'content_props WHERE content LIKE ?';
    $dbr = $db->GetCol($query,array('%'.$this->get_text().'%'));
    if( is_array($dbr) && count($dbr) ) {
      $output = array();
      $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

      foreach( $dbr as $one ) {
	if( !check_permission($userid,'Manage All Content') && 
	    !check_permission($userid,'Modify Any Page') &&
	    !cmsms()->GetContentOperations()->CheckPageAuthorship($userid,$one) ) {
	  // no access to this content page.
	  continue;
	}

	$content_obj = cmsms()->GetContentOperations()->LoadContentFromId($one);
	if( !is_object($content_obj) ) continue;

	// here we could actually have a smarty template to build the description.
	$tmp = array('title'=>$content_obj->Name(),
		     'description'=>$content_obj->Name(),
		     'edit_url'=>"editcontent.php{$urlext}&amp;content_id=$one");

	$output[] = $tmp;
      }

      return $output;
    }
    
  }
} // end of class

?>