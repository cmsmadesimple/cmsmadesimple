<?php

final class News_AdminSearch_slave extends AdminSearch_slave
{
  public function get_name() 
  {
    $mod = cms_utils::get_module('News');
    return $mod->Lang('lbl_adminsearch');
  }

  public function get_description()
  {
    $mod = cms_utils::get_module('News');
    return $mod->Lang('desc_adminsearch');
  }

  public function check_permission()
  {
    $userid = get_userid();
    return check_permission($userid,'Modify News');
  }

  public function get_matches()
  {
    $mod = cms_utils::get_module('News');
    if( !is_object($mod) ) return;
    $db = cmsms()->GetDb();
    // need to get the fielddefs of type textbox or textarea
    $query = 'SELECT id, name FROM '.CMS_DB_PREFIX.'module_news_fielddefs WHERE type IN (?,?)';
    $fdlist = $db->GetArray($query,array('textbox','textarea'));

    $fields = array('N.*');
    $joins = array();
    $where = array('news_title LIKE ?','news_data LIKE ?','summary LIKE ?');
    $str = '%'.$this->get_text().'%';
    $parms = array($str,$str,$str);
    
    // add in fields 
    $fieldNames = array();
    for( $i = 0; $i < count($fdlist); $i++ ) {
      $tmp = 'FV'.$i;
      //$fdid = $fdlist[$i];
      $fdid = $fdlist[$i]['id'];
      $fieldNames[$tmp] = $fdlist[$i]['name'];
      $fields[] = "$tmp.value as $tmp";
      $joins[] = 'LEFT JOIN '.CMS_DB_PREFIX."module_news_fieldvals $tmp ON N.news_id = $tmp.news_id AND $tmp.fielddef_id = $fdid";
      $where[] = "$tmp.value LIKE ?";
      $parms[] = $str;
    }

    // build the query.
    $query = 'SELECT '.implode(',',$fields).' FROM '.CMS_DB_PREFIX.'module_news N';
    if( count($joins) ) $query .= ' ' . implode(' ',$joins);
    if( count($where) ) $query .= ' WHERE '.implode(' OR ',$where);
    $query .= ' ORDER BY N.modified_date DESC';

    $this->process_query_string($query);

    $dbr = $db->GetArray($query,array($parms));
    if( is_array($dbr) && count($dbr) ) {
      // got some results.
      $output = array();
      $resultSets = array();

      if ($this->show_snippets()) {
        $fieldNames['news_title'] = $mod->lang('title');
        $fieldNames['news_data'] = $mod->lang('content');
        $fieldNames['summary'] = $mod->lang('summary');
      }

      foreach( $dbr as $row ) {

        if (!isset($resultSets[$row['news_id']])) {
          $resultSets[$row['news_id']] = $this->get_resultset($row['news_title'],AdminSearch_tools::summarize($row['summary']),$mod->create_url('m1_','editarticle','',array('articleid'=>$row['news_id'])));
        }

	      foreach( $fieldNames as $key => $value ) {
          $content = $row[$key];
          $resultSets[$row['news_id']]->count += $count = $this->get_number_of_occurrences($content);
          if ($this->show_snippets() && $count > 0) {
            $resultSets[$row['news_id']]->locations[$value] = $this->generate_snippets($content);
          }
        }
      }
      
      #processing the results
      foreach ($resultSets as $result_object) {
        $output[] = json_encode($result_object);
      }
    
      return $output;
    }
  }
} // end of class

?>
