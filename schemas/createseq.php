<?php

if (isset($CMS_INSTALL_CREATE_TABLES)) {

	echo "<p>Creating additional_users table sequence...";

	$max = $db->GetOne("SELECT max(additional_users_id) from ".$db_prefix."additional_users");
	$db->CreateSequence($db_prefix."additional_users_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating group_perms table sequence...";

	$max = $db->GetOne("SELECT max(group_perm_id) from ".$db_prefix."group_perms");
	$db->CreateSequence($db_prefix."group_perms_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating groups table sequence...";

	$max = $db->GetOne("SELECT max(group_id) from ".$db_prefix."groups");
	$db->CreateSequence($db_prefix."groups_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating module_news table sequence...";

	$max = $db->GetOne("SELECT max(module_news_id) from ".$db_prefix."module_news");
	$db->CreateSequence($db_prefix."module_news_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating pages table sequence...";

	$max = $db->GetOne("SELECT max(page_id) from ".$db_prefix."pages");
	$db->CreateSequence($db_prefix."pages_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating permissions table sequence...";

	$max = $db->GetOne("SELECT max(permission_id) from ".$db_prefix."permissions");
	$db->CreateSequence($db_prefix."permissions_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating templates table sequence...";

	$max = $db->GetOne("SELECT max(template_id) from ".$db_prefix."templates");
	$db->CreateSequence($db_prefix."templates_seq", $max+1);

	echo "[done]</p>";

	echo "<p>Creating users table sequence...";

	$max = $db->GetOne("SELECT max(user_id) from ".$db_prefix."users");
	$db->CreateSequence($db_prefix."users_seq", $max+1);

	echo "[done]</p>";
}

# vim:ts=4 sw=4 noet
?>
