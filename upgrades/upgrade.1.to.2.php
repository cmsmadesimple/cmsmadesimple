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

if ($doupgrade) {
*/

	echo "<p>Added item_order to existing items... ";

	$count = 1;
	$oldsection_id = -1;
	$query = "SELECT page_id, section_id FROM ".$config->db_prefix."pages ORDER BY section_id, page_title";
	$result = $db->query($query);

	#foreach loop
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ($oldsection_id != $row["section_id"]) {
			$oldsection_id = $row["section_id"];
			$count = 1;
		}
		$query = "UPDATE ".$config->db_prefix."pages SET item_order = $count WHERE page_id = " . $row["page_id"];
		$db->query($query);

		$count++;
	}

	mysql_free_result($result);

	echo "[done]</p>";

	echo "<p>Creating additional_users table... ";

	$query  = "CREATE TABLE ".$config->db_prefix."additional_users (";
	$query .= "  additional_users_id int(11) NOT NULL auto_increment,";
	$query .= "  user_id int(11) default NULL,";
	$query .= "  page_id int(11) default NULL,";
	$query .= "  PRIMARY KEY (additional_users_id)";
	$query .= ") TYPE=MyISAM";

	$db->query($query);

	echo "[done]</p>";

	echo "<p>Updating schema version... ";

	$query = "UPDATE ".$config->db_prefix."version SET version = 2";
	$db->query($query);

	echo "[done]</p>";
//}

$db->close();

# vim:ts=4 sw=4 noet
?>
