<?php
if (!isset($gCms)) exit;

$this->Database->create_table('module_search_words', "
	word C(255) KEY,
	count I
");
	
$this->Preference->set('stopwords', $this->DefaultStopWords());
$this->Preference->set('searchtext', 'Enter Search...');

$this->Template->set('search', 'default', $this->Template->get_from_file('orig_search_template'));
$this->Template->set('result', 'default', $this->Template->get_from_file('orig_result_template'));

/*
$this->CreateEvent('search_initiated');
$this->CreateEvent('search_completed');
$this->CreateEvent('search_item_added');
$this->CreateEvent('search_item_deleted');
$this->CreateEvent('search_all_items_deleted');
$this->CreateEvent('search_re_index');
*/

CmsSearch::get_instance()->reindex();

?>