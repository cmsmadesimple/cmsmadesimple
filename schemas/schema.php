<?php

if (isset($CMS_INSTALL_DROP_TABLES)) {

	$db->DropSequence($db_prefix."additional_users_seq");
	$db->DropSequence($db_prefix."group_perms_seq");
	$db->DropSequence($db_prefix."groups_seq");
	$db->DropSequence($db_prefix."pages_seq");
	$db->DropSequence($db_prefix."permissions_seq");
	$db->DropSequence($db_prefix."templates_seq");
	$db->DropSequence($db_prefix."users_seq");

	$dbdict = NewDataDictionary($db);

	$sqlarray = $dbdict->DropIndexSQL("idx_template_id_modified_date");
	$dbdict->ExecuteSQLArray($sqlarray);

	$sqlarray = $dbdict->DropTableSQL($db_prefix."additional_users");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."adminlog");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."group_perms");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."groups");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."modules");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."pages");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."permissions");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."templates");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."user_groups");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."userprefs");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."users");
	$dbdict->ExecuteSQLArray($sqlarray);
	$sqlarray = $dbdict->DropTableSQL($db_prefix."version");
	$dbdict->ExecuteSQLArray($sqlarray);

}

if (isset($CMS_INSTALL_CREATE_TABLES)) {

	echo "<p>Creating additional_users table...";

	$dbdict = NewDataDictionary($db);
	$flds = "
		additional_users_id I KEY,
		user_id I,
		page_id I
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."additional_users", $flds, $taboptarray);
	$dbdict->ExecuteSQLArray($sqlarray);
	$db->CreateSequence($db_prefix."additional_users_seq", 1);

	echo "[done]</p>";

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

	echo "<p>Creating group_perms table...";

	$dbdict = NewDataDictionary($db);
	$flds = "
		group_perm_id I KEY,
		group_id I,
		permission_id I,
		create_date T,
		modified_date T
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."group_perms", $flds, $taboptarray);
	$dbdict->ExecuteSQLArray($sqlarray);
	$db->CreateSequence($db_prefix."group_perms_seq", 1);

	echo "[done]</p>";

	echo "<p>Creating groups table...";

	$dbdict = NewDataDictionary($db);
	$flds = "
		group_id I KEY,
		group_name C(25),
		active I1,
		create_date T,
		modified_date I
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."groups", $flds, $taboptarray);
	$dbdict->ExecuteSQLArray($sqlarray);

	$db->CreateSequence($db_prefix."groups_seq", 1);

	echo "[done]</p>";

	echo "<p>Creating modules table...";

	$dbdict = NewDataDictionary($db);
	$flds = "
		module_name C(255),
		status C(255),
		version C(255),
		active L
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."modules", $flds, $taboptarray);
	$dbdict->ExecuteSQLArray($sqlarray);

	echo "[done]</p>";

	echo "<p>Creating pages table...";

	$dbdict = NewDataDictionary($db);
	$flds = "
		page_id I KEY,
		page_title C(255),
		page_url C(255),
		page_content X,
		menu_text C(25),
		default_page I1,
		show_in_menu I1,
		page_type C(25),
		owner I,
		item_order I,
		active I1,
		parent_id I,
		template_id I,
		create_date T,
		modified_date T
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."pages", $flds, $taboptarray);
	$dbdict->ExecuteSQLArray($sqlarray);

	$db->CreateSequence($db_prefix."pages_seq", 1);

	$sqlarray = $dbdict->CreateIndexSQL("idx_template_id_modified_date", $db_prefix."pages", array("template_id","modified_date"));
	$dbdict->ExecuteSQLArray($sqlarray);

	echo "[done]</p>";

	echo "<p>Creating permissions table...";

	$dbdict = NewDataDictionary($db);
	$flds = "
		permission_id I KEY,
		permission_name C(25),
		permission_text C(255),
		create_date T,
		modified_date T
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."permissions", $flds, $taboptarray);
	$dbdict->ExecuteSQLArray($sqlarray);

	$db->CreateSequence($db_prefix."permissions_seq", 1);

	echo "[done]</p>";

	echo "<p>Creating templates table...";

	$dbdict = NewDataDictionary($db);
	$flds = "
		template_id I KEY,
		template_name C(25),
		template_content X,
		stylesheet X,
		active I1,
		create_date T,
		modified_date T
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."templates", $flds, $taboptarray);
	$dbdict->ExecuteSQLArray($sqlarray);

	$db->CreateSequence($db_prefix."templates_seq", 1);

	echo "[done]</p>";

	echo "<p>Creating user_groups table...";

	$dbdict = NewDataDictionary($db);
	$flds = "
		group_id I,
		user_id I,
		create_date T,
		modified_date T
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
		value C(255),
		type C(25)
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."userprefs", $flds, $taboptarray);
	$dbdict->ExecuteSQLArray($sqlarray);

	echo "[done]</p>";

	echo "<p>Creating users table...";

	$dbdict = NewDataDictionary($db);
	$flds = "
		user_id I KEY,
		username C(25),
		password C(40),
		active I1,
		create_date T,
		modified_date T
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dbdict->CreateTableSQL($db_prefix."users", $flds, $taboptarray);
	$dbdict->ExecuteSQLArray($sqlarray);

	$db->CreateSequence($db_prefix."users_seq", 1);

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

}

# vim:ts=4 sw=4 noet
?>
