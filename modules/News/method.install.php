<?php
if (!isset($gCms)) exit;

if( !class_exists('news_admin_ops') ) {
  // this is required if called from the installer
  $fn = dirname(__FILE__).'/lib/class.news_admin_ops.php';
  require_once($fn);
}

$uid = null;
if( cmsms()->test_state(CmsApp::STATE_INSTALL) ) {
  $uid = 1; // hardcode to first user
} else {
  $uid = get_userid();
}

$db = $this->GetDb();
$dict = NewDataDictionary($db);
$flds = "
	news_id I KEY,
	news_category_id I,
	news_title C(255),
	news_data X,
	news_date " . CMS_ADODB_DT . ",
	summary X,
	start_time " . CMS_ADODB_DT . ",
	end_time " . CMS_ADODB_DT . ",
	status C(25),
	icon C(255),
	create_date " . CMS_ADODB_DT . ",
	modified_date " . CMS_ADODB_DT . ",
	author_id I,
        news_extra C(255),
        news_url C(255),
        searchable I1
"; // icon is no longer used.

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX."module_news", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(CMS_DB_PREFIX."module_news_seq");

$flds = "
	news_category_id I KEY,
	news_category_name C(255) NOTNULL,
	parent_id I,
	hierarchy C(255),
    item_order I,
	long_name X,
	create_date T,
	modified_date T
";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX."module_news_categories",$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(CMS_DB_PREFIX."module_news_categories_seq");

$flds = "
	id I KEY AUTO,
	name C(255),
	type C(50),
	max_length I,
	create_date " . CMS_ADODB_DT . ",
	modified_date " . CMS_ADODB_DT . ",
        item_order I,
        public I,
        extra  X
";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX."module_news_fielddefs", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$flds = "
	news_id I KEY NOT NULL,
	fielddef_id I KEY NOT NULL,
	value X,
	create_date " . CMS_ADODB_DT . ",
	modified_date " . CMS_ADODB_DT . "
";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX."module_news_fieldvals", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

#Set Permission
$this->CreatePermission('Modify News', 'Modify News');
$this->CreatePermission('Approve News', 'Approve News For Frontend Display');
$this->CreatePermission('Delete News', 'Delete News Articles');

