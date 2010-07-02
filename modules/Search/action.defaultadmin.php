<?php
if (!isset($gCms)) exit;
$this->Permission->check('dummy permission'); //Redirect if not logged in
//if( $this->Permission->check('Modify Templates') )
//  {
//echo '<div class="pageoverflow" style="text-align: right; width: 80%;"><a href="listmodtemplates.php" title="'.
//		$this->Lang('modify_templates').'">'.$this->Lang('modify_templates').'</a></div><br/>';
//  }


if (isset($params['reindex']))
  {
    CmsSearch::get_instance()->reindex();
    echo '<div class="pagemcontainer"><p class="pagemessage">'.$this->Lang('reindexcomplete').'</p></div>';
  }
else if (isset($params['optimize']))
  {
    CmsSearch::get_instance()->optimize();
    echo '<div class="pagemcontainer"><p class="pagemessage">'.$this->Lang('optimizecomplete').'</p></div>';
  }
else if (isset($params['clearwordcount']))
  {
    $query = 'DELETE FROM '.cms_db_prefix().'module_search_words';
    $db->Execute($query);
  }
else if (isset($params['exportcsv']) )
  {
    $query = 'SELECT * FROM '.cms_db_prefix().'module_search_words ORDER BY count DESC';
    $data = $db->GetArray($query);
    if( is_array($data) )
      {
	header('Content-Description: File Transfer');
	header('Content-Type: application/force-download');
	header('Content-Disposition: attachment; filename=search.csv');
	while(@ob_end_clean());

	$output = '';
	for( $i = 0; $i < count($data); $i++ )
	  {
	    $output .= "\"{$data[$i]['word']}\",{$data[$i]['count']}\n";
	  }
	echo $output;
	exit();
      }
    
  }
else if (isset($params['submit']))
   {
     $this->SetPreference('stopwords', $params['stopwords']);
     $this->SetPreference('searchtext', $params['searchtext']);

   }


#The tabs
if (FALSE == empty($params['active_tab']))
  {
    $tab = $params['active_tab'];
  } else {
  $tab = 'statistics';
 }
$smarty->assign('active_tab_for_modules', $tab);
include(dirname(__FILE__).'/function.admin_statistics_tab.php');


echo $this->Template->process('defaultadmin.tpl', $id, $return_id);
?>
