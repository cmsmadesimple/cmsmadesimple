<?php

echo "<p>Adding module_templates table...";

$dbdict = NewDataDictionary($db);
$flds = "
	module_name C(160),
	template_name C(160),
	content X,
	create_date DT,
	modified_date DT
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."module_templates", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Adding fields to content table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."content", "last_modified_by I");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Removing field from content_props table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->DropColumnSQL(cms_db_prefix()."content_props", "content_prop_id");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Setting a last_modified_by to something...";

$oneuserid = $db->GetOne("SELECT user_id FROM ".cms_db_prefix()."users");
$db->Execute("UPDATE ".cms_db_prefix()."content SET last_modified_by = ".$oneuserid);

echo "[done]</p>";

echo "<p>Adding fields to template table...";

$dbdict = NewDataDictionary($db);
$sqlarray = $dbdict->AddColumnSQL(cms_db_prefix()."templates", "default_template I1");
$dbdict->ExecuteSQLArray($sqlarray);

echo "[done]</p>";

echo "<p>Setting a default template...";

$onetemplateid = $db->GetOne("SELECT template_id FROM ".cms_db_prefix()."templates");
$db->Execute("UPDATE ".cms_db_prefix()."templates SET default_template = 0");
$db->Execute("UPDATE ".cms_db_prefix()."templates SET default_template = 1 WHERE template_id = ".$onetemplateid);

echo "[done]</p>";

echo "<p>Updating Permissions...";

$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Add Stylesheets', permission_text='Add Stylesheets' where permission_name='Add CSS'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Modify Stylesheets', permission_text='Modify Stylesheets' where permission_name='Modify CSS'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Remove Stylesheets', permission_text='Remove Stylesheets' where permission_name='Remove CSS'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Add Pages', permission_text='Add Pages' where permission_name='Add Content'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Modify Any Page', permission_text='Modify Any Page' where permission_name='Modify Any Content'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Remove Pages', permission_text='Remove Pages' where permission_name='Remove Content'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Add Groups', permission_text='Add Groups' where permission_name='Add Group'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Modify Groups', permission_text='Modify Groups' where permission_name='Modify Group'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Remove Groups', permission_text='Remove Groups' where permission_name='Remove Group'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Add Templates', permission_text='Add Templates' where permission_name='Add Template'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Modify Templates', permission_text='Modify Templates' where permission_name='Modify Template'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Remove Templates', permission_text='Remove Templates' where permission_name='Remove Template'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Add Users', permission_text='Add Users' where permission_name='Add User'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Modify Users', permission_text='Modify Users' where permission_name='Modify User'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Remove Users', permission_text='Remove Users' where permission_name='Remove User'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Add Stylesheet Assoc', permission_text='Add Stylesheet Associations' where permission_name='Add CSS association'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Modify Stylesheet Assoc', permission_text='Modify Stylesheet Associations' where permission_name='Edit CSS association'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_name = 'Remove Stylesheet Assoc', permission_text='Remove Stylesheet Associations' where permission_name='Remove CSS association'");
$db->Execute("UPDATE ".cms_db_prefix()."permissions SET permission_text = 'Modify Permissions for Groups' where permission_name='Modify Permissions'");
echo "[done]</p>";

echo "<p>Adding admin_bookmarks table...";

$dbdict = NewDataDictionary($db);
$flds = "
	bookmark_id I KEY,
	user_id I,
	title C(255),
	url C(255)
";
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."admin_bookmarks", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence(cms_db_prefix()."admin_bookmarks_seq");

echo '[done]</p>';

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
$sqlarray = $dbdict->CreateTableSQL(cms_db_prefix()."admin_recent_pages", $flds, $taboptarray);
$dbdict->ExecuteSQLArray($sqlarray);

$db->CreateSequence(cms_db_prefix()."admin_recent_pages_seq");

echo '[done]</p>';

echo "<p>Adding Primary Keys...";

$db->Execute("ALTER TABLE ".cms_db_prefix()."content ADD PRIMARY KEY (content_id, prop_name)");
$db->Execute("ALTER TABLE ".cms_db_prefix()."htmlblobs ADD PRIMARY KEY (htmlblob_id)");
$db->Execute("ALTER TABLE ".cms_db_prefix()."css ADD PRIMARY KEY (css_id)");
$db->Execute("ALTER TABLE ".cms_db_prefix()."userplugins ADD PRIMARY KEY (userplugin_id)");

echo '[done]</p>';

echo "<p>Adding Indexes...";

$db->Execute("ALTER TABLE ".cms_db_prefix()."content_props ADD INDEX (content_id, prop_name)");
$db->Execute("ALTER TABLE ".cms_db_prefix()."content ADD INDEX (content_alias, active)");
$db->Execute("ALTER TABLE ".cms_db_prefix()."content ADD INDEX (content_alias)");
$db->Execute("ALTER TABLE ".cms_db_prefix()."module_templates ADD INDEX (module_name, template_name)");
$db->Execute("ALTER TABLE ".cms_db_prefix()."group_perms ADD INDEX (group_id, permission_id)");
$db->Execute("ALTER TABLE ".cms_db_prefix()."admin_bookmarks ADD INDEX (user_id)");
$db->Execute("ALTER TABLE ".cms_db_prefix()."userprefs ADD INDEX (user_id)");

echo '[done]</p>';

echo '<p>Updating schema version... ';

$query = "UPDATE ".cms_db_prefix()."version SET version = 10";
$db->Execute($query);

echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
