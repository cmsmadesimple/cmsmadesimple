<?php
namespace News2;
use News2;

$gCms = cmsms();
$db = $gCms->GetDb();
$dict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=InnoDB');
$contaner = $this->getContainer();
$categories_table = $this->categoriesManager()->table_name();
$fielddefs_table = $this->fielddefManager()->table_name();
$news_table = $this->articleManager()->news_table();
$fieldvals_table = $this->articleManager()->fieldvals_table();
$uid = get_userid(FALSE);
if( $uid < 1 ) $uid = 1;

$flds = "
    id I KEY AUTO NOTNULL,
    name C(255) NOTNULL,
    alias C(255),
    parent_id I NOTNULL,
    item_order I NOTNULL,
    hierarchy C(255),
    long_name X,
    image_url C(255)
";
$sqlarray = $dict->CreateTableSQL( $categories_table, $flds, $taboptarray );
$dict->ExecuteSQLArray( $sqlarray );
$sqlarray = $dict->CreateIndexSQL( CMS_DB_PREFIX.'mod_news2cat_idx0', $categories_table, 'alias', [ 'UNIQUE' ] );
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL( CMS_DB_PREFIX.'mod_news2cat_idx1', $categories_table, 'name' );
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL( CMS_DB_PREFIX.'mod_news2cat_idx2', $categories_table, 'long_name', [ 'UNIQUE' ] );
$dict->ExecuteSQLArray($sqlarray);

$flds = "
	id I KEY AUTO,
	name C(255) NOTNULL,
	type C(255) NOTNULL,
    item_order I NOTNULL,
    extra  X
";
$sqlarray = $dict->CreateTableSQL( $fielddefs_table, $flds, $taboptarray );
$dict->ExecuteSQLArray( $sqlarray );
$sqlarray = $dict->CreateIndexSQL( CMS_DB_PREFIX.'mod_news2fielddefs_idx0', $fielddefs_table, 'name', [ 'UNIQUE' ] );
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL( CMS_DB_PREFIX.'mod_news2fielddefs_idx1', $fielddefs_table, 'item_order', [ 'UNIQUE' ] );
$dict->ExecuteSQLArray($sqlarray);

$flds = "
	id I KEY AUTO NOTNULL,
	category_id I,
	title C(255) NOTNULL,
	summary X,
	content X2 NOTNULL,
    news_date I NOTNULL,
	start_time I,
	end_time I,
	status C(25) NOTNULL,
	create_date I,
	modified_date i,
	author_id I,
    extra X,
    url_slug C(255),
    searchable I1
"; // icon is no longer used.
$sqlarray = $dict->CreateTableSQL( $news_table, $flds, $taboptarray );
$dict->ExecuteSQLArray( $sqlarray );
$db->Execute("ALTER TABLE $news_table ADD FOREIGN KEY (category_id) REFERENCES $categories_table (id)");
$sqlarray = $dict->CreateIndexSQL( CMS_DB_PREFIX.'mod_news2_idx0', $news_table, 'category_id' );
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL( CMS_DB_PREFIX.'mod_news2_idx1', $news_table, 'news_date' );
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL( CMS_DB_PREFIX.'mod_news2_idx2', $news_table, 'status,start_time,end_time' );
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL( CMS_DB_PREFIX.'mod_news2_idx3', $news_table, 'create_date,modified_date' );
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL( CMS_DB_PREFIX.'mod_news2_idx4', $news_table, 'url_slug', [ 'UNIQUE' ]);
$dict->ExecuteSQLArray($sqlarray);

$flds = "
	news_id I KEY NOTNULL,
	fielddef_id I KEY NOTNULL,
	value X,
";
$sqlarray = $dict->CreateTableSQL( $fieldvals_table, $flds, $taboptarray );
$dict->ExecuteSQLArray( $sqlarray );
$db->Execute("ALTER TABLE $fieldvals_table ADD FOREIGN KEY (news_id) REFERENCES $news_table (id)");
$db->Execute("ALTER TABLE $fieldvals_table ADD FOREIGN KEY (fielddef_id) REFERENCES $fielddefs_table (id)");

// Permissions
$this->CreatePermission( News2::MANAGE_PERM, News2::MANAGE_PERM );   // do anything
$this->CreatePermission( News2::OWN_PERM, News2::OWN_PERM );         // add articles, can edit own articles, cannot delete articles
$this->CreatePermission( News2::DELOWN_PERM, News2::DELOWN_PERM );   // can delete My arciles
$this->CreatePermission( News2::APPROVE_PERM, News2::APPROVE_PERM ); // can approve articles for display.

// routes
$this->CreateStaticRoutes();

// create a new category
$arr = [
    'name'=>'General',
    'alias'=>'general'
    ];
$category = $this->categoriesManager()->createNew($arr);
$category_id = $this->categoriesManager()->save( $category );

// create a new article in this new category.
$arr = [
    'category_id'=>$category_id,
    'status'=>Article::STATUS_PUBLISHED,
    'news_date'=>time(),
    'title'=>'News2 module installed',
    'summary'=>'News2 is installed',
    'content'=>'<p>The News2 moedule has been installed. <strong>Exciting!</strong></p>',
    'author_id'=> $uid
    ];
$article = $this->articleManager()->createNew( $arr );
$this->articleManager()->save( $article );
