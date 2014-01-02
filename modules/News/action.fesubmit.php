<?php
// calguy1000: this action is officially deprecated.
if (!isset($gCms)) exit;


$title = '';
$extra = '';
$content = '';
$summary = '';
$status = $this->GetPreference('fesubmit_status','draft');
$startdate = time();
$ndays = (int)$this->GetPreference('expiry_interval',180);
if( $ndays <= 0 ) $ndays = 180;
$enddate = strtotime(sprintf("+%d days",$ndays), time());
$userid = get_userid(false);
$category_id = $this->GetPreference('default_category', '');
$do_send_email = false;
$do_redirect = false;

// handle the page to go to after submit.
$dest_page = $returnid;
$tmp = $this->GetPreference('fesubmit_redirect');
if( !empty($tmp) ) {
  $manager = $gCms->GetHierarchyManager();
  $node = $manager->sureGetNodeByAlias($tmp);
  if (isset($node)) {
    $dest_page = $node->getID();
  }
  else {
    $node = $manager->sureGetNodeById($tmp);
    if (isset($node)) $dest_page = $tmp;
  }
}

if( $userid == '' ) {
  // not logged in to the admin console
  // see if we're logged into FEU.
  $module = $this->GetModuleInstance('FrontEndUsers');
  if( $module ) {
    $userid = $module->LoggedInId();
    $userid = $userid * -1;
  }
}

if (isset($params['category'])) {
  $query = 'SELECT news_category_id FROM '.cms_db_prefix().'module_news_categories WHERE news_category_name = ?';
  $tmp = $db->GetOne($query,array($params['category']));
  if( $tmp ) $category_id = $tmp;
}

if( isset( $params['submit'] ) ) {
  try {
    if( isset($params['content']) ) $content = cms_html_entity_decode(trim($params['content']));
    if( isset($params['summary']) ) $summary = cms_html_entity_decode(trim($params['summary']));
    if( isset($params['extra']) ) $extra = cms_html_entity_decode(trim($params['extra']));
    if( isset($params['category_id']) ) $category_id = (int)$params['category_id'];
    if( isset($params['input_category'])) $category_id = (int)$params['input_category'];

    if (isset($params['startdate_Month'])) {
      $startdate = mktime($params['startdate_Hour'], $params['startdate_Minute'], $params['startdate_Second'], 
			  $params['startdate_Month'], $params['startdate_Day'], $params['startdate_Year']);
    }
    
    if (isset($params['enddate_Month'])) {
      $enddate = mktime($params['enddate_Hour'], $params['enddate_Minute'], $params['enddate_Second'], 
			$params['enddate_Month'], $params['enddate_Day'], $params['enddate_Year']);
    }

    if( $startdate > $enddate ) throw new CmsException($this->Lang('startdatetoolate'));

    if( isset($params['title'] ) ) $title = strip_tags(cms_html_entity_decode(trim($params['title'])));
    if( $title == '' ) throw new CmsException($this->Lang('notitlegiven'));
    if( $content == '' ) throw new CmsException($this->Lang('nocontentgiven'));

    // generate a new article id
    $articleid = $db->GenID(cms_db_prefix()."module_news_seq");

    // test file upload custom fields
    $qu = "SELECT id,name,type FROM ".cms_db_prefix()."module_news_fielddefs WHERE type='file'";
    $fields = $db->GetArray($qu);

    foreach( $fields as $onefield ) {
      $elem = $id.'news_customfield_'.$onefield['id'];
      if( isset($_FILES[$elem]) && $_FILES[$elem]['name'] != '') {
	if( $_FILES[$elem]['error'] == 0 && $_FILES[$elem]['tmp_name'] != '' ) {
	  $error = '';
	  $value = news_admin_ops::handle_upload($articleid,$elem,$error);
	  if( $value === FALSE ) throw new CmsException($error);
	  $params['news_customfield_'.$onefield['id']] = $value;
	}
	else {
	  // error with upload
	  // abort the whole thing
	  throw new CmsException($this->Lang('error_upload'));
	}
      }
    }

    // and generate the insert query
    // note: there's no option for fesubmit wether it's searchable or not.
    $query = 'INSERT INTO '.cms_db_prefix().'module_news 
              (news_id, news_category_id, news_title, news_data, summary,
               news_extra, status, news_date, start_time, end_time, create_date, 
               modified_date,author_id,searchable) 
               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    $dbr = $db->Execute($query, 
			array($articleid, $category_id, $title, 
			      $content, $summary, $extra, $status, 
			      trim($db->DBTimeStamp($startdate), "'"), 
			      trim($db->DBTimeStamp($startdate), "'"), 
			      trim($db->DBTimeStamp($enddate), "'"), 
			      trim($db->DBTimeStamp(time()), "'"), 
			      trim($db->DBTimeStamp(time()), "'"), 
			      $userid,1));

    if( $dbr ) {
      // handle the custom fields
      $now = $db->DbTimeStamp(time());
      $query = 'INSERT INTO '.cms_db_prefix()."module_news_fieldvals (news_id, fielddef_id, value, create_date, modified_date)
                VALUES (?,?,?,$now,$now)";
      foreach( $params as $key => $value ) {
	$value = trim($value);
	if( empty($value) ) continue;
	if( preg_match('/^news_customfield_/',$key) ) {
	  $field_id = intval(substr($key,17));
	  $db->Execute($query,array($articleid,$field_id,$value));
	}
      }

      // should've checked those errors too, but eh, I'm up for the odds.

      //Update search index
      $module = cms_utils::get_search_module();
      if (is_object($module)) {
	$module->AddWords($this->GetName(), $articleid, 'article', $content . ' ' . $summary . ' ' . $title . ' ' . $title, $enddate );
      }

      // Send an email
      $do_send_email = true;
      $do_redirect = true;

      // send an event
      @$this->SendEvent('NewsArticleAdded', 
			array('news_id' => $articleid, 
			      'category_id' => $category_id, 
			      'title' => $title, 
			      'content' => $content, 
			      'summary' => $summary, 
			      'status' => $status, 
			      'start_time' => $startdate, 
			      'end_time' => $enddate, 
			      'useexp' => 1));

      // put mention into the admin log
      audit('', 'News Frontend Submit', 'Article added');

      // and we're done
      $smarty->assign('message',$this->Lang('articleadded'));
    }
  }
  catch( Exception $e ) {
    $smarty->assign('error',$error);
  }
}


