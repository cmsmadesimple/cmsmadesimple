<?php
$db = null;

CmsInstallOperations::create_table($db, 'additional_users', "
	id I KEY,
	user_id I,
	page_id I,
	content_id I
");

CmsInstallOperations::create_table($db, 'admin_bookmarks', "
	id I KEY,
	user_id I,
	title C(255),
	url C(255)
");
CmsInstallOperations::create_index($db, 'admin_bookmarks', 'user_id');

CmsInstallOperations::create_table($db, 'adminlog', "
	timestamp I,
	user_id I,
	username C(25),
	item_id I,
	item_name C(50),
	action C(255)
");

CmsInstallOperations::create_table($db, 'admin_recent_pages', "
	id I KEY,
	user_id I,
	title C(255),
	url C(255),
	access_time DT
");

CmsInstallOperations::create_table($db, 'content', "
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
");
$db->Execute("ALTER TABLE ".cms_db_prefix()."content ADD INDEX (content_alias, active)");
CmsInstallOperations::create_index($db, 'content', 'default_content');
CmsInstallOperations::create_index($db, 'content', 'parent_id');

CmsInstallOperations::create_table($db, 'content_props', "
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
");
CmsInstallOperations::create_index($db, 'content_props', 'content_id');

CmsInstallOperations::create_table($db, 'crossref', "
	child_type C(100),
	child_id I,
	parent_type C(100),
	parent_id I,
	create_date DT,
	modified_date DT
");
$db->Execute("ALTER TABLE ".cms_db_prefix()."crossref ADD INDEX (child_type, child_id)");
$db->Execute("ALTER TABLE ".cms_db_prefix()."crossref ADD INDEX (parent_type, parent_id)");

CmsInstallOperations::create_table($db, 'css', "
	id I KEY,
	css_name C(255),
	css_text XL,
	media_type C(255),
	create_date DT,
	modified_date DT
");
CmsInstallOperations::create_index($db, 'css', 'css_name');

CmsInstallOperations::create_table($db, 'css', "
	assoc_to_id I,
	assoc_css_id I,
	assoc_type C(80),
	create_date DT,
	modified_date DT
");
CmsInstallOperations::create_index($db, 'css_assoc', 'assoc_to_id');
CmsInstallOperations::create_index($db, 'css_assoc', 'assoc_css_id');

CmsInstallOperations::create_table($db, 'event_handlers', "
	event_id      I,
	tag_name      c(255),
	module_name   c(255),
	removable     I,
	handler_order I,
	id    I KEY
");

CmsInstallOperations::create_table($db, 'events', "
	originator   c(200) NOTNULL,
	event_name   c(200) NOTNULL,
	id     I KEY
");
CmsInstallOperations::create_index($db, 'events', 'originator');
CmsInstallOperations::create_index($db, 'events', 'event_name');

CmsInstallOperations::create_table($db, 'group_perms', "
	id I KEY,
	group_id I,
	permission_id I,
	create_date DT,
	modified_date DT
");
$db->Execute("ALTER TABLE ".cms_db_prefix()."group_perms ADD INDEX (group_id, permission_id)");

CmsInstallOperations::create_table($db, 'groups', "
	id I KEY,
	group_name C(25),
	active I1,
	create_date DT,
	modified_date DT
");

CmsInstallOperations::create_table($db, 'htmlblobs', "
	id I KEY,
	htmlblob_name C(255),
	html XL,
	owner I,
	create_date DT,
	modified_date DT
");
CmsInstallOperations::create_index($db, 'htmlblobs', 'htmlblob_name');

CmsInstallOperations::create_table($db, 'additional_htmlblob_users', "
	id I KEY,
	user_id I,
	htmlblob_id I
");

CmsInstallOperations::create_table($db, 'modules', "
	module_name C(255),
	status C(255),
	version C(255),
	admin_only I1 DEFAULT 0,
	active I1
");
CmsInstallOperations::create_index($db, 'modules', 'module_name');

CmsInstallOperations::create_table($db, 'module_deps', "
	parent_module C(25),
	child_module C(25),
	minimum_version C(25),
	create_date DT,
	modified_date DT
");

CmsInstallOperations::create_table($db, 'module_templates', "
	module_name C(200),
	template_name C(200),
	content XL,
	create_date DT,
	modified_date DT
");
$db->Execute("ALTER TABLE ".cms_db_prefix()."module_templates ADD INDEX (module_name, template_name)");

CmsInstallOperations::create_table($db, 'permissions', "
	id I KEY,
	permission_name C(255),
	permission_text C(255),
	create_date DT,
	modified_date DT
");

CmsInstallOperations::create_table($db, 'siteprefs', "
	sitepref_name C(255) KEY,
	sitepref_value text,
	create_date DT,
	modified_date DT
");

CmsInstallOperations::create_table($db, 'templates', "
	id I KEY,
	template_name C(255),
	template_content XL,
	encoding C(25),
	active I1,
	default_template I1,
	create_date DT,
	modified_date DT
");
CmsInstallOperations::create_index($db, 'templates', 'template_name');

CmsInstallOperations::create_table($db, 'user_groups', "
	group_id I,
	user_id I,
	create_date DT,
	modified_date DT
");

CmsInstallOperations::create_table($db, 'userprefs', "
	user_id I,
	preference C(50),
	value X,
	type C(25)
");
CmsInstallOperations::create_index($db, 'userprefs', 'user_id');

CmsInstallOperations::create_table($db, 'userprefs', "
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
");

CmsInstallOperations::create_table($db, 'userplugins', "
	id I KEY,
	userplugin_name C(255),
	code X,
	create_date DT,
	modified_date DT
");

CmsInstallOperations::create_table($db, 'version', "
	version I
");

# vim:ts=4 sw=4 noet
?>
