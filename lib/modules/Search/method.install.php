<?php
if (!isset($gCms)) exit;

$db = $this->GetDb();

$uid = null;
if( cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
    $uid = 1; // hardcode to first user
} else {
    $uid = get_userid();
}


$db_prefix = CMS_DB_PREFIX;
$dict = NewDataDictionary($db);
$flds= "
		id I KEY,
		module_name C(100),
		content_id I,
		extra_attr C(100),
		expires " . CMS_ADODB_DT;

$taboptarray = array('mysql' => 'ENGINE=MyISAM');
$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX.'module_search_items', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$db->CreateSequence(CMS_DB_PREFIX."module_search_items_seq");

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
$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX.'module_search_index', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->CreateIndexSQL(CMS_DB_PREFIX.'index_search_count', $db_prefix."module_search_index", 'count');
$dict->ExecuteSQLArray($sqlarray);

$flds = "word C(255) KEY,
         count       I
        ";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX.'module_search_words', $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

# Indexes
$sqlarray = $dict->CreateIndexSQL(CMS_DB_PREFIX.'index_search_items',
				   CMS_DB_PREFIX.'module_search_items',
				   'module_name,content_id');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(CMS_DB_PREFIX.'index_search_index',
				   CMS_DB_PREFIX.'module_search_index',
				   'word');
$dict->ExecuteSQLArray($sqlarray);

$this->SetPreference('stopwords', $this->DefaultStopWords());
$this->SetPreference('usestemming', 'false');
$this->SetPreference('searchtext','Enter Search...');

try {
    $searchform_type = new CmsLayoutTemplateType();
    $searchform_type->set_originator($this->GetName());
    $searchform_type->set_name('searchform');
    $searchform_type->set_dflt_flag(TRUE);
    $searchform_type->set_lang_callback('Search::page_type_lang_callback');
    $searchform_type->set_content_callback('Search::reset_page_type_defaults');
    $searchform_type->reset_content_to_factory();
    $searchform_type->save();

    $tpl = new CmsLayoutTemplate();
    $tpl->set_name('Search Form Sample');
    $tpl->set_owner($uid);
    $tpl->set_content($this->GetSearchHtmlTemplate());
    $tpl->set_type($searchform_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();

    // Setup Simplex Theme search form template
    try {
        $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'Simplex_Search_template.tpl';
        if( file_exists( $fn ) ) {
            $template = @file_get_contents($fn);
            $tpl = new CmsLayoutTemplate();
            $tpl->set_name('Simplex Search');
            $tpl->set_owner($uid);
            $tpl->set_content($template);
            $tpl->set_type($searchform_type);
            $tpl->add_design('Simplex');
            $tpl->save();
        }
    }
    catch( Exception $e ) {
        audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
    }

    $searchresults_type = new CmsLayoutTemplateType();
    $searchresults_type->set_originator($this->GetName());
    $searchresults_type->set_name('searchresults');
    $searchresults_type->set_dflt_flag(TRUE);
    $searchresults_type->set_lang_callback('Search::page_type_lang_callback');
    $searchresults_type->set_content_callback('Search::reset_page_type_defaults');
    $searchresults_type->reset_content_to_factory();
    $searchresults_type->save();

    $tpl = new CmsLayoutTemplate();
    $tpl->set_name('Search Results Sample');
    $tpl->set_owner($uid);
    $tpl->set_content($this->GetResultsHtmlTemplate());
    $tpl->set_type($searchresults_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
}
catch( CmsException $e ) {
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

$this->CreateEvent('SearchInitiated');
$this->CreateEvent('SearchCompleted');
$this->CreateEvent('SearchItemAdded');
$this->CreateEvent('SearchItemDeleted');
$this->CreateEvent('SearchAllItemsDeleted');

$this->RegisterEvents();
$this->RegisterModulePlugin(true);
$this->RegisterSmartyPlugin('search','function','function_plugin');

$this->Reindex();