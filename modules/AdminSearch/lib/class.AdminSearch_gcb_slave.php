<?php

final class AdminSearch_gcb_slave extends AdminSearch_slave
{
  public function get_name() 
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('lbl_gcb_search');
  }

  public function get_description()
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('desc_gcb_search');
  }

  public function check_permission()
  {
    return TRUE;
  }

  public function get_matches()
  {
    $userid = get_userid();
    $mypages = author_pages($userid);

    $db = cmsms()->GetDb();
    $query = 'SELECT *
              FROM '.cms_db_prefix().'htmlblobs
              WHERE html LIKE ?
              OR description LIKE ?';
    $str = '%'.$this->get_text().'%';
    $dbr = $db->GetCol($query,array($str,$str));
    if( is_array($dbr) && count($dbr) ) {
      $output = array();
      $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

      $gcbops = cmsms()->GetGlobalContentOperations();
      foreach( $dbr as $row ) {
	$one = $row['htmlblob_id'];
	if( !check_permission('Modify Global Content Blocks') &&
	    !$gcbops->CheckOwnership($one,$userid) &&
	    !$gcbops->CheckAuthorship($one,$userid) ) {
	  // no access to this blob.
	  continue;
	}

	// here we could actually have a smarty template to build the description.
	$tmp = array('title'=>$row['htmlblob_name'],
		     'description'=>$row['htmlblob_description'],
		     'edit_url'=>"editcontent.php{$urlext}&amp;htmlblob_id=$one");

	$output[] = $tmp;
      }

      return $output;
    }
    
  }
} // end of class

?>