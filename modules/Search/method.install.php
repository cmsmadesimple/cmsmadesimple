<?php
if (!isset($gCms)) exit;

$db =& $this->GetDb();

$db_prefix = cms_db_prefix();
$dict = NewDataDictionary($db);
$flds= "
		id I KEY,
		module_name C(100),
		content_id I,
		extra_attr C(100),
		expires " . CMS_ADODB_DT;

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_search_items', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$db->CreateSequence(cms_db_prefix()."module_search_items_seq");

$sqlarray = $dict->CreateIndexSQL('module_name', $db_prefix."module_search_items", 'module_name');
$dict->ExecuteSQLArray($sqlarray);
	
$sqlarray = $dict->CreateIndexSQL('content_id', $db_prefix."module_search_items", 'content_id');
$dict->ExecuteSQLArray($sqlarray);
	
$sqlarray = $dict->CreateIndexSQL('extra_attr', $db_prefix."module_search_items", 'extra_attr');
$dict->ExecuteSQLArray($sqlarray);

$flds= "
		item_id I,
		word C(255),
		count I
	";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_search_index', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'index_search_count', $db_prefix."module_search_index", 'count');
$dict->ExecuteSQLArray($sqlarray);

$flds = "word C(255) KEY,
         count       I
        ";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_search_words', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

# Indexes
$sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'index_search_items',
				   cms_db_prefix().'module_search_items',
				   'module_name,content_id');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'index_search_index',
				   cms_db_prefix().'module_search_index',
				   'word');
$dict->ExecuteSQLArray($sqlarray);

$this->SetPreference('stopwords', $this->DefaultStopWords());
$this->SetPreference('usestemming', 'false');
$this->SetPreference('searchtext','Enter Search...');
	
$this->SetTemplate('displaysearch', $this->GetSearchHtmlTemplate());
$this->SetTemplate('displayresult', $this->GetResultsHtmlTemplate());
	
$this->CreateEvent('SearchInitiated');
$this->CreateEvent('SearchCompleted');
$this->CreateEvent('SearchItemAdded');
$this->CreateEvent('SearchItemDeleted');
$this->CreateEvent('SearchAllItemsDeleted');
	
$this->RegisterEvents();
$this->RegisterModulePlugin(true);
$this->RegisterSmartyPlugin('search','function','function_plugin');

$this->Reindex();

?>
