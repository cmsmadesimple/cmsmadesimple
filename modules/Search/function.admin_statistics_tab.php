<?php

$smarty->assign('formstart',$this->CreateFormStart($id,'defaultadmin'));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('wordtext',$this->Lang('word'));
$smarty->assign('counttext',$this->Lang('count'));
$smarty->assign('exportcsv',
		$this->CreateInputSubmit($id,'exportcsv',$this->Lang('export_to_csv')));
$smarty->assign('clearwordcount',
		$this->CreateInputSubmit($id,'clearwordcount',$this->Lang('clear'),'','',
					 $this->Lang('confirm_clearstats')));

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

echo $this->ProcessTemplate('admin_statistics_tab.tpl');

?>