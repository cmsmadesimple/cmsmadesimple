<?php

$smarty->assign('formstart',$this->Form->form_start(array('id'=>$id,'action'=>'defaultadmin')));
$smarty->assign('formend',$this->Form->form_start(array('id'=>$id,'action'=>'defaultadmin')));
$smarty->assign('wordtext',$this->Lang('word'));
$smarty->assign('counttext',$this->Lang('count'));

$query = 'SELECT * FROM '.cms_db_prefix().'module_search_words ORDER BY count DESC';
$results = array();
$dbr = $db->SelectLimit($query,50,0);
while( $dbr && $row = $dbr->FetchRow() )
  {
    $results[] = $row;
  }
if( count($results) )
  {
    $smarty->assign('topwords',$results);
  }

$smarty->assign('statistics_tab',$this->Template->process('admin_statistics_tab.tpl', $id, $return_id));

?>
