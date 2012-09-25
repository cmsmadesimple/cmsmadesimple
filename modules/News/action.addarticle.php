<?php
if (!isset($gCms)) exit;
	$detailpage = '';

	if (!$this->CheckPermission('Modify News'))
	{
		echo $this->ShowErrors($this->Lang('needpermission', array('Modify News')));
		return;
	}

	if (isset($params['cancel']))
	{
		$this->Redirect($id, 'defaultadmin', $returnid);
	}

// Give the form id to smarty
$smarty->assign('formid',$id);

	$content = '';
	if (isset($params['content']))
	{
		$content = $params['content'];
	}

	$summary = '';
	if (isset($params['summary']))
	{
		$summary = $params['summary'];
	}

$status = 'draft';
if( $this->CheckPermission('Approve News') )
  {
    $status = 'published';
  }
	if (isset($params['status']))
	{
		$status = $params['status'];
	}

	$usedcategory = $this->GetPreference('default_category', '');
	if (isset($params['category']))
	{
		$usedcategory = $params['category'];
	}

	$postdate = time();
	if (isset($params['postdate_Month']))
	{
		$postdate = mktime($params['postdate_Hour'], $params['postdate_Minute'], $params['postdate_Second'], $params['postdate_Month'], $params['postdate_Day'], $params['postdate_Year']);
	}

	$useexp = 0;
	if (isset($params['useexp']))
	{
		$useexp = 1;
	}

        $news_url = '';
        if (isset($params['news_url']))
	  {
	    $news_url = trim($params['news_url']);
	  }

	$userid = get_userid();
	
	$startdate = time();
	if (isset($params['startdate_Month']))
	{
		$startdate = mktime($params['startdate_Hour'], $params['startdate_Minute'], $params['startdate_Second'], $params['startdate_Month'], $params['startdate_Day'], $params['startdate_Year']);
	}
$ndays = (int)$this->GetPreference('expiry_interval',180);
if( $ndays == 0 ) $ndays = 180;
$enddate = strtotime(sprintf("+%d days",$ndays), time());
	if (isset($params['enddate_Month']))
	{
		$enddate = mktime($params['enddate_Hour'], $params['enddate_Minute'], $params['enddate_Second'], $params['enddate_Month'], $params['enddate_Day'], $params['enddate_Year']);
	}

    $extra = '';
    if (isset($params['extra']))
	{
		$extra = trim($params['extra']);
	}

	$title = '';
	if (isset($params['title']))
	{
	  $title = $params['title'];
	}

