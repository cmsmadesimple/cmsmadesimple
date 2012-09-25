<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) return;

$entryarray = array();
	
$max = $db->GetOne("SELECT max(item_order) as max_item_order FROM ".cms_db_prefix()."module_news_fielddefs");

$usedfields = array();
$tmp = $db->GetArray('SELECT DISTINCT fielddef_id FROM '.cms_db_prefix().'module_news_fieldvals');
if( is_array($tmp) )
  {
    foreach( $tmp as $row )
      {
	$usedfields[] = $row['fielddef_id'];
      }
  }

$query = "SELECT * FROM ".cms_db_prefix()."module_news_fielddefs ORDER BY item_order";
$dbresult = $db->Execute($query);

$rowclass = 'row1';

while ($dbresult && $row = $dbresult->FetchRow())
  {
    $onerow = new stdClass();
    
    $onerow->id = $row['id'];
    $onerow->name = $this->CreateLink($id, 'admin_editfielddef', $returnid, $row['name'], array('fdid'=>$row['id']));
    $onerow->type = $this->Lang($row['type']);
    $onerow->max_length = $row['max_length'];
    $onerow->item_order = $row['item_order'];
    
    if ($onerow->item_order > 1)
      {
	$onerow->uplink = $this->CreateLink($id, 'admin_movefielddef', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/arrow-u.gif', $this->Lang('up'),'','','systemicon'), array('fdid'=>$row['id'], 'dir'=>'up'));
      }
    else
      {
	$onerow->uplink = '';
      }
    if ($max > $onerow->item_order)
      {
	$onerow->downlink = $this->CreateLink($id, 'admin_movefielddef', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/arrow-d.gif', $this->Lang('down'),'','','systemicon'), array('fdid'=>$row['id'], 'dir'=>'down'));
      }
    else
      {
	$onerow->downlink = '';
      }
    
    $onerow->editlink = $this->CreateLink($id, 'admin_editfielddef', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/edit.gif', $this->Lang('edit'),'','','systemicon'), array('fdid'=>$row['id']));

    if( !in_array($row['id'],$usedfields) )
      {
	$onerow->deletelink = $this->CreateLink($id, 'admin_deletefielddef', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif', $this->Lang('delete'),'','','systemicon'), array('fdid'=>$row['id']), $this->Lang('areyousure'));
      }
    
    $entryarray[] = $onerow;
    
    ($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
  }

$smarty->assign_by_ref('items', $entryarray);
$smarty->assign('itemcount', count($entryarray));

$smarty->assign('addlink', $this->CreateLink($id, 'admin_addfielddef', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/newfolder.gif', $this->Lang('addfielddef'),'','','systemicon'), array(), '', false, false, '') .' '. $this->CreateLink($id, 'admin_addfielddef', $returnid, $this->Lang('addfielddef'), array(), '', false, false, 'class="pageoptions"'));

$smarty->assign('fielddeftext', $this->Lang('fielddef'));
$smarty->assign('typetext', $this->Lang('type'));

#Display template
echo $this->ProcessTemplate('customfieldstab.tpl');

// EOF
?>