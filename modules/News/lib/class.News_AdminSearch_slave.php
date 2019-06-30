<?php

if( !class_exists('AdminSearch_slave') ) {
    abstract class AdminSearch_slave extends \Adminsearch\Slaves\AbstractSlave {}
}

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
    $query = 'SELECT id FROM '.CMS_DB_PREFIX.'module_news_fielddefs WHERE type IN (?,?)';
    $fdlist = $db->GetCol($query,array('textbox','textarea'));

    $fields = array('N.*');
    $joins = array();
    $where = array('news_title LIKE ?','news_data LIKE ?','summary LIKE ?');
    $str = '%'.$this->get_text().'%';
    $parms = array($str,$str,$str);
    
    // add in fields 
    for( $i = 0; $i < count($fdlist); $i++ ) {
      $tmp = 'FV'.$i;
      $fdid = $fdlist[$i];
      $fields[] = "$tmp.value";
      $joins[] = 'LEFT JOIN '.CMS_DB_PREFIX."module_news_fieldvals $tmp ON N.news_id = $tmp.news_id AND $tmp.fielddef_id = $fdid";
      $where[] = "$tmp.value LIKE ?";
      $parms[] = $str;
    }

    // build the query.
    $query = 'SELECT '.implode(',',$fields).' FROM '.CMS_DB_PREFIX.'module_news N';
    if( count($joins) ) $query .= ' ' . implode(' ',$joins);
    if( count($where) ) $query .= ' WHERE '.implode(' OR ',$where);
    $query .= ' ORDER BY N.modified_date DESC';

    $dbr = $db->GetArray($query,array($parms));
    if( is_array($dbr) && count($dbr) ) {
      // got some results.
      $output = array();
      foreach( $dbr as $row ) {
	$text = null;
	foreach( $row as $key => $value ) {
	  // search for the keyword
	  $pos = strpos($value,$this->get_text());
	  if( $pos !== FALSE ) {
	    // build the text
	    $start = max(0,$pos - 50);
	    $end = min(strlen($value),$pos+50);
	    $text = substr($value,$start,$end-$start);
	    $text = cms_htmlentities($text);
	    $text = str_replace($this->get_text(),'<span class="search_oneresult">'.$this->get_text().'</span>',$text);
	    $text = str_replace("\r",'',$text);
	    $text = str_replace("\n",'',$text);
	    break;
	  }
	}
	$url = $mod->create_url('m1_','editarticle','',array('articleid'=>$row['news_id']));
	$tmp = array('title'=>$row['news_title'],
		     'description'=>AdminSearch_tools::summarize($row['summary']),
		     'edit_url'=>$url,'text'=>$text);
	$output[] = $tmp;
      }
      return $output;
    }
  }
} // end of class

?>
