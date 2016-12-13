<?php

if (isset($CMS_INSTALL_DROP_TABLES)) {

  status_msg(ilang('install_dropping_tables'));
  $db->DropSequence(CMS_DB_PREFIX."additional_users_seq");
  $db->DropSequence(CMS_DB_PREFIX."admin_bookmarks_seq");
  $db->DropSequence(CMS_DB_PREFIX."additional_users_seq");
  $db->DropSequence(CMS_DB_PREFIX."content_seq");
  $db->DropSequence(CMS_DB_PREFIX."content_props_seq");
  $db->DropSequence(CMS_DB_PREFIX."events_seq");
  $db->DropSequence(CMS_DB_PREFIX."event_handler_seq");
  $db->DropSequence(CMS_DB_PREFIX."group_perms_seq");
  $db->DropSequence(CMS_DB_PREFIX."groups_seq");
  $db->DropSequence(CMS_DB_PREFIX."module_deps_seq");
  $db->DropSequence(CMS_DB_PREFIX."module_templates_seq");
  $db->DropSequence(CMS_DB_PREFIX."permissions_seq");
  $db->DropSequence(CMS_DB_PREFIX."users_seq");
  $db->DropSequence(CMS_DB_PREFIX."userplugins_seq");

  $dbdict = NewDataDictionary($db);

  $sqlarray = $dbdict->DropIndexSQL("idx_template_id_modified_date");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropIndexSQL(CMS_DB_PREFIX."idx_template_id_modified_date");
  $dbdict->ExecuteSQLArray($sqlarray);

  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."additional_users");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."adminlog");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."admin_bookmarks");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."content");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."content_props");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."events");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."event_handlers");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."group_perms");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."groups");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."modules");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."module_deps");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."module_templates");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."permissions");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."siteprefs");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."user_groups");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."userprefs");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."users");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."userplugins");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."version");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."module_smarty_plugins");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX."routes");
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.CmsLayoutTemplateType::TABLENAME);
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.CmsLayoutTemplateCategory::TABLENAME);
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME);
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.CmsLayoutTemplate::ADDUSERSTABLE);
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.CmsLayoutStylesheet::TABLENAME);
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.CmsLayoutCollection::TABLENAME);
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE);
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE);
  $dbdict->ExecuteSQLArray($sqlarray);
  $sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.CmsLock::LOCK_TABLE);
  $dbdict->ExecuteSQLArray($sqlarray);
}

