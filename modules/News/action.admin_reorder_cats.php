<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) return;

$query = 'SELECT * FROM '.cms_db_prefix().'module_news_categories ORDER BY hierarchy';
$allcats = $db->GetArray($query);


$smarty->assign('allcats',$allcats);
echo $this->ProcessTemplate('admin_reorder_cats.tpl');

#
# EOF
#
?>