<?php

require_once("../include.php");

check_login($config);

$group_id = -1;
if (isset($_GET["group_id"])) {

	$group_id = $_GET["group_id"];
	$userid = get_userid();
	$access = check_permission($config, $userid, 'Remove Group');

	if ($access) {
		$db = new DB($config);

		$query = "DELETE FROM ".$config->db_prefix."groups where group_id = $group_id";
		$result = $db->query($query);
		$db->close();
	}
}

redirect("listgroups.php");
