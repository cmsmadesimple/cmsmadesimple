<?php

final class AdminSearch_oldmodtemplate_slave extends AdminSearch_slave
{
  public function get_name() 
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('lbl_oldmodtemplate_search');
  }

  public function get_description()
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('desc_oldmodtemplate_search');
  }

  public function check_permission()
  {
    return check_permission(get_userid(),'Modify Templates');
  }

  public function get_matches()
  {
    $userid = get_userid();

    $db = cmsms()->GetDb();
    $query = 'SELECT module_name,template_name,content FROM '.CMS_DB_PREFIX.'module_templates WHERE content LIKE ? OR template_name LIKE ?';
    
    $this->process_query_string($query);
    $dbr = $db->GetArray($query,array_fill(0,2,'%'.$this->get_text().'%'));
    if( is_array($dbr) && count($dbr) ) {
      $output = array();
      $resultSets = array();
      $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

      foreach( $dbr as $row ) {
	
        $resultSet = $this->get_resultset("{$row['module_name']} :: {$row['template_name']}",AdminSearch_tools::summarize($this->get_description()));

        $content = $row['template_name'];
        $resultSet->count += $count = $this->get_number_of_occurrences($content);
        if ($this->show_snippets() && $count > 0) {
            $resultSet->locations[\CmsLangOperations::lang_from_realm('admin','name')] = $this->generate_snippets($content);
        }

        $content = $row['content'];
        $resultSet->count += $count = $this->get_number_of_occurrences($content);
        if ($this->show_snippets() && $count > 0) {
            $resultSet->locations[\CmsLangOperations::lang_from_realm('admin','content')] = $this->generate_snippets($content);
        }

        $resultSets[] = $resultSet;
      }


		#processing the results
      foreach ($resultSets as $result_object) {
        $output[] = json_encode($result_object);
      }

    return $output;
    }
    
  }

  public function get_section_description()
  {
    $mod = cms_utils::get_module('AdminSearch');
    return $mod->Lang('sectiondesc_oldmodtemplates');
  }
} // end of class

?>