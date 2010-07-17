<?php

if (isset($CMS_INSTALL_DROP_TABLES)) {

	@$db->DropSequence($db_prefix."additional_htmlblob_users_seq");
	@$db->DropSequence($db_prefix."additional_users_seq");
	@$db->DropSequence($db_prefix."admin_bookmarks_seq");
	@$db->DropSequence($db_prefix."admin_recent_pages_seq");
	@$db->DropSequence($db_prefix."content_seq");
	@$db->DropSequence($db_prefix."content_props_seq");
	@$db->DropSequence($db_prefix."css_seq");
	@$db->DropSequence($db_prefix."events_seq");
	@$db->DropSequence($db_prefix."event_handler_seq");
	@$db->DropSequence($db_prefix."group_perms_seq");
	@$db->DropSequence($db_prefix."groups_seq");
	@$db->DropSequence($db_prefix."htmlblobs_seq");
	@$db->DropSequence($db_prefix."permissions_seq");
	@$db->DropSequence($db_prefix."templates_seq");
	@$db->DropSequence($db_prefix."users_seq");
	@$db->DropSequence($db_prefix."userplugins_seq");

	$dbdict = NewDataDictionary($db);

	$sqlarray = $dbdict->DropTableSQL($db_prefix."additional_users");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."additional_htmlblob_users");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."adminlog");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."admin_bookmarks");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."admin_recent_pages");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."cache");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."content");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."content_types");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."crossref");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."css");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."css_assoc");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."events");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."event_handlers");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."group_perms");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."groups");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."htmlblobs");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."modules");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."module_templates");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."pages");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."permissions");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."siteprefs");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."templates");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."user_groups");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."userprefs");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."users");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."userplugins");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."version");
	$dbdict->ExecuteSQLArray($sqlarray);

}