# Setup summary template
try {
  $summary_template_type = new CmsLayoutTemplateType();
  $summary_template_type->set_originator($this->GetName());
  $summary_template_type->set_name('summary');
  $summary_template_type->set_dflt_flag(TRUE);
  $summary_template_type->set_lang_callback('News::page_type_lang_callback');
  $summary_template_type->set_content_callback('News::reset_page_type_defaults');
  $summary_template_type->set_help_callback('News::template_help_callback');
  $summary_template_type->reset_content_to_factory();
  $summary_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'orig_summary_template.tpl';
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name('News Summary Sample');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($summary_template_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  // Setup Simplex Theme HTML5 sample summary template
  $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'Summary_Simplex_template.tpl';
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name('Simplex News Summary');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($summary_template_type);
	$tpl->add_design('Simplex');
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  // Setup detail template
  $detail_template_type = new CmsLayoutTemplateType();
  $detail_template_type->set_originator($this->GetName());
  $detail_template_type->set_name('detail');
  $detail_template_type->set_dflt_flag(TRUE);
  $detail_template_type->set_lang_callback('News::page_type_lang_callback');
  $detail_template_type->set_content_callback('News::reset_page_type_defaults');
  $detail_template_type->reset_content_to_factory();
  $detail_template_type->set_help_callback('News::template_help_callback');
  $detail_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'orig_detail_template.tpl';
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name('News Detail Sample');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($detail_template_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  // Setup Simplex Theme HTML5 sample detail template
  $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'Simplex_Detail_template.tpl';
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name('Simplex News Detail');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($detail_template_type);
	$tpl->add_design('Simplex');
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  // Setup form template
  $form_template_type = new CmsLayoutTemplateType();
  $form_template_type->set_originator($this->GetName());
  $form_template_type->set_name('form');
  $form_template_type->set_dflt_flag(TRUE);
  $form_template_type->set_lang_callback('News::page_type_lang_callback');
  $form_template_type->set_content_callback('News::reset_page_type_defaults');
  $form_template_type->reset_content_to_factory();
  $form_template_type->set_help_callback('News::template_help_callback');
  $form_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'orig_form_template.tpl';
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name('News Fesubmit Form Sample');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($form_template_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  // Setup browsecat template
  $browsecat_template_type = new CmsLayoutTemplateType();
  $browsecat_template_type->set_originator($this->GetName());
  $browsecat_template_type->set_name('browsecat');
  $browsecat_template_type->set_dflt_flag(TRUE);
  $browsecat_template_type->set_lang_callback('News::page_type_lang_callback');
  $browsecat_template_type->set_content_callback('News::reset_page_type_defaults');
  $browsecat_template_type->reset_content_to_factory();
  $browsecat_template_type->set_help_callback('News::template_help_callback');
  $browsecat_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
  $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'browsecat.tpl';
  if( file_exists( $fn ) ) {
      $template = @file_get_contents($fn);
      $tpl = new CmsLayoutTemplate();
      $tpl->set_name('News Browse Category Sample');
      $tpl->set_owner($uid);
      $tpl->set_content($template);
      $tpl->set_type($browsecat_template_type);
      $tpl->set_type_dflt(TRUE);
      $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

# Setup default email template and email preferences
$this->SetPreference('email_subject',$this->Lang('subject_newnews'));
$this->SetTemplate('email_template',$this->GetDfltEmailTemplate());

# Other preferences
$this->SetPreference('allowed_upload_types','gif,png,jpeg,jpg');
$this->SetPreference('auto_create_thumbnails','gif,png,jpeg,jpg');

# Setup General category
$catid = $db->GenID(CMS_DB_PREFIX."module_news_categories_seq");
$query = 'INSERT INTO '.CMS_DB_PREFIX.'module_news_categories (news_category_id, news_category_name, parent_id, create_date, modified_date) VALUES (?,?,?,'.$db->DBTimeStamp(time()).','.$db->DBTimeStamp(time()).')';
$db->Execute($query, array($catid, 'General', -1));

# Setup initial news article
$articleid = $db->GenID(CMS_DB_PREFIX."module_news_seq");
$query = 'INSERT INTO '.CMS_DB_PREFIX.'module_news ( NEWS_ID, NEWS_CATEGORY_ID, AUTHOR_ID, NEWS_TITLE, NEWS_DATA, NEWS_DATE, SUMMARY, START_TIME, END_TIME, STATUS, ICON, SEARCHABLE, CREATE_DATE, MODIFIED_DATE ) VALUES (?,?,?,?,?,'.$db->DBTimeStamp(time()).',?,?,?,?,?,?,'.$db->DBTimeStamp(time()).','.$db->DBTimeStamp(time()).')';
$db->Execute($query, array($articleid, $catid, 1, 'News Module Installed', 'The news module was installed.  Exciting. This news article is not using the Summary field and therefore there is no link to read more. But you can click on the news heading to read only this article.', null, null, null, 'published', null, 1));
news_admin_ops::UpdateHierarchyPositions();

# Setup permissions
$perm_id = $db->GetOne("SELECT permission_id FROM ".CMS_DB_PREFIX."permissions WHERE permission_name = 'Modify News'");
$group_id = $db->GetOne("SELECT group_id FROM ".CMS_DB_PREFIX."groups WHERE group_name = 'Admin'");

$count = $db->GetOne("SELECT count(*) FROM " . CMS_DB_PREFIX . "group_perms WHERE group_id = ? AND permission_id = ?", array($group_id, $perm_id));
if (isset($count) && intval($count) == 0) {
  $new_id = $db->GenID(CMS_DB_PREFIX."group_perms_seq");
  $query = "INSERT INTO " . CMS_DB_PREFIX . "group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (".$new_id.", ".$group_id.", ".$perm_id.", ". $db->DBTimeStamp(time()) . ", " . $db->DBTimeStamp(time()) . ")";
  $db->Execute($query);
}

$group_id = $db->GetOne("SELECT group_id FROM ".CMS_DB_PREFIX."groups WHERE group_name = 'Editor'");

$count = $db->GetOne("SELECT count(*) FROM " . CMS_DB_PREFIX . "group_perms WHERE group_id = ? AND permission_id = ?", array($group_id, $perm_id));
if (isset($count) && intval($count) == 0) {
  $new_id = $db->GenID(CMS_DB_PREFIX."group_perms_seq");
  $query = "INSERT INTO " . CMS_DB_PREFIX . "group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (".$new_id.", ".$group_id.", ".$perm_id.", ". $db->DBTimeStamp(time()) . ", " . $db->DBTimeStamp(time()) . ")";
  $db->Execute($query);
}

# Indexes
$sqlarray = $dict->CreateIndexSQL(CMS_DB_PREFIX.'news_postdate',
				  CMS_DB_PREFIX.'module_news',
				  'news_date');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(CMS_DB_PREFIX.'news_daterange',
				  CMS_DB_PREFIX.'module_news',
				  'start_time,end_time');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(CMS_DB_PREFIX.'news_author',
				  CMS_DB_PREFIX.'module_news',
				  'author_id');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(CMS_DB_PREFIX.'news_hier',
				  CMS_DB_PREFIX.'module_news',
				  'news_category_id');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(CMS_DB_PREFIX.'news_url',
				  CMS_DB_PREFIX.'module_news',
				  'news_url');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(CMS_DB_PREFIX.'news_startenddate',
				  CMS_DB_PREFIX.'module_news',
				  'start_time,end_time');
$dict->ExecuteSQLArray($sqlarray);

#Setup events
$this->CreateEvent('NewsArticleAdded');
$this->CreateEvent('NewsArticleEdited');
$this->CreateEvent('NewsArticleDeleted');
$this->CreateEvent('NewsCategoryAdded');
$this->CreateEvent('NewsCategoryEdited');
$this->CreateEvent('NewsCategoryDeleted');

$this->RegisterModulePlugin(TRUE);
$this->RegisterSmartyPlugin('news','function','function_plugin');

// and routes...
$this->CreateStaticRoutes();

?>