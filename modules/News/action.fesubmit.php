<?php
// calguy1000: this action is officially deprecated.

if (!isset($gCms)) exit;
$title = '';
$extra = '';
$content = '';
$summary = '';
$status = $this->GetPreference('fesubmit_status','draft');
$useexp = 1;
$startdate = time();
$postdate = time();
$ndays = (int)$this->GetPreference('expiry_interval',180);
if( $ndays <= 0 ) $ndays = 180;
$enddate = strtotime(sprintf("+%d days",$ndays), time());
$userid = get_userid(false);
$category_id = $this->GetPreference('default_category', '');
$do_send_email = false;
$do_redirect = false;

// handle the page to go to after cancel or submit.
$dest_page = $returnid;
$tmp = $this->GetPreference('fesubmit_redirect');
if( !empty($tmp) )
  {
    $manager = $gCms->GetHierarchyManager();
    $node = $manager->sureGetNodeByAlias($tmp);
    if (isset($node))
      {
	$dest_page = $node->getID();
      }
    else
      {
	$node = $manager->sureGetNodeById($tmp);
	if (isset($node))
	  {
	    $dest_page = $tmp;
	  }
      }
  }

if( $userid == '' )
  {
    $module =& $this->GetModuleInstance('FrontEndUsers');
    if( $module ) 
      {
	$userid = $module->LoggedInId();
	$userid = $userid * -1;
      }
  }

if (isset($params['category']))
  {
    $query = 'SELECT news_category_id FROM '.cms_db_prefix().'module_news_categories WHERE news_category_name = ?';
    $tmp = $db->GetOne($query,array($params['category']));
    if( $tmp ) $category_id = $tmp;
  }

if( isset( $params['cancel'] ) )
  {
    $this->RedirectContent($dest_page);
  }

