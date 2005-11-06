<?php

echo "<p>Creating adminlog table...";

$query  = "CREATE TABLE ".$config["db_prefix"]."adminlog (";
$query .= "  timestamp int(11),";
$query .= "  user_id int(11),";
$query .= "  username varchar(25),";
$query .= "  item_id int(11),";
$query .= "  item_name varchar(50),";
$query .= "  action varchar(255)";
$query .= ") TYPE=MyISAM";

$db->Execute($query);

echo "[done]</p>";

echo "<p>Creating userprefs table...";

$query  = "CREATE TABLE ".$config["db_prefix"]."userprefs (";
$query .= "  user_id int(11),";
$query .= "  preference varchar(50),";
$query .= "  value varchar(255),";
$query .= "  type varchar(25)";
$query .= ") TYPE=MyISAM";

$db->Execute($query);

$query = "SELECT user_id FROM ".$config["db_prefix"]."users";
$result = $db->Execute($query);
if ($result)
{
	while ($row = $result->FetchRow()) {
		$query = "INSERT INTO ".$config["db_prefix"]."userprefs (user_id, preference, value) VALUES (".$row["user_id"].", 'use_wysiwyg', '1')";
		$db->Execute($query);
	}
}

echo "[done]</p>";

echo "<p>Creating indexes...";

$query = "CREATE INDEX idx_template_id_modified_date ON ".$config["db_prefix"]."pages (template_id, modified_date)";
$db->Execute($query);

echo "[done]</p>";

echo "<p>Updating sections table schema...";

$query = "ALTER table ".$config["db_prefix"]."sections ADD parent_id int(11) NOT NULL DEFAULT 0";
$db->Execute($query);

echo "[done]</p>";

echo "<p>Updating user table schema...";

$query = "ALTER table ".$config["db_prefix"]."users CHANGE password password varchar(40)";
$db->Execute($query);

echo "[done]</p>";

echo "<p>Hashing passwords...";

$query = "SELECT user_id, password FROM ".$config["db_prefix"]."users";
$result = $db->Execute($query);
if ($result)
{
	while ($row = $result->FetchRow()) {
		$query = "UPDATE ".$config["db_prefix"]."users SET password = '".md5($row["password"])."' where user_id = " . $row["user_id"];
		$db->Execute($query);
	}
}

echo "[done]</p>";

echo "<p>Updating content types...";

$query = "UPDATE ".$config["db_prefix"]."pages SET page_type = 'content'";
$db->Execute($query);

echo "[done]</p>";

echo "<p>Added item_order to existing sections... ";

$count = 1;
$query = "SELECT section_id FROM ".$config["db_prefix"]."sections ORDER BY section_id";
$result = $db->query($query);

#foreach loop
if ($result)
{
	while($row = $result->FetchRow()) {
		$query = "UPDATE ".$config["db_prefix"]."sections SET item_order = $count WHERE section_id = " . $row["section_id"];
		$db->Execute($query);
		$count++;
	}
}

echo "[done]</p>";

echo "<p>Reseting cache update time...";

$query = "UPDATE ".$config["db_prefix"]."templates SET modified_date = now()";
$db->Execute($query);

echo "[done]</p>";

echo "<p>Updating schema version... ";

$query = "UPDATE ".$config["db_prefix"]."version SET version = 3";
$db->Execute($query);

echo "[done]</p>";

# vim:ts=4 sw=4 noet
?>