if (isset($CMS_INSTALL_CREATE_TABLES)) {

	if (isset($db->dbtype))
	  if ($db->dbtype == 'mysql' || $db->dbtype == 'mysqli')
		@$db->Execute("ALTER DATABASE `" . $db->database . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

	$dbdict = NewDataDictionary($db);
	$taboptarray = array('mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci', 'mysqli' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');

	$flds = "
		additional_users_id I KEY AUTO,
		user_id I,
		page_id I,
		content_id I
	";
	echo installer_create_tablesql($dbdict, $db_prefix."additional_users", $flds, $taboptarray);

	$flds = "
		bookmark_id I KEY AUTO,
		user_id I,
		title C(255),
		url C(255)
	";
	echo installer_create_tablesql($dbdict, $db_prefix."admin_bookmarks", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."admin_bookmarks", array('user_id'));

	$flds = "
		timestamp I,
		user_id I,
		username C(25),
		item_id I,
		item_name C(50),
		action C(255)
	";
	echo installer_create_tablesql($dbdict, $db_prefix."adminlog", $flds, $taboptarray);

	$flds = "
		id I KEY AUTO,
		user_id I,
		title C(255),
		url C(255),
		access_time T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."admin_recent_pages", $flds, $taboptarray);

	$flds = "
		k C(255) UNIQUE KEY,
		v XL,
		expiry T,
		create_date T,
		modified_date T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."cache", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."cache", array('k'));
	echo installer_create_indexsql($dbdict, $db_prefix."cache", array('expiry'));

	$flds = "
		id I KEY AUTO,
		type C(25),
		owner_id I,
		parent_id I,
		template_id I,
		item_order I,
		hierarchy C(255),
		default_content I1 default 0,
		content_alias C(255),
		show_in_menu I1 default 0,
		active I1 default 1,
		cachable I1 default 1,
		id_hierarchy C(255),
		hierarchy_path X,
		metadata X,
		titleattribute C(255),
		tabindex C(10),
		accesskey C(5),
		target C(25),
		secure I1 default 0,
		last_modified_by I,
		url_text C(255),
		lft I1,
		rgt I1,
		create_date T,
		modified_date T,
		extra_params XL
	";
	echo installer_create_tablesql($dbdict, $db_prefix."pages", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."pages", array('content_alias', 'active'));
	echo installer_create_indexsql($dbdict, $db_prefix."pages", array('default_content'));
	echo installer_create_indexsql($dbdict, $db_prefix."pages", array('parent_id'));
	
	$flds = "
		id I KEY AUTO,
		type C(50),
		lang C(50),
		title C(255),
		autotitled I1 default 0,
		content XL,
		unique_name C(255),
		last_modified_by I,
		create_date T,
		modified_date T,
		extra_params XL
	";
	echo installer_create_tablesql($dbdict, $db_prefix."content", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."content", array('type'));
	echo installer_create_indexsql($dbdict, $db_prefix."content", array('type', 'lang'));
	echo installer_create_indexsql($dbdict, $db_prefix."content", array('unique_name'));
	
	$flds = "
		id I KEY AUTO,
		name C(50),
		description XL,
		module C(50),
		class C(50),
		create_date T,
		modified_date T,
		extra_params XL
	";
	echo installer_create_tablesql($dbdict, $db_prefix."content_types", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."content_types", array('name'));

	$flds = '
		child_type C(100),
		child_id I,
		parent_type C(100),
		parent_id I,
		create_date T,
		modified_date T
	';
	echo installer_create_tablesql($dbdict, $db_prefix."crossref", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."crossref", array('child_type', 'child_id'));
	echo installer_create_indexsql($dbdict, $db_prefix."crossref", array('parent_type', 'parent_id'));

	$flds = "
		css_id I KEY AUTO,
		css_name C(255),
		css_text X,
		media_type C(255),
		create_date T,
		modified_date T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."css", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."css", array('css_name'));

	$flds = "
		assoc_to_id I,
		assoc_css_id I,
		assoc_type C(80),
		create_date T,
		modified_date T,
		assoc_order I
	";
	echo installer_create_tablesql($dbdict, $db_prefix."css_assoc", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."css_assoc", array('assoc_to_id'));
	echo installer_create_indexsql($dbdict, $db_prefix."css_assoc", array('assoc_css_id'));

	$flds = "
		event_id I,
		tag_name C(255),
		module_name C(160),
		removable I,
		handler_order I,
		handler_id I KEY AUTO
	";
	echo installer_create_tablesql($dbdict, $db_prefix."event_handlers", $flds, $taboptarray);

	$flds = "
		originator C(200) NOTNULL,
		event_name C(200) NOTNULL,
		event_id I KEY AUTO
	";
	echo installer_create_tablesql($dbdict, $db_prefix."events", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."events", array('originator'));
	echo installer_create_indexsql($dbdict, $db_prefix."events", array('event_name'));

	$flds = "
		group_perm_id I KEY AUTO,
		group_id I,
		permission_id I,
		object_id I DEFAULT 0,
		has_access I(1) DEFAULT 1,
		create_date T,
		modified_date T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."group_perms", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."group_perms", array('group_id', 'permission_id'));
	echo installer_create_indexsql($dbdict, $db_prefix."group_perms", array('group_id', 'permission_id', 'object_id'));

	$flds = "
		group_id I KEY AUTO,
		group_name C(25),
		active I1,
		create_date T,
		modified_date T,
		extra_params XL
	";
	echo installer_create_tablesql($dbdict, $db_prefix."groups", $flds, $taboptarray);

	$flds = "
		htmlblob_id I KEY AUTO,
		htmlblob_name C(255),
		html X,
		owner I,
		create_date T,
		modified_date T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."htmlblobs", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."htmlblobs", array('htmlblob_name'));

	$flds = "
		additional_htmlblob_users_id I KEY AUTO,
		user_id I,
		htmlblob_id I
	";
	echo installer_create_tablesql($dbdict, $db_prefix."additional_htmlblob_users", $flds, $taboptarray);

	$flds = "
		module_name C(160),
		status C(255),
		version C(255),
		admin_only I1 DEFAULT 0,
		active I1
	";
	echo installer_create_tablesql($dbdict, $db_prefix."modules", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."modules", array('module_name'));

	$flds = "
    id I KEY AUTO,
    module_name C(160) NOTNULL,
    template_type C(160) NOTNULL default '',
    template_name C(160) NOTNULL,
    content XL,
    default_template I1 default 0,
    create_date T,
    modified_date T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."module_templates", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."module_templates", array('module_name', 'template_name'));

	$flds = "
		permission_id I KEY AUTO,
		permission_name C(255),
		permission_text C(255),
		module C(100),
		extra_attr C(50),
		hierarchical I(1) DEFAULT 0,
		link_table C(50),
		create_date T,
		modified_date T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."permissions", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."permissions", array('permission_name', 'module', 'extra_attr'));

	$flds = "
		sitepref_name C(255) KEY,
		sitepref_value text,
		create_date T,
		modified_date T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."siteprefs", $flds, $taboptarray);

	$flds = "
		template_id I KEY AUTO,
		template_name C(160),
		template_content X,
		stylesheet X,
		encoding C(25),
		active I1,
		default_template I1,
		create_date T,
		modified_date T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."templates", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."templates", array('template_name'));

	$flds = "
		group_id I KEY,
		user_id I KEY,
		create_date T,
		modified_date T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."user_groups", $flds, $taboptarray);

	$flds = "
		user_id I,
		preference C(50),
		value X,
		type C(25)
	";
	echo installer_create_tablesql($dbdict, $db_prefix."userprefs", $flds, $taboptarray);
	echo installer_create_indexsql($dbdict, $db_prefix."userprefs", array('user_id'));

	$flds = "
		user_id I KEY AUTO,
		username C(25),
		password C(40),
		admin_access I1,
		first_name C(50),
		last_name C(50),
		email C(255),
		active I1,
		create_date T,
		modified_date T,
		extra_params XL
	";
	echo installer_create_tablesql($dbdict, $db_prefix."users", $flds, $taboptarray);

	$flds = "
		userplugin_id I KEY AUTO,
		userplugin_name C(255),
		code X,
		create_date T,
		modified_date T
	";
	echo installer_create_tablesql($dbdict, $db_prefix."userplugins", $flds, $taboptarray);

	$flds = "
		version I
	";
	echo installer_create_tablesql($dbdict, $db_prefix."version", $flds, $taboptarray);
}

# vim:ts=4 sw=4 noet
?>
