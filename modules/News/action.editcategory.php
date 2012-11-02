<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Site Preferences')) return;

$this->SetCurrentTab('categories');
if (isset($params['cancel'])) {
  $this->Redirect($id, 'defaultadmin', $returnid);
}

$catid = '';
if (isset($params['catid'])) {
  $catid = $params['catid'];
}

$parentid = '-1';
if (isset($params['parent'])) {
  $parentid = (int)$params['parent'];
}

$origname = '';
if (isset($params['origname'])) {
  $origname = $params['origname'];
}

$name = '';
if (isset($params['name'])) {
  $name = trim($params['name']);
  if ($name != '') {
    $query = 'SELECT news_category_id FROM '.cms_db_prefix().'module_news_categories WHERE parent_id = ? AND news_category_name = ? AND news_category_id != ?';
    $tmp = $db->GetOne($query,array($parentid,$name,$catid));
    if( $tmp ) {
      echo $this->ShowErrors($this->Lang('error_duplicatename'));
    }
    else {
      $query = 'UPDATE '.cms_db_prefix().'module_news_categories SET news_category_name = ?, parent_id = ?, modified_date = '.$db->DBTimeStamp(time()).' WHERE news_category_id = ?';
      $parms = array($name,$parentid);
      $parms[] = $catid;
      $db->Execute($query, $parms);

      news_admin_ops::UpdateHierarchyPositions();
      @$this->SendEvent('NewsCategoryEdited', array('category_id' => $catid, 'name' => $name, 'origname' => $origname));
      // put mention into the admin log
      audit($catid, 'News category: '.$catid, ' Category edited');

      $this->SetMessage($this->Lang('categoryupdated'));
      $this->RedirectToAdminTab();
    }
  }
  else {
    echo $this->ShowErrors($this->Lang('nonamegiven'));
  }
}
else {
  $query = 'SELECT * FROM '.cms_db_prefix().'module_news_categories WHERE news_category_id = ?';
  $row = $db->GetRow($query, array($catid));
  if ($row) {
    $name = $row['news_category_name'];
    $parentid = (int)$row['parent_id'];
  }
}

#Display template
$tmp = news_ops::get_category_list();
$tmp2 = array_flip($tmp);
$categories = array(-1=>$this->Lang('none'));
foreach( $tmp2 as $k => $v ) {
  $categories[$k] = $v;
}
$parms = array('catid'=>$catid,'origname'=>$origname);
$smarty->assign('parent',$parentid);
$smarty->assign('name',$name);
$smarty->assign('categories',$categories);
$smarty->assign('startform', $this->CreateFormStart($id, 'editcategory', $returnid, 'post', '', false, '', $parms));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('nametext', $this->Lang('name'));
$smarty->assign('inputname', $this->CreateInputText($id, 'name', $name, 20, 255));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

$smarty->assign('mod',$this);
echo $this->ProcessTemplate('editcategory.tpl');
?>
