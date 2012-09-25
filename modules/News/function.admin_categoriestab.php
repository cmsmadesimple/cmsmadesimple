<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) return;
	
	#Put together a list of current categories...
	$entryarray = array();
	
	$query = "SELECT * FROM ".cms_db_prefix()."module_news_categories ORDER BY hierarchy";
	$dbresult = $db->Execute($query);
	
	$rowclass = 'row1';
	
	while ($dbresult && $row = $dbresult->FetchRow())
	{
		$onerow = new stdClass();
	
		$depth = count(preg_split('/\./', $row['hierarchy']));
	
		$onerow->id = $row['news_category_id'];
		$onerow->name = str_repeat('&nbsp;&gt;&nbsp;', $depth-1).$this->CreateLink($id, 'editcategory', $returnid, $row['news_category_name'], array('catid'=>$row['news_category_id']));
	
		$onerow->editlink = $this->CreateLink($id, 'editcategory', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/edit.gif', $this->Lang('edit'),'','','systemicon'), array('catid'=>$row['news_category_id']));
		$onerow->deletelink = $this->CreateLink($id, 'deletecategory', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif', $this->Lang('delete'),'','','systemicon'), array('catid'=>$row['news_category_id']), $this->Lang('areyousure'));
	
		$onerow->rowclass = $rowclass;

		$entryarray[] = $onerow;
	
		($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
	}
	
	$smarty->assign_by_ref('items', $entryarray);
	$smarty->assign('itemcount', count($entryarray));
	
	#Setup links
	$smarty->assign('addlink', $this->CreateLink($id, 'addcategory', $returnid, $this->Lang('addcategory'), array(), '', false, false, 'class="pageoptions"'));
	$smarty->assign('addlink', $this->CreateLink($id, 'addcategory', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/newfolder.gif', $this->Lang('addcategory'),'','','systemicon'), array(), '', false, false, '') .' '. $this->CreateLink($id, 'addcategory', $returnid, $this->Lang('addcategory'), array(), '', false, false, 'class="pageoptions"'));
	
	$smarty->assign('categorytext', $this->Lang('category'));
	
	#Display template
	echo $this->ProcessTemplate('categorylist.tpl');
	
// EOF
?>