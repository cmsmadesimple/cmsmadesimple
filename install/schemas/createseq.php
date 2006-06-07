<?php

if (isset($CMS_INSTALL_CREATE_TABLES)) {

	echo "<p>Creating additional_users table sequence...";
    $max = $db->Execute("SELECT max(additional_users_id) from ".$db_prefix."additional_users");
    if ($max && $row=$max->FetchRow()) $max =$row['max(additional_users_id)']+1; else $max=1;
    $db->CreateSequence($db_prefix."additional_users_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating admin_bookmarks table sequence...";

	$max = $db->Execute("SELECT max(bookmark_id) from ".$db_prefix."admin_bookmarks");
    if ($max && $row=$max->FetchRow()) $max =$row['max(bookmark_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."admin_bookmarks_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating admin_recent_pages table sequence...";

	$max = $db->Execute("SELECT max(id) from ".$db_prefix."admin_recent_pages");
	if ($max && $row=$max->FetchRow()) $max =$row['max(id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."admin_recent_pages_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating content table sequence...";

	$max = $db->Execute("SELECT max(content_id) from ".$db_prefix."content");
    if ($max && $row=$max->FetchRow()) $max =$row['max(content_id)']+1; else $max=1;
    $db->CreateSequence($db_prefix."content_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating content_props table sequence...";

	$max = $db->Execute("SELECT max(content_id) from ".$db_prefix."content_props");
    if ($max && $row=$max->FetchRow()) $max =$row['max(content_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."content_props_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating css table sequence...";

	$max = $db->Execute("SELECT max(css_id) from ".$db_prefix."css");
    if ($max && $row=$max->FetchRow()) $max =$row['max(css_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."css_seq", $max);

	echo "[done]</p>";
	
	echo "<p>Creating events table sequence...";

	$max = $db->Execute("SELECT max(event_id) from ".$db_prefix."events");
    if ($max && $row=$max->FetchRow()) $max =$row['max(event_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."events_seq", $max);

	echo "[done]</p>";
	
	echo "<p>Creating event_handlers table sequence...";

	$max = $db->Execute("SELECT max(handler_id) from ".$db_prefix."event_handlers");
    if ($max && $row=$max->FetchRow()) $max =$row['max(handler_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."event_handler_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating group_perms table sequence...";

	$max = $db->Execute("SELECT max(group_perm_id) from ".$db_prefix."group_perms");
    if ($max && $row=$max->FetchRow()) $max =$row['max(group_perm_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."group_perms_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating groups table sequence...";

	$max = $db->Execute("SELECT max(group_id) from ".$db_prefix."groups");
    if ($max && $row=$max->FetchRow()) $max =$row['max(group_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."groups_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating htmlblobs table sequence...";

	$max = $db->Execute("SELECT max(htmlblob_id) from ".$db_prefix."htmlblobs");
    if ($max && $row=$max->FetchRow()) $max =$row['max(htmlblob_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."htmlblobs_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating additional_htmlblob_users table sequence...";

	$max = $db->Execute("SELECT max(additional_htmlblob_users_id) from".$db_prefix."additional_htmlblob_users");
    if ($max && $row=$max->FetchRow()) $max =$row['max(additional_htmlblob_users_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."additional_htmlblob_users_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating permissions table sequence...";

	$max = $db->Execute("SELECT max(permission_id) from ".$db_prefix."permissions");
    if ($max && $row=$max->FetchRow()) $max =$row['max(permission_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."permissions_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating templates table sequence...";

	$max = $db->Execute("SELECT max(template_id) from ".$db_prefix."templates");
    if ($max && $row=$max->FetchRow()) $max =$row['max(template_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."templates_seq", $max);

	echo "[done]</p>";
	

	echo "<p>Creating users table sequence...";

	$max = $db->Execute("SELECT max(user_id) from ".$db_prefix."users");
    if ($max && $row=$max->FetchRow()) $max =$row['max(user_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."users_seq", $max);

	echo "[done]</p>";

	echo "<p>Creating users table sequence...";

	$max = $db->Execute("SELECT max(userplugin_id) from ".$db_prefix."userplugins");
    if ($max && $row=$max->FetchRow()) $max =$row['max(userplugin_id)']+1; else $max=1;
	$db->CreateSequence($db_prefix."userplugins_seq", $max);

	echo "[done]</p>";
}

# vim:ts=4 sw=4 noet
?>