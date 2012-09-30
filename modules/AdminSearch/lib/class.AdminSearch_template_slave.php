<?php

final class AdminSearch_template_slave extends AdminSearch_slave
{
  public function get_name() 
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('lbl_template_search');
  }

  public function get_description()
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('desc_template_search');
  }

  public function check_permission()
  {
    $userid = get_userid();
    return check_permission($userid,'Modify Templates');
  }

  public function get_matches()
  {
    debug_to_log('adminsearch template slave');
    $userid = get_userid();
    $mypages = author_pages($userid);

    $db = cmsms()->GetDb();
    $query = 'SELECT *
              FROM '.cms_db_prefix().'templates
              WHERE template_name LIKE ?
              OR template_content LIKE ?';
    $str = '%'.$this->get_text().'%';
    $dbr = $db->GetArray($query,array($str,$str));
    debug_to_log($db->sql);
    debug_to_log($dbr);
    if( is_array($dbr) && count($dbr) ) {
      $output = array();
      $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

      $gcbops = cmsms()->GetGlobalContentOperations();
      foreach( $dbr as $row ) {
	$one = $row['template_id'];
	// here we could actually have a smarty template to build the description.
	$tmp = array('title'=>$row['template_name'],
		     'description'=>$row['template_name'],
		     'edit_url'=>"edittemplate.php{$urlext}&amp;template_id=$one");
	debug_to_log($tmp,'result');
	$output[] = $tmp;
      }

      return $output;
    }
    
  }
} // end of class

?>