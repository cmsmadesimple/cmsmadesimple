<?php
if( !isset($gCms) ) exit();
if( !$this->CheckPermission('Approve News') ) exit();

if( !isset($params['approve']) || !isset($params['articleid']) ) {
  die('missing parameter, this should not happen');
}

$this->SetCurrentTab('articles');
$articleid = (int)$params['articleid'];
$search = cms_utils::get_search_module();
$status = '';
$uquery = "UPDATE ".CMS_DB_PREFIX."module_news SET status = ?,modified_date = NOW() WHERE news_id = ?";
switch( $params['approve'] ) {
 case 0:
   $status = 'draft';
   break;
 case 1:
   $status = 'published';
   break;
 default:
   die('unknown value for approve parameter, I do not know what to do with this');
   break;
}

// Get the record
if( is_object($search) ) {
  if( $status == 'draft' ) {
    $search->DeleteWords($this->GetName(),$articleid,'article');
  }
  else if( $status == 'published' ) {
    $query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_news WHERE news_id = ?';;
    $article = $db->GetRow($query,array($articleid));
    if( !$article ) return;

    $useexp = 0;
    $t_end = time() + 3600; // just for the math
    if( $article['end_time'] != "" ) {
      $useexp = 1;
      $t_end = $db->UnixTimeStamp($article['end_time']);
    }

    if( $t_end > time() || $this->GetPreference('expired_searchble',1) == 1 ) {
      $text = $article['news_data'] . ' ' . $article['summary'] . ' ' . $article['news_title'] . ' ' . $article['news_title'];
      $query = 'SELECT value FROM '.CMS_DB_PREFIX.'module_news_fieldvals WHERE news_id = ?';
      $flds = $db->GetArray($query,array($articleid));
      if( is_array($flds) ) {
	for( $i = 0; $i < count($flds); $i++ ) {
	  $text .= ' '.$flds[$i]['value'];
	}
      }

      $search->AddWords($this->GetName(), $articleid, 'article', $text,
			($useexp == 1 && $this->GetPreference('expired_searchable',0) == 0) ? $t_end : NULL);
    }
  }
}

$db->Execute($uquery,array($status,$articleid));
\CMSMS\HookManager::do_hook('News::NewsArticleEdited', [ 'news_id'=>$articleid, 'status'=>$status ] );
$this->SetMessage($this->Lang('msg_success'));
$this->RedirectToAdminTab();
?>