if( isset($params['submit']) )
  {
    $error = FALSE;
    if( empty($title) )
      {
	$error = $this->ShowErrors($this->Lang('notitlegiven'));
      }
    else if( empty($content) )
      {
	$error = $this->ShowErrors($this->Lang('nocontentgiven'));
      }
    else if( $useexp == 1 )
      {
	if( $startdate >= $enddate )
	  {
	    $error = $this->ShowErrors($this->Lang('error_invaliddates'));
	  }
      }

    if( empty($error) && $news_url != '' )
      {
	// check for starting or ending slashes
	if( startswith($news_url,'/') || endswith($news_url,'/') )
	  {
	    $error = $this->ShowErrors($this->Lang('error_invalidurl'));
	  }

	if( $error === FALSE )
	  {
	    // check for invalid chars.
	    $translated = munge_string_to_url($news_url,false,true);
	    if( strtolower($translated) != strtolower($news_url) )
	      {
		$error = $this->ShowErrors($this->Lang('error_invalidurl'));
	      }
	  }

	if( $error === FALSE )
	  {
	    // make sure this url isn't taken.
	    $news_url = trim($news_url," /\t\r\n\0\x08");
	    cms_route_manager::load_routes();
	    $route = cms_route_manager::find_match($news_url);
	    if( $route )
	      {
		// we're adding an article, not editing... any matching route is bad.
		$error = $this->ShowErrors($this->Lang('error_invalidurl'));
	      }
	  }
      }

    //
    // database work
    //
    if( $error !== FAlSE )
      {
	echo $error;
      }
    else
      {
	$articleid = $db->GenID(cms_db_prefix()."module_news_seq");
	$query = 'INSERT INTO '.cms_db_prefix().'module_news (news_id, news_category_id, news_title, news_data, summary, status, news_date, start_time, end_time, create_date, modified_date,author_id,news_extra,news_url) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
	if ($useexp == 1)
	  {
	    $dbr = $db->Execute($query, array($articleid, $usedcategory, $title, $content, $summary, $status, trim($db->DBTimeStamp($postdate), "'"), trim($db->DBTimeStamp($startdate), "'"), trim($db->DBTimeStamp($enddate), "'"), trim($db->DBTimeStamp(time()), "'"), trim($db->DBTimeStamp(time()), "'"), $userid, $extra, $news_url));
	  }
	else
	  {
	    $dbr = $db->Execute($query, array($articleid, $usedcategory, $title, $content, $summary, $status, trim($db->DBTimeStamp($postdate), "'"), NULL, NULL, trim($db->DBTimeStamp(time()), "'"), trim($db->DBTimeStamp(time()), "'"), $userid, $extra, $news_url));
	  }
    
	if( !$dbr )
	  {
	    echo "DEBUG: SQL = ".$db->sql."<br/>";
	    die($db->ErrorMsg());
	  }
    
	//
	//Handle submitting the 'custom' fields
	//
	// get the field types
	$qu = "SELECT id,name,type FROM ".cms_db_prefix()."module_news_fielddefs 
                        WHERE type='file'";
	$types = $db->GetArray($qu);
    
	foreach( $types as $onetype )
	  {
	    $elem = $id.'customfield_'.$onetype['id'];
	    if( isset($_FILES[$elem]) && 
		$_FILES[$elem]['name'] != '' )
	      {
		if( $_FILES[$elem]['error'] != 0 ||
		    $_FILES[$elem]['tmp_name'] == '' )
		  {
		    echo $this->ShowErrors($this->Lang('error_upload'));
		    $error = true;
		  }
		else
		  {
		    $error = '';
		    $value = news_admin_ops::handle_upload($articleid,$elem,$error);
		    if( $value === FALSE )
		      {
			echo $this->ShowErrors($error);
			$error = true;
		      }
		    else 
		      {
			$params['customfield'][$onetype['id']] = $value;
		      }
		  }
	      }
	  }
    
	if( isset($params['customfield']) && !$error )
	  {
	    $now = trim($db->DBTimeStamp(time()), "'");
	    foreach( $params['customfield'] as $fldid => $value )
	      {
		if( $value == '' ) continue;
	    
		$query = "INSERT INTO ".cms_db_prefix()."module_news_fieldvals (news_id,fielddef_id,value,create_date,modified_date) VALUES (?,?,?,?,?)";
		$dbr = $db->Execute($query,
				    array($articleid,
					  $fldid,
					  $value,
					  $now,
					  $now));
		if( !$dbr )
		  {
		    die('FATAL SQL ERROR: '.$db->ErrorMsg().'<br/>QUERY: '.$db->sql);
		  }
	      }
	  } // if

	if( !$error && $status == 'published' && $news_url != '' )
	  {
	    // todo: if not expired
	    // register the route.
	    news_admin_ops::delete_static_route($articleid);
	    news_admin_ops::register_static_route($news_url,$articleid);
	  }

	if( !$error && $status == 'published' )
	  {
	    //Update search index
	    $module = cms_utils::get_search_module();
	    if (is_object($module) )
	      {
		$text = '';
		if( isset($params['customfield']) )
		  {
		    foreach( $params['customfield'] as $fldid => $value )
		      {
			if( strlen($value) > 1 )
			  $text .= $value.' ';
		      }
		  }
	    
		$text .= $content.' '.$summary.' '.$title.' '.$title;
		$module->AddWords($this->GetName(), $articleid, 'article', $text, 
			($useexp == 1 && $this->GetPreference('expired_searchable',0) == 0) ? $enddate : NULL);
	      }
	  }
	
	if( !$error )
	  {
	    @$this->SendEvent('NewsArticleAdded', array('news_id' => $articleid, 'category_id' => $usedcategory, 'title' => $title, 'content' => $content, 'summary' => $summary, 'status' => $status, 'start_time' => $startdate, 'end_time' => $enddate, 'useexp' => $useexp, 'extra' => $extra));
		// put mention into the admin log
		audit($articleid, 'News: '.$articleid, 'Article added');
	    $params = array('tab_message'=> 'articleadded', 'active_tab' => 'articles');
	    $this->Redirect($id, 'defaultadmin', $returnid, $params);
	  } // if !$error
      } // outer if !$error
  } // submit
