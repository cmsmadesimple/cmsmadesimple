<?php
if (!isset($gCms)) exit;

$template = null;
if (isset($params['browsecattemplate'])) {
  $template = trim($params['browsecattemplate']);
}
else {
  $tpl = CmsLayoutTemplate::load_dflt_by_type('News::browsecat');
  if( !is_object($tpl) ) {
    audit('',$this->GetName(),'No default summary template found');
    return;
  }
  $template = $tpl->get_name();
}

$cache_id = '|ns'.md5(serialize($params));
if( !$smarty->isCached($this->GetDatabaseResource($template),$cache_id) ) {
  $items = news_ops::get_categories($id,$params,$returnid);

  // Display template
  $smarty->assign('count', count($items));
  $smarty->assign('cats', $items);
}

// Display template
echo $smarty->fetch($this->GetDatabaseResource($template),$cache_id);
# vim:ts=4 sw=4 noet
?>
