<?php

final class AdminSearch_css_slave extends AdminSearch_slave
{
  public function get_name() 
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('lbl_css_search');
  }

  public function get_description()
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('desc_css_search');
  }

  public function check_permission()
  {
    $userid = get_userid();
    return check_permission($userid,'Modify Stylesheets');
  }

  public function get_matches()
  {
    $userid = get_userid();
    $mypages = author_pages($userid);

    $db = cmsms()->GetDb();
    $query = 'SELECT *
              FROM '.cms_db_prefix().'css
              WHERE css_text LIKE ?';
    $str = '%'.$this->get_text().'%';
    $dbr = $db->GetCol($query,array($str));
    if( is_array($dbr) && count($dbr) ) {
      $output = array();
      $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

      $gcbops = cmsms()->GetGlobalContentOperations();
      foreach( $dbr as $row ) {
	$one = $row['css_id'];

	// here we could actually have a smarty template to build the description.
	$tmp = array('title'=>$row['css_name'],
		     'description'=>$row['css_name'],
		     'edit_url'=>"editcss.php{$urlext}&amp;css_id=$one");

	$output[] = $tmp;
      }

      return $output;
    }
    
  }
} // end of class

?>