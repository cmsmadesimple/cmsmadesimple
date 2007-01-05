<?php
echo "<p>Creating additional_users table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	user_id I,
	page_id I,
	content_id I
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."additional_users", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Adding admin_bookmarks table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	user_id I,
	title C(255),
	url C(255)
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."admin_bookmarks", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."admin_bookmarks ADD INDEX (user_id)");

echo '[done]</p>';

echo "<p>Creating adminlog table...";

$dbdict = NewDataDictionary($db);
$flds = "
	timestamp I,
	user_id I,
	username C(25),
	item_id I,
	item_name C(50),
	action C(255)
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."adminlog", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Adding admin_recent_pages table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	user_id I,
	title C(255),
	url C(255),
	access_time DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."admin_recent_pages", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo '[done]</p>';

echo "<p>Creating content table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	content_name C(255),
	type C(25),
	owner_id I,
	parent_id I,
	template_id I,
	item_order I,
	hierarchy C(255),
	default_content I1,
	menu_text C(255),
	content_alias C(255),
	show_in_menu I1,
	collapsed I1,
	markup C(25),
	active I1,
	cachable I1,
	id_hierarchy C(255),
	hierarchy_path X,
	prop_names X,
	metadata X,
	titleattribute C(255),
	tabindex C(10),
	accesskey C(5),
	last_modified_by I,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."content", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."content ADD INDEX (content_alias, active)");
$db->Execute("ALTER TABLE ".$db_prefix."content ADD INDEX (default_content)");
$db->Execute("ALTER TABLE ".$db_prefix."content ADD INDEX (parent_id)");

echo "[done]</p>";

echo "<p>Creating content_props table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	content_id I,
	type C(25),
	prop_name C(255),
	param1 C(255),
	param2 C(255),
	param3 C(255),
	content XL,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."content_props", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."content_props ADD INDEX (content_id)");

echo "[done]</p>";

echo "<p>Adding crossref table...";

$dbdict = NewDataDictionary($db);
$flds = '
	child_type C(100),
	child_id I,
	parent_type C(100),
	parent_id I,
	create_date DT,
	modified_date DT
';

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."crossref", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."crossref ADD INDEX (child_type, child_id)");
$db->Execute("ALTER TABLE ".$db_prefix."crossref ADD INDEX (parent_type, parent_id)");

echo '[done]</p>';

echo "<p>Creating css table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	css_name C(255),
	css_text X,
	media_type C(255),
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."css", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."css ADD INDEX (css_name)");

echo "[done]</p>";

echo "<p>Creating css_assoc table...";

$dbdict = NewDataDictionary($db);
$flds = "
	assoc_to_id I,
	assoc_css_id I,
	assoc_type C(80),
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."css_assoc", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."css_assoc ADD INDEX (assoc_to_id)");
$db->Execute("ALTER TABLE ".$db_prefix."css_assoc ADD INDEX (assoc_css_id)");

echo "[done]</p>";

echo "<p>Creating event_handlers table...";

$dbdict = NewDataDictionary($db);

$flds = "
          event_id      I,
          tag_name      c(255),
          module_name   c(255),
          removable     I,
          handler_order I,
          id    I KEY
        ";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."event_handlers", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating events table...";

$flds = "
          originator   c(200) NOTNULL,
          event_name   c(200) NOTNULL,
          id     I KEY
        ";

$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."events", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->CreateIndexSQL('originator', $db_prefix."events", 'originator');
$dbdict->ExecuteSQLArray($sqlarray);
   
$sqlarray = $dbdict->CreateIndexSQL('event_name', $db_prefix."events", 'event_name');
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating group_perms table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	group_id I,
	permission_id I,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."group_perms", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."group_perms ADD INDEX (group_id, permission_id)");

echo "[done]</p>";

echo "<p>Creating groups table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	group_name C(25),
	active I1,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."groups", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating htmlblobs table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	htmlblob_name C(255),
	html X,
	owner I,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."htmlblobs", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."htmlblobs ADD INDEX (htmlblob_name)");

echo "[done]</p>";

echo "<p>Creating additional_htmlblob_users table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	user_id I,
	htmlblob_id I
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."additional_htmlblob_users", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating modules table...";

$dbdict = NewDataDictionary($db);
$flds = "
	module_name C(255),
	status C(255),
	version C(255),
	admin_only I1 DEFAULT 0,
	active I1
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."modules", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."modules ADD INDEX (module_name)");

echo "[done]</p>";

echo '<p>Creating module_deps table...';

$dbdict = NewDataDictionary($db);
$flds = "
	parent_module C(25),
	child_module C(25),
	minimum_version C(25),
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."module_deps", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo '[done]</p>';

echo "<p>Adding module_templates table...";

$dbdict = NewDataDictionary($db);
$flds = "
	module_name C(200),
	template_name C(200),
	content X,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."module_templates", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."module_templates ADD INDEX (module_name, template_name)");

echo "[done]</p>";

echo "<p>Creating permissions table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	permission_name C(255),
	permission_text C(255),
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."permissions", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating siteprefs table...";

$dbdict = NewDataDictionary($db);
$flds = "
	sitepref_name C(255) KEY,
	sitepref_value text,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."siteprefs", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating templates table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	template_name C(255),
	template_content X,
	stylesheet X,
	encoding C(25),
	active I1,
	default_template I1,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."templates", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."templates ADD INDEX (template_name)");

echo "[done]</p>";

echo "<p>Creating user_groups table...";

$dbdict = NewDataDictionary($db);
$flds = "
	group_id I,
	user_id I,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."user_groups", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating userprefs table...";

$dbdict = NewDataDictionary($db);
$flds = "
	user_id I,
	preference C(50),
	value X,
	type C(25)
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."userprefs", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->Execute("ALTER TABLE ".$db_prefix."userprefs ADD INDEX (user_id)");

echo "[done]</p>";

echo "<p>Creating users table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	username C(25),
	password C(40),
	admin_access I1,
	first_name C(50),
	last_name C(50),
	email C(255),
	active I1,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."users", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating userplugins table...";

$dbdict = NewDataDictionary($db);
$flds = "
	id I KEY,
	userplugin_name C(255),
	code X,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."userplugins", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Creating version table...";

$dbdict = NewDataDictionary($db);
$flds = "
	version I
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL($db_prefix."version", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
