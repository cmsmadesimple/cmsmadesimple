<?php

$current_schema_ver = 2;
$new_schema_ver = 3;
$doupgrade = false;

$db = new DB($config);

$query = "SELECT version from ".$config->db_prefix."version";
$result = $db->query($query);
while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	if ($row["version"] == $current_schema_ver) {
		$doupgrade = true;
	}
}
mysql_free_result($result);

if ($doupgrade) {

	echo "<p>Creating plugins table...";

	$query  = "CREATE TABLE ".$config->db_prefix."plugins (";
	$query .= "  plugin_id int(11) NOT NULL auto_increment,";
	$query .= "  plugin_name varchar(50) default NULL,";
	$query .= "  plugin_path varchar(250) default NULL,";
	$query .= "  PRIMARY KEY (plugin_id)";
	$query .= ") TYPE=MyISAM";

	$result = $db->query($query);

	echo "[done]</p>";

	echo "<p>Updating schema version... ";

	$query = "UPDATE ".$config->db_prefix."version SET version = ".$new_schema_ver;
	$db->query($query);

	echo "[done]</p>";
}

$db->close();

# vim:ts=4 sw=4 noet
?>