if (isset($CMS_INSTALL_CREATE_TABLES)) {

  status_msg(ilang('install_createtablesindexes'));
  if ($db->dbtype == 'mysql' || $db->dbtype == 'mysqli') {
    @$db->Execute("ALTER DATABASE `" . $db->database . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
  }

  $dbdict = NewDataDictionary($db);
  $taboptarray = array('mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci', 'mysqli' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');

	$flds = "
		additional_users_id I KEY,
		user_id I,
		page_id I,
		content_id I
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."additional_users", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'additional_users', $ado_ret));



	$flds = "
		bookmark_id I KEY,
		user_id I,
		title C(255),
		url C(255)
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."admin_bookmarks", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'admin_bookmarks', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_admin_bookmarks_by_user_id', CMS_DB_PREFIX."admin_bookmarks", 'user_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'admin_bookmarks', $ado_ret));


	$flds = "
		timestamp I,
		user_id I,
		username C(25),
		item_id I,
		item_name C(50),
		action C(255),
                ip_addr C(40)
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."adminlog", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	if( $return == 2 )
	  {
	    $sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_adminlog1',CMS_DB_PREFIX."adminlog",'timestamp');
	    $return = $dbdict->ExecuteSQLArray($sqlarray);
	  }
	verbose_msg(ilang('install_created_table', 'adminlog', $ado_ret));

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
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."content", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'content', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_content_by_alias_active', CMS_DB_PREFIX."content", 'content_alias, active');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_content_by_alias_active', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_content_default_content', CMS_DB_PREFIX."content", 'default_content');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_content_default_content', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_content_by_parent_id', CMS_DB_PREFIX."content", 'parent_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_content_by_parent_id', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_content_by_hier', CMS_DB_PREFIX."content", 'hierarchy');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_content_by_hier', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_content_by_idhier', CMS_DB_PREFIX."content", 'content_id, hierarchy');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_content_by_idhier', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_content_by_modified', CMS_DB_PREFIX."content", 'modified_date');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_content_by_modified', $ado_ret));

	$flds = "
		content_id I,
		type C(25),
		prop_name C(255),
		param1 C(255),
		param2 C(255),
		param3 C(255),
		content X2,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."content_props", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'content_props', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_content_props_by_content', CMS_DB_PREFIX."content_props", 'content_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_content_props_by_content', $ado_ret));

	$flds = "
		event_id I,
		tag_name C(255),
		module_name C(160),
		removable I,
		handler_order I,
		handler_id I KEY
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."event_handlers", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'event_handlers', $ado_ret));



	$flds = "
		originator C(200) NOTNULL,
		event_name C(200) NOTNULL,
		event_id I KEY
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."events", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'events', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'originator', CMS_DB_PREFIX."events", 'originator');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'originator', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'event_name', CMS_DB_PREFIX."events", 'event_name');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'event_name', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'event_id', CMS_DB_PREFIX."events", 'event_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'event_id', $ado_ret));

	$flds = "
		group_perm_id I KEY,
		group_id I,
		permission_id I,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."group_perms", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'group_perms', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_grp_perms_by_grp_id_perm_id', CMS_DB_PREFIX."group_perms", 'group_id, permission_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_grp_perms_by_grp_id_perm_id', $ado_ret));

	$flds = "
		group_id I KEY,
		group_name C(25),
        group_desc C(255),
		active I1,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."groups", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'groups', $ado_ret));



	$flds = "
		module_name C(160) KEY,
		status C(255),
		version C(255),
		admin_only I1 DEFAULT 0,
		active I1,
                allow_fe_lazyload I1,
                allow_admin_lazyload I1
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."modules", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'modules', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_modules_by_name', CMS_DB_PREFIX."modules", 'module_name');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_modules_by_name', $ado_ret));



	$flds = "
		parent_module C(25),
		child_module C(25),
		minimum_version C(25),
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."module_deps", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'module_deps', $ado_ret));


	// deprecated
	$flds = "
		module_name C(160),
		template_name C(160),
		content X,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."module_templates", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'module_templates', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_module_templates_by_module_and_tpl_name', CMS_DB_PREFIX."module_templates", 'module_name, template_name');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_module_templates_by_module_and_tpl_name', $ado_ret));


	$flds = "
		permission_id I KEY,
		permission_name C(255),
		permission_text C(255),
        permission_source C(255),
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."permissions", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'permissions', $ado_ret));


	$flds = "
		sitepref_name C(255) KEY,
		sitepref_value text,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."siteprefs", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'siteprefs', $ado_ret));



	$flds = "
		group_id I KEY,
		user_id I KEY,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."user_groups", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'user_groups', $ado_ret));


	$flds = "
		user_id I KEY,
		preference C(50) KEY,
		value X,
		type C(25)
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."userprefs", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'userprefs', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_userprefs_by_user_id', CMS_DB_PREFIX."userprefs", 'user_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_userprefs_by_user_id', $ado_ret));



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
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."users", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'users', $ado_ret));



	$flds = "
		userplugin_id I KEY,
		userplugin_name C(255),
		code X,
		description X,
		create_date DT,
		modified_date DT
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."userplugins", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'userplugins', $ado_ret));



	$flds = "
		version I
	";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."version", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'version', $ado_ret));



	$flds = "
                sig  C(80) KEY NOTNULL,
                name C(80) NOTNULL,
                module C(160) NOTNULL,
                type C(40) NOTNULL,
                callback C(255) NOTNULL,
                available I,
                cachable I1
        ";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."module_smarty_plugins", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'module_smarty_plugins', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_smp_module', CMS_DB_PREFIX."module_smarty_plugins", 'module');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_smp_module', $ado_ret));


	$flds = "
                term C(255) KEY NOTNULL,
                key1 C(50) KEY NOTNULL,
                key2 C(50),
                key3 C(50),
                data X,
                created ".CMS_ADODB_DT;
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX."routes", $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', 'routes', $ado_ret));

	$flds = "
         id I KEY AUTO,
         originator C(50) NOTNULL,
         name C(100) NOTNULL,
         has_dflt I1,
         dflt_contents X2,
         description X,
         lang_cb     C(255),
         dflt_content_cb C(255),
         requires_contentblocks I1,
         help_content_cb C(255),
         one_only I1,
         owner   I,
         created I,
         modified I";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutTemplateType::TABLENAME, $flds,
					  $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', CmsLayoutTemplateType::TABLENAME, $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_tpl_type_1', CMS_DB_PREFIX.CmsLayoutTemplateType::TABLENAME,
                                        'originator,name',array('UNIQUE'));
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_layout_tpl_type_1', $ado_ret));


	$flds = "
         id I KEY AUTO,
         name C(100) NOTNULL,
         description X,
         item_order X,
         modified I";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutTemplateCategory::TABLENAME, $flds,
					  $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	verbose_msg(ilang('install_created_table', CmsLayoutTemplateCategory::TABLENAME, $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_tpl_cat_1', CMS_DB_PREFIX.CmsLayoutTemplateCategory::TABLENAME,
                                        'name',array('UNIQUE'));
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_layout_tpl_type_1', $ado_ret));

	$flds = "
         id I KEY AUTO,
         name C(100) NOTNULL,
         content X2,
         description X,
         type_id I NOTNULL,
         type_dflt I1,
         category_id I,
         owner_id I NOTNULL,
         listable I1 DEFAULT 1,
         created I,
         modified I";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME, $flds,
					  $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', CmsLayoutTemplate::TABLENAME, $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_tpl_1', CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME, 'name',array('UNIQUE'));
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_layout_tpl_1', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_tpl_2', CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME, 'type_id,type_dflt');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_creating_index', 'idx_layout_tpl_2', $ado_ret));

	$flds = "
         id I KEY AUTO,
         name C(100) NOTNULL,
         content X2,
         description X,
  	     media_type C(255),
	     media_query X,
         created I,
         modified I";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutStylesheet::TABLENAME, $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', CmsLayoutStylesheet::TABLENAME, $ado_ret));
    $sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_css_1',CMS_DB_PREFIX.CmsLayoutStylesheet::TABLENAME, 'name', array('UNIQUE'));
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_index', 'idx_layout_css_1', $ado_ret));

	$flds = "
         tpl_id I KEY,
         user_id I KEY
        ";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutTemplate::ADDUSERSTABLE, $flds,
					  $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	verbose_msg(ilang('install_created_table', CmsLayoutTemplate::ADDUSERSTABLE, $ado_ret));


	$flds = "
         id   I KEY AUTO,
         name C(100) NOTNULL,
         description X,
         dflt I1,
         created I,
         modified I
        ";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutCollection::TABLENAME, $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', CmsLayoutCollection::TABLENAME, $ado_ret));
    $sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_dsn_1',CMS_DB_PREFIX.CmsLayoutCollection::TABLENAME, 'name', array('UNIQUE'));
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_index', 'idx_layout_dsn_1', $ado_ret));


	$flds = "
         design_id I KEY NOTNULL,
         tpl_id   I KEY NOTNULL
        ";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE, $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', CmsLayoutCollection::TPLTABLE, $ado_ret));
	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_dsnassoc1', CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE, 'tpl_id');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_index', 'index_dsnassoc1', $ado_ret));

	$flds = "
         design_id I KEY NOTNULL,
         css_id   I KEY NOTNULL,
         item_order I NOTNULL
        ";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE, $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', CmsLayoutCollection::CSSTABLE, $ado_ret));

	$flds = "
                 id I AUTO KEY NOTNULL,
                 type C(20) NOTNULL,
                 oid  I NOTNULL,
                 uid  I NOTNULL,
                 created I NOTNULL,
                 modified I NOTNULL,
                 lifetime I NOTNULL,
                 expires  I NOTNULL
                ";
	$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLock::LOCK_TABLE, $flds, $taboptarray);
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	$ado_ret = ($return == 2) ? ilang('done') : ilang('failed');
	verbose_msg(ilang('install_created_table', CmsLock::LOCK_TABLE, $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_locks1', CMS_DB_PREFIX."locks", 'type,oid', array('UNIQUE'));
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	verbose_msg(ilang('install_created_index', 'index_locks1', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_locks2', CMS_DB_PREFIX."locks", 'expires');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	verbose_msg(ilang('install_created_index', 'index_locks2', $ado_ret));

	$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_locks3', CMS_DB_PREFIX."locks", 'uid');
	$return = $dbdict->ExecuteSQLArray($sqlarray);
	verbose_msg(ilang('install_created_index', 'index_locks3', $ado_ret));

}

# vim:ts=4 sw=4 noet
?>
