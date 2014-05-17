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
              FROM '.cms_db_prefix().CmsLayoutStylesheet::TABLENAME.'
              WHERE content LIKE ?';
    $str = '%'.$this->get_text().'%';
    $parms = array($str);
    if( $this->search_descriptions() ) {
      $query .= ' OR description LIKE ?';
      $parms[] = $str;
    }
    $dbr = $db->GetArray($query,$parms);
    if( is_array($dbr) && count($dbr) ) {
      $output = array();
      $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

      foreach( $dbr as $row ) {
	$one = $row['id'];

	// here we could actually have a smarty template to build the description.
	$mod = cms_utils::get_module('DesignManager');
	$url = $mod->create_url('m1_','admin_edit_css','',array('css'=>$one));
	$tmp = array('title'=>$row['name'],
		     'description'=>AdminSearch_tools::summarize($row['description']),
		     'edit_url'=>$url);
	$output[] = $tmp;
      }

      return $output;
    }
    
  }
} // end of class

?>