else if( isset($params['preview']) )
  {
    // save data for preview.
    unset($params['apply']); unset($params['preview']); unset($params['submit']); unset($params['cancel']); unset($params['ajsx']);
    
    $tmpfname = tempnam(TMP_CACHE_LOCATION,$this->GetName().'_preview');
    file_put_contents($tmpfname,serialize($params));

    $detail_returnid = $this->GetPreference('detail_returnid',-1);
    if( $detail_returnid <= 0 )
      {
	// now get the default content id.
	$detail_returnid = ContentOperations::get_instance()->GetDefaultContent();
      }
    if( isset($params['previewpage']) && (int)$params['previewpage'] > 0 )
      {
	$detail_returnid = (int)$params['previewpage'];
      }
    
    $_SESSION['news_preview'] = array('fname'=>basename($tmpfname),'checksum'=>md5_file($tmpfname));
    $tparms = array('preview'=>md5(serialize($_SESSION['news_preview'])));
    if( isset($params['detailtemplate']) )
      {
	$tparms['detailtemplate'] = trim($params['detailtemplate']);
      }
    $url = $this->create_url('_preview_','detail',$detail_returnid,$tparms,TRUE);
    
    $response = '<?xml version="1.0"?>';
    $response .= '<EditArticle>';
    if( isset($error) && $error != '' )
      {
	$response .= '<Response>Error</Response>';
	$response .= '<Details><![CDATA['.$error.']]></Details>';
      }
    else
      {
	$response .= '<Response>Success</Response>';
	$response .= '<Details><![CDATA['.$url.']]></Details>';
      }
    $response .= '</EditArticle>';

    $handlers = ob_list_handlers(); 
    for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }
    header('Content-Type: text/xml');
    echo $response;
    exit;
  }


//
// build the form
//    
$statusdropdown = array();
$statusdropdown[$this->Lang('draft')] = 'draft';
$statusdropdown[$this->Lang('published')] = 'published';
    
    $categorylist = array();
    $query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY hierarchy";
    $dbresult = $db->Execute($query);
    
    while ($dbresult && $row = $dbresult->FetchRow())
      {
		$categorylist[$row['long_name']] = $row['news_category_id'];
	}

$onchangetext='onchange="document'.$id.'moduleform_1.submit()"';
	#Display template
$smarty->assign('hide_summary_field',$this->GetPreference('hide_summary_field','0'));
	$smarty->assign('authortext', '');
	$smarty->assign('inputauthor', '');
	$smarty->assign('hidden', '');
$smarty->assign('startform', $this->CreateFormStart($id, 'addarticle', $returnid,'post','multipart/form-data'));
	$smarty->assign('endform', $this->CreateFormEnd());
	$smarty->assign('titletext', $this->Lang('title'));
	$smarty->assign('inputtitle', $this->CreateInputText($id, 'title', $title, 30, 255));
	$smarty->assign('inputcontent', $this->CreateTextArea(true, $id, $content, 'content'));
$smarty->assign('inputsummary', $this->CreateTextArea($this->GetPreference('allow_summary_wysiwyg',1), $id, $summary, 'summary', '', '', '', '', '80', '3'));

$smarty->assign('extratext',$this->Lang('extra'));
$smarty->assign('inputextra',$this->CreateInputText($id,'extra',$extra,30,255));
$smarty->assign('extravalue',$extra);
$smarty->assign('urltext',$this->Lang('url'));
$smarty->assign('inputurl',$this->CreateInputText($id,'news_url',$news_url,50,255));

	$smarty->assign('postdate', $postdate);
	$smarty->assign('postdateprefix', $id.'postdate_');
$smarty->assign('useexp',$useexp);
$smarty->assign('actionid',$id);
	$smarty->assign('inputexp', $this->CreateInputCheckbox($id, 'useexp', '1', $useexp, 'class="pagecheckbox"'));
	$smarty->assign('startdate', $startdate);
	$smarty->assign('startdateprefix', $id.'startdate_');
	$smarty->assign('enddate', $enddate);
	$smarty->assign('enddateprefix', $id.'enddate_');
