<?php

require_once("../include.php");

check_login($config);

$user_id = -1;
if (isset($_GET["user_id"])) {

	$user_id = $_GET["user_id"];
	$userid = get_userid();
	$access = check_permission($config, $userid, 'Remove User');

	if ($access) {
		$db = new DB($config);

		$query = "DELETE FROM ".$config->db_prefix."users where user_id = $user_id";
		$result = $db->query($query);
		$db->close();
	}
}

redirect("listusers.php");
