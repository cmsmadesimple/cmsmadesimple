<?php

//$doupgrade = false;

$db = new DB($config);

/*
$query = "SELECT version from ".$config->db_prefix."version";
$result = $db->query($query);
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	if ($row["version"] == $current_version) {
		$doupgrade = true;
	}
}
mysql_free_result($result);
*/

//if ($doupgrade) {

	echo "<p>Creating plugins table...";

	$query  = "CREATE TABLE ".$config->db_prefix."plugins (";
	$query .= "  plugin_id int(11) NOT NULL auto_increment,";
	$query .= "  plugin_name varchar(50) default NULL,";
	$query .= "  plugin_path varchar(250) default NULL,";
	$query .= "  PRIMARY KEY (plugin_id)";
	$query .= ") TYPE=MyISAM";

	$db->query($query);

	echo "[done]</p>";

	echo "<p>Creating adminlog table...";

	$query  = "CREATE TABLE ".$config->db_prefix."adminlog (";
	$query .= "  timestamp int(11),";
	$query .= "  user_id int(11),";
	$query .= "  username varchar(25),";
	$query .= "  item_id int(11),";
	$query .= "  item_name varchar(50),";
	$query .= "  action varchar(255)";
	$query .= ") TYPE=MyISAM";

	$db->query($query);

	echo "[done]</p>";

	echo "<p>Creating indexes...";

	$query = "CREATE INDEX idx_template_id_modified_date ON ".$config->db_prefix."pages (template_id, modified_date)";
	$db->query($query);

	echo "[done]</p>";

	echo "<p>Updating user table schema...";

	$query = "ALTER table ".$config->db_prefix."users CHANGE password password varchar(40)";
	$db->query($query);

	echo "[done]</p>";

	echo "<p>Hashing passwords...";

	$query = "SELECT user_id, password FROM ".$config->db_prefix."users";
	$result = $db->query($query);
	while ($row = $db->getresulthash($result)) {
		$query = "UPDATE ".$config->db_prefix."users SET password = '".md5($row["password"])."' where user_id = " . $row["user_id"];
		$db->query($query);
	}
	$db->freeresult($result);

	echo "[done]</p>";

	echo "<p>Updating content types...";

	$query = "UPDATE ".$config->db_prefix."pages SET page_type = 'content'";
	$db->query($query);

	echo "[done]</p>";

	echo "<p>Added item_order to existing sections... ";

	$count = 1;
	$query = "SELECT section_id FROM ".$config->db_prefix."sections ORDER BY section_id";
	$result = $db->query($query);

	#foreach loop
	while($row = $db->getresulthash($result)) {
		$query = "UPDATE ".$config->db_prefix."sections SET item_order = $count WHERE section_id = " . $row["section_id"];
		$db->query($query);
		$count++;
	}

	$db->freeresult($result);

	echo "[done]</p>";

	echo "<p>Reseting cache update time...";

	$query = "UPDATE ".$config->db_prefix."templates SET modified_date = now()";
	$db->query($query);

	echo "[done]</p>";

	echo "<p>Updating schema version... ";

	$query = "UPDATE ".$config->db_prefix."version SET version = 3";
	$db->query($query);

	echo "[done]</p>";
//}

$db->close();

# vim:ts=4 sw=4 noet
?>
