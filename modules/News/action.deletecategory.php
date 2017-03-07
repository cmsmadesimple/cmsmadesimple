<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Site Preferences')) return;

$catid = '';
if (isset($params['catid'])) $catid = $params['catid'];

// Get the category details
$query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_news_categories
           WHERE news_category_id = ?';
$row = $db->GetRow( $query, array( $catid ) );

//Reset all categories using this parent to have no parent (-1)
$query = 'UPDATE '.CMS_DB_PREFIX.'module_news_categories SET parent_id=?, modified_date='.$db->DBTimeStamp(time()).' WHERE parent_id=?';
$db->Execute($query, array(-1, $catid));

//Now remove the category
$query = "DELETE FROM ".CMS_DB_PREFIX."module_news_categories WHERE news_category_id = ?";
$db->Execute($query, array($catid));

//And remove it from any articles
$query = "UPDATE ".CMS_DB_PREFIX."module_news SET news_category_id = -1 WHERE news_category_id = ?";
$db->Execute($query, array($catid));

\CMSMS\HookManager::do_hook('News::NewsCategoryDeleted', [ 'category_id'=>$catid, 'name'=>$row['news_category_name'] ] );
audit($catid, 'News category: '.$catid, ' Category deleted');

news_admin_ops::UpdateHierarchyPositions();
$params = array('tab_message'=> 'categorydeleted', 'active_tab' => 'categories');
$this->Setmessage($this->Lang('categorydeleted'));
$this->RedirectToAdminTab('categories','','admin_settings');