// build the category list
$categorylist = array();
$query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY hierarchy";
$dbresult = $db->Execute($query);
while ($dbresult && $row = $dbresult->FetchRow()) {
  $categorylist[$row['long_name']] = $row['news_category_id'];
}

// Display template
$smarty->assign('category_id',$category_id);
$smarty->assign('title',$title);
$smarty->assign('categorylist',$categorylist);
$smarty->assign('extra',$extra);
$smarty->assign('content',$content);
$smarty->assign('summary',$summary);
$smarty->assign('hide_summary_field',$this->GetPreference('hide_summary_field','0'));
$smarty->assign('allow_summary_wysiwyg',$this->GetPreference('allow_summary_wysiwyg',1));
$smarty->assign('postdate', $postdate);
$smarty->assign('startdate', $startdate);
$smarty->assign('enddate', $enddate);
$smarty->assign('status',$this->CreateInputHidden($id,'status',$status));

$query = 'SELECT * FROM '.cms_db_prefix().'module_news_fielddefs WHERE public = 1 ORDER BY item_order';
$dbr = $db->Execute($query);
$customfields = array();
$customfieldsbyname = array();
while( $dbr && ($row = $dbr->FetchRow()) ) {
  $obj = new StdClass();
  $obj->name = $row['name'];
  $obj->type = $row['type'];
  $obj->id = $row['id'];
  $obj->max_length = $row['max_length'];
  $key = str_replace(' ','_',strtolower($row['name']));
  $customfieldsbyname[$key] = $obj;
}
if( count($customfields) ) $smarty->assign('customfields',$customfieldsbyname);

$template = null;
if (isset($params['formtemplate'])) {
  $template = trim($params['formtemplate']);
}
else {
  $tpl = CmsLayoutTemplate::load_dflt_by_type('News::form');
  if( !is_object($tpl) ) {
    audit('',$this->GetName(),'No default form template found');
    return;
  }
  $template = $tpl->get_name();
}
echo $smarty->fetch($this->GetDatabaseResource($template));

if( $do_send_email == true ) {
  // this needs to be done after the form is generated
  // because we use some of the same smarty variables
  $cmsmailer = $this->GetModuleInstance('CMSMailer');
  if( $cmsmailer ) {
    $addy = trim($this->GetPreference('formsubmit_emailaddress'));
    if( $addy != '' ) {
      if( $title != '' ) $smarty->assign('title',$title);
      if( $summary != '' ) $smarty->assign('summary',$summary);
      if( $content != '' ) $smarty->assign('content',$content);

      $cmsmailer->AddAddress( $addy );
      $cmsmailer->SetSubject( $this->GetPreference('email_subject',$this->Lang('subject_newnews')));
      $cmsmailer->IsHTML( false );

      $body = $this->ProcessTemplateFromDatabase('email_template');
      $cmsmailer->SetBody( $body );
      $cmsmailer->Send();
    }
  }
}

if( $do_redirect ) $this->RedirectContent($dest_page);

// END OF FILE
?>
