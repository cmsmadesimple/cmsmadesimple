<?php
if (!isset($gCms)) exit;
$config = $gCms->GetConfig();

//
// initialization
//
$query = null;
$article = null;
$preview = FALSE;
$articleid = (isset($params['articleid']))?$params['articleid']:-1;
$cache_id = 'nd'.md5(serialize($params));
$compile_id = 'nd'.$articleid;
$template = 'detail'.$this->GetPreference('current_detail_template');
if (isset($params['detailtemplate']))
  {
    $template = 'detail'.$params['detailtemplate'];
  }

if( $id == '_preview_' && isset($_SESSION['news_preview']) && isset($params['preview']) )
  {
    // see if our data matches.
    if( md5(serialize($_SESSION['news_preview'])) == $params['preview'] )
      {
	$fname = TMP_CACHE_LOCATION.'/'.$_SESSION['news_preview']['fname'];
	if( file_exists($fname) && (md5_file($fname) == $_SESSION['news_preview']['checksum']) )
	  {
	    $data = unserialize(file_get_contents($fname));
	    if( is_array($data) )
	      {
		// get passed data into a standard format.
		$article = new news_article;
		$article->set_linkdata($id,$params);
		news_ops::fill_article_from_formparams($article,$data,FALSE,FALSE);
		$compile_id = 'news_preview_'.time();
		$preview = TRUE;
	      }
	  }
      }
  }

if( $preview || 
    !$smarty->isCached($this->GetDatabaseResource($template),$cache_id,$compile_id) ) {
  // not cached... have to do to the work.
  if( isset($params['articleid']) && $params['articleid'] == -1 ) {
    $article = news_ops::get_latest_article();
  }
  else if( isset($params['articleid']) && (int)$params['articleid'] > 0 ) {
    $show_expired = $this->GetPreference('expired_viewable',1);
    if( isset($params['showall']) ) $show_expired = 1;
    $article = news_ops::get_article_by_id((int)$params['articleid'],TRUE,$show_expired);
  }
  if( !$article ) {
    throw new CmsError404Exception('Article '.(int)$params['articleid'].' not found, or otherwise unavailable');
    return;
  }
  $article->set_linkdata($id,$params);

  $return_url = $this->CreateReturnLink($id, isset($params['origid'])?$params['origid']:$returnid, $this->lang('news_return'));
  $smarty->assign_by_ref('return_url', $return_url);
  $smarty->assign_by_ref('entry', $article);
  
  $catName = '';
  if (isset($params['category_id'])) {
    $catName = $db->GetOne('SELECT news_category_name FROM '.cms_db_prefix() . 'module_news_categories where news_category_id=?',array($params['category_id']));		
  }
  $smarty->assign('category_name',$catName);
  unset($params['article_id']);
  $smarty->assign('category_link',$this->CreateLink($id, 'default', $returnid, $catName, $params));
  
  $smarty->assign('category_label', $this->Lang('category_label'));
  $smarty->assign('author_label', $this->Lang('author_label'));
  $smarty->assign('extra_label', $this->Lang('extra_label'));
 }

//Display template
echo $smarty->fetch($this->GetDatabaseResource($template),$cache_id,$compile_id);

# vim:ts=4 sw=4 noet
?>