if( isset( $params['submit'] ) )
  {
    if( isset($params['content']) )
      {
	$content = cms_html_entity_decode($params['content']);
      }

    if( isset($params['summary']) )
      {
	$summary = cms_html_entity_decode($params['summary']);
      }

    if( isset($params['extra']) )
      {
	$extra = cms_html_entity_decode($params['extra']);
      }

    if( isset($params['category_id']) )
      {
	$category_id = $params['category_id'];
      }

    if (isset($params['input_category']))
      {
	$category_id = $params['input_category'];
      }

    if (isset($params['postdate_Month']))
      {
	$postdate = mktime($params['postdate_Hour'], 
			   $params['postdate_Minute'], 
			   $params['postdate_Second'], 
			   $params['postdate_Month'], 
			   $params['postdate_Day'], 
			   $params['postdate_Year']);
      }

    if (isset($params['startdate_Month']))
      {
	$startdate = mktime($params['startdate_Hour'], 
			    $params['startdate_Minute'], 
			    $params['startdate_Second'], 
			    $params['startdate_Month'], 
			    $params['startdate_Day'], 
			    $params['startdate_Year']);
      }
    
    if (isset($params['enddate_Month']))
      {
	$enddate = mktime($params['enddate_Hour'], 
			  $params['enddate_Minute'], 
			  $params['enddate_Second'], 
			  $params['enddate_Month'], 
			  $params['enddate_Day'], 
			  $params['enddate_Year']);
	}

    $error = false;
    if( $startdate > $enddate )
      {
	$error = true;
	$smarty->assign('error',$this->Lang('startdatetoolate'));
      }

    if( isset($params['title'] ) )
      {
	$title = strip_tags(cms_html_entity_decode($params['title']));
      }

    if( $title == '' )
      {
	$error = true;
	$smarty->assign('error',$this->Lang('notitlegiven'));
      }

    if( $content == '' )
      {
	$error = true;
	$smarty->assign('error',$this->Lang('nocontentgiven'));
      }

    // generate a new article id
    $articleid = $db->GenID(cms_db_prefix()."module_news_seq");
	
    if( $error == false )
      {
	// test file upload custom fields
	$qu = "SELECT id,name,type FROM ".cms_db_prefix()."module_news_fielddefs 
                WHERE type='file'";
	$fields = $db->GetArray($qu);
	
	foreach( $fields as $onefield )
	  {
	    $elem = $id.'news_customfield_'.$onefield['id'];
	    if( isset($_FILES[$elem]) && 
		$_FILES[$elem]['name'] != '')
	      {
		if( $_FILES[$elem]['error'] == 0 &&
		    $_FILES[$elem]['tmp_name'] != '' )
		  {
		    $error = '';
		    $value = news_admin_ops::handle_upload($articleid,$elem,$error);
		    if( $value === FALSE )
		      {
			$error = true;
			$smarty->assign('error',$error);
		      }
		    $params['news_customfield_'.$onefield['id']] = $value;
		  }
		else
		  {
		    // error with upload
		    // abort the whole thing
		    $error = true;
		    $smarty->assign('error',$this->Lang('error_upload'));
		  }
	      }
	  }
      }

    if( $error == false )
      {
	// and generate the insert query
	$query = 'INSERT INTO '.cms_db_prefix().'module_news 
                   (news_id, news_category_id, news_title, news_data, summary,
                    news_extra, status, news_date, start_time, end_time, create_date, 
                    modified_date,author_id) 
                   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
	$dbr = $db->Execute($query, 
		     array($articleid, $category_id, $title, 
			   $content, $summary, $extra, $status, 
			   trim($db->DBTimeStamp($postdate), "'"), 
			   trim($db->DBTimeStamp($startdate), "'"), 
			   trim($db->DBTimeStamp($enddate), "'"), 
			   trim($db->DBTimeStamp(time()), "'"), 
			   trim($db->DBTimeStamp(time()), "'"), 
			   $userid));
	if( $dbr )
	  {
	    // handle the custom fields
	    $now = $db->DbTimeStamp(time());
	    $query = 'INSERT INTO '.cms_db_prefix()."module_news_fieldvals
                   (news_id, fielddef_id, value, create_date, modified_date)
                  VALUES (?,?,?,$now,$now)";
	    foreach( $params as $key => $value )
	      {
		$value = trim($value);
		if( empty($value) ) continue;
		if( preg_match('/^news_customfield_/',$key) )
		  {
		    $field_id = intval(substr($key,17));
		    $db->Execute($query,array($articleid,$field_id,$value));
		  }
	      }

	    // should've checked those errors too, but eh, I'm up for the odds.

	    //Update search index
	    $module = cms_utils::get_search_module();
	    if (is_object($module))
	      {
		$module->AddWords($this->GetName(), $articleid, 'article', $content . ' ' . $summary . ' ' . $title . ' ' . $title, $useexp == 1 ? $enddate : NULL);
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
				    'useexp' => $useexp));
	
		// put mention into the admin log
		audit('', 'News Frontend Submit', 'Article added');
		
		// and we're done
	    $smarty->assign('message',$this->Lang('articleadded'));
	  }
      }
  }


// build the category list
$categorylist = array();
$query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY hierarchy";
$dbresult = $db->Execute($query);
while ($dbresult && $row = $dbresult->FetchRow())
  {
    $categorylist[$row['long_name']] = $row['news_category_id'];
  }

// build the form
$txt =$this->CreateFrontEndFormStart($id,$returnid,'fesubmit','post','multipart/form-data');
$smarty->assign('startform',$txt);
$smarty->assign('endform',$this->CreateFormEnd());

#Display template
$smarty->assign('hidden', 
		$this->CreateInputHidden($id,'category_id',$category_id));
$smarty->assign('titletext', $this->Lang('title'));
$smarty->assign('inputtitle', $this->CreateInputText($id, 'title', $title, 30, 255));
$smarty->assign('inputcategory', 
		$this->CreateInputDropdown($id, 'input_category', $categorylist, -1, 
					   $category_id));
