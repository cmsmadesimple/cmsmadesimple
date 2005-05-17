<?php

if (isset($CMS_INSTALL_CREATE_TABLES)) {

	echo "<p>Creating additional_users table sequence...";

	$max = $db->GetOne("SELECT max(additional_users_id) from ".$db_prefix."additional_users");
	$db->CreateSequence($db_prefix."additional_users_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating admin_bookmarks table sequence...";

	$max = $db->GetOne("SELECT max(bookmark_id) from ".$db_prefix."admin_bookmarks");
	$db->CreateSequence($db_prefix."admin_bookmarks_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating admin_recent_pages table sequence...";

	$max = $db->GetOne("SELECT max(id) from ".$db_prefix."admin_recent_pages");
	$db->CreateSequence(cms_db_prefix()."admin_recent_pages_seq");

	echo "[done]</p>";

	echo "<p>Creating content table sequence...";

	$max = $db->GetOne("SELECT max(content_id) from ".$db_prefix."content");
	$db->CreateSequence($db_prefix."content_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating content_props table sequence...";

	$max = $db->GetOne("SELECT max(content_prop_id) from ".$db_prefix."content_props");
	$db->CreateSequence($db_prefix."content_props_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating css table sequence...";

	$max = $db->GetOne("SELECT max(css_id) from ".$db_prefix."css");
	$db->CreateSequence($db_prefix."css_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating group_perms table sequence...";

	$max = $db->GetOne("SELECT max(group_perm_id) from ".$db_prefix."group_perms");
	$db->CreateSequence($db_prefix."group_perms_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating groups table sequence...";

	$max = $db->GetOne("SELECT max(group_id) from ".$db_prefix."groups");
	$db->CreateSequence($db_prefix."groups_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating htmlblobs table sequence...";

	$max = $db->GetOne("SELECT max(htmlblob_id) from ".$db_prefix."htmlblobs");
	$db->CreateSequence($db_prefix."htmlblobs_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating additional_htmlblob_users table sequence...";

	$max = $db->GetOne("SELECT max(additional_htmlblob_users_id) from ".$db_prefix."additional_htmlblob_users");
	$db->CreateSequence($db_prefix."additional_htmlblobs_users_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating module_news table sequence...";

	$max = $db->GetOne("SELECT max(news_id) from ".$db_prefix."module_news");
	$db->CreateSequence($db_prefix."module_news_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating module_news_categories table sequence...";

	$max = $db->GetOne("SELECT max(news_category_id) from ".$db_prefix."module_news_categories");
	$db->CreateSequence($db_prefix."module_news_categories_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating permissions table sequence...";

	$max = $db->GetOne("SELECT max(permission_id) from ".$db_prefix."permissions");
	$db->CreateSequence($db_prefix."permissions_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating templates table sequence...";

	$max = $db->GetOne("SELECT max(template_id) from ".$db_prefix."templates");
	$db->CreateSequence($db_prefix."templates_seq", $max+1);

	echo "[done]</p>";
	
	#echo "<p>Creating sequence table sequence...";

	#$max = $db->GetOne("SELECT max(sequence_id) from ".$db_prefix."sequence");
	#$db->CreateSequence($db_prefix."sequence_seq", $max+1);

	#echo "[done]</p>";

	echo "<p>Creating users table sequence...";

	$max = $db->GetOne("SELECT max(user_id) from ".$db_prefix."users");
	$db->CreateSequence($db_prefix."users_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating users table sequence...";

	$max = $db->GetOne("SELECT max(userplugin_id) from ".$db_prefix."userplugins");
	$db->CreateSequence($db_prefix."userplugins_seq", $max+1);

	echo "[done]</p>";
}

# vim:ts=4 sw=4 noet
?>
