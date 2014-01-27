<?php
if (!isset($gCms)) exit;

$template = null;
if (isset($params['summarytemplate'])) {
  $template = trim($params['summarytemplate']);
}
else {
  $tpl = CmsLayoutTemplate::load_dflt_by_type('News::summary');
  if( !is_object($tpl) ) {
    audit('',$this->GetName(),'No default summary template found');
    return;
  }
  $template = $tpl->get_name();
}

$cache_id = '|ns'.md5(serialize($params));
if( !$smarty->isCached($this->GetDatabaseResource($template),$cache_id) ) {
  $detailpage = '';
  $tmp = $this->GetPreference('detail_returnid',-1);
  if( $tmp > 0 ) $detailpage = $tmp;
  if (isset($params['detailpage'])) {
    $manager = $gCms->GetHierarchyManager();
    $node = $manager->sureGetNodeByAlias($params['detailpage']);
    if (isset($node)) {
      $detailpage = $node->getID();
    }
    else {
      $node = $manager->sureGetNodeById($params['detailpage']);
      if (isset($node)) $detailpage = $params['detailpage'];
    }
    $params['detailpage'] = $detailpage;
  }
  if (isset($params['browsecat']) && $params['browsecat']==1) {
    $this->DoAction('browsecat', $id, $params, $returnid);
    return;
  }

  $entryarray = array();
  $query1 = "
            SELECT 
                mn.*, 
                mnc.news_category_name, 
                mnc.long_name, 
                u.username, 
                u.first_name,
                u.last_name 
            FROM " . 
    cms_db_prefix() . "module_news mn
            LEFT OUTER JOIN " . cms_db_prefix() . "module_news_categories mnc 
            ON mnc.news_category_id = mn.news_category_id 
            LEFT OUTER JOIN " . cms_db_prefix() . "users u 
            ON u.user_id = mn.author_id 
            WHERE 
                status = 'published' 
            AND
        ";

  $query2 = "
            SELECT count(mn.news_id) as count
            FROM " .
    cms_db_prefix() . "module_news mn
            LEFT OUTER JOIN " . cms_db_prefix() . "module_news_categories mnc 
            ON mnc.news_category_id = mn.news_category_id 
            LEFT OUTER JOIN " . cms_db_prefix() . "users u 
            ON u.user_id = mn.author_id 
            WHERE 
                status = 'published' 
            AND
        ";

  if( isset($params['idlist']) ) {
    $idlist = $params['idlist'];
    if( is_string($idlist) ) {
      $tmp = explode(',',$idlist);
      for( $i = 0; $i < count($tmp); $i++ ) {
	$tmp[$i] = (int)$tmp[$i];
	if( $tmp[$i] < 1 ) unset($tmp[$i]);
      }
      $idlist = array_unique($tmp);
      $query1 .= ' (mn.news_id IN ('.implode(',',$idlist).')) AND ';
      $query2 .= ' (mn.news_id IN ('.implode(',',$idlist).')) AND ';
    }
  }

  if( isset($params['category_id']) ) {
    $query1 .= " ( mnc.news_category_id = '".(int)$params['category_id']."' ) AND ";
    $query2 .= " ( mnc.news_category_id = '".(int)$params['category_id']."' ) AND ";
  }
  else if (isset($params["category"]) && $params["category"] != '') {
    $category = cms_html_entity_decode(trim($params['category']));
    $categories = explode(',', $category);
    $query1 .= " (";
    $query2 .= " (";
    $count = 0;
    foreach ($categories as $onecat) {
      if ($count > 0) {
	$query1 .= ' OR ';
	$query2 .= ' OR ';
      }
      if (strpos($onecat, '|') !== FALSE || strpos($onecat, '*') !== FALSE) {
	$tmp = $db->qstr(trim(str_replace('*', '%', str_replace("'",'_',$onecat))));
	$query1 .= "upper(mnc.long_name) like upper({$tmp})";
	$query2 .= "upper(mnc.long_name) like upper({$tmp})";
      }
      else {
	$tmp = $db->qstr(trim(str_replace("'",'_',$onecat)));
	$query1 .= "mnc.news_category_name = {$tmp}";
	$query2 .= "mnc.news_category_name = {$tmp}";
      }
      $count++;
    }
    $query1 .= ") AND ";
    $query2 .= ") AND ";
  }
  
  if( isset($params['showall']) ) {
    // show everything irrespective of end date.
    $query1 .= 'IF(start_time IS NULL,news_date <= NOW(),start_time <= NOW())';
    $query2 .= 'IF(start_time IS NULL,news_date <= NOW(),start_time <= NOW())';
  }
  else {
    // we're concerned about start time, end time, and news_date
    if( isset($params['showarchive']) ) {
      // show only expired entries.
      $query1 .= 'IF(end_time IS NULL,0,end_time < NOw())';
      $query2 .= 'IF(end_time IS NULL,0,end_time < NOw())';
    }
    else {
      $query1 .= 'IF(start_time IS NULL AND end_time IS NULL,news_date <= NOW(),NOw() BETWEEN start_time AND end_time)';
      $query2 .= 'IF(start_time IS NULL AND end_time IS NULL,news_date <= NOW(),NOw() BETWEEN start_time AND end_time)';
    }
  }

  $sortrandom = false;
  $sortby = trim(get_parameter_value($params,'sortby','news_date'));
  switch( $sortby ) {
    case 'news_category':
      $query1 .= "ORDER BY 'long_name', 'news_date' ";
      break;

    case 'random':
      $query1 .= "ORDER BY RAND() ";
      $sortrandom = true;
      break;

    case 'summary':
    case 'news_data':
    case 'news_category':
    case 'news_title':
    case 'end_time':
    case 'start_time':
    case 'news_extra':
      $query1 .= "ORDER BY mn.".trim($params['sortby']);
      break;

    default:
      $query1 .= "ORDER BY mn.news_date ";
      break;
  }

  if( $sortrandom == false ) {
    if (isset($params['sortasc']) && (strtolower($params['sortasc']) == 'true')) {
      $query1 .= "asc"; 
    }
    else {
      $query1 .= "desc"; 
    }
  }

  $pagelimit = 100000;
  if( isset( $params['pagelimit'] ) ) {
    $pagelimit = intval($params['pagelimit']);
  }
  else if( isset( $params['number'] ) ) {
    $pagelimit = intval($params['number']);
  }
  $pagelimit = max(2,$pagelimit);

  // Get the number of rows (so we can determine the numer of pages)
  $pagecount = -1;
  $startelement = 0;
  $pagenumber = 1;
  {
    // get the total number of items that match the query
    // and determine a number of pages
    $row2 = $db->GetRow($query2);
    $count = intval($row2['count']);
    if( isset( $params['start'] ) ) $count -= (int)$params['start'];
    $pagecount = (int)($count / $pagelimit);
    if( ($count % $pagelimit) != 0 ) $pagecount++;
  }
  if( isset( $params['pagenumber'] ) && $params['pagenumber'] != '' ) {
    // if given a page number, determine a start element
    $pagenumber = (int)$params['pagenumber'];
    $startelement = ($pagenumber-1) * $pagelimit;
  }
  if( isset( $params['start'] ) ) {
    // given a start element, determine a page number
    $startelement = $startelement + (int)$params['start'];
  }

  // Assign some pagination variables to smarty
  if( $pagenumber == 1 ) {
    $smarty->assign('prevpage',$this->Lang('prevpage'));
    $smarty->assign('firstpage',$this->Lang('firstpage'));
  }
  else {
    $params['pagenumber']=$pagenumber-1;
    $smarty->assign('prevpage',$this->CreateFrontendLink($id,$returnid,'default',$this->Lang('prevpage'),$params));
    $smarty->assign('prevurl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
    $params['pagenumber']=1;
    $smarty->assign('firstpage',$this->CreateFrontendLink($id,$returnid,'default',$this->Lang('firstpage'),$params));
    $smarty->assign('firsturl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
  }
  
  if( $pagenumber >= $pagecount ) {
    $smarty->assign('nextpage',$this->Lang('nextpage'));
    $smarty->assign('lastpage',$this->Lang('lastpage'));
  }
  else {
    $params['pagenumber']=$pagenumber+1;
    $smarty->assign('nextpage',$this->CreateFrontendLink($id,$returnid,'default',$this->Lang('nextpage'),$params));
    $smarty->assign('nexturl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
    $params['pagenumber']=$pagecount;
    $smarty->assign('lastpage',$this->CreateFrontendLink($id,$returnid,'default',$this->Lang('lastpage'),$params));
    $smarty->assign('lasturl',$this->CreateFrontendLink($id,$returnid,'default','',$params, '', true));
  }
  $smarty->assign('pagenumber',$pagenumber);
  $smarty->assign('pagecount',$pagecount);
  $smarty->assign('oftext',$this->Lang('prompt_of'));
  $smarty->assign('pagetext',$this->Lang('prompt_page'));
  
  $dbresult = '';
  if( $pagelimit < 100000 || $startelement > 0 ) {
    $dbresult = $db->SelectLimit( $query1,$pagelimit,$startelement);
  }
  else {
    $dbresult = $db->Execute($query1);
  }
  
  {
    // build a list of news id's so we can preload stuff from other tables.
    $result_ids = array();
    while( $dbresult && !$dbresult->EOF ) {
      $result_ids[] = $dbresult->fields['news_id'];
      $dbresult->MoveNext();
    }
    $dbresult->MoveFirst();
    news_ops::preloadFieldData($result_ids);
  }

  while( $dbresult && !$dbresult->EOF ) {
    $row = $dbresult->fields;
    $onerow = new stdClass();
    
    $onerow->author_id = $row['author_id'];
    if( $onerow->author_id > 0 ) {
      $onerow->author = $row['username'];
      $onerow->authorname = $row['first_name'].' '.$row['last_name'];
    }
    else if( $onerow->author_id == 0 ) {
      $onerow->author = $this->Lang('anonymous');
      $onerow->authorname = $this->Lang('unknown');
    }
    else {
      $feu = $this->GetModuleInstance('FrontEndUsers');
      if( $feu ) {
	$uinfo = $feu->GetUserInfo($onerow->author_id * -1);
	if( $uinfo[0] ) $onerow->author = $uinfo[1]['username'];
      }
    }
    $onerow->id = $row['news_id'];
    $onerow->title = $row['news_title'];
    $onerow->content = $row['news_data'];
    $onerow->summary = (trim($row['summary'])!='<br/>'?$row['summary']:'');
    $onerow->postdate = $row['news_date'];
    if( FALSE == empty($row['news_extra']) ) $onerow->extra = $row['news_extra'];
    $onerow->postdate = $row['news_date'];
    $onerow->startdate = $row['start_time'];
    $onerow->enddate = $row['end_time'];
    $onerow->create_date = $row['create_date'];
    $onerow->modified_date = $row['modified_date'];
    $onerow->category = $row['news_category_name'];

    //
    // Handle the custom fields
    //
    $onerow->fields = news_ops::get_fields($row['news_id'],TRUE);
    $onerow->fieldsbyname = $onerow->fields; // dumb, I know.
    $onerow->file_location = $gCms->config['uploads_url'].'/news/id'.$row['news_id'];
    
    $moretext = isset($params['moretext'])?$params['moretext']:$this->Lang('more');
    $sendtodetail = array('articleid'=>$row['news_id']);
    if (isset($params['showall'])) $sendtodetail['showall'] = $params['showall'];
    if (isset($params['detailpage'])) $sendtodetail['origid'] = $returnid;
    if (isset($params['detailtemplate'])) $sendtodetail['detailtemplate'] = $params['detailtemplate'];
    
    $prettyurl = $row['news_url'];
    if( $prettyurl == '' ) {
      $aliased_title = munge_string_to_url($row['news_title']);
      $prettyurl = 'news/'.$row['news_id'].'/'.($detailpage!=''?$detailpage:$returnid)."/$aliased_title";
      if (isset($sendtodetail['detailtemplate'])) $prettyurl .= '/d,' . $sendtodetail['detailtemplate'];
    }

    if (isset($params['lang'])) $sendtodetail['lang'] = $params['lang'];
    if (isset($params['category_id'])) $sendtodetail['category_id'] = $params['category_id'];
    if (isset($params['pagelimit'])) $sendtodetail['pagelimit'] = $params['pagelimit'];

    $onerow->link = $this->CreateLink($id, 'detail', $detailpage!=''?$detailpage:$returnid, '', $sendtodetail,'', true, false, '', true, 
				      $prettyurl);
    $onerow->titlelink = $this->CreateLink($id, 'detail', $detailpage!=''?$detailpage:$returnid, $row['news_title'], $sendtodetail, '', 
					   false, false, '', true, $prettyurl);
    $onerow->morelink = $this->CreateLink($id, 'detail', $detailpage!=''?$detailpage:$returnid, $moretext, $sendtodetail, '', false, 
					  false, '', true, $prettyurl);
    $onerow->moreurl = $this->CreateLink($id, 'detail', $detailpage!=''?$detailpage:$returnid, $moretext, $sendtodetail, '', true, false, '', 
					 true, $prettyurl);

    $entryarray[]= $onerow;
    $dbresult->MoveNext();
  }

  $smarty->assign('itemcount', count($entryarray));
  $smarty->assign('items', $entryarray);
  $smarty->assign('category_label', $this->Lang('category_label'));
  $smarty->assign('author_label', $this->Lang('author_label'));

  foreach( $params as $key => $value ) {
    if( $key == 'mact' || $key == 'action' ) continue;

    $smarty->assign('param_'.$key,$value);
  }

  $catName = '';
  if (isset($params['category'])) {
    $catName = $params['category'];
  }
  else if (isset($params['category_id'])) {
    $catName = $db->GetOne('SELECT news_category_name FROM '.cms_db_prefix() . 'module_news_categories where news_category_id=?',array($params['category_id']));		
  }
  $smarty->assign('category_name',$catName);

  unset($params['pagenumber']);
  $items = news_ops::get_categories($id,$params,$returnid);
  $smarty->assign('count', count($items));
  $smarty->assign('cats', $items);
}

// Display template
echo $smarty->fetch($this->GetDatabaseResource($template),$cache_id);
# vim:ts=4 sw=4 noet
?>