$smarty->assign('extratext',$this->Lang('extra'));
$smarty->assign('inputextra',$this->CreateInputText($id,'extra',$extra,30,255));
$smarty->assign('inputcontent', $this->CreateTextArea(true, $id, $content, 'content'));
$smarty->assign('hide_summary_field',$this->GetPreference('hide_summary_field','0'));
$smarty->assign('inputsummary', 
		$this->CreateTextArea($this->GetPreference('allow_summary_wysiwyg',1), $id, $summary, 'summary'));
$smarty->assign_by_ref('postdate', $postdate);
$smarty->assign('postdateprefix', $id.'postdate_');
$smarty->assign('inputexp', 
		$this->CreateInputCheckbox($id, 'useexp', '1', $useexp, 'class="pagecheckbox"'));
$smarty->assign_by_ref('startdate', $startdate);
$smarty->assign('startdateprefix', $id.'startdate_');
$smarty->assign_by_ref('enddate', $enddate);
$smarty->assign('enddateprefix', $id.'enddate_');
$smarty->assign('status',$this->CreateInputHidden($id,'status',$status));
$smarty->assign('submit', $this->CreateInputSubmit($id, 
						   'submit', 
						   $this->Lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', 
						   $this->Lang('cancel')));

$query = 'SELECT * FROM '.cms_db_prefix().'module_news_fielddefs WHERE public = 1 ORDER BY item_order';
$dbr = $db->Execute($query);
$customfields = array();
$customfieldsbyname = array();
while( $dbr && ($row = $dbr->FetchRow()) )
  {
    $obj = new StdClass();
    $obj->name = $row['name'];
    switch($row['type'])
      {
      case 'file':
	$obj->field = $this->CreateFileUploadInput($id,'news_customfield_'.$row['id']);
	break;
      case 'checkbox':
	$obj->field = $this->CreateInputCheckbox($id,'news_customfield_'.$row['id'],1);
	break;
      case 'textarea':
	$obj->field = $this->CreateTextArea(true,$id,'','news_customfield_'.$row['id']);
	break;
      case 'textbox':
	$obj->field = $this->CreateInputText($id,'news_customfield_'.$row['id'],'',$row['max_length'],$row['max_length']);
	break;
      }
    $customfields[] = $obj;
    $key = str_replace(' ','_',strtolower($row['name']));
    $customfieldsbyname[$key] = $obj;
  }
if( count($customfields) )
  {
    $smarty->assign('customfields',$customfields);
    $smarty->assign('customfieldsbyname',$customfieldsbyname);
  }

$smarty->assign('titletext', $this->Lang('title'));
$smarty->assign('summarytext', $this->Lang('summary'));
$smarty->assign('categorytext',$this->Lang('category'));
$smarty->assign('contenttext', $this->Lang('content'));
$smarty->assign('postdatetext', $this->Lang('postdate'));
$smarty->assign('useexpirationtext', $this->Lang('useexpiration'));
$smarty->assign('startdatetext', $this->Lang('startdate'));
$smarty->assign('enddatetext', $this->Lang('enddate'));
$smarty->assign('ipaddress',getenv("REMOTE_ADDR"));


$template = 'form'.$this->GetPreference('current_form_template');
if (isset($params['formtemplate']))
  {
    $template = 'form'.$params['formtemplate'];
  }
echo $this->ProcessTemplateFromDatabase($template);

if( $do_send_email == true )
  {
    // this needs to be done after the form is generated
    // because we use some of the same smarty variables
    $cmsmailer = $this->GetModuleInstance('CMSMailer');
    if( $cmsmailer )
      {
	$addy = trim($this->GetPreference('formsubmit_emailaddress'));
	if( $addy != '' )
	  {
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

if( $do_redirect )
  {
    $this->RedirectContent($dest_page);
  }
// END OF FILE
?>