if( $this->CheckPermission('Approve News') )
  {
    $smarty->assign('statustext', lang('status'));
    $smarty->assign('status', $this->CreateInputDropdown($id, 'status', $statusdropdown, -1, $status));
  }
 else
   {
    $smarty->assign('status',$this->CreateInputHidden($id,'status',$status));
   }
$smarty->assign('inputcategory', $this->CreateInputDropdown($id, 'category', $categorylist, -1, $usedcategory, $onchangetext));
	$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
	$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

	$smarty->assign('titletext', $this->Lang('title'));
	$smarty->assign('categorytext', $this->Lang('category'));
	$smarty->assign('summarytext', $this->Lang('summary'));
	$smarty->assign('contenttext', $this->Lang('content'));
	$smarty->assign('postdatetext', $this->Lang('postdate'));
	$smarty->assign('useexpirationtext', $this->Lang('useexpiration'));
	$smarty->assign('startdatetext', $this->Lang('startdate'));
	$smarty->assign('enddatetext', $this->Lang('enddate'));

// Display custom fields
$query = 'SELECT * FROM '.cms_db_prefix().'module_news_fielddefs ORDER BY item_order';
$dbr = $db->Execute($query);
$custom_flds = array();

while( $dbr && ($row = $dbr->FetchRow()) )
  {
    $obj = new StdClass();
    $name = "customfield[".$row['id']."]";
	$value = isset($params['customfield'][$row['id']])&&in_array($params['customfield'][$row['id']],$params['customfield']) ? $params['customfield'][$row['id']]:'';
    $obj->prompt = $row['name'];
    switch( $row['type'] )
      {
      case 'textbox':
		$size = min(50,$row['max_length']);
		$obj->field = $this->CreateInputText($id,$name,$value,$size,$row['max_length']);
		break;
      case 'checkbox':
		$obj->field = $this->CreateInputHidden($id,$name,$value!=''?$value:'0').$this->CreateInputCheckbox($id,$name,'1',$value!=''?$value:'0');
		break;
      case 'textarea':
		$obj->field = $this->CreateTextArea(true,$id,$value,$name);
		break;
      case 'file':
		$name = "customfield_".$row['id'];
		$obj->field = $this->CreateFileUploadInput($id,$name);
		break;
      }

    $custom_flds[] = $obj;
  }
if( count($custom_flds) > 0 )
  {
    $smarty->assign('custom_fields',$custom_flds);
  }


// tab stuff.
$smarty->assign('start_tab_headers',$this->StartTabHeaders());
$smarty->assign('tabheader_article',$this->SetTabHeader('article',$this->Lang('article')));
$smarty->assign('tabheader_preview',$this->SetTabHeader('preview',$this->Lang('preview')));
$smarty->assign('end_tab_headers',$this->EndTabHeaders());

$smarty->assign('start_tab_content',$this->StartTabContent());
$smarty->assign('start_tab_article',$this->StartTab('article',$params));
$smarty->assign('end_tab_article',$this->EndTab());
$smarty->assign('start_tab_preview',$this->StartTab('preview',$params));
$smarty->assign('end_tab_preview',$this->EndTab());
$smarty->assign('end_tab_content',$this->EndTabContent());

$smarty->assign('warning_preview',$this->Lang('warning_preview'));
$contentops = cmsms()->GetContentOperations();
$smarty->assign('preview_returnid',
		$contentops->CreateHierarchyDropdown('',$this->GetPreference('detail_returnid',-1),
						     'preview_returnid'));
{
  $tmp = $this->ListTemplates();
  $tmp2 = array();
  for( $i = 0; $i < count($tmp); $i++ )
    {
      if( startswith($tmp[$i],'detail') )
	{
	  $x = substr($tmp[$i],6);
	  $tmp2[$x] = $x;
	}
    }
  $smarty->assign('prompt_detail_template',$this->Lang('detail_template'));
  $smarty->assign('prompt_detail_page',$this->Lang('detail_page'));
  $smarty->assign('detail_templates',$tmp2);
  $smarty->assign('cur_detail_template',$this->GetPreference('current_detail_template'));
}
echo $this->ProcessTemplate('editarticle.tpl');
?>
