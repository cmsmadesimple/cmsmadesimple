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

	echo "<p>Creating indexes...";

	$query = "CREATE INDEX idx_template_id_modified_date ON cms_pages (template_id, modified_date)";
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
