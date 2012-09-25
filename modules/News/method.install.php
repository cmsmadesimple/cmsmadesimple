<?php
if (!isset($gCms)) exit;

if( !class_exists('news_admin_ops') )
  {
    // this is required if called from the installer
    $fn = dirname(__FILE__).'/lib/class.news_admin_ops.php';
    require_once($fn);
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
        news_url C(255)
"; // icon is no longer used.

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news", 
		$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(cms_db_prefix()."module_news_seq");

$flds = "
	news_category_id I KEY,
	news_category_name C(255),
	parent_id I,
	hierarchy C(255),
	long_name X,
	create_date T,
	modified_date T
";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news_categories", 
		$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(cms_db_prefix()."module_news_categories_seq");

$flds = "
	id I KEY AUTO,
	name C(255),
	type C(50),
	max_length I,
	create_date " . CMS_ADODB_DT . ",
	modified_date " . CMS_ADODB_DT . ",
        item_order I,
        public I
";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news_fielddefs", 
		$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$flds = "
	news_id I KEY NOT NULL,
	fielddef_id I KEY NOT NULL,
	value X,
	create_date " . CMS_ADODB_DT . ",
	modified_date " . CMS_ADODB_DT . "
";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news_fieldvals", 
		$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

#Set Permission
$this->CreatePermission('Modify News', 'Modify News');
$this->CreatePermission('Approve News', 'Approve News For Frontend Display');
$this->CreatePermission('Delete News', 'Delete News Articles');

# Setup summary template
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.
  'templates'.DIRECTORY_SEPARATOR.'orig_summary_template.tpl';
if( file_exists( $fn ) )
  {
    $template = @file_get_contents($fn);
    $this->SetPreference('default_summary_template_contents',$template);
    $this->SetTemplate('summarySample',$template);
    $this->SetPreference('current_summary_template','Sample');
  }
//$this->SetTemplate('displaysummary', $this->GetSummaryHtmlTemplate());

# Setup Simplex Theme HTML5 sample summary template
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.
  'templates'.DIRECTORY_SEPARATOR.'Summary_Simplex_template.tpl';
if( file_exists( $fn ) )
  {
    $template = @file_get_contents($fn);
    $this->SetTemplate('summarySummary_Simplex',$template);
  }
  
# Setup detail template
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.
  'templates'.DIRECTORY_SEPARATOR.'orig_detail_template.tpl';
if( file_exists( $fn ) )
  {
    $template = @file_get_contents($fn);
    $this->SetPreference('default_detail_template_contents',$template);
    $this->SetTemplate('detailSample',$template);
    $this->SetPreference('current_detail_template','Sample');
  }
//$this->SetTemplate('displaydetail', $this->GetDetailHtmlTemplate());

# Setup Simplex Theme HTML5 sample detail template
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.
  'templates'.DIRECTORY_SEPARATOR.'Simplex_Detail_template.tpl';
if( file_exists( $fn ) )
  {
    $template = @file_get_contents($fn);
    $this->SetTemplate('detailSimplex_Detail',$template);
  }
  
# Setup form template
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.
  'templates'.DIRECTORY_SEPARATOR.'orig_form_template.tpl';
if( file_exists( $fn ) )
  {
    $template = @file_get_contents($fn);
    $this->SetPreference('default_form_template_contents',$template);
    $this->SetTemplate('formSample',$template);
    $this->SetPreference('current_form_template','Sample');
  }

# Setup browsecat template
$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.
  'templates'.DIRECTORY_SEPARATOR.'browsecat.tpl';
if( file_exists( $fn ) )
  {
    $template = @file_get_contents($fn);
    $this->SetPreference('default_browsecat_template_contents',$template);
    $this->SetTemplate('browsecatSample',$template);
    $this->SetPreference('current_browsecat_template','Sample');
  }


# Setup default email template and email preferences
$this->SetPreference('email_subject',$this->Lang('subject_newnews'));
$this->SetTemplate('email_template',
		   $this->GetDfltEmailTemplate());

# Other preferences
$this->SetPreference('allowed_upload_types','gif,png,jpeg,jpg');
$this->SetPreference('auto_create_thumbnails','gif,png,jpeg,jpg');

# Setup General category
$catid = $db->GenID(cms_db_prefix()."module_news_categories_seq");
$query = 'INSERT INTO '.cms_db_prefix().'module_news_categories (news_category_id, news_category_name, parent_id, create_date, modified_date) VALUES (?,?,?,'.$db->DBTimeStamp(time()).','.$db->DBTimeStamp(time()).')';
$db->Execute($query, array($catid, 'General', -1));

# Setup initial news article
$articleid = $db->GenID(cms_db_prefix()."module_news_seq");
$query = 'INSERT INTO '.cms_db_prefix().'module_news ( NEWS_ID, NEWS_CATEGORY_ID, AUTHOR_ID, NEWS_TITLE, NEWS_DATA, NEWS_DATE, SUMMARY, START_TIME, END_TIME, STATUS, ICON, CREATE_DATE, MODIFIED_DATE ) VALUES (?,?,?,?,?,'.$db->DBTimeStamp(time()).',?,?,?,?,?,'.$db->DBTimeStamp(time()).','.$db->DBTimeStamp(time()).')';
$db->Execute($query, array($articleid, $catid, 1, 'News Module Installed', 'The news module was installed.  Exciting. This news article is not using the Summary field and therefore there is no link to read more. But you can click on the news heading to read only this article.', null, null, null, 'published', null));
news_admin_ops::UpdateHierarchyPositions();

# Setup permissions
$perm_id = $db->GetOne("SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name = 'Modify News'");
$group_id = $db->GetOne("SELECT group_id FROM ".cms_db_prefix()."groups WHERE group_name = 'Admin'");

$count = $db->GetOne("SELECT count(*) FROM " . cms_db_prefix() . "group_perms WHERE group_id = ? AND permission_id = ?", array($group_id, $perm_id));
if (isset($count) && intval($count) == 0)
{
	$new_id = $db->GenID(cms_db_prefix()."group_perms_seq");
	$query = "INSERT INTO " . cms_db_prefix() . "group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (".$new_id.", ".$group_id.", ".$perm_id.", ". $db->DBTimeStamp(time()) . ", " . $db->DBTimeStamp(time()) . ")";
	$db->Execute($query);
}

$group_id = $db->GetOne("SELECT group_id FROM ".cms_db_prefix()."groups WHERE group_name = 'Editor'");

$count = $db->GetOne("SELECT count(*) FROM " . cms_db_prefix() . "group_perms WHERE group_id = ? AND permission_id = ?", array($group_id, $perm_id));
if (isset($count) && intval($count) == 0)
{
	$new_id = $db->GenID(cms_db_prefix()."group_perms_seq");
	$query = "INSERT INTO " . cms_db_prefix() . "group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (".$new_id.", ".$group_id.", ".$perm_id.", ". $db->DBTimeStamp(time()) . ", " . $db->DBTimeStamp(time()) . ")";
	$db->Execute($query);
}

# Indexes
$sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'news_postdate',
				  cms_db_prefix().'module_news',
				  'news_date');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'news_daterange',
				  cms_db_prefix().'module_news',
				  'start_time,end_time');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'news_author',
				  cms_db_prefix().'module_news',
				  'author_id');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'news_hier',
				  cms_db_prefix().'module_news',
				  'news_category_id');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'news_url',
				  cms_db_prefix().'module_news',
				  'news_url');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL(cms_db_prefix().'news_startenddate',
				  cms_db_prefix().'module_news',
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
$route = new CmsRoute('/[nN]ews\/(?P<articleid>[0-9]+)\/(?P<returnid>[0-9]+)\/(?P<junk>.*?)\/d,(?P<detailtemplate>.*?)$/',
		      $this->GetName());
cms_route_manager::add_static($route);
$route = new CmsRoute('/[nN]ews\/(?P<articleid>[0-9]+)\/(?P<returnid>[0-9]+)\/(?P<junk>.*?)$/',$this->GetName());
cms_route_manager::add_static($route);
$route = new CmsRoute('/[nN]ews\/(?P<articleid>[0-9]+)\/(?P<returnid>[0-9]+)$/',$this->GetName());
cms_route_manager::add_static($route);
$route = new CmsRoute('/[nN]ews\/(?P<articleid>[0-9]+)$/',$this->GetName());
cms_route_manager::add_static($route);

?>
