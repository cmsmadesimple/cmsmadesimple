<?php

if (isset($CMS_INSTALL_DROP_TABLES)) {

	$db->DropSequence($db_prefix."additional_htmlblob_users_seq");
	$db->DropSequence($db_prefix."additional_users_seq");
	$db->DropSequence($db_prefix."admin_bookmarks_seq");
	$db->DropSequence($db_prefix."admin_recent_pages_seq");
	$db->DropSequence($db_prefix."additional_users_seq");
	$db->DropSequence($db_prefix."content_seq");
	$db->DropSequence($db_prefix."content_props_seq");
	$db->DropSequence($db_prefix."css_seq");
	$db->DropSequence($db_prefix."events_seq");
	$db->DropSequence($db_prefix."event_handler_seq");
	$db->DropSequence($db_prefix."group_perms_seq");
	$db->DropSequence($db_prefix."groups_seq");
	$db->DropSequence($db_prefix."htmlblobs_seq");
	$db->DropSequence($db_prefix."module_deps_seq");
	$db->DropSequence($db_prefix."module_templates_seq");
	$db->DropSequence($db_prefix."permissions_seq");
	$db->DropSequence($db_prefix."templates_seq");
	$db->DropSequence($db_prefix."users_seq");
	$db->DropSequence($db_prefix."userplugins_seq");

	$dbdict = NewDataDictionary($db);

	$sqlarray = $dbdict->DropIndexSQL("idx_template_id_modified_date");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropIndexSQL($db_prefix."idx_template_id_modified_date");
	$dbdict->ExecuteSQLArray($sqlarray);

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
	$sqlarray = $dbdict->DropTableSQL($db_prefix."content");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."content_props");
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
	$sqlarray = $dbdict->DropTableSQL($db_prefix."module_deps");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."module_templates");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."permissions");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."siteprefs");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."templates");
	$dbdict->ExecuteSQLArray($sqlarray);
	#$sqlarray = $dbdict->DropTableSQL($db_prefix."sequence");
	#$dbdict->ExecuteSQLArray($sqlarray);
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
	$sqlarray = $dbdict->DropTableSQL($db_prefix."module_smarty_plugins");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."routes");
	$dbdict->ExecuteSQLArray($sqlarray);
}

