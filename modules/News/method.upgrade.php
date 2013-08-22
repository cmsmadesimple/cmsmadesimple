<?php
if (!isset($gCms)) exit;

$current_version = $oldversion;
$db =& $this->GetDb();

switch($current_version)
{
	case "1.0":
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "start_time " . CMS_ADODB_DT . ", end_time " . CMS_ADODB_DT . ", icon C(255)");
		$dict->ExecuteSQLArray($sqlarray);
		$current_version = "1.1";
	case "1.1":
	case "1.2":
	case "1.3":
	case "1.4":
	case "1.5":
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "news_cat C(255)");
		$dict->ExecuteSQLArray($sqlarray);
		$current_version = "1.6";
	case "1.6":
		$this->SetTemplate('displaysummary', $this->GetSummaryHtmlTemplate());
		$this->SetTemplate('displaydetail', $this->GetDetailHtmlTemplate());

		$current_version = "1.7";
	case '1.7':
		#Makey new tables....

		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "status C(25)");
		$dict->ExecuteSQLArray($sqlarray);

		$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "summary X");
		$dict->ExecuteSQLArray($sqlarray);

		$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "news_category_id I");
		$dict->ExecuteSQLArray($sqlarray);

		$query = "UPDATE ".cms_db_prefix()."module_news SET summary = ?, status = ?";
		$db->Execute($query, array('', 'published'));

		$flds = "
			news_category_id I KEY,
			news_category_name C(255),
			parent_id I,
			hierarchy C(255),
			long_name X,
			create_date " . CMS_ADODB_DT . ",
			modified_date " . CMS_ADODB_DT
		;
		$dict = NewDataDictionary($db);

		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news_categories", 
				$flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix()."module_news_categories_seq");

		$query = "SELECT DISTINCT news_cat FROM ".cms_db_prefix()."module_news WHERE news_cat IS NOT NULL";
		$dbresult = $db->Execute($query);
		while ($dbresult && $row = $dbresult->FetchRow())
		{
			$catid = $db->GenID(cms_db_prefix()."module_news_categories_seq");
			$query = "INSERT INTO ".cms_db_prefix()."module_news_categories (news_category_id, news_category_name, parent_id, hierarchy, long_name, create_date, modified_date) VALUES (?,?,?,?,?,".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
			$db->Execute($query,array($catid, $row['news_cat'], -1, '', ''));

			$query = "UPDATE ".cms_db_prefix()."module_news SET news_category_id = ? WHERE news_cat = ?";
			$db->Execute($query, array($catid, $row['news_cat']));
		}

		# Setup summary template
		$this->SetTemplate('displaysummary', $this->GetSummaryHtmlTemplate());

		# Setup detail template
		$this->SetTemplate('displaydetail', $this->GetDetailHtmlTemplate());

		news_admin_ops::UpdateHierarchyPositions();

		$current_version = "2.0";

	case '2.0':
	case '2.0.1':
	case '2.0.2':
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "author_id I");
		$dict->ExecuteSQLArray($sqlarray);
		$current_version = "2.0.3";
	case '2.0.3':
		#Setup events
		$this->CreateEvent('NewsArticleAdded');
		$this->CreateEvent('NewsArticleEdited');
		$this->CreateEvent('NewsArticleDeleted');
		$this->CreateEvent('NewsCategoryAdded');
		$this->CreateEvent('NewsCategoryEdited');
		$this->CreateEvent('NewsCategoryDeleted');
		$current_version = '2.2';
        case '2.2':
	        #
                #move templates around for multiple database templates
                #
		# Setup summary template
		$fn = dirname(__FILE__).DIRECTORY_SEPARATOR.
		  'templates'.DIRECTORY_SEPARATOR.'orig_summary_template.tpl';
		if( file_exists( $fn ) )
		  {
		    $template = @file_get_contents($fn);
		    $this->SetPreference('default_summary_template_contents',
					 $template);
		    $this->SetTemplate('summarySample',$template);
		    $this->SetPreference('current_summary_template','Sample');
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

                # move the displaysummary and displaydetail templates
		# into the new scheme
	        $tmpl = $this->GetTemplate('displaysummary');
                $this->SetTemplate('summary_dflt',$tmpl);
		$this->SetPreference('current_summary_template','_dflt');

		$tmpl = $this->GetTemplate('displaydetail');
                $this->SetTemplate('detail_dflt',$tmpl);
		$this->SetPreference('current_detail_template','_dflt');
		$this->DeleteTemplate('displaysummary');
		$this->DeleteTemplate('displaydetail');
		$this->CreatePermission('Approve News', 
					'Approve News For Frontend Display');

 case '2.3':
 case '2.3.0.1':
 case '2.3.0.2':
   $this->CreatePermission('Approve News', 
			   'Approve News For Frontend Display');
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
   # Setup default email template and email preferences
   $this->SetPreference('email_subject',$this->Lang('subject_newnews'));
   $this->SetTemplate('email_template',
		      $this->GetDfltEmailTemplate());
	  
 case '2.4':
   $dict = NewDataDictionary($db);
   $sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", 
				   "news_extra C(255)");
   $dict->ExecuteSQLArray($sqlarray);

 case '2.5':
 case '2.5.1':
   $this->SetPreference('allowed_upload_types','gif,png,jpeg,jpg');
   $this->SetPreference('auto_create_thumbnails','gif,png,jpeg,jpg');
   $dict = NewDataDictionary($db);
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
	news_id I,
	fielddef_id I,
	value X,
	create_date " . CMS_ADODB_DT . ",
	modified_date " . CMS_ADODB_DT . "
   ";

   $taboptarray = array('mysql' => 'TYPE=MyISAM');
   $sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news_fieldvals", 
				     $flds, $taboptarray);
   $dict->ExecuteSQLArray($sqlarray);
   $this->CreatePermission('Delete News', 'Delete News Articles');

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

 case '2.9':
 case '2.9.1':
 case '2.9.2':
 case '2.9.3':
 case '2.9.4':
 case '2.9.5':
 case '2.9.6':
 case '2.9.7':
 case '2.9.8':
 case '2.9.9':
 case '2.9.10':
 case '2.10':
 case '2.10.1':
 case '2.10.2':
 case '2.10.3':
 case '2.10.4':
   {
     $dict = NewDataDictionary($db);
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
   }


