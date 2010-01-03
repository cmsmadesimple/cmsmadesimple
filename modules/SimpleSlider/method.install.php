<?php
$db = $this->cms->db;

// mysql-specific, but ignored by other database
$taboptarray = array('mysql' => 'TYPE=MyISAM');

//Make a new "dictionary" (ADODB-speak for a table)
$dict = NewDataDictionary($db);

//Add the fields as a comma-separated string.
$fields = "id I AUTO KEY,
			slide I DEFAULT 0,
			title C(50),
			imagelink C(255),
			description X ";

//Note the naming scheme that should be followed when adding tables to the database,
// so as to make it easy to recognize who the table belongs to, and to avoid conflict with other modules.
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_simpleslider_images', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

//Add the fields as a comma-separated string.
$fields = "showid I AUTO KEY,
			showname C(50),
			width I,
			height I,
			fadetime I,
      defaultstylesheet I,
      stylesheet X";

//Note the naming scheme that should be followed when adding tables to the database,
// so as to make it easy to recognize who the table belongs to, and to avoid conflict with other modules.
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_simpleslider_shows', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->CreateIndexSQL('showname', cms_db_prefix().'module_simpleslider_shows', 'showname', array('UNIQUE'));
$dict->ExecuteSQLArray($sqlarray);

//Add the fields as a comma-separated string.
$fields = "showid I,
			imageid I
			";

// so as to make it easy to recognize who the table belongs to, and to avoid conflict with other modules.
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_simpleslider_showimages', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->CreateIndexSQL('showid', cms_db_prefix().'module_simpleslider_showimages', 'showid');
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->CreateIndexSQL('showimage', cms_db_prefix().'module_simpleslider_showimages', array('showid', 'imageid'), array('UNIQUE'));
$dict->ExecuteSQLArray($sqlarray);

$this->setPreference('defwidth', 400);
$this->setPreference('defheight', 300);
$this->setPreference('deffadetime', 4000);

$this->CreatePermission('SimpleSlider Admin', 'SimpleSlider Admin');

mkdir('../uploads/sliderimages/');

?>
