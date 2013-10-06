<?php
if (!isset($gCms)) exit;
$db =& $this->GetDb();
$dict = NewDataDictionary($db);

if ($oldversion == '1.0')
{
	$this->CreateEvent('SearchInitiated');
	$this->CreateEvent('SearchCompleted');
	$this->CreateEvent('SearchItemAdded');
	$this->CreateEvent('SearchItemDeleted');
	$this->CreateEvent('SearchAllItemsDeleted');

	$this->RegisterEvents();

	$oldversion = '1.0.1';
}

if ($oldversion == '1.0.1')
{
	$this->SetTemplate('displaysearch', $this->GetSearchHtmlTemplate());
	$this->SetTemplate('displayresult', $this->GetResultsHtmlTemplate());

	$oldversion = '1.0.2';
}

if ($oldversion == '1.0.2')
{
	$this->AddEventHandler('Core','ModuleUninstalled',false);
	$this->SetPreference('searchtext','Enter Search...');
	$oldversion = '1.0.3';
}

if ($oldversion == '1.0.3')
{
	$oldversion = '1.0.4';
}

if ($oldversion == '1.0.4')
{
	$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_search_items');
	$dict->ExecuteSQLArray($sqlarray);
	$db->DropSequence(cms_db_prefix().'module_search_items_seq');	
	
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

	$sqlarray = $dict->CreateIndexSQL('module_name', cms_db_prefix()."module_search_items", 'module_name');
	$dict->ExecuteSQLArray($sqlarray);

	$sqlarray = $dict->CreateIndexSQL('content_id', cms_db_prefix()."module_search_items", 'content_id');
	$dict->ExecuteSQLArray($sqlarray);

	$sqlarray = $dict->CreateIndexSQL('extra_attr', cms_db_prefix()."module_search_items", 'extra_attr');
	$dict->ExecuteSQLArray($sqlarray);


	$sqlarray = $dict->DropTableSQL(cms_db_prefix().'module_search_index');
	$dict->ExecuteSQLArray($sqlarray);

	$flds= "
			item_id I,
			word C(255),
			count I
		";

	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_search_index', $flds, $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);

	$sqlarray = $dict->CreateIndexSQL('count', cms_db_prefix()."module_search_index", 'count');
	$dict->ExecuteSQLArray($sqlarray);
	
	$oldversion = '1.1';
}

if( version_compare($oldversion,'1.5') < 1 )
  {
    $flds = "word C(255) KEY,
             count       I
            ";
    $taboptarray = array('mysql' => 'TYPE=MyISAM');
    $sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_search_words', $flds, $taboptarray);
    $dict->ExecuteSQLArray($sqlarray);
  }
if( version_compare($oldversion,'1.6.2') < 1 )
  {
    // Indexes
    $sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'index_search_items',
				       cms_db_prefix().'module_search_items',
				       'module_name,content_id');
    $dict->ExecuteSQLArray($sqlarray);
    $sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'index_search_index',
				       cms_db_prefix().'module_search_index',
				       'word');
    $dict->ExecuteSQLArray($sqlarray);
  }

if( version_compare($oldversion,'1.7.6') < 1 )
  {
    $this->RegisterModulePlugin(true);
    $this->RegisterSmartyPlugin('search','function','function_plugin');
  }


?>