if (isset($CMS_INSTALL_CREATE_TABLES)) {
	
	if ($db->dbtype == 'mysql' || $db->dbtype == 'mysqli')
		@$db->Execute("ALTER DATABASE `" . $db->database . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

	$dbdict = NewDataDictionary($db);
	$taboptarray = array('mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci', 'mysqli' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');



	$flds = "
		additional_users_id I KEY,
		user_id I,
		page_id I,
		content_id I
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."additional_users", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'additional_users', $ado_ret);



	$flds = "
		bookmark_id I KEY,
		user_id I,
		title C(255),
		url C(255)
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."admin_bookmarks", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'admin_bookmarks', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_admin_bookmarks_by_user_id', $db_prefix."admin_bookmarks", 'user_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'admin_bookmarks', $ado_ret);



	$flds = "
		timestamp I,
		user_id I,
		username C(25),
		item_id I,
		item_name C(50),
		action C(255),
                ip_addr C(40)
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."adminlog", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	if( $return == 2 )
	  {
	    $sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_adminlog1',$db_prefix."adminlog",'timestamp');
	    $return = $dbdict->ExecuteSQLArray($sqlarray);
	  }
	echo ilang('install_creating_table', 'adminlog', $ado_ret);



	$flds = "
		id I KEY,
		user_id I,
		title C(255),
		url C(255),
		access_time DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."admin_recent_pages", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'admin_recent_pages', $ado_ret);



	$flds = "
		content_id I KEY,
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
		modified_date DT,
                secure I1,
                page_url C(255)
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."content", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'content', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_content_by_content_alias_active', $db_prefix."content", 'content_alias, active');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_content_by_default_content', $db_prefix."content", 'default_content');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_content_by_parent_id', $db_prefix."content", 'parent_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_content_by_hierarchy', $db_prefix."content", 'hierarchy');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_content_by_idhier', $db_prefix."content", 'content_id, hierarchy');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);

	$flds = "
		content_id I,
		type C(25),
		prop_name C(255),
		param1 C(255),
		param2 C(255),
		param3 C(255),
		content X,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."content_props", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'content_props', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_content_props_by_content_id', $db_prefix."content_props", 'content_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);



	$flds = '
		child_type C(100),
		child_id I,
		parent_type C(100),
		parent_id I,
		create_date DT,
		modified_date DT
	';

	$sqlarray = $dbdict->CreateTableSQL($db_prefix."crossref", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'crossref', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_crossref_by_child_type_child_id', $db_prefix."crossref", 'child_type, child_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_crossref_by_parent_type_parent_id', $db_prefix."crossref", 'parent_type, parent_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);



	$flds = "
		css_id I KEY,
		css_name C(255),
		css_text X,
		media_type C(255),
		media_query X,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."css", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'css', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_css_by_css_name', $db_prefix."css", 'css_name');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);



	$flds = "
		assoc_to_id I,
		assoc_css_id I,
		assoc_type C(80),
		create_date DT,
		modified_date DT,
		assoc_order I
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."css_assoc", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'css_assoc', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_css_assoc_by_assoc_to_id', $db_prefix."css_assoc", 'assoc_to_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_css_assoc_by_assoc_css_id', $db_prefix."css_assoc", 'assoc_css_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);

	

	$flds = "
		event_id I,
		tag_name C(255),
		module_name C(160),
		removable I,
		handler_order I,
		handler_id I KEY
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."event_handlers", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'event_handlers', $ado_ret);



	$flds = "
		originator C(200) NOTNULL,
		event_name C(200) NOTNULL,
		event_id I KEY
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."events", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'events', $ado_ret);
	
	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'originator', $db_prefix."events", 'originator');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);
    
	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'event_name', $db_prefix."events", 'event_name');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'event_id', $db_prefix."events", 'event_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);
	
	

	$flds = "
		group_perm_id I KEY,
		group_id I,
		permission_id I,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."group_perms", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'group_perms', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_group_perms_by_group_id_permission_id', $db_prefix."group_perms", 'group_id, permission_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);



	$flds = "
		group_id I KEY,
		group_name C(25),
		active I1,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."groups", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'groups', $ado_ret);



	$flds = "
		htmlblob_id I KEY,
		htmlblob_name C(255),
		html X,
                description X,
                use_wysiwyg I1,
		owner I,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."htmlblobs", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'htmlblobs', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_htmlblobs_by_htmlblob_name', $db_prefix."htmlblobs", 'htmlblob_name');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);



	$flds = "
		additional_htmlblob_users_id I KEY,
		user_id I,
		htmlblob_id I
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."additional_htmlblob_users", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'additional_htmlblob_users', $ado_ret);



	$flds = "
		module_name C(160) KEY,
		status C(255),
		version C(255),
		admin_only I1 DEFAULT 0,
		active I1,
                allow_fe_lazyload I1,
                allow_admin_lazyload I1
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."modules", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'modules', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_modules_by_module_name', $db_prefix."modules", 'module_name');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);



	$flds = "
		parent_module C(25),
		child_module C(25),
		minimum_version C(25),
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."module_deps", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'module_deps', $ado_ret);



	$flds = "
		module_name C(160),
		template_name C(160),
		content X,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."module_templates", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'module_templates', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_module_templates_by_module_name_template_name', $db_prefix."module_templates", 'module_name, template_name');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);



	$flds = "
		permission_id I KEY,
		permission_name C(255),
		permission_text C(255),
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."permissions", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'permissions', $ado_ret);



	$flds = "
		sitepref_name C(255) KEY,
		sitepref_value text,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."siteprefs", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'siteprefs', $ado_ret);



	$flds = "
		template_id I KEY,
		template_name C(160),
		template_content X,
		stylesheet X,
		encoding C(25),
		active I1,
		default_template I1,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."templates", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'templates', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_templates_by_template_name', $db_prefix."templates", 'template_name');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);



	$flds = "
		group_id I,
		user_id I,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."user_groups", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'user_groups', $ado_ret);



	$flds = "
		user_id I KEY,
		preference C(50) KEY,
		value X,
		type C(25)
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."userprefs", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'userprefs', $ado_ret);

	$sqlarray = $dbdict->CreateIndexSQL($db_prefix.'index_userprefs_by_user_id', $db_prefix."userprefs", 'user_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_index', 'content', $ado_ret);



	$flds = "
		user_id I KEY,
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
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."users", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'users', $ado_ret);



	$flds = "
		userplugin_id I KEY,
		userplugin_name C(255),
		code X,
		description X,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."userplugins", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'userplugins', $ado_ret);



	$flds = "
		version I
	";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."version", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'version', $ado_ret);
	

	
	$flds = "
                sig  C(80) KEY NOT NULL,
                name C(80) NOT NULL,
                module C(160) NOT NULL,
                type C(40) NOT NULL,
                callback C(255) NOT NULL,
                available I,
                cachable I1
        ";
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."module_smarty_plugins", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'module_smarty_plugins', $ado_ret);



	$flds = "
                term C(255) KEY NOT NULL,
                key1 C(50) KEY NOT NULL,
                key2 C(50),
                key3 C(50),
                data X, 
                created ".CMS_ADODB_DT;
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."routes", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	echo ilang('install_creating_table', 'routes', $ado_ret);
}

# vim:ts=4 sw=4 noet
?>