/*
 case '2.11':
 case '2.11.1':
 case '2.11.2':
 case '2.11.3':
 case '2.11.4':
 case '2.11.5':
   {
     $dict = NewDataDictionary($db);
     $sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_news", 
				       $flds, $taboptarray);
     $dict->ExecuteSQLArray($sqlarray);
   }
*/
}

if( version_compare($oldversion,'2.10.6') < 0 )
  {
     $dict = NewDataDictionary($db);
     $sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_news", "news_url C(255)");
     $dict->ExecuteSQLArray($sqlarray);
  }

if( version_compare($oldversion,'2.12.7') < 0 )
  {
    $res = $this->RegisterModulePlugin(true);

    $this->RegisterSmartyPlugin('news','function','function_plugin');

    $this->CreateStaticRoutes();
    
    # Setup Simplex Theme HTML5 sample summary template
    $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.
        'templates'.DIRECTORY_SEPARATOR.'Summary_Simplex_template.tpl';
    if( file_exists( $fn ) )
    {
        $template = @file_get_contents($fn);
        $this->SetTemplate('summarySummary_Simplex',$template);
    }

    # Setup Simplex Theme HTML5 sample detail template
    $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.
        'templates'.DIRECTORY_SEPARATOR.'Simplex_Detail_template.tpl';
    if( file_exists( $fn ) )
    {
        $template = @file_get_contents($fn);
        $this->SetTemplate('detailSimplex_Detail',$template);
    }
  }

if( version_compare($oldversion,'2.13') < 0 ) {
  $dict = NewDataDictionary($db);
  $sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_news_fielddefs', 'extra X');
  $dict->ExecuteSQLArray($sqlarray);
}

?>