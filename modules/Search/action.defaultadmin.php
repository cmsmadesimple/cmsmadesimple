<?php
if (!isset($gCms)) exit;
$this->CheckPermission('dummy permission'); //Redirect if not logged in
if( $this->CheckPermission('Modify Templates') )
  {
echo '<div class="pageoverflow" style="text-align: right; width: 80%;"><a href="'.
		$this->CreateLink($id,'admin_templates',$returnid,
		       $this->Lang('modify_templates'),array(),'',true).'" title="'.$this->Lang('modify_templates').'">'.$this->Lang('modify_templates').'</a></div><br/>';
  }

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
echo $this->StartTabHeaders();
if (FALSE == empty($params['active_tab']))
  {
    $tab = $params['active_tab'];
  } else {
  $tab = '';
 }

if ($this->CheckPermission('Modify Site Preferences'))
  {
    echo $this->SetTabHeader('statistics',$this->Lang('statistics'), 
			     ('statistics' == $tab)?true:false);
    echo $this->SetTabHeader('options',$this->Lang('options'), ('options' == $tab)?true:false);
  }

echo $this->EndTabHeaders();



#The content of the tabs
echo $this->StartTabContent();

if ($this->CheckPermission('Modify Site Preferences'))
  {
    echo $this->StartTab('statistics',$params);
    include(dirname(__FILE__).'/function.admin_statistics_tab.php');
    echo $this->EndTab();

    echo $this->StartTab('options', $params);
    echo $this->CreateFormStart($id, 'defaultadmin');

    echo $this->CreateInputSubmit($id, 'reindex', $this->Lang('reindexallcontent'));
    echo $this->CreateInputSubmit($id, 'optimize', $this->Lang('optimizecontent'));

    echo '<hr />';

    echo '<p>'.$this->Lang('stopwords').':<br />'.$this->CreateTextArea(false, $id, str_replace(array("\r", "\n"), '', $this->GetPreference('stopwords', $this->DefaultStopWords())), 'stopwords', '', '', '', '', '50', '6').'</p>';

    echo '<p>'.$this->Lang('prompt_searchtext').':&nbsp;' . $this->CreateInputText($id,'searchtext',
									     $this->GetPreference('searchtext','')).'</p>';

    echo '<p>'.$this->Lang('prompt_alpharesults').':&nbsp;' . $this->CreateInputCheckbox($id,'alpharesults','true',$this->GetPreference('alpharesults','false')).'</p>';

    echo $this->CreateInputSubmit($id, 'submit', $this->Lang('submit'));

    echo $this->CreateFormEnd();
    echo $this->EndTab();
  }

echo $this->EndTabContent();

?>
